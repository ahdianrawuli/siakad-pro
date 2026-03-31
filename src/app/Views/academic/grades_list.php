<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6">
        <h3 class="text-3xl font-medium text-gray-700">Input Nilai Siswa</h3>
        <p class="text-gray-500 text-sm">Pilih mata pelajaran dan kelas untuk mulai mengisi nilai.</p>
    </div>

    <div class="bg-white p-4 rounded shadow-sm border border-gray-200 mb-6">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[300px] relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Cari Mapel atau Kelas..." 
                       class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-black transition">
                Cari
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if(empty($schedules)): ?>
            <div class="col-span-full text-center py-12 text-gray-400 italic bg-white rounded shadow-sm">
                Tidak ada jadwal pelajaran yang ditemukan.
            </div>
        <?php endif; ?>

        <?php foreach ($schedules as $s): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition overflow-hidden flex flex-col">
            <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-green-600 to-emerald-500 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-lg leading-tight mb-1"><?= $s['subject_name'] ?></h4>
                        <span class="text-xs bg-white/20 px-2 py-1 rounded font-medium"><?= $s['class_name'] ?></span>
                    </div>
                    <div class="bg-white/20 p-2 rounded-lg">
                        <i class="fa-solid fa-star text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="p-5 flex-1">
                <div class="space-y-2 text-sm text-gray-600 mb-4">
                    <div class="flex items-center">
                        <i class="fa-solid fa-user-tie w-6 text-center text-gray-400"></i>
                        <span><?= $s['teacher_name'] ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fa-solid fa-users w-6 text-center text-gray-400"></i>
                        <span><?= $s['student_count'] ?> Siswa</span>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-100">
                <a href="/academic/grades/manage?schedule_id=<?= $s['id'] ?>" class="block w-full bg-green-600 text-white font-bold text-center py-2 rounded-lg hover:bg-green-700 shadow-sm transition">
                    <i class="fa-solid fa-pen-to-square mr-2"></i> Input Nilai
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if($totalPages > 1): ?>
    <div class="mt-6 flex justify-center gap-2">
        <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= $search ?>" 
               class="px-4 py-2 border rounded text-sm font-bold transition <?= $i == $currentPage ? 'bg-green-600 text-white border-green-600' : 'bg-white hover:bg-gray-100 text-gray-600' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
