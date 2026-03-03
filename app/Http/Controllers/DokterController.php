<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Mahasiswa;
use App\Models\PemeriksaanDokter;
use App\Models\PemeriksaanPlp;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DokterController extends Controller
{
    public function completed(Request $request)
    {
        $allowedPerPage = [10, 25, 50, 100];
        $perPage = (int) $request->get('per_page', 25);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 25;
        }

        $query = Mahasiswa::query()
            ->select([
                'id',
                'no_pendaftaran',
                'nama',
                'no_identitas',
                'prodi',
                'kesimpulan_akhir',
                'status_dokter',
                'updated_at',
            ])
            ->where('status_dokter', 'selesai')
            ->with([
                'pemeriksaanDokter:id,mahasiswa_id,tgl_periksa,dokter_id,kesimpulan,is_locked',
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('no_identitas', 'like', "%{$search}%");
            });
        }

        if ($request->filled('prodi')) {
            $query->where('prodi', $request->prodi);
        }

        if ($request->filled('kesimpulan_akhir')) {
            $query->where('kesimpulan_akhir', $request->kesimpulan_akhir);
        }

        if ($request->filled('tgl_periksa_mulai') || $request->filled('tgl_periksa_selesai')) {
            $startDate = $request->tgl_periksa_mulai;
            $endDate = $request->tgl_periksa_selesai;

            $query->whereHas('pemeriksaanDokter', function ($q) use ($startDate, $endDate) {
                if (!empty($startDate)) {
                    $q->whereDate('tgl_periksa', '>=', $startDate);
                }

                if (!empty($endDate)) {
                    $q->whereDate('tgl_periksa', '<=', $endDate);
                }
            });
        }

        $mahasiswa = $query->orderByDesc('updated_at')->paginate($perPage)->withQueryString();
        $prodis = Prodi::aktif()->orderBy('nama')->pluck('nama');

        return view('dokter.completed', compact('mahasiswa', 'prodis'));
    }

    public function form(Mahasiswa $mahasiswa)
    {
        if ($mahasiswa->status_plp !== 'selesai') {
            return redirect()->route('dokter.index')->with('error', 'Peserta belum menyelesaikan pemeriksaan PLP.');
        }

        if ($mahasiswa->status_dokter === 'selesai') {
            return redirect()->route('dokter.index')->with('error', 'Pemeriksaan dokter peserta ini sudah selesai.');
        }

        $pemeriksaanPlp = $mahasiswa->pemeriksaanPlp;
        $pemeriksaanDokter = $mahasiswa->pemeriksaanDokter;

        Cache::put('dokter_active_' . Auth::id(), [
            'dokter_id' => Auth::id(),
            'dokter_nama' => Auth::user()->nama,
            'mahasiswa_id' => $mahasiswa->id,
            'mahasiswa_nama' => $mahasiswa->nama,
            'mahasiswa_no_pendaftaran' => $mahasiswa->no_pendaftaran,
            'started_at' => now()->toDateTimeString(),
        ], now()->addMinutes(30));

        return view('dokter.form', compact('mahasiswa', 'pemeriksaanPlp', 'pemeriksaanDokter'));
    }

    public function show(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load(['pemeriksaanPlp.plp', 'pemeriksaanDokter.dokter']);

        if ($mahasiswa->status_dokter !== 'selesai' || !$mahasiswa->pemeriksaanDokter) {
            return redirect()->route('dokter.completed')->with('error', 'Hasil pemeriksaan dokter belum tersedia.');
        }

        $petugasPendaftaran = LogAktivitas::with('user')
            ->where('target_tabel', 'mahasiswa')
            ->where('target_id', $mahasiswa->id)
            ->where('aksi', 'like', 'Validasi kehadiran:%')
            ->orderByDesc('waktu')
            ->first()?->user;

        return view('dokter.show', compact('mahasiswa', 'petugasPendaftaran'));
    }

    public function print(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load(['pemeriksaanPlp.plp', 'pemeriksaanDokter.dokter']);

        if ($mahasiswa->status_dokter !== 'selesai' || !$mahasiswa->pemeriksaanDokter) {
            return redirect()->route('dokter.completed')->with('error', 'Hasil pemeriksaan dokter belum tersedia.');
        }

        $petugasPendaftaran = LogAktivitas::with('user')
            ->where('target_tabel', 'mahasiswa')
            ->where('target_id', $mahasiswa->id)
            ->where('aksi', 'like', 'Validasi kehadiran:%')
            ->orderByDesc('waktu')
            ->first()?->user;

        return view('dokter.print', compact('mahasiswa', 'petugasPendaftaran'));
    }

    public function store(Request $request, Mahasiswa $mahasiswa)
    {
        $request->merge([
            'mata_minus_nilai' => $this->normalizeDecimalInput($request->input('mata_minus_nilai')),
            'mata_silindris_nilai' => $this->normalizeDecimalInput($request->input('mata_silindris_nilai')),
        ]);

        if ($mahasiswa->status_plp !== 'selesai' || $mahasiswa->status_dokter !== 'belum') {
            return redirect()->route('dokter.index')->with('error', 'Peserta tidak siap untuk pemeriksaan dokter.');
        }

        $existing = PemeriksaanDokter::where('mahasiswa_id', $mahasiswa->id)->first();
        if ($existing && $existing->is_locked) {
            return redirect()->route('dokter.index')->with('error', 'Data pemeriksaan dokter sudah terkunci.');
        }

        $validated = $request->validate([
            'tgl_periksa' => 'required|date',
            'mata_kacamata' => 'required|in:Berkacamata,Tidak berkacamata',
            'mata_ikterik' => 'required|in:Tidak,Ya',
            'mata_konjungtiva_anemis' => 'required|in:Tidak,Ya',
            'mata_minus' => 'required|boolean',
            'mata_minus_nilai' => 'nullable|numeric|between:-9.99,9.99|required_if:mata_minus,1',
            'mata_silindris' => 'required|boolean',
            'mata_silindris_nilai' => 'nullable|numeric|between:-9.99,9.99|required_if:mata_silindris,1',
            'mata_strabismus' => 'required|boolean',
            'mata_strabismus_nilai' => 'nullable|string|max:50|required_if:mata_strabismus,1',
            'pendengaran' => 'required|in:Normal,Terganggu',
            'pendengaran_ket' => 'nullable|string|required_if:pendengaran,Terganggu',
            'hidung_cuping' => 'required|boolean',
            'hidung_cuping_ket' => 'nullable|string',
            'mulut_labioskisis' => 'required|in:Tidak,Ya',
            'mulut_palatoskisis' => 'required|in:Tidak,Ya',
            'pharing_nyeri_tekan' => 'required|boolean',
            'pharing_nyeri_tekan_ket' => 'nullable|string',
            'tonsil_kemerahan' => 'required|boolean',
            'tonsil_kemerahan_ket' => 'nullable|string',
            'tonsil_pembesaran' => 'required|boolean',
            'gigi_lengkap' => 'required|boolean',
            'leher_kgb_pembesaran' => 'required|in:Tidak,Ya',
            'jantung_dbn' => 'required|in:DBN,Ada Kelainan',
            'jantung_kelainan' => 'nullable|string|required_if:jantung_dbn,Ada Kelainan',
            'paru_dbn' => 'required|in:DBN,Ada Kelainan',
            'paru_kelainan' => 'nullable|string|required_if:paru_dbn,Ada Kelainan',
            'abdomen_hamil' => 'required|boolean',
            'thorax_photo_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'thorax_photo_ket' => 'nullable|string',
            'tulang_belakang' => 'required|in:DBN,Lordosis,Kifosis,Skoliosis',
            'jari_tangan_lengkap' => 'required|in:Lengkap,Tidak Lengkap',
            'jari_tangan_ket' => 'nullable|string|required_if:jari_tangan_lengkap,Tidak Lengkap',
            'bicara_artikulasi' => 'required|in:Artikulasi jelas,Tidak jelas',
            'bicara_artikulasi_ket' => 'nullable|string',
            'cacat_tubuh' => 'nullable|string',
            'status_kelulusan' => 'required|in:Lulus,Pending,Tidak Lulus,Lulus Dengan Syarat',
            'surat_rujukan' => [
                'nullable',
                'string',
                Rule::requiredIf(function () use ($request) {
                    return in_array($request->input('status_kelulusan'), ['Pending', 'Lulus Dengan Syarat'], true);
                }),
            ],
            'keterangan_kesimpulan' => [
                'nullable',
                'string',
                Rule::requiredIf(function () use ($request) {
                    return in_array($request->input('status_kelulusan'), ['Pending', 'Tidak Lulus'], true);
                }),
            ],
            'catatan_warning_dokter' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $mahasiswa, $validated, $existing) {
            $oldData = $existing ? $existing->toArray() : null;

            $data = $validated;
            $data['dokter_id'] = Auth::id();
            $data['is_locked'] = true;

            if ((string) ($validated['mata_minus'] ?? '0') !== '1') {
                $data['mata_minus_nilai'] = null;
            }

            if ((string) ($validated['mata_silindris'] ?? '0') !== '1') {
                $data['mata_silindris_nilai'] = null;
            }

            if ((string) ($validated['mata_strabismus'] ?? '0') !== '1') {
                $data['mata_strabismus_nilai'] = null;
            }

            $data['mata_normal'] = 'Normal';
            $data['mata_sklera'] = $validated['mata_ikterik'] === 'Ya' ? 'Tidak normal' : 'Normal';
            $data['mata_konjungtiba'] = $validated['mata_konjungtiva_anemis'] === 'Ya' ? 'Tidak normal' : 'Normal';
            $data['telinga_kiri'] = $validated['pendengaran'] === 'Normal' ? 'Mendengar jelas' : 'Tidak bisa mendengar';
            $data['telinga_kanan'] = $validated['pendengaran'] === 'Normal' ? 'Mendengar jelas' : 'Tidak bisa mendengar';
            $data['telinga_kiri_ket'] = $validated['pendengaran_ket'] ?? null;
            $data['telinga_kanan_ket'] = $validated['pendengaran_ket'] ?? null;
            $data['jantung_murmur'] = $validated['jantung_dbn'] === 'Ada Kelainan';
            $data['jantung_murmur_ket'] = $validated['jantung_kelainan'] ?? null;
            $data['paru_suara_tambahan'] = $validated['paru_dbn'] === 'Ada Kelainan';
            $data['tulang_skoliosis'] = $validated['tulang_belakang'] === 'Skoliosis';
            $data['tulang_lordosis'] = $validated['tulang_belakang'] === 'Lordosis';
            $data['tulang_kifosis'] = $validated['tulang_belakang'] === 'Kifosis';
            $data['tulang_lainnya'] = false;
            $data['tulang_skoliosis_ket'] = null;
            $data['tulang_lordosis_ket'] = null;
            $data['tulang_kifosis_ket'] = null;
            $data['tulang_lainnya_ket'] = null;
            $data['lidah_kebersihan'] = null;
            $data['lidah_kebersihan_ket'] = null;
            $data['lidah_stomatitis'] = false;
            $data['lidah_stomatitis_ket'] = null;
            $data['tiroid'] = null;
            $data['pupil'] = null;
            $data['kulit'] = null;

            $data['surat_rujukan'] = filled(trim((string) ($validated['surat_rujukan'] ?? '')))
                ? trim((string) $validated['surat_rujukan'])
                : null;

            $statusKelulusan = $validated['status_kelulusan'];

            $kesimpulanDokter = match ($statusKelulusan) {
                'Lulus', 'Lulus Dengan Syarat' => 'Memenuhi Syarat',
                'Pending',
                'Tidak Lulus' => 'Tidak Memenuhi Syarat',
            };

            $data['kesimpulan'] = $kesimpulanDokter;
            if (in_array($statusKelulusan, ['Pending', 'Lulus Dengan Syarat'], true) && filled($data['surat_rujukan'])) {
                $keteranganSebelumnya = trim((string) ($validated['keterangan_kesimpulan'] ?? ''));
                $data['keterangan_kesimpulan'] = $keteranganSebelumnya !== ''
                    ? $keteranganSebelumnya . ' | Rujukan: ' . $data['surat_rujukan']
                    : 'Rujukan: ' . $data['surat_rujukan'];
            }

            if ($request->hasFile('thorax_photo_file')) {
                $data['thorax_photo_file'] = $request->file('thorax_photo_file')->store('uploads/thorax', 'public');
            } elseif ($existing) {
                $data['thorax_photo_file'] = $existing->thorax_photo_file;
            }

            $pemeriksaanDokter = PemeriksaanDokter::updateOrCreate(
                ['mahasiswa_id' => $mahasiswa->id],
                $data
            );

            $kesimpulanAkhir = in_array($statusKelulusan, ['Lulus', 'Lulus Dengan Syarat'], true)
                ? 'memenuhi_syarat'
                : 'tidak_memenuhi_syarat';

            $mahasiswa->update([
                'status_dokter' => 'selesai',
                'kesimpulan_akhir' => $kesimpulanAkhir,
                'keterangan_kesimpulan' => $validated['keterangan_kesimpulan'] ?? null,
            ]);

            PemeriksaanPlp::where('mahasiswa_id', $mahasiswa->id)->update([
                'catatan_warning_dokter' => filled(trim((string) ($validated['catatan_warning_dokter'] ?? '')))
                    ? trim((string) $validated['catatan_warning_dokter'])
                    : '-',
            ]);

            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aksi' => 'Menyelesaikan pemeriksaan Dokter',
                'target_tabel' => 'pemeriksaan_dokter',
                'target_id' => $pemeriksaanDokter->id,
                'data_lama' => $oldData,
                'data_baru' => $pemeriksaanDokter->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'waktu' => now(),
            ]);
        });

        Cache::forget('dokter_active_' . Auth::id());

        return redirect()->route('dokter.index')->with('success', 'Pemeriksaan dokter berhasil disimpan dan dikunci.');
    }

    private function normalizeDecimalInput($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);
        if ($normalized === '') {
            return null;
        }

        $normalized = str_replace(',', '.', $normalized);

        return $normalized;
    }
}
