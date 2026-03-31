<?php use App\Models\AppConfig; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi #<?= $trx['id'] ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; margin: 0; padding: 20px; background: #eee; }
        .receipt { 
            width: 80mm; /* Ukuran Struk Thermal 80mm atau A6 */
            background: white; margin: 0 auto; padding: 15px; 
            border: 1px dashed #aaa; 
        }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
        .header h2 { margin: 0; font-size: 14pt; }
        .header p { margin: 0; font-size: 8pt; }
        .row { display: flex; justify-content: space-between; font-size: 9pt; margin-bottom: 5px; }
        .total { border-top: 2px dashed #000; border-bottom: 2px dashed #000; padding: 5px 0; font-weight: bold; font-size: 11pt; margin-top: 10px; }
        .footer { text-align: center; font-size: 8pt; margin-top: 15px; }
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .receipt { border: none; width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="header">
            <h2><?= AppConfig::get('school_name', 'SIAKAD PARABEK') ?></h2>
            <p><?= date('d M Y H:i', strtotime($trx['created_at'])) ?></p>
            <p>No: #<?= str_pad($trx['id'], 6, '0', STR_PAD_LEFT) ?></p>
        </div>

        <div class="row">
            <span>Siswa:</span>
            <span style="text-align:right"><?= substr($trx['full_name'], 0, 18) ?></span>
        </div>
        <div class="row">
            <span>NIS:</span>
            <span><?= $trx['nis'] ?></span>
        </div>
        <div class="row">
            <span>Kelas:</span>
            <span><?= $trx['class_name'] ?? '-' ?></span>
        </div>

        <hr style="border:none; border-top:1px dashed #ccc; margin:10px 0;">

        <div class="row" style="font-weight:bold;">
            <span>Pembayaran:</span>
        </div>
        <div class="row">
            <span><?= $trx['fee_name'] ?></span>
            <span><?= number_format($trx['amount_paid']) ?></span>
        </div>

        <div class="row total">
            <span>TOTAL BAYAR</span>
            <span>Rp <?= number_format($trx['amount_paid'], 0, ',', '.') ?></span>
        </div>

        <div class="row" style="margin-top:5px;">
            <span>Metode:</span>
            <span><?= $trx['payment_method'] ?></span>
        </div>
        <div class="row">
            <span>Kasir:</span>
            <span><?= explode(' ', $trx['admin_name'])[0] ?></span>
        </div>

        <div class="footer">
            <p>Terima Kasih.</p>
            <p>Simpan struk ini sebagai bukti sah.</p>
        </div>
    </div>
</body>
</html>
