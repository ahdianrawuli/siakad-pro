<?php use App\Models\AppConfig; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rapor - <?= $student['full_name'] ?></title>
    <style>
        @page { size: A4; margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.3; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .header p { margin: 0; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px; vertical-align: top; }
        
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 5px; }
        .main-table th { background-color: #f0f0f0; text-align: center; }
        
        .section-title { font-weight: bold; margin-bottom: 5px; border-bottom: 1px solid #000; display: inline-block; }
        
        .cols-2 { display: flex; gap: 20px; }
        .col { flex: 1; }
        
        .signature { margin-top: 30px; display: flex; justify-content: space-between; text-align: center; }
        .sign-box { width: 30%; }
        
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>LAPORAN HASIL BELAJAR SANTRI</h1>
        <h2><?= AppConfig::get('school_name') ?></h2>
        <p><?= AppConfig::get('school_address') ?></p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Nama Santri</td><td width="35%">: <b><?= $student['full_name'] ?></b></td>
            <td width="15%">Kelas</td><td width="35%">: <?= $student['class_name'] ?></td>
        </tr>
        <tr>
            <td>NIS / NISN</td><td>: <?= $student['nis'] ?></td>
            <td>Semester</td><td>: <?= $year['semester'] ?> - <?= $year['name'] ?></td>
        </tr>
    </table>

    <div class="section-title">A. CAPAIAN KOMPETENSI AKADEMIK</div>
    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Mata Pelajaran</th>
                <th width="8%">KKM</th>
                <th width="8%">Nilai</th>
                <th width="8%">Pred</th>
                <th>Deskripsi Kemajuan Belajar</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach($grades as $group => $subjects): 
                if(!empty($subjects)):
            ?>
                <tr><td colspan="6" style="background:#eee; font-weight:bold;"><?= $group ?></td></tr>
                <?php foreach($subjects as $s): ?>
                <tr>
                    <td align="center"><?= $no++ ?></td>
                    <td><?= $s['subject_name'] ?></td>
                    <td align="center"><?= $s['kkm'] ?></td>
                    <td align="center" style="font-weight:bold;"><?= $s['final_score'] ?></td>
                    <td align="center"><?= $s['predicate'] ?></td>
                    <td style="font-size: 8pt;"><?= $s['description'] ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; endforeach; ?>
        </tbody>
    </table>

    <div class="cols-2">
        <div class="col">
            <div class="section-title">B. PENGEMBANGAN DIRI & KEPRIBADIAN</div>
            <table class="main-table">
                <tr>
                    <td><b>Sikap / Perilaku</b></td>
                    <td align="center"><b><?= $attitude ?></b></td>
                </tr>
                <tr>
                    <td>Poin Pelanggaran</td>
                    <td align="center"><?= $violation_points ?></td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:8pt; font-style:italic;">
                        *Predikat Sikap didasarkan pada poin pelanggaran dan pengamatan Wali Kelas.
                    </td>
                </tr>
            </table>

            <div class="section-title">C. KETIDAKHADIRAN</div>
            <table class="main-table">
                <tr><td>Sakit</td><td align="center"><?= $attendance['S'] ?> hari</td></tr>
                <tr><td>Izin</td><td align="center"><?= $attendance['I'] ?> hari</td></tr>
                <tr><td>Tanpa Keterangan</td><td align="center"><?= $attendance['A'] ?> hari</td></tr>
            </table>
        </div>

        <div class="col">
            <div class="section-title">D. CAPAIAN TAHFIDZ AL-QURAN</div>
            <table class="main-table">
                <thead><tr><th>Surat</th><th>Ayat</th><th>Nilai</th></tr></thead>
                <tbody>
                    <?php foreach($tahfidz as $t): ?>
                    <tr>
                        <td><?= $t['surah_name'] ?></td>
                        <td align="center"><?= $t['verses'] ?></td>
                        <td align="center"><?= $t['grade'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($tahfidz)): ?><tr><td colspan="3" align="center">- Belum ada data -</td></tr><?php endif; ?>
                </tbody>
            </table>

            <div class="section-title">E. PRESTASI</div>
            <table class="main-table">
                <?php foreach($achievements as $ach): ?>
                <tr>
                    <td>
                        <b><?= $ach['title'] ?></b><br>
                        <span style="font-size:8pt;">Tingkat: <?= $ach['level'] ?> (<?= date('Y', strtotime($ach['date'])) ?>)</span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($achievements)): ?><tr><td align="center">- Tidak ada data prestasi -</td></tr><?php endif; ?>
            </table>
        </div>
    </div>

    <div class="signature">
        <div class="sign-box">
            <p>Orang Tua / Wali,</p>
            <br><br><br>
            <p>( .................................... )</p>
        </div>
        <div class="sign-box">
            <p>Wali Kelas,</p>
            <br><br><br>
            <p>( .................................... )</p>
        </div>
        <div class="sign-box">
            <p>Bukittinggi, <?= date('d F Y') ?></p>
            <p>Kepala Sekolah,</p>
            <br><br><br>
            <p><b>H. M. Zaki Munawwar</b></p>
        </div>
    </div>

</body>
</html>
