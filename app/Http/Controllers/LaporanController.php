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
                $row->status_plp,
                
                // Hasil Pemeriksaan Dokter
                $dokter ? optional($dokter->tgl_periksa)->format('Y-m-d H:i') : '-',
                $dokter->kulit ?? '-',
                $dokter->mata_kacamata ?? '-',
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
                $row->status_dokter,
                
                // Kesimpulan Akhir
                $row->kesimpulan_akhir,
                $row->keterangan_kesimpulan,
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
