<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index(Request $request)
    {
        $query = Prodi::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $prodis = $query->orderBy('nama')->paginate(25)->withQueryString();

        return view('prodis.index', compact('prodis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:prodis,nama',
            'is_active' => 'nullable|boolean',
        ]);

        Prodi::create([
            'nama' => trim($validated['nama']),
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return redirect()->route('prodis.index')->with('success', 'Prodi berhasil ditambahkan.');
    }

    public function update(Request $request, Prodi $prodi)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:prodis,nama,' . $prodi->id,
            'is_active' => 'nullable|boolean',
        ]);

        $prodi->update([
            'nama' => trim($validated['nama']),
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('prodis.index')->with('success', 'Prodi berhasil diperbarui.');
    }

    public function toggle(Prodi $prodi)
    {
        $prodi->update([
            'is_active' => !$prodi->is_active,
        ]);

        return redirect()->route('prodis.index')->with('success', 'Status prodi berhasil diubah.');
    }

    public function destroy(Prodi $prodi)
    {
        $usedCount = Mahasiswa::query()
            ->where('prodi', $prodi->nama)
            ->orWhere('prodi_pilihan_1', $prodi->nama)
            ->orWhere('prodi_pilihan_2', $prodi->nama)
            ->count();

        if ($usedCount > 0) {
            return redirect()->route('prodis.index')->with('error', 'Prodi tidak bisa dihapus karena sudah dipakai oleh peserta.');
        }

        $prodi->delete();

        return redirect()->route('prodis.index')->with('success', 'Prodi berhasil dihapus.');
    }
}
