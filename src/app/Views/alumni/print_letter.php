<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Alumni</title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 12pt; margin: 0; padding: 20px 40px; color: #000; }
        .header { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header img { width: 80px; height: 80px; margin-right: 15px; }
        .header-text { flex: 1; text-align: center; }
        .header-text h2 { margin: 0; font-size: 14pt; text-transform: uppercase; }
        .header-text h3 { margin: 2px 0; font-size: 12pt; }
        .header-text p { margin: 2px 0; font-size: 10pt; }
        .title { text-align: center; margin: 20px 0 5px; font-size: 14pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; }
        .nomor { text-align: center; margin-bottom: 20px; font-size: 11pt; }
        .body-text { line-height: 2; margin-bottom: 10px; }
        .data-table { width: 100%; margin: 15px 0; }
        .data-table td { padding: 2px 0; vertical-align: top; }
        .data-table td:first-child { width: 180px; }
        .data-table td:nth-child(2) { width: 10px; }
        .signature { margin-top: 40px; display: flex; justify-content: flex-end; }
        .signature-box { text-align: center; width: 250px; }
        .signature-box .space { height: 70px; }
        @media print {
            body { margin: 0; padding: 15px 30px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom:15px;">
    <button onclick="window.print()" style="background:#2E603E;color:white;padding:8px 20px;border:none;border-radius:6px;cursor:pointer;font-size:13px;">
        🖨️ Cetak Surat
    </button>
    <a href="/school/alumni" style="margin-left:10px;color:#555;font-size:13px;">← Kembali</a>
</div>

<div class="header">
    <div class="header-text">
        <h2>Pondok Pesantren Sumatera Thawalib Parabek</h2>
        <h3><?= htmlspecialchars($school) ?></h3>
        <p>Jl. Parabek, Bukittinggi, Sumatera Barat | Telp. (0752) 123456</p>
    </div>
</div>

<div class="title">Surat Keterangan Alumni</div>
<div class="nomor">Nomor: SKA/<?= date('Y') ?>/<?= str_pad($alumni['id'], 4, '0', STR_PAD_LEFT) ?></div>

<p class="body-text">Yang bertanda tangan di bawah ini, Pimpinan Pondok Pesantren Sumatera Thawalib Parabek, menerangkan bahwa:</p>

<table class="data-table">
    <tr>
        <td>Nama Lengkap</td><td>:</td>
        <td><strong><?= htmlspecialchars($alumni['full_name']) ?></strong></td>
    </tr>
    <tr>
        <td>NIS</td><td>:</td>
        <td><?= htmlspecialchars($alumni['nis']) ?></td>
    </tr>
    <tr>
        <td>Tahun Lulus</td><td>:</td>
        <td><?= htmlspecialchars($alumni['graduation_year']) ?></td>
    </tr>
    <?php if (!empty($alumni['phone'])): ?>
    <tr>
        <td>No. Telepon</td><td>:</td>
        <td><?= htmlspecialchars($alumni['phone']) ?></td>
    </tr>
    <?php endif; ?>
    <?php if (!empty($alumni['activity'])): ?>
    <tr>
        <td>Aktivitas Saat Ini</td><td>:</td>
        <td><?= htmlspecialchars($alumni['activity']) ?><?= !empty($alumni['detail_activity']) ? ' — ' . htmlspecialchars($alumni['detail_activity']) : '' ?></td>
    </tr>
    <?php endif; ?>
</table>

<p class="body-text">
    Adalah benar merupakan alumni dari Pondok Pesantren Sumatera Thawalib Parabek yang telah menyelesaikan pendidikannya pada tahun <strong><?= htmlspecialchars($alumni['graduation_year']) ?></strong>.
</p>

<p class="body-text">
    Surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
</p>

<div class="signature">
    <div class="signature-box">
        <p>Bukittinggi, <?= $date ?></p>
        <p>Pimpinan Pesantren,</p>
        <div class="space"></div>
        <p><strong><?= htmlspecialchars($principal ?: '___________________') ?></strong></p>
    </div>
</div>

</body>
</html>
