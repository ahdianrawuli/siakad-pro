<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h3 class="text-3xl font-medium text-gray-700">Master Jabatan</h3>
            <p class="text-gray-500 text-sm">Kelola posisi struktural dan fungsional sekolah.</p>
        </div>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow-sm h-fit border border-gray-200">
            <h4 class="font-bold mb-4 text-gray-700 border-b pb-2">Tambah Jabatan</h4>
            <form action="/staff/positions/store" method="POST">
                <?= \App\Core\Csrf::input() ?>
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Jabatan</label>
                    <input type="text" name="name" class="w-full p-2 border rounded" required placeholder="Contoh: Kepala Tata Usaha">
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Kode</label>
                    <input type="text" name="code" class="w-full p-2 border rounded" placeholder="KTU">
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tipe</label>
                    <select name="type" class="w-full p-2 border rounded">
                        <option value="TEKNIS">Teknis / Staff</option>
                        <option value="STRUKTURAL">Struktural</option>
                        <option value="FUNGSIONAL">Fungsional</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700">Simpan</button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
             <div class="p-4 bg-gray-50 border-b flex justify-between">
                <form class="flex gap-2 w-full">
                    <input type="text" name="search" value="<?= $search ?>" placeholder="Cari jabatan..." class="p-2 border rounded text-sm w-full">
                    <button class="bg-gray-800 text-white px-4 rounded text-sm">Cari</button>
                </form>
             </div>

             <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Kode</th>
                            <th class="px-4 py-3 text-left">Nama Jabatan</th>
                            <th class="px-4 py-3 text-left">Tipe</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($positions as $p): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono font-bold text-blue-600"><?= $p['code'] ?></td>
                            <td class="px-4 py-3 font-bold"><?= $p['name'] ?></td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2 py-1 rounded bg-gray-200"><?= $p['type'] ?></span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="/staff/positions/delete?id=<?= $p['id'] ?>" class="text-red-500" onclick="return confirm('Hapus?')"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
             </div>
             </div>
    </div>
</main>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>

