<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 md:p-6">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Executive Dashboard</h1>
            <p class="text-sm text-gray-600">Overview sistem manajemen sekolah.</p>
        </div>
        <div class="text-right hidden md:block">
            <span class="text-xs text-gray-500 font-bold uppercase block">Hari ini</span>
            <span class="text-lg font-bold text-blue-800"><?= date('l, d F Y') ?></span>
        </div>
    </div>
    
    <h3 class="font-bold text-sm text-gray-500 uppercase tracking-wider mb-3 flex items-center">
        <i class="fa-solid fa-user-plus mr-2"></i> Penerimaan Santri Baru (PPDB)
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6 mb-8">
        <div class="bg-white rounded-xl p-4 shadow-sm border-b-4 border-blue-500">
            <span class="text-gray-500 text-[10px] md:text-xs font-bold uppercase">Total Pendaftar</span>
            <div class="flex justify-between items-end mt-1">
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800"><?= $ppdb_summary['total'] ?></h2>
                <i class="fa-solid fa-users text-blue-200 text-2xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border-b-4 border-yellow-500">
            <span class="text-gray-500 text-[10px] md:text-xs font-bold uppercase">Belum Lunas</span>
            <div class="flex justify-between items-end mt-1">
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800"><?= $ppdb_summary['pending'] ?></h2>
                <i class="fa-solid fa-hourglass-half text-yellow-200 text-2xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border-b-4 border-green-500">
            <span class="text-gray-500 text-[10px] md:text-xs font-bold uppercase">Diterima / Lulus</span>
            <div class="flex justify-between items-end mt-1">
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800"><?= $ppdb_summary['active'] ?></h2>
                <i class="fa-solid fa-user-check text-green-200 text-2xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border-b-4 border-red-500">
            <span class="text-gray-500 text-[10px] md:text-xs font-bold uppercase">Gagal / Ditolak</span>
            <div class="flex justify-between items-end mt-1">
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800"><?= $ppdb_summary['failed'] ?></h2>
                <i class="fa-solid fa-ban text-red-200 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <div class="bg-white rounded-xl shadow-sm p-5 lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-700 flex items-center">
                    <i class="fa-solid fa-graduation-cap mr-2 text-blue-600"></i> Siswa Aktif
                </h3>
                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded-lg">
                    Total: <?= $student_stats['total'] ?> Siswa
                </span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                    <span class="block text-gray-400 text-xs font-bold uppercase">Jenjang MTS</span>
                    <span class="block text-xl font-extrabold text-green-600 mt-1"><?= $student_stats['mts'] ?></span>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                    <span class="block text-gray-400 text-xs font-bold uppercase">Jenjang MA</span>
                    <span class="block text-xl font-extrabold text-blue-600 mt-1"><?= $student_stats['ma'] ?></span>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                    <span class="block text-gray-400 text-xs font-bold uppercase">Putra</span>
                    <span class="block text-xl font-extrabold text-gray-700 mt-1"><?= $student_stats['putra'] ?></span>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                    <span class="block text-gray-400 text-xs font-bold uppercase">Putri</span>
                    <span class="block text-xl font-extrabold text-gray-700 mt-1"><?= $student_stats['putri'] ?></span>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl shadow-lg p-5 text-white">
            <h3 class="font-bold text-gray-200 mb-4 flex items-center">
                <i class="fa-solid fa-wallet mr-2 text-yellow-400"></i> Keuangan Hari Ini
            </h3>
            
            <div class="mb-4">
                <span class="text-xs text-gray-400 uppercase font-bold">Pemasukan (Cash/Transfer)</span>
                <div class="text-3xl font-mono font-bold text-green-400 mt-1">
                    Rp <?= number_format($finance_stats['income_today'], 0, ',', '.') ?>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-700">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-300">Tagihan Belum Lunas</span>
                    <span class="bg-red-500/20 text-red-300 text-xs font-bold px-2 py-1 rounded">
                        <?= $finance_stats['unpaid_count'] ?> Invoice
                    </span>
                </div>
            </div>
            
            <div class="mt-4">
                 <a href="/finance" class="block w-full text-center bg-white/10 hover:bg-white/20 py-2 rounded text-xs font-bold transition">
                    Lihat Laporan Keuangan
                </a>
            </div>
        </div>
    </div>

    <h3 class="font-bold text-sm text-gray-500 uppercase tracking-wider mb-3">
        Progress Kuota PPDB
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <?php foreach($tracks_data as $track): ?>
            <?php 
                $percent = ($track['quota'] > 0) ? ($track['registered_count'] / $track['quota']) * 100 : 0;
                $percent = min(100, round($percent));
                $color = match($track['level']) { 'MTS' => 'green', 'MA' => 'blue', default => 'purple' };
            ?>
            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-<?= $color ?>-100 text-<?= $color ?>-800">
                            <?= $track['level'] ?>
                        </span>
                        <h4 class="font-bold text-gray-800 mt-1"><?= $track['name'] ?></h4>
                    </div>
                    <span class="text-2xl font-bold text-gray-700"><?= $track['registered_count'] ?></span>
                </div>
                
                <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3 mb-1">
                    <div class="bg-<?= $color ?>-500 h-1.5 rounded-full" style="width: <?= $percent ?>%"></div>
                </div>
                <div class="text-[10px] text-gray-400 flex justify-between">
                    <span>Terisi <?= $percent ?>%</span>
                    <span>Kuota: <?= $track['quota'] ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
