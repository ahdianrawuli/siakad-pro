<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-3xl font-medium text-gray-700">Manajemen Kurikulum</h3>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-sm">
            <i class="fa fa-plus"></i> Tambah Kurikulum
        </button>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
            <form class="flex gap-2">
                <input type="text" name="search" value="<?= $search ?>" placeholder="Cari kurikulum..." class="p-2 border rounded text-sm w-64">
                <button class="bg-gray-800 text-white px-4 py-2 rounded text-sm">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Kode</th>
                        <th class="px-6 py-3 text-left">Nama Kurikulum</th>
                        <th class="px-6 py-3 text-left">Keterangan</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($curriculums)): ?>
                        <tr><td colspan="5" class="p-4 text-center text-gray-400">Belum ada data.</td></tr>
                    <?php endif; ?>
                    
                    <?php foreach($curriculums as $row): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-mono font-bold"><?= $row['code'] ?></td>
                        <td class="px-6 py-3 font-medium"><?= $row['name'] ?></td>
                        <td class="px-6 py-3 text-gray-500"><?= $row['description'] ?></td>
                        <td class="px-6 py-3 text-center">
                            <span class="px-2 py-1 rounded text-xs font-bold <?= $row['is_active'] ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' ?>">
                                <?= $row['is_active'] ? 'AKTIF' : 'NON-AKTIF' ?>
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <a href="/academic/curriculum/delete?id=<?= $row['id'] ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Hapus kurikulum?')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl">
        <h3 class="text-xl font-bold mb-4">Tambah Kurikulum</h3>
        <form action="/academic/curriculum/store" method="POST">
            <?= \App\Core\Csrf::input() ?>
            <div class="mb-3">
                <label class="block text-xs font-bold mb-1">Nama Kurikulum</label>
                <input type="text" name="name" class="w-full p-2 border rounded" required placeholder="Misal: Kurikulum Merdeka">
            </div>
            <div class="mb-3">
                <label class="block text-xs font-bold mb-1">Kode</label>
                <input type="text" name="code" class="w-full p-2 border rounded" required placeholder="KM-2024">
            </div>
            <div class="mb-3">
                <label class="block text-xs font-bold mb-1">Keterangan</label>
                <textarea name="description" class="w-full p-2 border rounded h-20"></textarea>
            </div>
            <div class="mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4">
                    <span class="text-sm">Set sebagai Aktif</span>
                </label>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>

