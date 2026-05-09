<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Inventaris Aset</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }
        .header { text-align: center; border-bottom: 2px solid #1e293b; padding-bottom: 10px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; font-weight: bold; }
        .header p { font-size: 11px; color: #475569; margin-top: 2px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 10px; color: #64748b; }
        .summary { display: flex; gap: 16px; margin-bottom: 16px; }
        .summary-box { border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 14px; background: #f8fafc; }
        .summary-box .label { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
        .summary-box .value { font-size: 14px; font-weight: bold; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead tr { background: #1e293b; color: #fff; }
        thead th { padding: 7px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .badge { display: inline-block; padding: 2px 7px; border-radius: 4px; font-size: 9px; font-weight: bold; }
        .badge-baik { background: #dcfce7; color: #166534; }
        .badge-ringan { background: #fef9c3; color: #854d0e; }
        .badge-berat { background: #fee2e2; color: #991b1b; }
        .badge-hilang { background: #f1f5f9; color: #475569; }
        .footer { text-align: right; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 8px; }
        @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN INVENTARIS ASET</h1>
        <p>Pondok Pesantren Sumatera Thawalib Parabek</p>
    </div>

    <div class="meta">
        <span>Dicetak: <?= $printDate ?></span>
        <span>
            <?php if ($catId): ?>Filter Kategori: <?= htmlspecialchars($catId) ?> | <?php endif; ?>
            <?php if ($cond): ?>Filter Kondisi: <?= str_replace('_', ' ', $cond) ?> | <?php endif; ?>
            Total: <?= $summary['total_item'] ?? 0 ?> item
        </span>
    </div>

    <div class="summary">
        <div class="summary-box">
            <div class="label">Total Item</div>
            <div class="value"><?= $summary['total_item'] ?? 0 ?></div>
        </div>
        <div class="summary-box">
            <div class="label">Total Nilai Aset</div>
            <div class="value">Rp <?= number_format($summary['total_asset'] ?? 0, 0, ',', '.') ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Merk</th>
                <th>Lokasi</th>
                <th style="text-align:center">Jml</th>
                <th style="text-align:right">Harga Satuan</th>
                <th style="text-align:right">Total Nilai</th>
                <th style="text-align:center">Kondisi</th>
                <th>Sumber Dana</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="11" style="text-align:center;padding:20px;color:#94a3b8">Tidak ada data.</td></tr>
            <?php endif; ?>
            <?php $no = 1; foreach ($items as $i): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td style="font-family:monospace;font-size:10px"><?= htmlspecialchars($i['code']) ?></td>
                <td><strong><?= htmlspecialchars($i['name']) ?></strong></td>
                <td><?= $i['category_name'] ?></td>
                <td><?= $i['brand'] ?? '-' ?></td>
                <td><?= $i['location'] ?? '-' ?></td>
                <td style="text-align:center"><?= $i['quantity'] ?></td>
                <td style="text-align:right">Rp <?= number_format($i['price'] ?? 0, 0, ',', '.') ?></td>
                <td style="text-align:right">Rp <?= number_format(($i['price'] ?? 0) * ($i['quantity'] ?? 0), 0, ',', '.') ?></td>
                <td style="text-align:center">
                    <?php
                    $badgeClass = ['BAIK'=>'badge-baik','RUSAK_RINGAN'=>'badge-ringan','RUSAK_BERAT'=>'badge-berat','HILANG'=>'badge-hilang'];
                    ?>
                    <span class="badge <?= $badgeClass[$i['condition_status']] ?? '' ?>"><?= str_replace('_', ' ', $i['condition_status']) ?></span>
                </td>
                <td><?= $i['source_fund'] ?? '-' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">Dokumen ini digenerate otomatis oleh SIAKAD PRO — <?= $printDate ?></div>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
