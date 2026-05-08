<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Pengumuman';
$pageSubtitle = 'Informasi terbaru dari pesantren';
$pageBadge    = 'Total: ' . count($announcements ?? []);
$pageBadgeIcon = 'fa-bullhorn';
$infoItems    = [
    'Halaman ini menampilkan pengumuman resmi dari pesantren.',
    'Pengumuman diurutkan dari yang terbaru.',
    'Pastikan Anda membaca setiap pengumuman agar tidak ketinggalan informasi penting.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php if (empty($announcements)): ?>
    <div class="bg-white rounded-2xl p-10 text-center text-slate-400 border border-slate-200">
        <i class="fa-solid fa-bell-slash text-3xl mb-3 block opacity-30"></i>
        Belum ada pengumuman.
    </div>
    <?php else: ?>
    <div class="space-y-3">
        <?php foreach ($announcements as $a): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-start justify-between mb-2">
                <h3 class="font-semibold text-slate-800"><?= htmlspecialchars($a['title'] ?? 'Pengumuman') ?></h3>
                <span class="text-xs text-slate-400 shrink-0 ml-4"><?= date('d M Y', strtotime($a['created_at'])) ?></span>
            </div>
            <p class="text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3 mt-2"><?= nl2br(htmlspecialchars($a['content'] ?? '')) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
