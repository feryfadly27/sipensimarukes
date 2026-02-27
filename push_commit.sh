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

if [[ -n "$(git status --porcelain)" ]]; then
  :
else
  echo "Tidak ada perubahan untuk di-commit."
  exit 0
fi

BRANCH="$(git rev-parse --abbrev-ref HEAD)"

if [[ $# -gt 0 ]]; then
  COMMIT_MSG="$*"
else
  read -r -p "Masukkan pesan commit: " COMMIT_MSG
fi

if [[ -z "${COMMIT_MSG// }" ]]; then
  echo "Error: pesan commit tidak boleh kosong."
  exit 1
fi

echo "\n==> Menambahkan semua perubahan..."
git add -A

echo "==> Commit: $COMMIT_MSG"
git commit -m "$COMMIT_MSG"

echo "==> Push ke origin/$BRANCH"
git push origin "$BRANCH"

echo "\nSelesai. Commit dan push berhasil ke branch '$BRANCH'."
