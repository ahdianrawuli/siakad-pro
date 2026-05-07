<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Biodata Siswa - <?= htmlspecialchars($student['full_name']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #000; background: #fff; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 15mm; }
        h1 { font-size: 16px; text-align: center; margin-bottom: 4px; }
        .subtitle { text-align: center; font-size: 11px; color: #555; margin-bottom: 16px; }
        hr { border: 1px solid #000; margin-bottom: 16px; }
        table.info { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.info td { padding: 4px 6px; vertical-align: top; }
        table.info td:first-child { width: 35%; font-weight: bold; }
        table.info td:nth-child(2) { width: 5%; }
        .section-title { font-size: 13px; font-weight: bold; background: #eee; padding: 4px 6px; margin-bottom: 8px; border-left: 4px solid #333; }
        .footer { margin-top: 30px; text-align: right; }
        .footer .sign-box { display: inline-block; text-align: center; width: 180px; }
        .sign-box .line { margin-top: 60px; border-top: 1px solid #000; padding-top: 4px; }
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<div class="page">
    <h1>BIODATA SISWA</h1>
    <p class="subtitle">Pondok Pesantren Sumatera Thawalib Parabek</p>
    <hr>

    <div class="section-title">Data Pribadi</div>
    <table class="info">
        <tr><td>Nama Lengkap</td><td>:</td><td><?= htmlspecialchars($student['full_name']) ?></td></tr>
        <tr><td>NIS</td><td>:</td><td><?= htmlspecialchars($student['nis'] ?? '-') ?></td></tr>
        <tr><td>NISN</td><td>:</td><td><?= htmlspecialchars($student['nisn'] ?? '-') ?></td></tr>
        <tr><td>NIK</td><td>:</td><td><?= htmlspecialchars($student['nik'] ?? '-') ?></td></tr>
        <tr><td>Jenis Kelamin</td><td>:</td><td><?= htmlspecialchars($student['gender'] ?? '-') ?></td></tr>
        <tr><td>Tempat, Tgl Lahir</td><td>:</td><td><?= htmlspecialchars(($student['place_of_birth'] ?? '-') . ', ' . (isset($student['date_of_birth']) ? date('d-m-Y', strtotime($student['date_of_birth'])) : '-')) ?></td></tr>
        <tr><td>Kelas</td><td>:</td><td><?= htmlspecialchars($student['class_name'] ?? '-') ?></td></tr>
        <tr><td>Alamat</td><td>:</td><td><?= htmlspecialchars($student['address'] ?? '-') ?></td></tr>
        <tr><td>No. WhatsApp</td><td>:</td><td><?= htmlspecialchars($student['whatsapp_number'] ?? '-') ?></td></tr>
    </table>

    <div class="section-title">Data Orang Tua / Wali</div>
    <table class="info">
        <tr><td>Nama Ayah</td><td>:</td><td><?= htmlspecialchars($student['father_name'] ?? '-') ?></td></tr>
        <tr><td>Pekerjaan Ayah</td><td>:</td><td><?= htmlspecialchars($student['father_job'] ?? '-') ?></td></tr>
        <tr><td>No. HP Ayah</td><td>:</td><td><?= htmlspecialchars($student['father_phone'] ?? '-') ?></td></tr>
        <tr><td>Nama Ibu</td><td>:</td><td><?= htmlspecialchars($student['mother_name'] ?? '-') ?></td></tr>
        <tr><td>Pekerjaan Ibu</td><td>:</td><td><?= htmlspecialchars($student['mother_job'] ?? '-') ?></td></tr>
        <tr><td>No. HP Ibu</td><td>:</td><td><?= htmlspecialchars($student['mother_phone'] ?? '-') ?></td></tr>
        <?php if (!empty($student['guardian_name'])): ?>
        <tr><td>Nama Wali</td><td>:</td><td><?= htmlspecialchars($student['guardian_name']) ?></td></tr>
        <tr><td>Hubungan Wali</td><td>:</td><td><?= htmlspecialchars($student['guardian_relation'] ?? '-') ?></td></tr>
        <tr><td>No. HP Wali</td><td>:</td><td><?= htmlspecialchars($student['guardian_phone'] ?? '-') ?></td></tr>
        <?php endif; ?>
    </table>

    <div class="footer">
        <div class="sign-box">
            <p>Parabek, <?= date('d F Y') ?></p>
            <p>Pimpinan Pesantren</p>
            <div class="line">(__________________________)</div>
        </div>
    </div>
</div>

<div class="no-print" style="text-align:center; margin: 20px;">
    <button onclick="window.print()" style="padding: 8px 24px; background:#2563eb; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:13px;">
        🖨️ Cetak
    </button>
    <button onclick="window.close()" style="padding: 8px 24px; background:#6b7280; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:13px; margin-left:8px;">
        Tutup
    </button>
</div>
</body>
</html>
