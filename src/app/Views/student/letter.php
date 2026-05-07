<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Surat Keterangan</h1>
    <p class="text-sm text-gray-500 mb-6">Pilih jenis surat yang ingin dicetak</p>

    <?php \App\Core\Session::flash(); ?>

    <?php if (empty($templates)): ?>
        <div class="bg-white rounded-xl border border-gray-100 p-12 text-center text-gray-400">
            <i class="fa-solid fa-file-lines text-4xl mb-3"></i>
            <p>Belum ada template surat tersedia.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($templates as $t): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col">
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-3">
                    <i class="fa-solid fa-file-lines"></i>
                </div>
                <h3 class="font-bold text-gray-800 flex-1"><?= htmlspecialchars($t['name']) ?></h3>
                <a href="/student/letter/print?code=<?= urlencode($t['code']) ?>"
                   target="_blank"
                   class="mt-4 w-full text-center bg-blue-600 text-white text-sm font-bold py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fa-solid fa-print mr-1"></i> Cetak
                </a>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
