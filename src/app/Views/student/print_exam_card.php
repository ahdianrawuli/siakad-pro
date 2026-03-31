<?php use App\Models\AppConfig; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Ujian - <?= $candidate['registration_no'] ?></title>
    <style>
        @page { size: A5 landscape; margin: 0; }
        body { font-family: Arial, sans-serif; -webkit-print-color-adjust: exact; background: #eee; }
        .card {
            width: 200mm; height: 138mm; /* A5 Landscape approx */
            background: white; margin: 20px auto; padding: 20px;
            border: 1px solid #ccc; position: relative;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header { border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; }
        .logo { width: 60px; height: 60px; background: #ccc; margin-right: 15px; } /* Ganti src img nanti */
        .kop h2 { margin: 0; font-size: 14pt; text-transform: uppercase; }
        .kop p { margin: 0; font-size: 9pt; }
        
        .title { text-align: center; background: #000; color: #fff; padding: 5px; margin-bottom: 20px; font-weight: bold; text-transform: uppercase; }
        
        .content { display: flex; }
        .photo-area { width: 30mm; height: 40mm; border: 1px solid #000; margin-right: 20px; display: flex; align-items: center; justify-content: center; background: #f0f0f0; }
        .photo-area img { max-width: 100%; max-height: 100%; }
        
        .data-table { width: 100%; border-collapse: collapse; font-size: 11pt; }
        .data-table td { padding: 4px; vertical-align: top; }
        .label { font-weight: bold; width: 130px; }

        .footer { position: absolute; bottom: 20px; right: 20px; text-align: center; font-size: 10pt; }
        
        @media print {
            body { background: white; }
            .card { margin: 0; border: none; box-shadow: none; width: 100%; height: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; padding: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: blue; color: white; border: none; cursor: pointer; font-weight: bold;">CETAK KARTU</button>
    </div>

    <div class="card">
        <div class="header">
            <?php if(AppConfig::get('school_logo')): ?>
                <img src="/uploads/<?= AppConfig::get('school_logo') ?>" class="logo" style="background:none;">
            <?php else: ?>
                <div class="logo"></div>
            <?php endif; ?>
            
            <div class="kop">
                <h2><?= AppConfig::get('school_name') ?></h2>
                <p>Panitia Penerimaan Peserta Didik Baru (PPDB)</p>
                <p><?= AppConfig::get('school_address') ?></p>
            </div>
        </div>

        <div class="title">KARTU PESERTA UJIAN SELEKSI</div>

        <div class="content">
            <div class="photo-area">
                <?php if($candidate['photo']): ?>
                    <img src="/uploads/documents/<?= $candidate['photo'] ?>">
                <?php else: ?>
                    <span style="font-size: 8pt; text-align: center;">Tempel Foto 3x4</span>
                <?php endif; ?>
            </div>
            
            <table class="data-table">
                <tr>
                    <td class="label">No. Ujian / Reg</td>
                    <td>: <b><?= $candidate['registration_no'] ?></b></td>
                </tr>
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td>: <?= strtoupper($candidate['full_name']) ?></td>
                </tr>
                <tr>
                    <td class="label">Asal Sekolah</td>
                    <td>: <?= $candidate['school_origin'] ?></td>
                </tr>
                <tr>
                    <td class="label">Jalur Pilihan</td>
                    <td>: <?= $candidate['track_name'] ?> (<?= $candidate['level'] ?>)</td>
                </tr>
                <tr>
                    <td class="label">Jadwal Ujian</td>
                    <td>: <?= $exam['date'] ?>, Pukul <?= $exam['time'] ?></td>
                </tr>
                <tr>
                    <td class="label">Lokasi</td>
                    <td>: <b><?= $exam['location'] ?></b></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Bukittinggi, <?= date('d F Y') ?></p>
            <p>Ketua Panitia,</p>
            <br><br>
            <p style="text-decoration: underline; font-weight: bold;">H. M. Zaki Munawwar</p>
        </div>
    </div>

</body>
</html>
