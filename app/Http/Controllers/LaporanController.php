<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class LaporanController extends Controller
{
    public function ringkas(Request $request)
    {
        $memenuhiBaseQuery = Mahasiswa::query()->where(function ($q) {
            $q->where('kesimpulan_akhir', 'memenuhi_syarat')
                ->orWhereHas('pemeriksaanDokter', function ($dokter) {
                    $dokter->where('kesimpulan', 'Memenuhi Syarat');
                });
        });

        $tidakMemenuhiBaseQuery = Mahasiswa::query()->where(function ($q) {
            $q->where('kesimpulan_akhir', 'tidak_memenuhi_syarat')
                ->orWhereHas('pemeriksaanDokter', function ($dokter) {
                    $dokter->where('kesimpulan', 'Tidak Memenuhi Syarat');
                });
        });

        $ringkasan = [
            'total_peserta' => Mahasiswa::count(),
            'hadir_hari_ini' => Mahasiswa::where('status_kehadiran', 'hadir')
                ->whereDate('updated_at', today())
                ->count(),
            'hadir_hari_sebelumnya' => Mahasiswa::where('status_kehadiran', 'hadir')
                ->whereDate('updated_at', today()->subDay())
                ->count(),
            'memenuhi_syarat' => (clone $memenuhiBaseQuery)->count(),
            'tidak_memenuhi_syarat' => (clone $tidakMemenuhiBaseQuery)->count(),
        ];

        $memenuhiList = (clone $memenuhiBaseQuery)
            ->select('id', 'nama', 'no_pendaftaran')
            ->orderBy('nama')
            ->paginate(20, ['*'], 'memenuhi_page')
            ->withQueryString();

        $tidakMemenuhiList = (clone $tidakMemenuhiBaseQuery)
            ->select('id', 'nama', 'no_pendaftaran')
            ->orderBy('nama')
            ->paginate(20, ['*'], 'tidak_page')
            ->withQueryString();

        return view('laporan.ringkas', compact('ringkasan', 'memenuhiList', 'tidakMemenuhiList'));
    }

    public function exportRingkas()
    {
        $memenuhiBaseQuery = Mahasiswa::query()->where(function ($q) {
            $q->where('kesimpulan_akhir', 'memenuhi_syarat')
                ->orWhereHas('pemeriksaanDokter', function ($dokter) {
                    $dokter->where('kesimpulan', 'Memenuhi Syarat');
                });
        });

        $tidakMemenuhiBaseQuery = Mahasiswa::query()->where(function ($q) {
            $q->where('kesimpulan_akhir', 'tidak_memenuhi_syarat')
                ->orWhereHas('pemeriksaanDokter', function ($dokter) {
                    $dokter->where('kesimpulan', 'Tidak Memenuhi Syarat');
                });
        });

        $ringkasan = [
            'Total Peserta' => Mahasiswa::count(),
            'Hadir Hari Ini' => Mahasiswa::where('status_kehadiran', 'hadir')
                ->whereDate('updated_at', today())
                ->count(),
            'Hadir Hari Sebelumnya' => Mahasiswa::where('status_kehadiran', 'hadir')
                ->whereDate('updated_at', today()->subDay())
                ->count(),
            'Memenuhi Syarat' => (clone $memenuhiBaseQuery)->count(),
            'Tidak Memenuhi Syarat' => (clone $tidakMemenuhiBaseQuery)->count(),
        ];

        $memenuhiList = (clone $memenuhiBaseQuery)
            ->select('no_pendaftaran', 'nama')
            ->orderBy('nama')
            ->get();

        $tidakMemenuhiList = (clone $tidakMemenuhiBaseQuery)
            ->select('no_pendaftaran', 'nama')
            ->orderBy('nama')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Ringkas');

        $sheet->mergeCells('A1:C1');
        $sheet->setCellValue('A1', 'LAPORAN RINGKAS UJI KESEHATAN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Tanggal Cetak');
        $sheet->setCellValue('B2', ':');
        $sheet->setCellValue('C2', now()->format('d-m-Y H:i:s'));

        $sheet->setCellValue('A4', 'RINGKASAN');
        $sheet->getStyle('A4')->getFont()->setBold(true);

        $row = 5;
        foreach ($ringkasan as $label => $value) {
            $sheet->setCellValue('A' . $row, $label);
            $sheet->setCellValue('B' . $row, ':');
            $sheet->setCellValue('C' . $row, $value);
            $row++;
        }

        $row += 1;
        $sheet->setCellValue('A' . $row, 'DAFTAR MAHASISWA MEMENUHI SYARAT');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'No. Pendaftaran');
        $sheet->setCellValue('C' . $row, 'Nama');
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D1FAE5');
        $row++;

        foreach ($memenuhiList as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->no_pendaftaran);
            $sheet->setCellValue('C' . $row, $item->nama);
            $row++;
        }

        if ($memenuhiList->isEmpty()) {
            $sheet->setCellValue('A' . $row, 'Tidak ada data');
            $row++;
        }

        $row += 1;
        $sheet->setCellValue('A' . $row, 'DAFTAR MAHASISWA TIDAK MEMENUHI SYARAT');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'No. Pendaftaran');
        $sheet->setCellValue('C' . $row, 'Nama');
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FEE2E2');
        $row++;

        foreach ($tidakMemenuhiList as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->no_pendaftaran);
            $sheet->setCellValue('C' . $row, $item->nama);
            $row++;
        }

        if ($tidakMemenuhiList->isEmpty()) {
            $sheet->setCellValue('A' . $row, 'Tidak ada data');
        }

        foreach (['A', 'B', 'C'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        LogAktivitas::catat(
            'Unduh laporan ringkas',
            auth()->id(),
            'mahasiswa',
            null,
            [
                'memenuhi' => $memenuhiList->count(),
                'tidak_memenuhi' => $tidakMemenuhiList->count(),
            ]
        );

        $filename = 'Laporan_Ringkas_Uji_Kesehatan_' . date('YmdHis') . '.xlsx';
        $publicPath = public_path('exports/' . $filename);

        if (!file_exists(public_path('exports'))) {
            mkdir(public_path('exports'), 0777, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($publicPath);

        foreach (glob(public_path('exports') . '/*.xlsx') as $file) {
            if (filemtime($file) < now()->subHour()->timestamp) {
                @unlink($file);
            }
        }

        return redirect(asset('exports/' . $filename));
    }

    /**
     * Display laporan data peserta
     */
    public function index(Request $request)
    {
        $plpCompletedScope = function ($q) {
            $q->where(function ($qq) {
                $qq->where('status_pemeriksaan', 'selesai')
                    ->orWhereNotNull('tgl_periksa')
                    ->orWhereNotNull('ended_at');
            });
        };

        $dokterCompletedScope = function ($q) {
            $q->where(function ($qq) {
                $qq->where('is_locked', true)
                    ->orWhereNotNull('tgl_periksa')
                    ->orWhereNotNull('kesimpulan');
            });
        };

        $query = Mahasiswa::query()->with([
            'pemeriksaanPlp:id,mahasiswa_id,tgl_periksa,status_pemeriksaan,ended_at',
            'pemeriksaanDokter:id,mahasiswa_id,tgl_periksa,is_locked,kesimpulan,keterangan_kesimpulan',
        ]);
        $allowedPerPage = [10, 25, 50, 100];
        $perPage = (int) $request->get('per_page', 25);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 25;
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('no_identitas', 'like', "%{$search}%");
            });
        }

        if ($request->filled('prodi')) {
            $query->where('prodi', $request->prodi);
        }

        if ($request->filled('status_kehadiran')) {
            $query->where('status_kehadiran', $request->status_kehadiran);
        }

        if ($request->filled('status_plp')) {
            if ($request->status_plp === 'selesai') {
                $query->where(function ($q) use ($plpCompletedScope) {
                    $q->where('status_plp', 'selesai')
                        ->orWhereHas('pemeriksaanPlp', $plpCompletedScope);
                });
            }

            if ($request->status_plp === 'belum') {
                $query->where('status_plp', 'belum')
                    ->whereDoesntHave('pemeriksaanPlp', $plpCompletedScope);
            }
        }

        if ($request->filled('status_dokter')) {
            if ($request->status_dokter === 'selesai') {
                $query->where(function ($q) use ($dokterCompletedScope) {
                    $q->where('status_dokter', 'selesai')
                        ->orWhereHas('pemeriksaanDokter', $dokterCompletedScope);
                });
            }

            if ($request->status_dokter === 'belum') {
                $query->where('status_dokter', 'belum')
                    ->whereDoesntHave('pemeriksaanDokter', $dokterCompletedScope);
            }
        }

        if ($request->filled('kesimpulan_akhir')) {
            if ($request->kesimpulan_akhir === 'memenuhi_syarat') {
                $query->where(function ($q) {
                    $q->where('kesimpulan_akhir', 'memenuhi_syarat')
                        ->orWhereHas('pemeriksaanDokter', function ($dokter) {
                            $dokter->where('kesimpulan', 'Memenuhi Syarat');
                        });
                });
            }

            if ($request->kesimpulan_akhir === 'tidak_memenuhi_syarat') {
                $query->where(function ($q) {
                    $q->where('kesimpulan_akhir', 'tidak_memenuhi_syarat')
                        ->orWhereHas('pemeriksaanDokter', function ($dokter) {
                            $dokter->where('kesimpulan', 'Tidak Memenuhi Syarat');
                        });
                });
            }

            if ($request->kesimpulan_akhir === '-') {
                $query->where('kesimpulan_akhir', '-')
                    ->whereDoesntHave('pemeriksaanDokter', function ($dokter) {
                        $dokter->whereNotNull('kesimpulan');
                    });
            }
        }

        $mahasiswa = $query->orderBy('nama')->paginate($perPage)->withQueryString();
        $prodis = Mahasiswa::distinct()->pluck('prodi')->filter();

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
                  ->orWhere('no_identitas', 'like', "%{$search}%");
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
            'Nama Lengkap',
            'Jenis Kelamin',
            'Program Studi',
            'Prodi Pilihan 1',
            'Prodi Pilihan 2',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Asal Sekolah',
            'Status Kehadiran',
            
            // Hasil Pemeriksaan PLP
            'Tgl Periksa PLP',
            'Riwayat Penyakit',
            'Suhu (°C)',
            'Tensi (mmHg)',
            'Riwayat Keluarga',
            'Buta Warna',
            'Tinggi Badan (cm)',
            'Berat Badan (kg)',
            'BMI',
            'Status PLP',
            
            // Hasil Pemeriksaan Dokter
            'Tgl Periksa Dokter',
            'Kulit',
            'Mata - Kacamata',
            'Mata - Normal',
            'Mata - Minus',
            'Mata - Silindris',
            'Mata - Strabismus',
            'Telinga Kiri',
            'Ket. Telinga Kiri',
            'Telinga Kanan',
            'Ket. Telinga Kanan',
            'Hidung - Cuping',
            'Ket. Hidung',
            'Lidah - Kebersihan',
            'Ket. Lidah',
            'Lidah - Stomatitis',
            'Ket. Stomatitis',
            'Pharing - Nyeri Tekan',
            'Ket. Pharing',
            'Tonsil - Kemerahan',
            'Ket. Tonsil Kemerahan',
            'Tonsil - Pembesaran',
            'Gigi Lengkap',
            'Tiroid',
            'Jantung - Murmur',
            'Ket. Jantung',
            'Paru - Suara Tambahan',
            'Abdomen - Hamil',
            'Pupil',
            'Thorax Photo',
            'Ket. Thorax',
            'Tulang - Skoliosis',
            'Ket. Skoliosis', 
            'Tulang - Lordosis',
            'Ket. Lordosis',
            'Tulang - Kifosis',
            'Ket. Kifosis',
            'Tulang - Lainnya',
            'Ket. Tulang Lainnya',
            'Bicara - Artikulasi',
            'Ket. Bicara',
            'Cacat Tubuh',
            'Kesimpulan Dokter',
            'Ket. Kesimpulan Dokter',
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

            $statusPlpExport = $row->status_plp;
            if ($plp && (($plp->status_pemeriksaan ?? null) === 'selesai' || $plp->tgl_periksa || $plp->ended_at)) {
                $statusPlpExport = 'selesai';
            }

            $statusDokterExport = $row->status_dokter;
            if ($dokter && ($dokter->is_locked || $dokter->tgl_periksa || $dokter->kesimpulan)) {
                $statusDokterExport = 'selesai';
            }

            $kesimpulanAkhirExport = $row->kesimpulan_akhir;
            if (($kesimpulanAkhirExport === '-' || empty($kesimpulanAkhirExport)) && $dokter?->kesimpulan) {
                $kesimpulanAkhirExport = $dokter->kesimpulan === 'Memenuhi Syarat'
                    ? 'memenuhi_syarat'
                    : 'tidak_memenuhi_syarat';
            }

            $keteranganAkhirExport = $row->keterangan_kesimpulan ?? $dokter->keterangan_kesimpulan ?? '-';
            
            $data = [
                // Data Dasar
                $row->no_pendaftaran,
                $row->no_identitas,
                $row->nama,
                $row->jenis_kelamin,
                $row->prodi,
                $row->prodi_pilihan_1,
                $row->prodi_pilihan_2,
                $row->tempat_lahir,
                optional($row->tanggal_lahir)->format('Y-m-d'),
                $row->asal_sekolah,
                $row->status_kehadiran,
                
                // Hasil Pemeriksaan PLP
                $plp ? optional($plp->tgl_periksa)->format('Y-m-d H:i') : '-',
                $plp->riwayat_penyakit ?? '-',
                $plp->suhu ?? '-',
                $plp->tensi ?? '-',
                $plp->riwayat_keluarga ?? '-',
                $plp->buta_warna ?? '-',
                $plp->tinggi_badan ?? '-',
                $plp->berat_badan ?? '-',
                $plp->bmi ?? '-',
                $statusPlpExport,
                
                // Hasil Pemeriksaan Dokter
                $dokter ? optional($dokter->tgl_periksa)->format('Y-m-d H:i') : '-',
                $dokter->kulit ?? '-',
                $dokter->mata_kacamata ?? '-',
                $dokter->mata_normal ?? '-',
                $dokter && $dokter->mata_minus ? 'Ya (' . $dokter->mata_minus_nilai . ')' : 'Tidak',
                $dokter && $dokter->mata_silindris ? 'Ya (' . $dokter->mata_silindris_nilai . ')' : 'Tidak',
                $dokter && $dokter->mata_strabismus ? 'Ya (' . $dokter->mata_strabismus_nilai . ')' : 'Tidak',
                $dokter->telinga_kiri ?? '-',
                $dokter->telinga_kiri_ket ?? '-',
                $dokter->telinga_kanan ?? '-',
                $dokter->telinga_kanan_ket ?? '-',
                $dokter && $dokter->hidung_cuping ? 'Ya' : 'Tidak',
                $dokter->hidung_cuping_ket ?? '-',
                $dokter->lidah_kebersihan ?? '-',
                $dokter->lidah_kebersihan_ket ?? '-',
                $dokter && $dokter->lidah_stomatitis ? 'Ya' : 'Tidak',
                $dokter->lidah_stomatitis_ket ?? '-',
                $dokter && $dokter->pharing_nyeri_tekan ? 'Ya' : 'Tidak',
                $dokter->pharing_nyeri_tekan_ket ?? '-',
                $dokter && $dokter->tonsil_kemerahan ? 'Ya' : 'Tidak',
                $dokter->tonsil_kemerahan_ket ?? '-',
                $dokter && $dokter->tonsil_pembesaran ? 'Ya' : 'Tidak',
                $dokter && $dokter->gigi_lengkap ? 'Ya' : 'Tidak',
                $dokter->tiroid ?? '-',
                $dokter && $dokter->jantung_murmur ? 'Ya' : 'Tidak',
                $dokter->jantung_murmur_ket ?? '-',
                $dokter && $dokter->paru_suara_tambahan ? 'Ya' : 'Tidak',
                $dokter && $dokter->abdomen_hamil ? 'Ya' : 'Tidak',
                $dokter->pupil ?? '-',
                $dokter->thorax_photo_file ?? '-',
                $dokter->thorax_photo_ket ?? '-',
                $dokter && $dokter->tulang_skoliosis ? 'Ya' : 'Tidak',
                $dokter->tulang_skoliosis_ket ?? '-',
                $dokter && $dokter->tulang_lordosis ? 'Ya' : 'Tidak',
                $dokter->tulang_lordosis_ket ?? '-',
                $dokter && $dokter->tulang_kifosis ? 'Ya' : 'Tidak',
                $dokter->tulang_kifosis_ket ?? '-',
                $dokter && $dokter->tulang_lainnya ? 'Ya' : 'Tidak',
                $dokter->tulang_lainnya_ket ?? '-',
                $dokter->bicara_artikulasi ?? '-',
                $dokter->bicara_artikulasi_ket ?? '-',
                $dokter->cacat_tubuh ?? '-',
                $dokter->kesimpulan ?? '-',
                $dokter->keterangan_kesimpulan ?? '-',
                $statusDokterExport,
                
                // Kesimpulan Akhir
                $kesimpulanAkhirExport,
                $keteranganAkhirExport,
            ];
            
            foreach ($data as $colIndex => $value) {
                $col = Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValue($col . $rowIndex, $value);
            }
            
            $rowIndex++;
        }

        // Set auto size untuk semua kolom
        for ($i = 1; $i <= count($headers); $i++) {
            $col = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

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
