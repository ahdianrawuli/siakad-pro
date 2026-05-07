<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($template['name']) ?></title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 12pt; margin: 2cm; color: #000; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { font-size: 16pt; margin: 0; }
        .header p { margin: 2px 0; font-size: 10pt; }
        .title { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin: 20px 0; }
        .content { line-height: 2; }
        .footer { margin-top: 40px; text-align: right; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:20px;">
        <button onclick="window.print()" style="background:#2563eb;color:#fff;padding:8px 20px;border:none;border-radius:6px;cursor:pointer;font-size:13px;">
            🖨️ Cetak Surat
        </button>
        <a href="/student/letter" style="margin-left:10px;color:#555;font-size:13px;">← Kembali</a>
    </div>

    <div class="header">
        <h2>PONDOK PESANTREN SUMATERA THAWALIB PARABEK</h2>
        <p>Jl. Raya Bukittinggi - Medan Km. 4, Agam, Sumatera Barat</p>
    </div>

    <div class="title"><?= htmlspecialchars($template['name']) ?></div>

    <div class="content">
        <?= $content ?>
    </div>

    <div class="footer">
        <p>Parabek, <?= date('d F Y') ?></p>
        <p>Kepala Sekolah,</p>
        <br><br><br>
        <p><strong>_______________________</strong></p>
    </div>
</body>
</html>
