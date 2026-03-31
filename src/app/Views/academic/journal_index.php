<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6">
        <h3 class="text-3xl font-medium text-gray-700">Jurnal Guru</h3>
        <p class="text-gray-500 text-sm">Pilih mata pelajaran untuk mengisi jurnal mengajar.</p>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($schedules as $s): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 overflow-hidden group">
            <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-blue-600 to-blue-500 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-lg leading-tight mb-1"><?= $s['subject_name'] ?></h4>
                        <span class="text-xs bg-white/20 px-2 py-1 rounded font-medium"><?= $s['class_name'] ?></span>
                    </div>
                    <div class="bg-white/20 p-2 rounded-lg">
                        <i class="fa-solid fa-book-open text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="p-5">
                <div class="flex items-center text-sm text-gray-600 mb-2">
                    <i class="fa-regular fa-clock w-5 text-center mr-2 text-gray-400"></i>
                    <?= $s['day'] ?>, <?= substr($s['start_time'],0,5) ?> - <?= substr($s['end_time'],0,5) ?>
                </div>
                <div class="flex items-center text-sm text-gray-600 mb-4">
                    <i class="fa-solid fa-chalkboard-user w-5 text-center mr-2 text-gray-400"></i>
                    <?= $s['total_entries'] ?> Pertemuan Tercatat
                </div>
                
                <a href="/academic/journals/history?schedule_id=<?= $s['id'] ?>" class="block w-full bg-gray-50 text-blue-600 font-bold text-center py-2.5 rounded-lg border border-blue-100 hover:bg-blue-50 hover:border-blue-300 transition">
                    Buka Jurnal
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
