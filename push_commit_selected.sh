#!/bin/bash

set -euo pipefail

if ! command -v git >/dev/null 2>&1; then
  echo "Error: git tidak ditemukan di PATH."
  exit 1
fi

if ! git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
  echo "Error: script harus dijalankan di dalam repository git."
  exit 1
fi

cd "$(git rev-parse --show-toplevel)"

usage() {
  echo "Penggunaan:"
  echo "  ./push_commit_selected.sh \"pesan commit\" file1 [file2 ...]"
  echo "  ./push_commit_selected.sh -i [\"pesan commit\"]"
  echo ""
  echo "Contoh:"
  echo "  ./push_commit_selected.sh \"fix: update login ui\" resources/views/auth/login.blade.php"
  echo "  ./push_commit_selected.sh -i \"chore: commit selected files\""
}

INTERACTIVE=false
if [[ "${1:-}" == "-i" || "${1:-}" == "--interactive" ]]; then
  INTERACTIVE=true
  shift
fi

parse_index_selection() {
  local raw="$1"
  local max="$2"
  local cleaned token start end i
  local -a numbers=()

  cleaned="${raw//,/ }"
  for token in $cleaned; do
    if [[ "$token" =~ ^[0-9]+-[0-9]+$ ]]; then
      start="${token%-*}"
      end="${token#*-}"

      if (( start > end )); then
        local tmp="$start"
        start="$end"
        end="$tmp"
      fi

      for ((i=start; i<=end; i++)); do
        numbers+=("$i")
      done
    elif [[ "$token" =~ ^[0-9]+$ ]]; then
      numbers+=("$token")
    else
      echo "Error: format pilihan tidak valid -> $token"
      return 1
    fi
  done

  if [[ ${#numbers[@]} -eq 0 ]]; then
    echo "Error: tidak ada nomor yang dipilih."
    return 1
  fi

  local -A seen=()
  local -a unique=()
  for i in "${numbers[@]}"; do
    if (( i < 1 || i > max )); then
      echo "Error: nomor $i di luar jangkauan 1-$max"
      return 1
    fi

    if [[ -z "${seen[$i]:-}" ]]; then
      seen[$i]=1
      unique+=("$i")
    fi
  done

  echo "${unique[*]}"
}

COMMIT_MSG=""
FILES=()

if [[ "$INTERACTIVE" == true ]]; then
  if [[ $# -gt 0 ]]; then
    COMMIT_MSG="$1"
  else
    read -r -p "Masukkan pesan commit: " COMMIT_MSG
  fi

  if [[ -z "${COMMIT_MSG// }" ]]; then
    echo "Error: pesan commit tidak boleh kosong."
    exit 1
  fi

  mapfile -t CHANGED_FILES < <(
    {
      git diff --name-only
      git diff --cached --name-only
      git ls-files --others --exclude-standard
      git ls-files --deleted
    } | awk 'NF' | sort -u
  )

  if [[ ${#CHANGED_FILES[@]} -eq 0 ]]; then
    echo "Tidak ada perubahan file untuk dipilih."
    exit 0
  fi

  echo "Pilih file yang ingin di-commit (pisah dengan koma/spasi, bisa range seperti 1-3):"
  for idx in "${!CHANGED_FILES[@]}"; do
    printf "  %d) %s\n" "$((idx + 1))" "${CHANGED_FILES[$idx]}"
  done

  read -r -p "Nomor file: " selection
  parsed_indexes="$(parse_index_selection "$selection" "${#CHANGED_FILES[@]}")" || exit 1

  for picked in $parsed_indexes; do
    FILES+=("${CHANGED_FILES[$((picked - 1))]}")
  done
else
  if [[ $# -lt 2 ]]; then
    usage
    exit 1
  fi

  COMMIT_MSG="$1"
  shift
  FILES=("$@")
fi

if [[ -z "${COMMIT_MSG// }" ]]; then
  echo "Error: pesan commit tidak boleh kosong."
  exit 1
fi

# Validasi file input
for file in "${FILES[@]}"; do
  if [[ ! -e "$file" ]]; then
    echo "Error: file/path tidak ditemukan -> $file"
    exit 1
  fi
done

BRANCH="$(git rev-parse --abbrev-ref HEAD)"

echo "==> Menambahkan file terpilih..."
git add -- "${FILES[@]}"

if git diff --cached --quiet; then
  echo "Tidak ada perubahan staged dari file yang dipilih."
  exit 0
fi

echo "==> Commit: $COMMIT_MSG"
git commit -m "$COMMIT_MSG"

echo "==> Push ke origin/$BRANCH"
git push origin "$BRANCH"

echo "Selesai. Commit dan push file terpilih berhasil ke branch '$BRANCH'."
