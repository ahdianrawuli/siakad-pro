<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Pengumuman';
$pageSubtitle = 'Informasi terbaru dari pesantren';
$pageBadge    = 'Total: ' . count($announcements);
$pageBadgeIcon = 'fa-bullhorn';
$infoItems    = [
    'Halaman ini menampilkan pengumuman resmi dari pesantren.',
    'Pengumuman diurutkan dari yang terbaru.',
    'Pastikan Anda membaca setiap pengumuman agar tidak ketinggalan informasi penting.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php if (empty($announcements)): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
            <i class="fa-solid fa-bullhorn text-4xl mb-3 block opacity-30"></i>
            <p>Belum ada pengumuman.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
        <?php foreach ($announcements as $a): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-slate-800 text-base"><?= htmlspecialchars($a['title']) ?></h3>
                        <p class="text-xs text-slate-400 mt-1"><?= date('d M Y', strtotime($a['created_at'])) ?></p>
                    </div>
                    <?php if (!empty($a['category'])): ?>
                    <span class="shrink-0 bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full"><?= htmlspecialchars($a['category']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="mt-3 text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3"><?= nl2br(htmlspecialchars($a['content'] ?? $a['body'] ?? '')) ?></div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
