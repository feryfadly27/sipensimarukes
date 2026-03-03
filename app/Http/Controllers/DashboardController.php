<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Prodi;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Stats berdasarkan role
        $stats = [
            'total_peserta' => Mahasiswa::count(),
            'hadir_hari_ini' => Mahasiswa::where('status_kehadiran', 'hadir')
                ->whereDate('updated_at', today())->count(),
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
        $prodis = $this->getProdiOptions();
        $statsPendaftaran = null;
        $plpData = null;
        $plpStats = null;
        $dokterData = null;
        $dokterStats = null;
        
        if (in_array($user->role, ['pendaftaran', 'superadmin'])) {
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

            $mahasiswa = $queryPendaftaran->orderBy('created_at', 'asc')->paginate(15);

            // Stats pendaftaran
            $statsPendaftaran = [
                'total_belum_konfirmasi' => Mahasiswa::where('status_kehadiran', 'belum_konfirmasi')->count(),
                'total_hadir' => Mahasiswa::where('status_kehadiran', 'hadir')->count(),
                'total_tidak_hadir' => Mahasiswa::where('status_kehadiran', 'tidak_hadir')->count(),
            ];
        }

        if (in_array($user->role, ['plp', 'superadmin'])) {
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

            $plpData = $queryPlp->orderBy('nomor_urut', 'asc')->paginate(15);

            // Stats PLP
            $plpStats = [
                'total_hadir' => Mahasiswa::where('status_kehadiran', 'hadir')->count(),
                'total_menunggu_plp' => Mahasiswa::where('status_kehadiran', 'hadir')->where('status_plp', 'belum')->count(),
                'total_selesai_plp' => Mahasiswa::where('status_plp', 'selesai')->count(),
            ];
        }

        if (in_array($user->role, ['dokter', 'superadmin'])) {
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

            $dokterData = $queryDokter->orderBy('updated_at', 'asc')->paginate(15);

            $dokterStats = [
                'total_plp_selesai' => Mahasiswa::where('status_plp', 'selesai')->count(),
                'total_menunggu_dokter' => Mahasiswa::where('status_plp', 'selesai')->where('status_dokter', 'belum')->count(),
                'total_selesai_dokter' => Mahasiswa::where('status_dokter', 'selesai')->count(),
            ];
        }
        
        return view('dashboard.index', compact('stats', 'mahasiswa', 'prodis', 'statsPendaftaran', 'plpData', 'plpStats', 'dokterData', 'dokterStats'));
    }

    private function getProdiOptions()
    {
        $prodis = Prodi::aktif()->orderBy('nama')->pluck('nama');

        if ($prodis->isNotEmpty()) {
            return $prodis;
        }

        return Mahasiswa::query()
            ->whereNotNull('prodi')
            ->where('prodi', '!=', '')
            ->distinct()
            ->orderBy('prodi')
            ->pluck('prodi');
    }
}
