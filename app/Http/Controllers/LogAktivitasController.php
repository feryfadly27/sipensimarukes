<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $query = LogAktivitas::with('user')
            ->excludingSuperadmin();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('aksi')) {
            $query->where('aksi', 'like', '%' . $request->aksi . '%');
        }

        // Filter by target table
        if ($request->filled('target_tabel')) {
            $query->where('target_tabel', $request->target_tabel);
        }

        // Filter by date range
        if ($request->filled('dari_tanggal') && $request->filled('sampai_tanggal')) {
            $query->whereBetween('waktu', [
                $request->dari_tanggal . ' 00:00:00',
                $request->sampai_tanggal . ' 23:59:59'
            ]);
        }

        // Search in action description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('aksi', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('nama', 'like', '%' . $search . '%');
                  });
            });
        }

        $logs = $query->orderBy('waktu', 'desc')->paginate(15);
        
        $users = \App\Models\User::where('role', '!=', 'superadmin')->pluck('nama', 'id');
        $tables = LogAktivitas::excludingSuperadmin()->distinct()->pluck('target_tabel');

        return view('logs.index', compact('logs', 'users', 'tables'));
    }
}
