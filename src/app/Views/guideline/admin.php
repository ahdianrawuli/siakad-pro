<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
    <div class="mb-6 bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-book-open text-2xl"></i></div>
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800">Panduan Penggunaan SIAKAD PRO</h3>
            <p class="text-slate-500 text-sm mt-0.5">Panduan lengkap untuk Admin, Guru, dan Staff — baca sebelum menggunakan sistem</p>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sticky top-6">
                <p class="text-xs font-bold text-slate-400 uppercase mb-3 tracking-wider">Daftar Isi</p>
                <nav class="space-y-0.5 text-sm">
                    <?php foreach ([
                        'mulai'       => ['fa-play-circle',       'Memulai Sistem'],
                        'dashboard'   => ['fa-gauge-high',        'Dashboard'],
                        'masterdata'  => ['fa-database',          'Master Data'],
                        'siswa'       => ['fa-users',             'Manajemen Siswa'],
                        'akademik'    => ['fa-book-open',         'Akademik & Nilai'],
                        'keuangan'    => ['fa-dollar-sign',       'Keuangan'],
                        'kepesantren' => ['fa-moon',              'Kepesantrenan'],
                        'ppdb'        => ['fa-user-plus',         'PPDB Online'],
                        'kepegawaian' => ['fa-briefcase',         'Kepegawaian'],
                        'laporan'     => ['fa-print',             'Laporan & Rapor'],
                        'pengaturan'  => ['fa-gear',              'Pengaturan Sistem'],
                        'tips'        => ['fa-lightbulb',         'Tips & FAQ'],
                    ] as $id => [$icon, $label]): ?>
                    <a href="#<?= $id ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition text-xs">
                        <i class="fa-solid <?= $icon ?> w-4 text-center text-slate-400"></i> <?= $label ?>
                    </a>
                    <?php endforeach; ?>
                </nav>
            </div>
        </div>
        <div class="lg:col-span-3 space-y-5">

            <?php require __DIR__ . '/partials/admin_content.php'; ?>

        </div>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
