<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\LogAktivitas;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of peserta
     */
    public function index(Request $request)
    {
        $query = Mahasiswa::query();
        
        // Filter by prodi
        if ($request->filled('prodi')) {
            $query->where('prodi', $request->prodi);
        }
        
        // Filter by status kehadiran
        if ($request->filled('status_kehadiran')) {
            $query->where('status_kehadiran', $request->status_kehadiran);
        }
        
        // Search by nama or no_pendaftaran
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('no_identitas', 'like', "%{$search}%")
                  ->orWhere('no_telp', 'like', "%{$search}%");
            });
        }
        
        $mahasiswa = $query->orderBy('nama', 'asc')->paginate(20);
        
        $prodis = $this->getProdiOptions();
        
        return view('mahasiswa.index', compact('mahasiswa', 'prodis'));
    }

    /**
     * Show form for creating new peserta
     */
    public function create()
    {
        $prodis = $this->getProdiOptions();
        
        return view('mahasiswa.create', compact('prodis'));
    }

    /**
     * Store a newly created peserta in storage
     */
    public function store(Request $request)
    {
        $request->merge([
            'no_telp' => $this->normalizeNoTelpValue($request->input('no_telp')),
        ]);

        $validated = $request->validate([
            'no_pendaftaran' => 'required|string|unique:mahasiswa,no_pendaftaran',
            'no_identitas' => 'required|string|unique:mahasiswa,no_identitas',
            'no_telp' => ['nullable', 'regex:/^08\d{0,10}$/'],
            'nama' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'prodi' => 'nullable|string|max:100|exists:prodis,nama',
            'prodi_pilihan_1' => 'required|string|max:100|exists:prodis,nama',
            'prodi_pilihan_2' => 'nullable|string|max:100|exists:prodis,nama',
            'asal_sekolah' => 'required|string|max:100',
        ], [
            'no_pendaftaran.unique' => 'No pendaftaran sudah terdaftar',
            'no_identitas.unique' => 'No identitas sudah terdaftar',
            'no_telp.regex' => 'Nomor telepon harus diawali 08, hanya angka, dan maksimal 12 digit',
        ]);

        try {
            if (empty($validated['prodi'])) {
                $validated['prodi'] = $validated['prodi_pilihan_1'];
            }

            $this->syncProdiMasterFromValues([
                $validated['prodi'] ?? null,
                $validated['prodi_pilihan_1'] ?? null,
                $validated['prodi_pilihan_2'] ?? null,
            ]);

            Mahasiswa::create($validated);
            
            LogAktivitas::catat(
                'Tambah data peserta: ' . $validated['nama'],
                auth()->id(),
                'mahasiswa',
                null,
                $validated
            );
            
            return redirect()->route('mahasiswa.index')
                ->with('success', 'Data peserta ' . $validated['nama'] . ' berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambah data peserta: ' . $e->getMessage());
        }
    }

    /**
     * Show form for editing peserta
     */
    public function edit(Mahasiswa $mahasiswa)
    {
        $prodis = $this->getProdiOptions();
        
        return view('mahasiswa.edit', compact('mahasiswa', 'prodis'));
    }

    /**
     * Display peserta details
     */
    public function show(Mahasiswa $mahasiswa)
    {
        return view('mahasiswa.show', compact('mahasiswa'));
    }

    /**
     * Update peserta in storage
     */
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $request->merge([
            'no_telp' => $this->normalizeNoTelpValue($request->input('no_telp')),
        ]);

        $validated = $request->validate([
            'no_pendaftaran' => 'required|string|unique:mahasiswa,no_pendaftaran,' . $mahasiswa->id,
            'no_identitas' => 'required|string|unique:mahasiswa,no_identitas,' . $mahasiswa->id,
            'no_telp' => ['nullable', 'regex:/^08\d{0,10}$/'],
            'nama' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'prodi' => 'required|string|max:100|exists:prodis,nama',
            'prodi_pilihan_1' => 'nullable|string|max:100|exists:prodis,nama',
            'prodi_pilihan_2' => 'nullable|string|max:100|exists:prodis,nama',
            'asal_sekolah' => 'required|string|max:100',
        ], [
            'no_telp.regex' => 'Nomor telepon harus diawali 08, hanya angka, dan maksimal 12 digit',
        ]);

        try {
            $this->syncProdiMasterFromValues([
                $validated['prodi'] ?? null,
                $validated['prodi_pilihan_1'] ?? null,
                $validated['prodi_pilihan_2'] ?? null,
            ]);

            $mahasiswa->update($validated);
            
            LogAktivitas::catat(
                'Edit data peserta: ' . $validated['nama'],
                auth()->id(),
                'mahasiswa',
                $mahasiswa->id,
                $validated
            );
            
            return redirect()->route('mahasiswa.index')
                ->with('success', 'Data peserta berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data peserta: ' . $e->getMessage());
        }
    }

    /**
     * Delete peserta
     */
    public function destroy(Mahasiswa $mahasiswa)
    {
        try {
            $nama = $mahasiswa->nama;
            $mahasiswa->delete();
            
            LogAktivitas::catat(
                'Hapus data peserta: ' . $nama,
                auth()->id(),
                'mahasiswa',
                null,
                ['nama' => $nama]
            );
            
            return redirect()->route('mahasiswa.index')
                ->with('success', 'Data peserta berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data peserta: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel for import
     */
    public function templateExcel()
    {
        $headers = [
            'No. Pendaftaran',
            'No. Identitas/KTP',
            'No. Telepon',
            'Nama Lengkap',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Program Studi Pilihan 1',
            'Program Studi Pilihan 2',
            'Asal Sekolah'
        ];

        $sampleData = [
            ['PEN2026001', '123456789012345', '081234567890', 'Ahmad Rizki Pratama', 'Jakarta', '2005-01-15', 'Laki-laki', 'Keperawatan', 'Farmasi', 'SMA Negeri 1 Jakarta'],
            ['PEN2026002', '123456789012346', '082345678901', 'Siti Nurhaliza', 'Bandung', '2004-06-20', 'Perempuan', 'Kebidanan', 'Keperawatan', 'SMA Negeri 3 Bandung'],
        ];

        // Fallback to CSV if Zip extension is missing.
        if (!extension_loaded('zip')) {
            $filename = 'Template_Data_Peserta_' . date('YmdHis') . '.csv';

            return response()->streamDownload(function () use ($headers, $sampleData) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, $headers);
                foreach ($sampleData as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            ]);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($headers as $key => $header) {
            $coordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($key + 1) . '1';
            $sheet->setCellValue($coordinate, $header);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '00BCD4']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        for ($i = 1; $i <= count($headers); $i++) {
            $coordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i) . '1';
            $sheet->getStyle($coordinate)->applyFromArray($headerStyle);
        }

        foreach ($sampleData as $rowIndex => $rowData) {
            foreach ($rowData as $colIndex => $cellValue) {
                $coordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1) . ($rowIndex + 2);
                $sheet->setCellValue($coordinate, $cellValue);
            }
        }

        for ($i = 1; $i <= count($headers); $i++) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filename = 'Template_Data_Peserta_' . date('YmdHis') . '.xlsx';
        $storagePath = 'public/exports/' . $filename;
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save(storage_path('app/' . $storagePath));

        // Direct file response for Codespaces compatibility
        $fullPath = storage_path('app/' . $storagePath);
        
        return response()->file($fullPath, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Import peserta from Excel file
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ], [
            'file.mimes' => 'File harus berformat Excel (.xlsx, .xls) atau CSV',
            'file.max' => 'Ukuran file maksimal 10MB',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $imported = 0;
            $failed = 0;
            $errors = [];

            // Skip header row (row 1)
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Skip header
                
                // Check if row is empty
                if (empty($row[0])) continue;

                try {
                    // Map columns (new): A=no_pendaftaran, B=no_identitas, C=no_telp, D=nama, E=tempat_lahir, F=tanggal_lahir, G=jenis_kelamin, H=prodi_pilihan_1, I=prodi_pilihan_2, J=asal_sekolah
                    // Backward compatibility (old): A=no_pendaftaran, B=no_identitas, C=nama, D=tempat_lahir, E=tanggal_lahir, F=jenis_kelamin, G=prodi_pilihan_1, H=prodi_pilihan_2, I=asal_sekolah
                    $hasNoTelpColumn = isset($row[9]);

                    $data = [
                        'no_pendaftaran' => $row[0] ?? null,
                        'no_identitas' => $row[1] ?? null,
                        'no_telp' => $hasNoTelpColumn ? ($row[2] ?? null) : null,
                        'nama' => $hasNoTelpColumn ? ($row[3] ?? null) : ($row[2] ?? null),
                        'tempat_lahir' => $hasNoTelpColumn ? ($row[4] ?? null) : ($row[3] ?? null),
                        'tanggal_lahir' => $hasNoTelpColumn ? ($row[5] ?? null) : ($row[4] ?? null),
                        'jenis_kelamin' => $hasNoTelpColumn ? ($row[6] ?? null) : ($row[5] ?? null),
                        'prodi_pilihan_1' => $hasNoTelpColumn ? ($row[7] ?? null) : ($row[6] ?? null),
                        'prodi_pilihan_2' => $hasNoTelpColumn ? ($row[8] ?? null) : ($row[7] ?? null),
                        'asal_sekolah' => $hasNoTelpColumn ? ($row[9] ?? null) : ($row[8] ?? null),
                        'prodi' => $hasNoTelpColumn ? ($row[7] ?? null) : ($row[6] ?? null), // Default prodi = prodi_pilihan_1
                    ];

                    if (isset($data['no_telp'])) {
                        $normalizedTelp = trim((string) $data['no_telp']);
                        $data['no_telp'] = ($normalizedTelp === '' || $normalizedTelp === '-') ? null : $normalizedTelp;
                    }

                    // Validate required fields
                    if (!$data['no_pendaftaran'] || !$data['no_identitas'] || !$data['nama'] || !$data['prodi_pilihan_1']) {
                        $errors[] = "Baris " . ($index + 1) . ": Data tidak lengkap (no_pendaftaran, no_identitas, nama, prodi_pilihan_1 wajib)";
                        $failed++;
                        continue;
                    }

                    if (!empty($data['no_telp']) && !preg_match('/^08\d{0,10}$/', (string) $data['no_telp'])) {
                        $errors[] = "Baris " . ($index + 1) . ": Nomor telepon harus diawali 08, hanya angka, dan maksimal 12 digit";
                        $failed++;
                        continue;
                    }

                    // Check if no_pendaftaran or no_identitas already exists
                    if (Mahasiswa::where('no_pendaftaran', $data['no_pendaftaran'])->exists()) {
                        $errors[] = "Baris " . ($index + 1) . ": No pendaftaran '{$data['no_pendaftaran']}' sudah terdaftar";
                        $failed++;
                        continue;
                    }

                    if (Mahasiswa::where('no_identitas', $data['no_identitas'])->exists()) {
                        $errors[] = "Baris " . ($index + 1) . ": No identitas '{$data['no_identitas']}' sudah terdaftar";
                        $failed++;
                        continue;
                    }

                    $this->syncProdiMasterFromValues([
                        $data['prodi'] ?? null,
                        $data['prodi_pilihan_1'] ?? null,
                        $data['prodi_pilihan_2'] ?? null,
                    ]);

                    // Create mahasiswa record
                    Mahasiswa::create($data);
                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                    $failed++;
                }
            }

            // Log aktivitas
            LogAktivitas::catat(
                'Import data peserta dari Excel',
                auth()->id(),
                'mahasiswa',
                null,
                ['imported' => $imported, 'failed' => $failed, 'file' => $file->getClientOriginalName()]
            );

            $message = "Import selesai: {$imported} data berhasil ditambahkan";
            if ($failed > 0) {
                $message .= ", {$failed} data gagal";
            }

            return redirect()->route('mahasiswa.index')
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor file: ' . $e->getMessage());
        }
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

    private function syncProdiMasterFromValues(array $values): void
    {
        foreach ($values as $value) {
            $nama = trim((string) $value);

            if ($nama === '') {
                continue;
            }

            Prodi::updateOrCreate(
                ['nama' => $nama],
                ['is_active' => true]
            );
        }
    }

    private function normalizeNoTelpValue($value): ?string
    {
        $normalized = trim((string) $value);

        if ($normalized === '' || $normalized === '-') {
            return null;
        }

        return $normalized;
    }
}
