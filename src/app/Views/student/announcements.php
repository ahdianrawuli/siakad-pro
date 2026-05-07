<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-6">Pengumuman</h1>

    <?php if (empty($announcements)): ?>
        <div class="bg-white rounded-xl border border-gray-100 p-12 text-center text-gray-400">
            <i class="fa-solid fa-bullhorn text-4xl mb-3"></i>
            <p>Belum ada pengumuman.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
        <?php foreach ($announcements as $a): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-gray-800 text-base"><?= htmlspecialchars($a['title']) ?></h3>
                        <p class="text-xs text-gray-400 mt-1"><?= date('d M Y', strtotime($a['created_at'])) ?></p>
                    </div>
                    <?php if (!empty($a['category'])): ?>
                    <span class="shrink-0 bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded-full"><?= htmlspecialchars($a['category']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="mt-3 text-sm text-gray-600 leading-relaxed"><?= nl2br(htmlspecialchars($a['content'] ?? $a['body'] ?? '')) ?></div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
