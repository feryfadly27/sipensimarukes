<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $allowedPerPage = [10, 25, 50, 100];
        $perPage = (int) $request->get('per_page', 25);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 25;
        }

        // Get students that need confirmation (status belum_konfirmasi)
        $query = Mahasiswa::where('status_kehadiran', 'belum_konfirmasi');

        // Search by name or registration number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('no_pendaftaran', 'like', '%' . $search . '%')
                  ->orWhere('no_identitas', 'like', '%' . $search . '%');
            });
        }

        // Filter by program
        if ($request->filled('prodi')) {
            $query->where('prodi', $request->prodi);
        }

        $mahasiswa = $query->orderBy('created_at', 'asc')->paginate($perPage)->withQueryString();
        
        // Get list of programs for filter
        $prodis = Mahasiswa::select('prodi')->distinct()->whereNotNull('prodi')->pluck('prodi');

        // Calculate stats
        $stats = [
            'total_belum_konfirmasi' => Mahasiswa::where('status_kehadiran', 'belum_konfirmasi')->count(),
            'total_hadir' => Mahasiswa::where('status_kehadiran', 'hadir')->count(),
            'total_tidak_hadir' => Mahasiswa::where('status_kehadiran', 'tidak_hadir')->count(),
        ];

        return view('pendaftaran.index', compact('mahasiswa', 'prodis', 'stats'));
    }

    public function validasi(Request $request, Mahasiswa $mahasiswa)
    {
        $validated = $request->validate([
            'status_kehadiran' => 'required|in:hadir,tidak_hadir',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // max 5MB
        ]);

        $dataLama = [
            'status_kehadiran' => $mahasiswa->status_kehadiran,
            'nomor_urut' => $mahasiswa->nomor_urut,
            'foto_kehadiran' => $mahasiswa->foto_kehadiran,
        ];

        $dataUpdate = [
            'status_kehadiran' => $validated['status_kehadiran'],
        ];

        // Auto-generate nomor_urut if hadir
        if ($validated['status_kehadiran'] === 'hadir') {
            $maxNomorUrut = Mahasiswa::where('status_kehadiran', 'hadir')
                ->max('nomor_urut') ?? 0;
            $dataUpdate['nomor_urut'] = $maxNomorUrut + 1;
        } else {
            $dataUpdate['nomor_urut'] = null;
        }

        // Handle photo upload if provided
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = 'kehadiran_' . $mahasiswa->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('kehadiran', $fileName, 'public');
            $dataUpdate['foto_kehadiran'] = $filePath;
        }

        // Update mahasiswa
        $mahasiswa->update($dataUpdate);

        $dataBaru = [
            'status_kehadiran' => $mahasiswa->status_kehadiran,
            'nomor_urut' => $mahasiswa->nomor_urut,
            'foto_kehadiran' => $mahasiswa->foto_kehadiran,
        ];

        // Log activity
        LogAktivitas::catat(
            'Validasi kehadiran: ' . $mahasiswa->nama . ' - ' . $validated['status_kehadiran'],
            $mahasiswa->id,
            'mahasiswa',
            $dataLama,
            $dataBaru
        );

        return response()->json([
            'success' => true,
            'message' => 'Validasi kehadiran berhasil disimpan',
            'data' => [
                'id' => $mahasiswa->id,
                'nama' => $mahasiswa->nama,
                'status_kehadiran' => $mahasiswa->status_kehadiran,
                'nomor_urut' => $mahasiswa->nomor_urut,
                'foto_kehadiran' => $mahasiswa->foto_kehadiran,
            ]
        ]);
    }
}
