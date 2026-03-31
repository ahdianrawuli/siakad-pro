<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-3xl font-medium text-gray-700">Data Wali Asrama</h3>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-sm">
            <i class="fa fa-user-plus"></i> Tugaskan Wali
        </button>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Asrama</th>
                        <th class="px-6 py-3 text-left">Nama Wali (Musyrif)</th>
                        <th class="px-6 py-3 text-left">Jabatan</th>
                        <th class="px-6 py-3 text-left">Tanggal Ditugaskan</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($supervisors)): ?>
                        <tr><td colspan="6" class="p-6 text-center text-gray-400">Belum ada wali asrama yang ditugaskan.</td></tr>
                    <?php endif; ?>

                    <?php foreach($supervisors as $s): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-bold text-gray-800"><?= $s['dorm_name'] ?></td>
                        <td class="px-6 py-3 text-blue-600 font-medium"><?= $s['user_name'] ?></td>
                        <td class="px-6 py-3 text-xs uppercase"><?= $s['role_name'] ?></td>
                        <td class="px-6 py-3 text-gray-600"><?= date('d F Y', strtotime($s['assigned_date'])) ?></td>
                        <td class="px-6 py-3 text-center">
                            <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-700 font-bold">AKTIF</span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <a href="/boarding/supervisors/delete?id=<?= $s['id'] ?>" class="text-red-500" onclick="return confirm('Hapus penugasan wali ini?')"><i class="fa fa-trash"></i></a>
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
        <h3 class="text-xl font-bold mb-4">Tugaskan Wali Asrama</h3>
        <form action="/boarding/supervisors/store" method="POST">
            <?= \App\Core\Csrf::input() ?>
            
            <div class="mb-4">
                <label class="block text-xs font-bold mb-1">Pilih Asrama</label>
                <select name="dorm_id" class="w-full p-2 border rounded" required>
                    <option value="">-- Pilih Gedung/Kamar --</option>
                    <?php foreach($dorms as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold mb-1">Pilih Guru / Staff</label>
                <select name="user_id" class="w-full p-2 border rounded" required>
                    <option value="">-- Cari Nama --</option>
                    <?php foreach($users as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= $u['name'] ?> (<?= strtoupper($u['slug']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
