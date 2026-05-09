<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">

<?php \App\Core\Session::flash(); ?>

<?php if (empty($students)): ?>
<!-- Tidak ada anak terhubung -->
<div class="relative bg-gradient-to-br from-green-700 via-green-600 to-emerald-500 rounded-2xl overflow-hidden mb-6 shadow-lg shadow-green-200 p-8 text-center">
    <div class="absolute inset-0 opacity-10">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="g" width="60" height="60" patternUnits="userSpaceOnUse"><circle cx="30" cy="30" r="20" fill="none" stroke="white" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(#g)"/></svg>
    </div>
    <div class="relative z-10">
        <p class="text-green-200 text-sm mb-1">Selamat datang di</p>
        <h1 class="text-2xl font-extrabold text-white mb-2">Portal Orang Tua</h1>
        <p class="text-green-100 text-sm">Akun Anda belum terhubung ke data santri. Hubungi admin pesantren.</p>
    </div>
</div>

<?php else: ?>

<?php foreach ($students as $s):
    $st = $stats[$s['id']] ?? ['unpaid'=>0,'absent'=>0];
?>

<!-- Hero per anak -->
<div class="relative bg-gradient-to-br from-green-700 via-green-600 to-emerald-500 rounded-2xl overflow-hidden mb-5 shadow-lg shadow-green-200">
    <div class="absolute inset-0 opacity-10">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="g<?= $s['id'] ?>" width="60" height="60" patternUnits="userSpaceOnUse"><circle cx="30" cy="30" r="20" fill="none" stroke="white" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(#g<?= $s['id'] ?>)"/></svg>
    </div>
    <div class="relative z-10 p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-white text-2xl font-extrabold shrink-0">
                <?= strtoupper(substr($s['full_name'], 0, 1)) ?>
            </div>
            <div>
                <p class="text-green-200 text-xs font-medium mb-0.5">Putra/Putri Anda</p>
                <h1 class="text-xl md:text-2xl font-extrabold text-white"><?= htmlspecialchars($s['full_name']) ?></h1>
                <p class="text-green-100 text-sm mt-0.5">
                    <i class="fa-solid fa-graduation-cap mr-1"></i> Kelas <?= htmlspecialchars($s['class_name'] ?? '-') ?>
                    &nbsp;·&nbsp;
                    <i class="fa-solid fa-id-card mr-1"></i> NIS: <?= htmlspecialchars($s['nis']) ?>
                </p>
            </div>
        </div>
        <span class="inline-flex items-center gap-2 bg-white/20 border border-white/30 text-white px-4 py-2 rounded-xl text-xs font-bold shrink-0">
            <span class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></span> AKTIF
        </span>
    </div>
</div>

<!-- Alert tagihan & absensi -->
<?php if ($st['unpaid'] > 0 || $st['absent'] > 0): ?>
<div class="grid grid-cols-1 <?= ($st['unpaid'] > 0 && $st['absent'] > 0) ? 'md:grid-cols-2' : '' ?> gap-3 mb-5">
    <?php if ($st['unpaid'] > 0): ?>
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-red-100 text-red-600 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <p class="text-sm text-red-800 font-medium"><strong><?= $st['unpaid'] ?></strong> tagihan belum dibayar</p>
        </div>
        <a href="/portal/orangtua/pembayaran?student_id=<?= $s['id'] ?>" class="shrink-0 bg-red-600 text-white px-3 py-1.5 rounded-xl text-xs font-bold hover:bg-red-700 transition">Lihat</a>
    </div>
    <?php endif; ?>
    <?php if ($st['absent'] > 0): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
            <p class="text-sm text-amber-800 font-medium"><strong><?= $st['absent'] ?></strong> kali alfa bulan ini</p>
        </div>
        <a href="/portal/orangtua/absensi?student_id=<?= $s['id'] ?>" class="shrink-0 bg-amber-500 text-white px-3 py-1.5 rounded-xl text-xs font-bold hover:bg-amber-600 transition">Lihat</a>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Menu Utama -->
<h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Menu Utama</h2>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
    <?php $mainMenus = [
        ['/portal/orangtua/absensi',    'fa-clipboard-check',      'green',  'Absensi',   'Rekap Kehadiran'],
        ['/portal/orangtua/nilai',      'fa-star',                 'yellow', 'Nilai',     'Akademik'],
        ['/portal/orangtua/pembayaran', 'fa-file-invoice-dollar',  'orange', 'Tagihan',   'Keuangan SPP'],
        ['/portal/orangtua/jadwal',     'fa-calendar-days',        'blue',   'Jadwal',    'Pelajaran'],
    ]; foreach ($mainMenus as [$href, $icon, $color, $title, $desc]): ?>
    <a href="<?= $href ?>?student_id=<?= $s['id'] ?>" class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:shadow-md hover:border-<?= $color ?>-300 transition text-center">
        <div class="w-12 h-12 bg-<?= $color ?>-100 text-<?= $color ?>-600 rounded-xl flex items-center justify-center text-xl mx-auto mb-2 group-hover:scale-110 transition">
            <i class="fa-solid <?= $icon ?>"></i>
        </div>
        <p class="font-bold text-slate-800 text-sm"><?= $title ?></p>
        <p class="text-xs text-slate-400 mt-0.5"><?= $desc ?></p>
    </a>
    <?php endforeach; ?>
</div>

<!-- Menu Lainnya -->
<h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Lainnya</h2>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
    <?php $otherMenus = [
        ['/portal/orangtua/kedisiplinan', 'fa-triangle-exclamation', 'red',    'Kedisiplinan', 'Pelanggaran'],
        ['/portal/orangtua/asrama',       'fa-house',                'teal',   'Asrama',       'Info Kamar'],
        ['/portal/orangtua/kesehatan',    'fa-heart-pulse',          'pink',   'Kesehatan',    'Riwayat Sehat'],
        ['/portal/orangtua/pengumuman',   'fa-bullhorn',             'indigo', 'Pengumuman',   'Info Pesantren'],
    ]; foreach ($otherMenus as [$href, $icon, $color, $title, $desc]): ?>
    <a href="<?= $href ?>?student_id=<?= $s['id'] ?>" class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:shadow-md transition text-center">
        <div class="w-10 h-10 bg-<?= $color ?>-100 text-<?= $color ?>-600 rounded-xl flex items-center justify-center text-lg mx-auto mb-2 group-hover:scale-110 transition">
            <i class="fa-solid <?= $icon ?>"></i>
        </div>
        <p class="font-bold text-slate-800 text-xs"><?= $title ?></p>
        <p class="text-[10px] text-slate-400 mt-0.5 hidden md:block"><?= $desc ?></p>
    </a>
    <?php endforeach; ?>
</div>

<?php endforeach; ?>
<?php endif; ?>

</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
