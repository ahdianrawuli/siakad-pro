<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-3xl font-medium text-gray-700">Rapor Ekstrakurikuler</h3>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-sm">
            <i class="fa fa-plus"></i> Tambah Ekstra
        </button>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Nama Kegiatan</th>
                        <th class="px-6 py-3 text-left">Pembina</th>
                        <th class="px-6 py-3 text-center">Anggota</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($extracurriculars)): ?>
                        <tr><td colspan="4" class="p-6 text-center text-gray-400">Belum ada data ekstrakurikuler.</td></tr>
                    <?php endif; ?>

                    <?php foreach($extracurriculars as $e): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-bold text-gray-800"><?= $e['name'] ?></td>
                        <td class="px-6 py-3 text-gray-600"><?= $e['coach_name'] ?? '-' ?></td>
                        <td class="px-6 py-3 text-center">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded font-bold text-xs">
                                <?= $e['total_members'] ?> Siswa
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <a href="/extracurricular/members?id=<?= $e['id'] ?>" class="text-white bg-green-500 hover:bg-green-600 px-3 py-1 rounded text-xs mr-2">
                                <i class="fa fa-list"></i> Anggota & Nilai
                            </a>
                            <a href="/extracurricular/delete?id=<?= $e['id'] ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Hapus data ini?')"><i class="fa fa-trash"></i></a>
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
        <h3 class="text-xl font-bold mb-4">Tambah Ekstrakurikuler</h3>
        <form action="/extracurricular/store" method="POST">
            <?= \App\Core\Csrf::input() ?>
            <div class="mb-3">
                <label class="block text-xs font-bold mb-1">Nama Kegiatan</label>
                <input type="text" name="name" class="w-full p-2 border rounded" required placeholder="Contoh: Pramuka, Futsal">
            </div>
            <div class="mb-3">
                <label class="block text-xs font-bold mb-1">Pembina</label>
                <select name="coach_id" class="w-full p-2 border rounded">
                    <option value="">-- Pilih Guru --</option>
                    <?php foreach($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold mb-1">Keterangan</label>
                <textarea name="description" class="w-full p-2 border rounded"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
