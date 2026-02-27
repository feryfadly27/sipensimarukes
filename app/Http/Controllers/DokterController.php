<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Mahasiswa;
use App\Models\PemeriksaanDokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        $prodis = Mahasiswa::select('prodi')->distinct()->whereNotNull('prodi')->pluck('prodi');

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
        if ($mahasiswa->status_plp !== 'selesai' || $mahasiswa->status_dokter !== 'belum') {
            return redirect()->route('dokter.index')->with('error', 'Peserta tidak siap untuk pemeriksaan dokter.');
        }

        $existing = PemeriksaanDokter::where('mahasiswa_id', $mahasiswa->id)->first();
        if ($existing && $existing->is_locked) {
            return redirect()->route('dokter.index')->with('error', 'Data pemeriksaan dokter sudah terkunci.');
        }

        $validated = $request->validate([
            'tgl_periksa' => 'required|date',
            'kulit' => 'required|in:Putih,Kuning,Hitam,Sawo matang',
            'mata_kacamata' => 'required|in:Berkacamata,Tidak berkacamata',
            'mata_minus' => 'required|boolean',
            'mata_minus_nilai' => 'nullable|numeric|required_if:mata_minus,1',
            'mata_silindris' => 'required|boolean',
            'mata_silindris_nilai' => 'nullable|numeric|required_if:mata_silindris,1',
            'mata_strabismus' => 'required|boolean',
            'mata_strabismus_nilai' => 'nullable|string|max:50|required_if:mata_strabismus,1',
            'telinga_kiri' => 'required|in:Mendengar jelas,Tidak bisa mendengar',
            'telinga_kiri_ket' => 'nullable|string',
            'telinga_kanan' => 'required|in:Mendengar jelas,Tidak bisa mendengar',
            'telinga_kanan_ket' => 'nullable|string',
            'hidung_cuping' => 'required|boolean',
            'hidung_cuping_ket' => 'nullable|string',
            'lidah_kebersihan' => 'required|in:Bersih,Kurang bersih,Kotor',
            'lidah_kebersihan_ket' => 'nullable|string',
            'lidah_stomatitis' => 'required|boolean',
            'lidah_stomatitis_ket' => 'nullable|string',
            'pharing_nyeri_tekan' => 'required|boolean',
            'pharing_nyeri_tekan_ket' => 'nullable|string',
            'tonsil_kemerahan' => 'required|boolean',
            'tonsil_kemerahan_ket' => 'nullable|string',
            'tonsil_pembesaran' => 'required|boolean',
            'gigi_lengkap' => 'required|boolean',
            'tiroid' => 'nullable|string',
            'jantung_murmur' => 'required|boolean',
            'jantung_murmur_ket' => 'nullable|string',
            'paru_suara_tambahan' => 'required|boolean',
            'abdomen_hamil' => 'required|boolean',
            'pupil' => 'required|in:Isokor,Anisokor',
            'thorax_photo_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'thorax_photo_ket' => 'nullable|string',
            'tulang_skoliosis' => 'required|boolean',
            'tulang_skoliosis_ket' => 'nullable|string',
            'tulang_lordosis' => 'required|boolean',
            'tulang_lordosis_ket' => 'nullable|string',
            'tulang_kifosis' => 'required|boolean',
            'tulang_kifosis_ket' => 'nullable|string',
            'tulang_lainnya' => 'required|boolean',
            'tulang_lainnya_ket' => 'nullable|string',
            'bicara_artikulasi' => 'required|in:Artikulasi jelas,Tidak jelas',
            'bicara_artikulasi_ket' => 'nullable|string',
            'cacat_tubuh' => 'nullable|string',
            'kesimpulan' => 'required|in:Memenuhi Syarat,Tidak Memenuhi Syarat',
            'keterangan_kesimpulan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $mahasiswa, $validated, $existing) {
            $oldData = $existing ? $existing->toArray() : null;

            $data = $validated;
            $data['dokter_id'] = Auth::id();
            $data['is_locked'] = true;

            if ($request->hasFile('thorax_photo_file')) {
                $data['thorax_photo_file'] = $request->file('thorax_photo_file')->store('uploads/thorax', 'public');
            } elseif ($existing) {
                $data['thorax_photo_file'] = $existing->thorax_photo_file;
            }

            $pemeriksaanDokter = PemeriksaanDokter::updateOrCreate(
                ['mahasiswa_id' => $mahasiswa->id],
                $data
            );

            $kesimpulanAkhir = $validated['kesimpulan'] === 'Memenuhi Syarat'
                ? 'memenuhi_syarat'
                : 'tidak_memenuhi_syarat';

            $mahasiswa->update([
                'status_dokter' => 'selesai',
                'kesimpulan_akhir' => $kesimpulanAkhir,
                'keterangan_kesimpulan' => $validated['keterangan_kesimpulan'] ?? null,
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
}
