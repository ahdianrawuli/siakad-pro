<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Portal Orang Tua';
$pageSubtitle = 'Pantau perkembangan putra/putri Anda';
$pageBadge    = 'Total Santri: ' . count($students ?? []);
$pageBadgeIcon = 'fa-users';
$infoItems    = [
    'Halaman ini menampilkan daftar santri yang terhubung dengan akun Anda.',
    'Klik menu di setiap kartu santri untuk melihat detail informasi.',
    'Hubungi admin jika akun Anda belum terhubung ke data santri.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php \App\Core\Session::flash(); ?>

    <?php if (empty($students)): ?>
    <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center text-slate-400">
        <i class="fa-solid fa-user-slash text-4xl mb-3 block opacity-30"></i>
        <p class="font-medium">Akun Anda belum terhubung ke data siswa.</p>
        <p class="text-sm mt-1">Hubungi admin untuk menghubungkan akun Anda dengan data siswa.</p>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($students as $s): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-teal-100 text-teal-700 rounded-full flex items-center justify-center text-xl font-bold shrink-0">
                    <?= strtoupper(substr($s['full_name'], 0, 1)) ?>
                </div>
                <div>
                    <p class="font-bold text-slate-800"><?= htmlspecialchars($s['full_name']) ?></p>
                    <p class="text-xs text-slate-500">NIS: <?= htmlspecialchars($s['nis']) ?> | Kelas: <?= htmlspecialchars($s['class_name'] ?? '-') ?></p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <a href="/portal/orangtua/absensi?student_id=<?= $s['id'] ?>" class="bg-blue-50 text-blue-700 rounded-xl p-2 hover:bg-blue-100 transition">
                    <i class="fa-solid fa-clipboard-check block text-lg mb-1"></i>Absensi
                </a>
                <a href="/portal/orangtua/nilai?student_id=<?= $s['id'] ?>" class="bg-yellow-50 text-yellow-700 rounded-xl p-2 hover:bg-yellow-100 transition">
                    <i class="fa-solid fa-chart-bar block text-lg mb-1"></i>Nilai
                </a>
                <a href="/portal/orangtua/pembayaran?student_id=<?= $s['id'] ?>" class="bg-orange-50 text-orange-700 rounded-xl p-2 hover:bg-orange-100 transition">
                    <i class="fa-solid fa-file-invoice-dollar block text-lg mb-1"></i>Tagihan
                </a>
                <a href="/portal/orangtua/kedisiplinan?student_id=<?= $s['id'] ?>" class="bg-red-50 text-red-700 rounded-xl p-2 hover:bg-red-100 transition">
                    <i class="fa-solid fa-triangle-exclamation block text-lg mb-1"></i>Disiplin
                </a>
                <a href="/portal/orangtua/asrama?student_id=<?= $s['id'] ?>" class="bg-green-50 text-green-700 rounded-xl p-2 hover:bg-green-100 transition">
                    <i class="fa-solid fa-house block text-lg mb-1"></i>Asrama
                </a>
                <a href="/portal/orangtua/kesehatan?student_id=<?= $s['id'] ?>" class="bg-pink-50 text-pink-700 rounded-xl p-2 hover:bg-pink-100 transition">
                    <i class="fa-solid fa-heart-pulse block text-lg mb-1"></i>Kesehatan
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
