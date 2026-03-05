<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\LogAktivitas;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class LaporanController extends Controller
{
    /**
     * Display laporan data peserta
     */
    public function index(Request $request)
    {
        $query = Mahasiswa::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('no_identitas', 'like', "%{$search}%")
                  ->orWhere('no_telp', 'like', "%{$search}%");
            });
        }

        if ($request->filled('prodi')) {
            $query->where('prodi', $request->prodi);
        }

        if ($request->filled('status_kehadiran')) {
            $query->where('status_kehadiran', $request->status_kehadiran);
        }

        if ($request->filled('status_plp')) {
            $query->where('status_plp', $request->status_plp);
        }

        if ($request->filled('status_dokter')) {
            $query->where('status_dokter', $request->status_dokter);
        }

        if ($request->filled('kesimpulan_akhir')) {
            $query->where('kesimpulan_akhir', $request->kesimpulan_akhir);
        }

        $mahasiswa = $query->orderBy('nama')->paginate(20)->withQueryString();
        $prodis = $this->getProdiOptions();

        $summary = [
            'total' => Mahasiswa::count(),
            'hadir' => Mahasiswa::where('status_kehadiran', 'hadir')->count(),
            'belum_konfirmasi' => Mahasiswa::where('status_kehadiran', 'belum_konfirmasi')->count(),
            'tidak_hadir' => Mahasiswa::where('status_kehadiran', 'tidak_hadir')->count(),
            'plp_selesai' => Mahasiswa::where('status_plp', 'selesai')->count(),
            'dokter_selesai' => Mahasiswa::where('status_dokter', 'selesai')->count(),
            'memenuhi' => Mahasiswa::where('kesimpulan_akhir', 'memenuhi_syarat')->count(),
            'tidak_memenuhi' => Mahasiswa::where('kesimpulan_akhir', 'tidak_memenuhi_syarat')->count(),
        ];

        return view('laporan.index', compact('mahasiswa', 'prodis', 'summary'));
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

    /**
     * Display laporan ringkas keseluruhan
     */
    public function ringkas(Request $request)
    {
        $today = now()->toDateString();

        $ringkasan = [
            'total_peserta' => Mahasiswa::count(),
            'hadir_hari_ini' => Mahasiswa::where('status_kehadiran', 'hadir')
                ->whereDate('updated_at', $today)
                ->count(),
            'hadir_hari_sebelumnya' => Mahasiswa::where('status_kehadiran', 'hadir')
                ->whereDate('updated_at', '<', $today)
                ->count(),
            'memenuhi_syarat' => Mahasiswa::where('kesimpulan_akhir', 'memenuhi_syarat')->count(),
            'tidak_memenuhi_syarat' => Mahasiswa::where('kesimpulan_akhir', 'tidak_memenuhi_syarat')->count(),
        ];

        $memenuhiList = Mahasiswa::query()
            ->select(['id', 'no_pendaftaran', 'nama'])
            ->where('kesimpulan_akhir', 'memenuhi_syarat')
            ->orderBy('nama')
            ->paginate(15, ['*'], 'memenuhi_page')
            ->withQueryString();

        $tidakMemenuhiList = Mahasiswa::query()
            ->select(['id', 'no_pendaftaran', 'nama'])
            ->where('kesimpulan_akhir', 'tidak_memenuhi_syarat')
            ->orderBy('nama')
            ->paginate(15, ['*'], 'tidak_memenuhi_page')
            ->withQueryString();

        return view('laporan.ringkas', compact('ringkasan', 'memenuhiList', 'tidakMemenuhiList'));
    }

    /**
     * Export laporan ringkas ke Excel
     */
    public function exportRingkas(Request $request)
    {
        $today = now()->toDateString();

        $ringkasan = [
            ['Total Peserta', Mahasiswa::count()],
            ['Hadir Hari Ini', Mahasiswa::where('status_kehadiran', 'hadir')->whereDate('updated_at', $today)->count()],
            ['Hadir Hari Sebelumnya', Mahasiswa::where('status_kehadiran', 'hadir')->whereDate('updated_at', '<', $today)->count()],
            ['Memenuhi Syarat', Mahasiswa::where('kesimpulan_akhir', 'memenuhi_syarat')->count()],
            ['Tidak Memenuhi Syarat', Mahasiswa::where('kesimpulan_akhir', 'tidak_memenuhi_syarat')->count()],
        ];

        $memenuhi = Mahasiswa::query()
            ->select(['no_pendaftaran', 'nama'])
            ->where('kesimpulan_akhir', 'memenuhi_syarat')
            ->orderBy('nama')
            ->get();

        $tidakMemenuhi = Mahasiswa::query()
            ->select(['no_pendaftaran', 'nama'])
            ->where('kesimpulan_akhir', 'tidak_memenuhi_syarat')
            ->orderBy('nama')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ringkasan');

        $sheet->setCellValue('A1', 'Laporan Ringkas Keseluruhan');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1:B1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A3', 'Kategori');
        $sheet->setCellValue('B3', 'Jumlah');
        $sheet->getStyle('A3:B3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00BCD4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 4;
        foreach ($ringkasan as [$label, $value]) {
            $sheet->setCellValue('A' . $row, $label);
            $sheet->setCellValue('B' . $row, $value);
            $row++;
        }

        $row += 1;
        $sheet->setCellValue('A' . $row, 'Mahasiswa Memenuhi Syarat');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, 'No. Pendaftaran');
        $sheet->setCellValue('B' . $row, 'Nama');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        $row++;

        foreach ($memenuhi as $item) {
            $sheet->setCellValueExplicit('A' . $row, (string) ($item->no_pendaftaran ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $row, $item->nama);
            $row++;
        }

        $row += 1;
        $sheet->setCellValue('A' . $row, 'Mahasiswa Tidak Memenuhi Syarat');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, 'No. Pendaftaran');
        $sheet->setCellValue('B' . $row, 'Nama');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        $row++;

        foreach ($tidakMemenuhi as $item) {
            $sheet->setCellValueExplicit('A' . $row, (string) ($item->no_pendaftaran ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $row, $item->nama);
            $row++;
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getStyle('A:A')->getNumberFormat()->setFormatCode('@');

        LogAktivitas::catat(
            'Unduh laporan ringkas',
            auth()->id(),
            'mahasiswa',
            null,
            [
                'total_memenuhi' => $memenuhi->count(),
                'total_tidak_memenuhi' => $tidakMemenuhi->count(),
            ]
        );

        $filename = 'Laporan_Ringkas_Uji_Kesehatan_' . date('YmdHis') . '.xlsx';
        $publicPath = public_path('exports/' . $filename);

        if (!file_exists(public_path('exports'))) {
            mkdir(public_path('exports'), 0777, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($publicPath);

        $exportDir = public_path('exports');
        foreach (glob($exportDir . '/*.xlsx') as $file) {
            if (filemtime($file) < now()->subHour()->timestamp) {
                @unlink($file);
            }
        }

        return redirect(asset('exports/' . $filename));
    }

    /**
     * Export laporan to Excel
     */
    public function exportExcel(Request $request)
    {
        $query = Mahasiswa::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('no_identitas', 'like', "%{$search}%")
                  ->orWhere('no_telp', 'like', "%{$search}%");
            });
        }

        if ($request->filled('prodi')) {
            $query->where('prodi', $request->prodi);
        }

        if ($request->filled('status_kehadiran')) {
            $query->where('status_kehadiran', $request->status_kehadiran);
        }

        if ($request->filled('status_plp')) {
            $query->where('status_plp', $request->status_plp);
        }

        if ($request->filled('status_dokter')) {
            $query->where('status_dokter', $request->status_dokter);
        }

        if ($request->filled('kesimpulan_akhir')) {
            $query->where('kesimpulan_akhir', $request->kesimpulan_akhir);
        }

        $rows = $query->with(['pemeriksaanPlp', 'pemeriksaanDokter'])->orderBy('nama')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            // Data Dasar
            'No. Pendaftaran',
            'No. Identitas',
            'No. Telepon',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Program Studi',
            'Prodi Pilihan 1',
            'Prodi Pilihan 2',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Asal Sekolah',
            'Status Kehadiran',
            
            // Hasil Pemeriksaan Nakes
            'Tgl Periksa Nakes',
            'Riwayat Penyakit',
            'Suhu (°C)',
            'Tensi (mmHg)',
            'Riwayat Keluarga',
            'Buta Warna',
            'Tinggi Badan (cm)',
            'Berat Badan (kg)',
            'BMI',
            'Status Nakes',
            
            // Hasil Pemeriksaan Dokter
            'Tgl Periksa Dokter',
            'Status Kelulusan',
            'Kesimpulan Dokter',
            'Ket. Kesimpulan Dokter',
            'Surat Rujukan',
            'Mata - Kacamata',
            'Mata - Ikterik',
            'Mata - Konjungtiva Anemis',
            'Mata - Minus',
            'Mata - Silindris',
            'Mata - Strabismus',
            'Pendengaran',
            'Hidung - Cuping',
            'Ket. Hidung',
            'Mulut - Labioskisis',
            'Mulut - Palatoskisis',
            'Pharing - Nyeri Tekan',
            'Ket. Pharing',
            'Tonsil - Kemerahan',
            'Ket. Tonsil Kemerahan',
            'Tonsil - Pembesaran',
            'Gigi Lengkap',
            'Leher - KGB Pembesaran',
            'Jantung',
            'Ket. Jantung',
            'Paru',
            'Ket. Paru',
            'Abdomen - Hamil',
            'Thorax Photo',
            'Ket. Thorax',
            'Tulang Belakang',
            'Jari Tangan',
            'Ket. Jari Tangan',
            'Bicara - Artikulasi',
            'Ket. Bicara',
            'Cacat Tubuh',
            'Status Dokter',
            
            // Kesimpulan Akhir
            'Kesimpulan Akhir',
            'Keterangan',
        ];

        foreach ($headers as $index => $header) {
            $col = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($col . '1', $header);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00BCD4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        for ($i = 1; $i <= count($headers); $i++) {
            $col = Coordinate::stringFromColumnIndex($i);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
        }

        $rowIndex = 2;
        foreach ($rows as $row) {
            $plp = $row->pemeriksaanPlp;
            $dokter = $row->pemeriksaanDokter;
            
            $data = [
                // Data Dasar
                $row->no_pendaftaran,
                $row->no_identitas,
                $row->no_telp,
                $row->nama,
                $row->jenis_kelamin,
                $row->prodi,
                $row->prodi_pilihan_1,
                $row->prodi_pilihan_2,
                $row->tempat_lahir,
                optional($row->tanggal_lahir)->format('Y-m-d'),
                $row->asal_sekolah,
                $row->status_kehadiran,
                
                // Hasil Pemeriksaan Nakes
                $plp ? optional($plp->tgl_periksa)->format('Y-m-d H:i') : '-',
                $plp->riwayat_penyakit ?? '-',
                $plp->suhu ?? '-',
                $plp->tensi ?? '-',
                $plp->riwayat_keluarga ?? '-',
                $plp->buta_warna ?? '-',
                $plp->tinggi_badan ?? '-',
                $plp->berat_badan ?? '-',
                $plp->bmi ?? '-',
                $row->status_plp,
                
                // Hasil Pemeriksaan Dokter
                $dokter ? optional($dokter->tgl_periksa)->format('Y-m-d H:i') : '-',
                $dokter->status_kelulusan ?? '-',
                $dokter->kesimpulan ?? '-',
                $dokter->keterangan_kesimpulan ?? '-',
                $dokter->surat_rujukan ?? '-',
                $dokter->mata_kacamata ?? '-',
                $dokter->mata_ikterik ?? '-',
                $dokter->mata_konjungtiva_anemis ?? '-',
                $dokter && $dokter->mata_minus ? 'Ya (' . $dokter->mata_minus_nilai . ')' : 'Tidak',
                $dokter && $dokter->mata_silindris ? 'Ya (' . $dokter->mata_silindris_nilai . ')' : 'Tidak',
                $dokter && $dokter->mata_strabismus ? 'Ya (' . $dokter->mata_strabismus_nilai . ')' : 'Tidak',
                $dokter->pendengaran ?? '-',
                $dokter ? ($dokter->hidung_cuping ? 'Ya' : 'Tidak') : '-',
                $dokter->hidung_cuping_ket ?? '-',
                $dokter->mulut_labioskisis ?? '-',
                $dokter->mulut_palatoskisis ?? '-',
                $dokter ? ($dokter->pharing_nyeri_tekan ? 'Ya' : 'Tidak') : '-',
                $dokter->pharing_nyeri_tekan_ket ?? '-',
                $dokter ? ($dokter->tonsil_kemerahan ? 'Ya' : 'Tidak') : '-',
                $dokter->tonsil_kemerahan_ket ?? '-',
                $dokter ? ($dokter->tonsil_pembesaran ? 'Ya' : 'Tidak') : '-',
                $dokter ? ($dokter->gigi_lengkap ? 'Ya' : 'Tidak') : '-',
                $dokter->leher_kgb_pembesaran ?? '-',
                ($dokter && $dokter->jantung_dbn === 'DBN') ? 'Dalam Batas Normal' : ($dokter->jantung_dbn ?? '-'),
                $dokter->jantung_kelainan ?? '-',
                ($dokter && $dokter->paru_dbn === 'DBN') ? 'Dalam Batas Normal' : ($dokter->paru_dbn ?? '-'),
                $dokter->paru_kelainan ?? '-',
                $dokter ? ($dokter->abdomen_hamil ? 'Ya' : 'Tidak') : '-',
                $dokter->thorax_photo_file ?? '-',
                $dokter->thorax_photo_ket ?? '-',
                ($dokter && $dokter->tulang_belakang === 'DBN') ? 'Dalam Batas Normal' : ($dokter->tulang_belakang ?? '-'),
                $dokter->jari_tangan_lengkap ?? '-',
                $dokter->jari_tangan_ket ?? '-',
                $dokter->bicara_artikulasi ?? '-',
                $dokter->bicara_artikulasi_ket ?? '-',
                $dokter->cacat_tubuh ?? '-',
                $row->status_dokter,
                
                // Kesimpulan Akhir
                $row->kesimpulan_akhir,
                $row->keterangan_kesimpulan,
            ];
            
            foreach ($data as $colIndex => $value) {
                $col = Coordinate::stringFromColumnIndex($colIndex + 1);
                $cell = $col . $rowIndex;

                if (in_array($colIndex, [0, 1, 2], true)) {
                    $sheet->setCellValueExplicit($cell, (string) ($value ?? ''), DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue($cell, $value);
                }
            }
            
            $rowIndex++;
        }

        // Set auto size untuk semua kolom
        for ($i = 1; $i <= count($headers); $i++) {
            $col = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('A:C')->getNumberFormat()->setFormatCode('@');

        LogAktivitas::catat(
            'Unduh laporan data peserta',
            auth()->id(),
            'mahasiswa',
            null,
            ['total' => $rows->count()]
        );

        $filename = 'Laporan_Uji_Kesehatan_' . date('YmdHis') . '.xlsx';
        $publicPath = public_path('exports/' . $filename);
        
        // Ensure exports directory exists
        if (!file_exists(public_path('exports'))) {
            mkdir(public_path('exports'), 0777, true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($publicPath);

        // Clean old exports (older than 1 hour)
        $exportDir = public_path('exports');
        foreach (glob($exportDir . '/*.xlsx') as $file) {
            if (filemtime($file) < now()->subHour()->timestamp) {
                @unlink($file);
            }
        }

        // Redirect to direct file URL
        return redirect(asset('exports/' . $filename));
    }
}
