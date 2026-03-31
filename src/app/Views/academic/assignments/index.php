<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-3xl font-medium text-gray-700">SK Pembagian Tugas</h3>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-sm">
            <i class="fa fa-plus"></i> Tambah Penugasan
        </button>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
            <form class="flex gap-2">
                <input type="text" name="search" value="<?= $search ?>" placeholder="Cari Guru / Mapel..." class="p-2 border rounded text-sm w-64">
                <button class="bg-gray-800 text-white px-4 py-2 rounded text-sm">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Tahun Ajaran</th>
                        <th class="px-6 py-3 text-left">Guru</th>
                        <th class="px-6 py-3 text-left">Mata Pelajaran</th>
                        <th class="px-6 py-3 text-left">Kelas</th>
                        <th class="px-6 py-3 text-left">Nomor SK</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($assignments)): ?>
                        <tr><td colspan="6" class="p-4 text-center text-gray-400">Belum ada data penugasan.</td></tr>
                    <?php endif; ?>
                    
                    <?php foreach($assignments as $row): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3"><?= $row['year_name'] ?></td>
                        <td class="px-6 py-3 font-bold text-gray-700"><?= $row['teacher_name'] ?></td>
                        <td class="px-6 py-3"><?= $row['subject_name'] ?></td>
                        <td class="px-6 py-3">
                            <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs font-bold"><?= $row['class_name'] ?></span>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-500"><?= $row['sk_number'] ?></td>
                        <td class="px-6 py-3 text-center">
                            <a href="/academic/assignments/delete?id=<?= $row['id'] ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Hapus penugasan ini?')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if($totalPages > 1): ?>
        <div class="p-4 bg-gray-50 border-t flex justify-between text-xs">
            <span>Hal <?= $currentPage ?> dari <?= $totalPages ?></span>
            <div class="flex gap-1">
                 <?php if($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage-1 ?>&search=<?= $search ?>" class="px-3 py-1 bg-white border rounded">Prev</a>
                 <?php endif; ?>
                 <?php if($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage+1 ?>&search=<?= $search ?>" class="px-3 py-1 bg-white border rounded">Next</a>
                 <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-lg shadow-xl">
        <h3 class="text-xl font-bold mb-4">Buat Penugasan (SK)</h3>
        <form action="/academic/assignments/store" method="POST">
            <?= \App\Core\Csrf::input() ?>
            
            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-xs font-bold mb-1">Tahun Ajaran</label>
                    <select name="academic_year_id" class="w-full p-2 border rounded bg-gray-50">
                        <?php foreach($years as $y): ?>
                            <option value="<?= $y['id'] ?>"><?= $y['name'] ?> - <?= $y['semester'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Nomor SK</label>
                    <input type="text" name="sk_number" class="w-full p-2 border rounded" placeholder="Contoh: SK/2025/001">
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-xs font-bold mb-1">Pilih Guru</label>
                <select name="teacher_id" class="w-full p-2 border rounded" required>
                    <option value="">-- Pilih Guru --</option>
                    <?php foreach($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold mb-1">Mata Pelajaran</label>
                    <select name="subject_id" class="w-full p-2 border rounded" required>
                        <option value="">-- Pilih Mapel --</option>
                        <?php foreach($subjects as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Kelas Tujuan</label>
                    <select name="classroom_id" class="w-full p-2 border rounded" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach($classrooms as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Penugasan</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>

