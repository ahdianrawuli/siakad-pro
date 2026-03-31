<?php use App\Models\AppConfig; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <style>
        @page { size: A4; margin: 0; }
        body { font-family: 'Times New Roman', serif; line-height: 1.6; }
        .page { width: 210mm; min-height: 297mm; padding: 20mm; margin: 0 auto; background: white; }
        .header { text-align: center; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18pt; text-transform: uppercase; }
        .header h2 { margin: 0; font-size: 14pt; font-weight: normal; }
        .header p { margin: 0; font-size: 10pt; }
        .content { text-align: justify; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="page">
        <div class="header">
            <h1><?= AppConfig::get('school_name') ?></h1>
            <p><?= AppConfig::get('school_address') ?></p>
            <p>Telp: <?= AppConfig::get('school_phone') ?> | Email: <?= AppConfig::get('school_email') ?></p>
        </div>

        <div class="content">
            <h3 style="text-align: center; text-decoration: underline; margin-bottom: 30px;"><?= strtoupper($title) ?></h3>
            <?= $content ?>
        </div>

        <div style="margin-top: 50px; float: right; text-align: center; width: 200px;">
            <p>Bukittinggi, <?= date('d F Y') ?></p>
            <p>Kepala Sekolah,</p>
            <br><br><br>
            <p style="font-weight: bold; text-decoration: underline;">H. M. Zaki Munawwar</p>
        </div>
    </div>
</body>
</html>
