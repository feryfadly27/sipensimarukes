<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                  ->orWhere('no_identitas', 'like', "%{$search}%");
            });
        }
        
        $mahasiswa = $query->orderBy('nama', 'asc')->paginate(20);
        
        $prodis = Mahasiswa::distinct()->pluck('prodi')->filter();
        
        return view('mahasiswa.index', compact('mahasiswa', 'prodis'));
    }

    /**
     * Show form for creating new peserta
     */
    public function create()
    {
        $prodis = ['Kebidanan', 'Keperawatan', 'Kesehatan Masyarakat', 'Administrasi Kesehatan', 'Farmasi'];
        
        return view('mahasiswa.create', compact('prodis'));
    }

    /**
     * Store a newly created peserta in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_pendaftaran' => 'required|string|unique:mahasiswa,no_pendaftaran',
            'no_identitas' => 'required|string|unique:mahasiswa,no_identitas',
            'nama' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'prodi' => 'required|string|max:50',
            'asal_sekolah' => 'required|string|max:100',
        ], [
            'no_pendaftaran.unique' => 'No pendaftaran sudah terdaftar',
            'no_identitas.unique' => 'No identitas sudah terdaftar',
        ]);

        try {
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
        $prodis = ['Kebidanan', 'Keperawatan', 'Kesehatan Masyarakat', 'Administrasi Kesehatan', 'Farmasi'];
        
        return view('mahasiswa.edit', compact('mahasiswa', 'prodis'));
    }

    /**
     * Update peserta in storage
     */
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $validated = $request->validate([
            'no_pendaftaran' => 'required|string|unique:mahasiswa,no_pendaftaran,' . $mahasiswa->id,
            'no_identitas' => 'required|string|unique:mahasiswa,no_identitas,' . $mahasiswa->id,
            'nama' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'prodi' => 'required|string|max:50',
            'asal_sekolah' => 'required|string|max:100',
        ]);

        try {
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
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $headers = [
            'No. Pendaftaran',
            'No. Identitas/KTP',
            'Nama Lengkap',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Program Studi',
            'Asal Sekolah'
        ];

        foreach ($headers as $key => $header) {
            $sheet->setCellValueByColumnAndRow($key + 1, 1, $header);
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '00BCD4']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        for ($i = 1; $i <= count($headers); $i++) {
            $sheet->getStyleByColumnAndRow($i, 1)->applyFromArray($headerStyle);
        }

        // Add sample data
        $sampleData = [
            ['PEN2026001', '123456789012345', 'Ahmad Rizki Pratama', 'Jakarta', '2005-01-15', 'Laki-laki', 'Keperawatan', 'SMA Negeri 1 Jakarta'],
            ['PEN2026002', '123456789012346', 'Siti Nurhaliza', 'Bandung', '2004-06-20', 'Perempuan', 'Kebidanan', 'SMA Negeri 3 Bandung'],
        ];

        foreach ($sampleData as $rowIndex => $rowData) {
            foreach ($rowData as $colIndex => $cellValue) {
                $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 2, $cellValue);
            }
        }

        // Auto-resize columns
        for ($i = 1; $i <= count($headers); $i++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Template_Data_Peserta_' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
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
                    // Map columns: A=no_pendaftaran, B=no_identitas, C=nama, D=tempat_lahir, E=tanggal_lahir, F=jenis_kelamin, G=prodi, H=asal_sekolah
                    $data = [
                        'no_pendaftaran' => $row[0] ?? null,
                        'no_identitas' => $row[1] ?? null,
                        'nama' => $row[2] ?? null,
                        'tempat_lahir' => $row[3] ?? null,
                        'tanggal_lahir' => $row[4] ?? null,
                        'jenis_kelamin' => $row[5] ?? null,
                        'prodi' => $row[6] ?? null,
                        'asal_sekolah' => $row[7] ?? null,
                    ];

                    // Validate required fields
                    if (!$data['no_pendaftaran'] || !$data['no_identitas'] || !$data['nama']) {
                        $errors[] = "Baris " . ($index + 1) . ": Data tidak lengkap (no_pendaftaran, no_identitas, nama wajib)";
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
}
