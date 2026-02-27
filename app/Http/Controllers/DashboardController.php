<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\PemeriksaanPlp;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $allowedPerPage = [10, 25, 50, 100];
        $perPage = (int) $request->get('per_page', 25);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 25;
        }
        
        // Stats berdasarkan role
        $stats = [
            'total_peserta' => Mahasiswa::count(),
            'total_hadir' => Mahasiswa::where('status_kehadiran', 'hadir')->count(),
            'plp_selesai' => Mahasiswa::where('status_plp', 'selesai')->count(),
            'dokter_selesai' => Mahasiswa::where('status_dokter', 'selesai')->count(),
            'memenuhi_syarat' => Mahasiswa::where('kesimpulan_akhir', 'memenuhi_syarat')->count(),
        ];
        
        // Data untuk role-specific
        switch ($user->role) {
            case 'pendaftaran':
                $stats['belum_hadir'] = Mahasiswa::where('status_kehadiran', 'belum_hadir')->count();
                $stats['antrian_hari_ini'] = Mahasiswa::where('status_kehadiran', 'belum_hadir')->count();
                break;
                
            case 'plp':
                $stats['antrian_plp'] = Mahasiswa::antrianPlp()->count();
                $peserta_menunggu = Mahasiswa::antrianPlp()
                    ->orderBy('updated_at', 'asc')
                    ->limit(10)
                    ->get();
                break;
                
            case 'dokter':
                $stats['antrian_dokter'] = Mahasiswa::antrianDokter()->count();
                $peserta_menunggu = Mahasiswa::antrianDokter()
                    ->orderBy('updated_at', 'asc')
                    ->limit(10)
                    ->get();
                break;
        }

        // Pendaftaran confirmation data (only for pendaftaran role)
        $mahasiswa = null;
        $prodis = null;
        $statsPendaftaran = null;
        $plpData = null;
        $plpStats = null;
        $dokterData = null;
        $dokterStats = null;
        $adminMonitoring = null;

        if (in_array($user->role, ['admin', 'superadmin'])) {
            $ongoingPlp = PemeriksaanPlp::where('status_pemeriksaan', 'sedang_diperiksa')
                ->with(['mahasiswa:id,nama,no_pendaftaran,nomor_urut', 'plp:id,nama'])
                ->orderByDesc('started_at')
                ->get();

            $activeDokter = User::where('role', 'dokter')
                ->select('id', 'nama')
                ->get()
                ->map(function ($dokter) {
                    $active = Cache::get('dokter_active_' . $dokter->id);

                    if (!$active) {
                        return null;
                    }

                    return [
                        'dokter_nama' => $active['dokter_nama'] ?? $dokter->nama,
                        'mahasiswa_nama' => $active['mahasiswa_nama'] ?? '-',
                        'mahasiswa_no_pendaftaran' => $active['mahasiswa_no_pendaftaran'] ?? '-',
                        'started_at' => $active['started_at'] ?? null,
                    ];
                })
                ->filter()
                ->values();

            $adminMonitoring = [
                'ongoing_plp' => $ongoingPlp,
                'active_dokter' => $activeDokter,
            ];
        }
        
        if ($user->role === 'pendaftaran') {
            $queryPendaftaran = Mahasiswa::where('status_kehadiran', 'belum_konfirmasi');

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $queryPendaftaran->where(function($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%')
                      ->orWhere('no_pendaftaran', 'like', '%' . $search . '%')
                      ->orWhere('no_identitas', 'like', '%' . $search . '%');
                });
            }

            // Filter by program
            if ($request->filled('prodi')) {
                $queryPendaftaran->where('prodi', $request->prodi);
            }

            $mahasiswa = $queryPendaftaran->orderBy('created_at', 'asc')->paginate($perPage)->withQueryString();
            $prodis = Mahasiswa::select('prodi')->distinct()->whereNotNull('prodi')->pluck('prodi');

            // Stats pendaftaran
            $statsPendaftaran = [
                'total_belum_konfirmasi' => Mahasiswa::where('status_kehadiran', 'belum_konfirmasi')->count(),
                'total_hadir' => Mahasiswa::where('status_kehadiran', 'hadir')->count(),
                'total_tidak_hadir' => Mahasiswa::where('status_kehadiran', 'tidak_hadir')->count(),
            ];
        } elseif ($user->role === 'plp') {
            // PLP: Tampilkan mahasiswa yang sudah hadir tapi belum pemeriksaan PLP
            $queryPlp = Mahasiswa::where('status_kehadiran', 'hadir')
                ->where('status_plp', 'belum');

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $queryPlp->where(function($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%')
                      ->orWhere('no_pendaftaran', 'like', '%' . $search . '%')
                      ->orWhere('no_identitas', 'like', '%' . $search . '%');
                });
            }

            // Filter by program
            if ($request->filled('prodi')) {
                $queryPlp->where('prodi', $request->prodi);
            }

            $plpData = $queryPlp->orderBy('nomor_urut', 'asc')->paginate($perPage)->withQueryString();
            $prodis = Mahasiswa::select('prodi')->distinct()->whereNotNull('prodi')->pluck('prodi');

            // Stats PLP
            $plpStats = [
                'total_hadir' => Mahasiswa::where('status_kehadiran', 'hadir')->count(),
                'total_menunggu_plp' => Mahasiswa::where('status_kehadiran', 'hadir')->where('status_plp', 'belum')->count(),
                'total_selesai_plp' => Mahasiswa::where('status_plp', 'selesai')->count(),
            ];
        } elseif ($user->role === 'dokter') {
            $queryDokter = Mahasiswa::antrianDokter();

            if ($request->filled('search')) {
                $search = $request->search;
                $queryDokter->where(function($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%')
                      ->orWhere('no_pendaftaran', 'like', '%' . $search . '%')
                      ->orWhere('no_identitas', 'like', '%' . $search . '%');
                });
            }

            if ($request->filled('prodi')) {
                $queryDokter->where('prodi', $request->prodi);
            }

            $dokterData = $queryDokter->orderBy('updated_at', 'asc')->paginate($perPage)->withQueryString();
            $prodis = Mahasiswa::select('prodi')->distinct()->whereNotNull('prodi')->pluck('prodi');

            $dokterStats = [
                'total_plp_selesai' => Mahasiswa::where('status_plp', 'selesai')->count(),
                'total_menunggu_dokter' => Mahasiswa::where('status_plp', 'selesai')->where('status_dokter', 'belum')->count(),
                'total_selesai_dokter' => Mahasiswa::where('status_dokter', 'selesai')->count(),
            ];
        }
        
        return view('dashboard.index', compact('stats', 'mahasiswa', 'prodis', 'statsPendaftaran', 'plpData', 'plpStats', 'dokterData', 'dokterStats', 'adminMonitoring'));
    }
}
