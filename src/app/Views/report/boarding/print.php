<?php use App\Models\AppConfig; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rapor Asrama - <?= htmlspecialchars($student['full_name']) ?></title>
    <style>
        @page { size: A4; margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.5; color: #000; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 14pt; text-transform: uppercase; letter-spacing: 1px; }
        .header h2 { margin: 4px 0 2px; font-size: 12pt; }
        .header p { margin: 0; font-size: 9pt; }
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 3px 5px; vertical-align: top; }
        .grade-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .grade-table th, .grade-table td { border: 1px solid #000; padding: 8px 10px; }
        .grade-table th { background: #f0f0f0; text-align: center; font-size: 10pt; }
        .grade-table td.grade { text-align: center; font-size: 18pt; font-weight: bold; width: 60px; }
        .note-box { border: 1px solid #000; padding: 10px; min-height: 60px; margin-bottom: 20px; font-size: 10pt; }
        .note-label { font-weight: bold; margin-bottom: 5px; }
        .signature { margin-top: 40px; display: flex; justify-content: space-between; text-align: center; }
        .sign-box { width: 40%; }
        .sign-box .line { margin-top: 60px; border-top: 1px solid #000; padding-top: 4px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>Rapor Kepesantrenan</h1>
        <h2><?= AppConfig::get('school_name') ?></h2>
        <p><?= AppConfig::get('school_address') ?></p>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%">Nama Santri</td>
            <td width="30%">: <b><?= htmlspecialchars($student['full_name']) ?></b></td>
            <td width="20%">Kelas</td>
            <td width="30%">: <?= htmlspecialchars($student['class_name'] ?? '-') ?></td>
        </tr>
        <tr>
            <td>NIS</td>
            <td>: <?= htmlspecialchars($student['nis'] ?? '-') ?></td>
            <td>Asrama</td>
            <td>: <?= htmlspecialchars($student['dorm_name'] ?? '-') ?></td>
        </tr>
        <tr>
            <td>Tahun Ajaran</td>
            <td colspan="3">: <?= htmlspecialchars($year['name'] ?? '-') ?> &mdash; Semester <?= htmlspecialchars($year['semester'] ?? '-') ?></td>
        </tr>
    </table>

    <table class="grade-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Aspek Penilaian</th>
                <th width="80px">Nilai</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">1</td>
                <td><b>Tahfidz Al-Qur'an</b></td>
                <td class="grade"><?= htmlspecialchars($grade['tahfidz_grade'] ?? '-') ?></td>
                <td style="font-size:9pt;"><?= nl2br(htmlspecialchars($grade['tahfidz_desc'] ?? '')) ?></td>
            </tr>
            <tr>
                <td align="center">2</td>
                <td><b>Bahasa (Arab &amp; Inggris)</b></td>
                <td class="grade"><?= htmlspecialchars($grade['language_grade'] ?? '-') ?></td>
                <td style="font-size:9pt;"><?= nl2br(htmlspecialchars($grade['language_desc'] ?? '')) ?></td>
            </tr>
            <tr>
                <td align="center">3</td>
                <td><b>Akhlaq &amp; Disiplin</b></td>
                <td class="grade"><?= htmlspecialchars($grade['character_grade'] ?? '-') ?></td>
                <td style="font-size:9pt;"><?= nl2br(htmlspecialchars($grade['character_desc'] ?? '')) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="note-label">Catatan Wali Asrama (Musyrif):</div>
    <div class="note-box"><?= nl2br(htmlspecialchars($grade['homeroom_note'] ?? '')) ?></div>

    <div class="signature">
        <div class="sign-box">
            <p>Orang Tua / Wali,</p>
            <div class="line">( ..................................... )</div>
        </div>
        <div class="sign-box">
            <p>Bukittinggi, <?= date('d F Y') ?></p>
            <p>Wali Asrama,</p>
            <div class="line">( ..................................... )</div>
        </div>
    </div>

</body>
</html>
