<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\LogAktivitas;

class DashboardController extends Controller
{
    public function index()
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
        
        // Recent activities
        $recent_activities = LogAktivitas::with('user')
            ->orderBy('waktu', 'desc')
            ->limit(10)
            ->get();
        
        return view('dashboard.index', compact('stats', 'recent_activities'));
    }
}
