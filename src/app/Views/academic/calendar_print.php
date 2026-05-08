<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kalender Akademik - <?= $monthName ?> <?= $year ?></title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 11px; color: #1e293b; padding: 20px; }

.no-print { margin-bottom: 16px; }
.no-print button { padding: 8px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 12px; }
.btn-print { background: #059669; color: white; border: none; margin-right: 8px; }
.btn-close  { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

.header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #1e293b; padding-bottom: 10px; }
.header h1 { font-size: 16px; font-weight: bold; }
.header p  { font-size: 10px; color: #64748b; margin-top: 2px; }

/* Calendar grid */
.cal-grid { width: 100%; border-collapse: collapse; table-layout: fixed; }
.cal-grid th { background: #1e293b; color: white; text-align: center; padding: 6px 4px; font-size: 10px; border: 1px solid #334155; }
.cal-grid th.sun { background: #dc2626; }
.cal-grid th.sat { background: #2563eb; }
.cal-grid td { border: 1px solid #cbd5e1; vertical-align: top; padding: 4px; height: 80px; width: 14.28%; }
.cal-grid td.empty { background: #f8fafc; }
.cal-grid td.weekend { background: #fafafa; }
.day-num { font-weight: bold; font-size: 12px; margin-bottom: 3px; }
.day-num.sun { color: #dc2626; }
.day-num.sat { color: #2563eb; }
.event-tag { display: flex; align-items: center; gap: 3px; border-radius: 3px; padding: 1px 4px; margin-bottom: 2px; font-size: 9px; font-weight: bold; overflow: hidden; }
.event-tag .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.event-tag span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.tag-KEGIATAN { background: #dbeafe; color: #1d4ed8; }
.tag-LIBUR    { background: #dcfce7; color: #15803d; }
.tag-UJIAN    { background: #fee2e2; color: #b91c1c; }

/* Legend */
.legend { margin-top: 14px; display: flex; gap: 16px; align-items: center; }
.legend-item { display: flex; align-items: center; gap: 5px; font-size: 10px; }
.legend-dot { width: 10px; height: 10px; border-radius: 2px; }

/* Event list below calendar */
.event-list { margin-top: 16px; }
.event-list h2 { font-size: 12px; font-weight: bold; margin-bottom: 8px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
.event-list table { width: 100%; border-collapse: collapse; }
.event-list th { background: #f8fafc; text-align: left; padding: 5px 8px; font-size: 10px; color: #64748b; border-bottom: 1px solid #e2e8f0; }
.event-list td { padding: 5px 8px; font-size: 10px; border-bottom: 1px solid #f1f5f9; }
.badge { padding: 1px 7px; border-radius: 20px; font-size: 9px; font-weight: bold; }
.badge-KEGIATAN { background: #dbeafe; color: #1d4ed8; }
.badge-LIBUR    { background: #dcfce7; color: #15803d; }
.badge-UJIAN    { background: #fee2e2; color: #b91c1c; }

.footer { margin-top: 16px; font-size: 9px; color: #94a3b8; text-align: right; }

@media print {
    .no-print { display: none; }
    body { padding: 10px; }
}
</style>
</head>
<body>

<div class="no-print">
    <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
    <button class="btn-close" onclick="window.close()">Tutup</button>
</div>

<div class="header">
    <h1>Kalender Akademik — <?= $monthName ?> <?= $year ?></h1>
    <p>Pondok Pesantren Sumatera Thawalib Parabek &mdash; Dicetak: <?= date('d F Y, H:i') ?></p>
</div>

<?php
$days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$daysShort = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
$daysInMonth = (int)date('t', mktime(0,0,0,$month,1,$year));
$firstDow    = (int)date('w', mktime(0,0,0,$month,1,$year)); // 0=Sun

// Map events by day
$byDay = [];
foreach ($events as $ev) {
    $start = strtotime($ev['start_date']);
    $end   = strtotime($ev['end_date']);
    // Span all days in range that fall within this month
    for ($ts = $start; $ts <= $end; $ts += 86400) {
        $d = (int)date('j', $ts);
        $m = (int)date('n', $ts);
        $y = (int)date('Y', $ts);
        if ($m === $month && $y === $year) {
            $byDay[$d][] = $ev;
        }
    }
}
?>

<table class="cal-grid">
    <thead>
        <tr>
            <?php foreach ($daysShort as $di => $dn): ?>
            <th class="<?= $di===0?'sun':($di===6?'sat':'') ?>"><?= $dn ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
    <?php
    $cell = 0;
    $day  = 1;
    echo '<tr>';
    // Empty leading cells
    for ($i = 0; $i < $firstDow; $i++) {
        echo '<td class="empty"></td>';
        $cell++;
    }
    while ($day <= $daysInMonth) {
        if ($cell % 7 === 0 && $cell > 0) echo '</tr><tr>';
        $dow = ($firstDow + $day - 1) % 7;
        $isSun = $dow === 0;
        $isSat = $dow === 6;
        $cls = ($isSun || $isSat) ? 'weekend' : '';
        echo '<td class="' . $cls . '">';
        echo '<div class="day-num ' . ($isSun?'sun':($isSat?'sat':'')) . '">' . $day . '</div>';
        if (!empty($byDay[$day])) {
            foreach ($byDay[$day] as $ev) {
                $type  = htmlspecialchars($ev['type']);
                $color = htmlspecialchars($ev['color'] ?? '#3788d8');
                $title = htmlspecialchars($ev['title']);
                echo '<div class="event-tag tag-' . $type . '">'
                   . '<span class="dot" style="background:' . $color . '"></span>'
                   . '<span title="' . $title . '">' . $title . '</span>'
                   . '</div>';
            }
        }
        echo '</td>';
        $day++;
        $cell++;
    }
    // Trailing empty cells
    $remaining = 7 - ($cell % 7);
    if ($remaining < 7) {
        for ($i = 0; $i < $remaining; $i++) echo '<td class="empty"></td>';
    }
    echo '</tr>';
    ?>
    </tbody>
</table>

<div class="legend">
    <strong style="font-size:10px">Keterangan:</strong>
    <div class="legend-item"><div class="legend-dot" style="background:#dbeafe;border:1px solid #93c5fd"></div> Kegiatan</div>
    <div class="legend-item"><div class="legend-dot" style="background:#dcfce7;border:1px solid #86efac"></div> Hari Libur</div>
    <div class="legend-item"><div class="legend-dot" style="background:#fee2e2;border:1px solid #fca5a5"></div> Ujian</div>
    <div class="legend-item"><div class="legend-dot" style="background:#fafafa;border:1px solid #cbd5e1"></div> Akhir Pekan</div>
</div>

<?php if (!empty($events)): ?>
<div class="event-list">
    <h2>Daftar Event — <?= $monthName ?> <?= $year ?></h2>
    <table>
        <thead>
            <tr>
                <th style="width:30px">#</th>
                <th>Nama Kegiatan</th>
                <th style="width:90px">Mulai</th>
                <th style="width:90px">Selesai</th>
                <th style="width:70px">Tipe</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $i => $ev): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td>
                    <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:<?= htmlspecialchars($ev['color']??'#3788d8') ?>;margin-right:4px;vertical-align:middle"></span>
                    <?= htmlspecialchars($ev['title']) ?>
                </td>
                <td><?= date('d M Y', strtotime($ev['start_date'])) ?></td>
                <td><?= date('d M Y', strtotime($ev['end_date'])) ?></td>
                <td><span class="badge badge-<?= htmlspecialchars($ev['type']) ?>"><?= ['KEGIATAN'=>'Kegiatan','LIBUR'=>'Hari Libur','UJIAN'=>'Ujian'][$ev['type']] ?? $ev['type'] ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div class="footer">Kalender Akademik <?= $monthName ?> <?= $year ?> &mdash; Dibuat otomatis oleh SIAKAD PRO</div>

<script>
if (window.location.search.includes('print=1')) window.onload = () => window.print();
</script>
</body>
</html>
