<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-bullhorn text-teal-600 mr-2"></i>Pengumuman</h1>
    </div>

    <?php if (empty($announcements)): ?>
    <div class="bg-white rounded-xl p-10 text-center text-gray-400">
        <i class="fa-solid fa-bell-slash text-3xl mb-3 block"></i>
        Belum ada pengumuman.
    </div>
    <?php else: ?>
    <div class="space-y-3">
        <?php foreach ($announcements as $a): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-start justify-between mb-2">
                <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($a['title'] ?? 'Pengumuman') ?></h3>
                <span class="text-xs text-gray-400 shrink-0 ml-4"><?= date('d M Y', strtotime($a['created_at'])) ?></span>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed"><?= nl2br(htmlspecialchars($a['content'] ?? '')) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
