<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Keuangan</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 12px; color: #1e293b; padding: 20px; }
    h1 { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
    .subtitle { color: #64748b; font-size: 11px; margin-bottom: 16px; }
    .summary { display: flex; gap: 20px; margin-bottom: 16px; }
    .summary-card { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 16px; flex: 1; }
    .summary-card .label { font-size: 10px; color: #64748b; }
    .summary-card .value { font-size: 16px; font-weight: bold; margin-top: 2px; }
    .green { color: #16a34a; } .red { color: #dc2626; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    thead tr { background: #f8fafc; }
    th { padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; color: #64748b; border-bottom: 2px solid #e2e8f0; }
    td { padding: 7px 10px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
    tr:hover { background: #f8fafc; }
    .badge-paid { background: #dcfce7; color: #16a34a; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: bold; }
    .badge-unpaid { background: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: bold; }
    .footer { margin-top: 20px; font-size: 10px; color: #94a3b8; text-align: right; }
    @media print {
        body { padding: 10px; }
        .no-print { display: none; }
    }
</style>
</head>
<body>

<div class="no-print" style="margin-bottom:16px;">
    <button onclick="window.print()" style="background:#2563eb;color:white;padding:8px 20px;border:none;border-radius:8px;font-weight:bold;cursor:pointer;margin-right:8px;">
        🖨️ Cetak / Simpan PDF
    </button>
    <button onclick="window.close()" style="background:#f1f5f9;color:#475569;padding:8px 20px;border:1px solid #e2e8f0;border-radius:8px;font-weight:bold;cursor:pointer;">
        Tutup
    </button>
</div>

<h1>Laporan Keuangan</h1>
<div class="subtitle">
    Pesantren Thawalib Parabek &mdash; Dicetak: <?= date('d F Y, H:i') ?>
    <?php if ($dateFrom || $dateTo): ?>
        | Periode: <?= $dateFrom ?: '...' ?> s/d <?= $dateTo ?: '...' ?>
    <?php endif; ?>
    <?php if ($statusFilter): ?>
        | Status: <?= $statusFilter ?>
    <?php endif; ?>
</div>

<div class="summary">
    <div class="summary-card">
        <div class="label">Total Pemasukan (Lunas)</div>
        <div class="value green">Rp <?= number_format($totalIncome, 0, ',', '.') ?></div>
    </div>
    <div class="summary-card">
        <div class="label">Piutang / Belum Lunas</div>
        <div class="value red">Rp <?= number_format($totalUnpaid, 0, ',', '.') ?></div>
    </div>
    <div class="summary-card">
        <div class="label">Total Transaksi</div>
        <div class="value"><?= count($rows) ?> transaksi</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Siswa</th>
            <th>NIS</th>
            <th>Keterangan</th>
            <th style="text-align:right">Nominal</th>
            <th style="text-align:center">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($rows)): ?>
            <tr><td colspan="7" style="text-align:center;padding:20px;color:#94a3b8;">Tidak ada data.</td></tr>
        <?php endif; ?>
        <?php foreach ($rows as $i => $r): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= date('d/m/Y', strtotime($r['created_at'])) ?></td>
            <td style="font-weight:bold"><?= htmlspecialchars($r['full_name']) ?></td>
            <td style="font-family:monospace"><?= $r['nis'] ?></td>
            <td><?= htmlspecialchars($r['title'] ?? 'Tagihan') ?></td>
            <td style="text-align:right;font-family:monospace">Rp <?= number_format($r['amount'], 0, ',', '.') ?></td>
            <td style="text-align:center">
                <span class="<?= $r['status'] == 'PAID' ? 'badge-paid' : 'badge-unpaid' ?>">
                    <?= $r['status'] == 'PAID' ? 'LUNAS' : 'BELUM BAYAR' ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="footer">
    Total <?= count($rows) ?> transaksi &mdash; Laporan dibuat otomatis oleh SIAKAD PRO
</div>

<script>
    // Auto print jika dibuka dari tombol PDF
    if (window.location.search.includes('format=pdf')) {
        window.onload = function() { window.print(); }
    }
</script>
</body>
</html>
