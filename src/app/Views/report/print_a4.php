<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rapor Siswa - <?= $student['full_name'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Pengaturan Kertas A4 */
        @page { size: A4; margin: 0; }
        body { font-family: 'Times New Roman', serif; background: #ccc; }
        .page {
            background: white;
            width: 210mm;
            min-height: 297mm;
            display: block;
            margin: 0 auto;
            padding: 20mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }
        @media print {
            body { background: white; }
            .page { margin: 0; box-shadow: none; width: 100%; height: auto; }
            .no-print { display: none !important; }
        }
        table { border-collapse: collapse; width: 100%; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px 12px; font-size: 12pt; }
        .header-table td { border: none; padding: 2px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .bg-gray-200 { background-color: #e5e7eb !important; -webkit-print-color-adjust: exact; }
    </style>
</head>
<body>

    <div class="no-print fixed top-0 left-0 w-full bg-gray-800 p-4 text-center shadow-lg z-50">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700 mx-2">
            <i class="fa-solid fa-print"></i> Download PDF / Cetak
        </button>
        <button onclick="window.close()" class="bg-gray-600 text-white px-6 py-2 rounded font-bold hover:bg-gray-700 mx-2">
            Tutup
        </button>
    </div>

    <div class="page mt-20 md:mt-0">
        
        <div class="text-center border-b-4 border-black pb-4 mb-6">
            <h2 class="text-xl font-bold uppercase">Yayasan Syekh Ibrahim Musa</h2>
            <h1 class="text-2xl font-bold uppercase tracking-wider">Pondok Pesantren Sumatera Thawalib Parabek</h1>
            <p class="text-sm">Jl. Raya Bukittinggi - Medan Km. 4, Banuhampu, Agam, Sumatera Barat</p>
            <p class="text-sm italic">Website: www.thawalibparabek.sch.id | Email: info@thawalibparabek.sch.id</p>
        </div>

        <div class="text-center mb-6">
            <h3 class="text-lg font-bold uppercase underline">Laporan Hasil Belajar Siswa</h3>
        </div>

        <table class="header-table mb-6 w-full">
            <tr>
                <td width="20%">Nama Siswa</td>
                <td width="2%">:</td>
                <td width="40%" class="font-bold"><?= $student['full_name'] ?></td>
                <td width="15%">Kelas</td>
                <td width="2%">:</td>
                <td width="21%"><?= $student['class_name'] ?></td>
            </tr>
            <tr>
                <td>NIS / NISN</td>
                <td>:</td>
                <td><?= $student['nis'] ?> / <?= $student['nisn'] ?></td>
                <td>Semester</td>
                <td>:</td>
                <td><?= $year['semester'] ?? 'Ganjil' ?></td>
            </tr>
            <tr>
                <td>Tahun Ajaran</td>
                <td>:</td>
                <td><?= $year['name'] ?? date('Y') ?></td>
                <td>Jurusan</td>
                <td>:</td>
                <td><?= $student['major'] ?></td>
            </tr>
        </table>

        <table class="mb-6">
            <thead>
                <tr class="bg-gray-200">
                    <th width="5%" class="text-center">No</th>
                    <th width="35%">Mata Pelajaran</th>
                    <th width="10%" class="text-center">KKM</th>
                    <th width="10%" class="text-center">Nilai</th>
                    <th width="10%" class="text-center">Predikat</th>
                    <th width="30%">Deskripsi Capaian</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($grades['NASIONAL'])): ?>
                <tr class="bg-gray-200"><td colspan="6" class="font-bold px-2">A. Muatan Nasional</td></tr>
                <?php $no=1; foreach($grades['NASIONAL'] as $g): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= $g['subject_name'] ?></td>
                        <td class="text-center"><?= $g['kkm'] ?></td>
                        <td class="text-center font-bold"><?= $g['final_score'] ?></td>
                        <td class="text-center"><?= $g['predicate'] ?></td>
                        <td class="text-sm"><?= $g['description'] ?></td>
                    </tr>
                <?php endforeach; endif; ?>

                <?php if(!empty($grades['PESANTREN'])): ?>
                <tr class="bg-gray-200"><td colspan="6" class="font-bold px-2">B. Muatan Pesantren (Diniyyah)</td></tr>
                <?php $no=1; foreach($grades['PESANTREN'] as $g): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= $g['subject_name'] ?></td>
                        <td class="text-center"><?= $g['kkm'] ?></td>
                        <td class="text-center font-bold"><?= $g['final_score'] ?></td>
                        <td class="text-center"><?= $g['predicate'] ?></td>
                        <td class="text-sm"><?= $g['description'] ?></td>
                    </tr>
                <?php endforeach; endif; ?>

                <?php if(!empty($grades['MULOK'])): ?>
                <tr class="bg-gray-200"><td colspan="6" class="font-bold px-2">C. Muatan Lokal</td></tr>
                <?php $no=1; foreach($grades['MULOK'] as $g): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= $g['subject_name'] ?></td>
                        <td class="text-center"><?= $g['kkm'] ?></td>
                        <td class="text-center font-bold"><?= $g['final_score'] ?></td>
                        <td class="text-center"><?= $g['predicate'] ?></td>
                        <td class="text-sm"><?= $g['description'] ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>

        <div class="w-1/2 mb-6">
            <table class="text-sm">
                <tr class="bg-gray-200"><th colspan="2" class="text-left px-2">Ketidakhadiran</th></tr>
                <tr><td width="60%">Sakit</td><td class="text-center"><?= $attendance['S'] ?> hari</td></tr>
                <tr><td>Izin</td><td class="text-center"><?= $attendance['I'] ?> hari</td></tr>
                <tr><td>Tanpa Keterangan</td><td class="text-center"><?= $attendance['A'] ?> hari</td></tr>
            </table>
        </div>

        <div class="flex justify-between mt-10 text-center text-sm">
            <div class="w-1/3">
                <p>Mengetahui,</p>
                <p>Orang Tua / Wali</p>
                <br><br><br>
                <p class="border-b border-black w-3/4 mx-auto pb-1">...................................</p>
            </div>
            <div class="w-1/3">
                <p>Bukittinggi, <?= date('d F Y') ?></p>
                <p>Wali Kelas</p>
                <br><br><br>
                <p class="font-bold underline">Ustadz/ah Wali Kelas</p>
                <p>NIP. ...........................</p>
            </div>
        </div>

        <div class="text-center mt-8">
            <p>Mengetahui,</p>
            <p>Kepala Sekolah</p>
            <br><br><br>
            <p class="font-bold underline">H. M. Zaki Munawwar, Lc</p>
            <p>NIY. 202401001</p>
        </div>

    </div>

</body>
</html>
