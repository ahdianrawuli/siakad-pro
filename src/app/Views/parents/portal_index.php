<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <div class="mb-6">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Portal Orang Tua</h1>
        <p class="text-sm text-gray-500">Pantau perkembangan putra/putri Anda.</p>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <?php if (empty($students)): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-400">
        <i class="fa-solid fa-user-slash text-4xl mb-3"></i>
        <p class="font-medium">Akun Anda belum terhubung ke data siswa.</p>
        <p class="text-sm mt-1">Hubungi admin untuk menghubungkan nomor HP Anda dengan data siswa.</p>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($students as $s): ?>
        <a href="/portal/orangtua/anak?id=<?= $s['id'] ?>"
           class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl font-bold shrink-0">
                    <?= strtoupper(substr($s['full_name'], 0, 1)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-800 truncate group-hover:text-blue-700"><?= htmlspecialchars($s['full_name']) ?></p>
                    <p class="text-xs text-gray-500">NIS: <?= htmlspecialchars($s['nis']) ?></p>
                    <p class="text-xs text-gray-500">Kelas: <?= htmlspecialchars($s['class_name'] ?? '-') ?></p>
                </div>
                <i class="fa-solid fa-chevron-right text-gray-300 group-hover:text-blue-500 transition"></i>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
