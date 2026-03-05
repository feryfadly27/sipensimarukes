<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\PemeriksaanPlp;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlpController extends Controller
{
    private function canBeHandledByNakes(Mahasiswa $mahasiswa): bool
    {
        if ($mahasiswa->status_kehadiran !== 'hadir') {
            return false;
        }

        if ($mahasiswa->status_plp === 'belum') {
            return true;
        }

        $existing = $mahasiswa->pemeriksaanPlp()->with('plp:id,role')->first();
        return $mahasiswa->status_plp === 'selesai' && $existing?->plp?->role === 'superadmin';
    }

    /**
     * Cek pemeriksaan yang sedang berjalan (untuk mencegah tumpang tindih)
     */
    public function checkOngoing()
    {
        $ongoingList = PemeriksaanPlp::where('status_pemeriksaan', 'sedang_diperiksa')
            ->with(['mahasiswa', 'plp'])
            ->get();
        
        if ($ongoingList->count() > 0) {
            return response()->json([
                'success' => false,
                'has_ongoing' => true,
                'message' => 'Ada ' . $ongoingList->count() . ' pemeriksaan yang sedang berjalan',
                'data' => $ongoingList->map(fn($exam) => [
                    'mahasiswa_id' => $exam->mahasiswa_id,
                    'mahasiswa_nama' => $exam->mahasiswa->nama,
                    'mahasiswa_no_urut' => $exam->mahasiswa->nomor_urut,
                    'plp_nama' => $exam->plp?->nama ?? '-',
                    'started_at' => $exam->started_at,
                ])->toArray()
            ]);
        }

        return response()->json([
            'success' => true,
            'has_ongoing' => false,
            'message' => 'Tidak ada pemeriksaan yang sedang berjalan'
        ]);
    }

    /**
     * Get student data for verification before examination
     */
    public function verifyStudent(Mahasiswa $mahasiswa)
    {
        // Verify student is ready for Nakes examination
        if (!$this->canBeHandledByNakes($mahasiswa)) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak siap untuk pemeriksaan PLP'
            ], 422);
        }

        // Check if THIS STUDENT is already being examined (per-mahasiswa lock)
        $ongoing = PemeriksaanPlp::where('mahasiswa_id', $mahasiswa->id)
            ->where('status_pemeriksaan', 'sedang_diperiksa')
            ->with('plp')
            ->first();
        $isResume = false;
        if ($ongoing) {
            if ($ongoing->plp_id !== Auth::id()) {
                if ($ongoing->plp?->role === 'superadmin') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Pemeriksaan superadmin ditemukan, Anda dapat melanjutkan pemeriksaan mahasiswa ini',
                        'is_resume' => false,
                        'data' => [
                            'id' => $mahasiswa->id,
                            'nama' => $mahasiswa->nama,
                            'no_pendaftaran' => $mahasiswa->no_pendaftaran,
                            'no_identitas' => $mahasiswa->no_identitas,
                            'jenis_kelamin' => $mahasiswa->jenis_kelamin,
                            'prodi' => $mahasiswa->prodi,
                            'prodi_pilihan_1' => $mahasiswa->prodi_pilihan_1,
                            'prodi_pilihan_2' => $mahasiswa->prodi_pilihan_2,
                            'tempat_lahir' => $mahasiswa->tempat_lahir,
                            'tanggal_lahir' => $mahasiswa->tanggal_lahir?->format('d-m-Y'),
                            'nomor_urut' => $mahasiswa->nomor_urut,
                            'foto_kehadiran' => $mahasiswa->foto_kehadiran ? asset('storage/' . $mahasiswa->foto_kehadiran) : null,
                        ],
                    ]);
                }

                $plpName = $ongoing->plp?->nama ?? 'Nakes lain';
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa ini sedang diperiksa oleh ' . $plpName
                ], 422);
            }

            $isResume = true;
        }

        // Prepare student data
        $fotoUrl = null;
        if ($mahasiswa->foto_kehadiran) {
            $fotoUrl = asset('storage/' . $mahasiswa->foto_kehadiran);
        }

        return response()->json([
            'success' => true,
            'message' => $isResume ? 'Melanjutkan pemeriksaan yang sebelumnya' : 'Data mahasiswa siap diverifikasi',
            'is_resume' => $isResume,
            'data' => [
                'id' => $mahasiswa->id,
                'nama' => $mahasiswa->nama,
                'no_pendaftaran' => $mahasiswa->no_pendaftaran,
                'no_identitas' => $mahasiswa->no_identitas,
                'jenis_kelamin' => $mahasiswa->jenis_kelamin,
                'prodi' => $mahasiswa->prodi,
                'prodi_pilihan_1' => $mahasiswa->prodi_pilihan_1,
                'prodi_pilihan_2' => $mahasiswa->prodi_pilihan_2,
                'tempat_lahir' => $mahasiswa->tempat_lahir,
                'tanggal_lahir' => $mahasiswa->tanggal_lahir?->format('d-m-Y'),
                'nomor_urut' => $mahasiswa->nomor_urut,
                'foto_kehadiran' => $fotoUrl,
            ]
        ]);
    }

    /**
     * Mulai pemeriksaan (set status ke sedang_diperiksa)
     */
    public function startExamination(Mahasiswa $mahasiswa)
    {
        // Cek apakah MAHASISWA INI sedang diperiksa oleh Nakes lain
        $ongoing = PemeriksaanPlp::where('mahasiswa_id', $mahasiswa->id)
            ->where('status_pemeriksaan', 'sedang_diperiksa')
            ->with('plp')
            ->first();
        if ($ongoing) {
            if ($ongoing->plp_id !== Auth::id()) {
                if ($ongoing->plp?->role === 'superadmin') {
                    $ongoing->update([
                        'plp_id' => Auth::id(),
                        'started_at' => now(),
                    ]);

                    $ongoing->load('mahasiswa');
                    return response()->json([
                        'success' => true,
                        'message' => 'Pemeriksaan superadmin diambil alih untuk ' . $mahasiswa->nama,
                        'data' => $ongoing,
                        'is_resume' => true,
                    ]);
                }

                $plpName = $ongoing->plp?->nama ?? 'Nakes lain';
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa ini sedang diperiksa oleh ' . $plpName
                ], 422);
            }

            $ongoing->load('mahasiswa');
            return response()->json([
                'success' => true,
                'message' => 'Melanjutkan pemeriksaan untuk ' . $mahasiswa->nama,
                'data' => $ongoing,
                'is_resume' => true,
            ]);
        }

        // Verify student is ready for Nakes examination
        if (!$this->canBeHandledByNakes($mahasiswa)) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak siap untuk pemeriksaan PLP'
            ], 422);
        }

        try {
            // Create or get examination record and mark as ongoing
            $pemeriksaanPlp = PemeriksaanPlp::firstOrCreate(
                ['mahasiswa_id' => $mahasiswa->id],
                [
                    'plp_id' => Auth::id(),
                    'status_pemeriksaan' => 'sedang_diperiksa',
                    'started_at' => now(),
                ]
            );

            // If already exists, update status
            if ($pemeriksaanPlp->status_pemeriksaan !== 'sedang_diperiksa') {
                $pemeriksaanPlp->update([
                    'status_pemeriksaan' => 'sedang_diperiksa',
                    'started_at' => now(),
                    'plp_id' => Auth::id(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pemeriksaan dimulai untuk ' . $mahasiswa->nama,
                'data' => $pemeriksaanPlp->load('mahasiswa')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulai pemeriksaan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, Mahasiswa $mahasiswa)
    {
        // Verify student is ready for Nakes examination
        if (!$this->canBeHandledByNakes($mahasiswa)) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak siap untuk pemeriksaan PLP'
            ], 422);
        }

        // Validate form data
        $validated = $request->validate([
            'tgl_periksa' => 'required|date',
            'riwayat_penyakit' => 'required|string|max:255',
            'suhu' => 'required|numeric|min:0|max:42',
            'tensi' => 'required|string|max:20',
            'riwayat_keluarga' => 'required|string|max:255',
            'keterangan_pemeriksaan' => 'nullable|string|max:500',
            'buta_warna' => 'required|in:Tidak buta warna,Buta warna parsial,Buta warna total',
            'tinggi_badan' => 'required|numeric|min:0|max:250',
            'berat_badan' => 'required|numeric|min:0|max:200',
        ]);

        try {
            $pemeriksaanPlp = DB::transaction(function () use ($validated, $mahasiswa, $request) {
                // Calculate BMI
                $tinggi_m = $validated['tinggi_badan'] / 100;
                $bmi = round($validated['berat_badan'] / ($tinggi_m * $tinggi_m), 2);

                // Store examination results
                $pemeriksaanPlp = PemeriksaanPlp::updateOrCreate(
                    ['mahasiswa_id' => $mahasiswa->id],
                    [
                        'plp_id' => Auth::id(),
                        'tgl_periksa' => $validated['tgl_periksa'],
                        'riwayat_penyakit' => $validated['riwayat_penyakit'],
                        'suhu' => $validated['suhu'],
                        'tensi' => $validated['tensi'],
                        'riwayat_keluarga' => $validated['riwayat_keluarga'],
                        'keterangan_pemeriksaan' => filled(trim((string) ($validated['keterangan_pemeriksaan'] ?? '')))
                            ? trim((string) $validated['keterangan_pemeriksaan'])
                            : '-',
                        'buta_warna' => $validated['buta_warna'],
                        'tinggi_badan' => $validated['tinggi_badan'],
                        'berat_badan' => $validated['berat_badan'],
                        'bmi' => $bmi,
                        'status_pemeriksaan' => 'selesai',
                        'ended_at' => now(),
                    ]
                );

                $mahasiswa->update(['status_plp' => 'selesai']);

                LogAktivitas::create([
                    'user_id' => Auth::id(),
                    'aksi' => 'Menyelesaikan pemeriksaan Nakes',
                    'target_tabel' => 'pemeriksaan_plp',
                    'target_id' => $pemeriksaanPlp->id,
                    'data_lama' => null,
                    'data_baru' => $validated,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'waktu' => now(),
                ]);

                return $pemeriksaanPlp;
            });

            return response()->json([
                'success' => true,
                'message' => 'Pemeriksaan Nakes berhasil direkam',
                'data' => $pemeriksaanPlp,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pemeriksaan Nakes: ' . $e->getMessage()
            ], 500);
        }
    }
}
