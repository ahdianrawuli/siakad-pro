<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Surat Keterangan';
$pageSubtitle = 'Pilih jenis surat yang ingin dicetak';
$pageBadge    = 'Template Tersedia: ' . count($templates ?? []);
$pageBadgeIcon = 'fa-file-lines';
$infoItems    = [
    'Halaman ini menyediakan template surat keterangan resmi dari pesantren.',
    'Klik tombol "Cetak" untuk mencetak atau mengunduh surat.',
    'Surat yang dicetak sudah berisi data Anda secara otomatis.',
    'Hubungi admin jika template surat yang Anda butuhkan belum tersedia.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php \App\Core\Session::flash(); ?>

    <?php if (empty($templates)): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
            <i class="fa-solid fa-file-lines text-4xl mb-3 block opacity-30"></i>
            <p>Belum ada template surat tersedia.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($templates as $t): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 flex flex-col hover:shadow-md transition">
                <div class="w-12 h-12 bg-green-100 text-green-700 rounded-xl flex items-center justify-center mb-3">
                    <i class="fa-solid fa-file-lines text-xl"></i>
                </div>
                <h3 class="font-bold text-slate-800 flex-1"><?= htmlspecialchars($t['name']) ?></h3>
                <a href="/student/letter/print?code=<?= urlencode($t['code']) ?>"
                   target="_blank"
                   class="mt-4 w-full text-center bg-green-700 text-white text-sm font-bold py-2.5 rounded-xl hover:bg-green-800 transition">
                    <i class="fa-solid fa-print mr-1"></i> Cetak
                </a>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
