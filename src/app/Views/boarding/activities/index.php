<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-3xl font-medium text-gray-700">Jadwal Kegiatan Asrama</h3>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-sm">
            <i class="fa fa-plus"></i> Tambah Kegiatan
        </button>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 bg-gray-50 border-b">
            <form class="flex gap-2">
                <select name="day" class="p-2 border rounded text-sm w-48" onchange="this.form.submit()">
                    <option value="">-- Semua Hari --</option>
                    <?php 
                    $days = ['SETIAP HARI', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'MINGGU'];
                    foreach($days as $d): 
                    ?>
                        <option value="<?= $d ?>" <?= $selectedDay == $d ? 'selected' : '' ?>><?= $d ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Hari</th>
                        <th class="px-6 py-3 text-left">Jam</th>
                        <th class="px-6 py-3 text-left">Nama Kegiatan</th>
                        <th class="px-6 py-3 text-left">Keterangan</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($activities)): ?>
                        <tr><td colspan="5" class="p-6 text-center text-gray-400">Belum ada jadwal kegiatan.</td></tr>
                    <?php endif; ?>

                    <?php foreach($activities as $act): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded text-xs font-bold <?= $act['day'] == 'SETIAP HARI' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100' ?>">
                                <?= $act['day'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-3 font-mono text-gray-700">
                            <?= substr($act['start_time'],0,5) ?> - <?= substr($act['end_time'],0,5) ?>
                        </td>
                        <td class="px-6 py-3 font-bold"><?= $act['name'] ?></td>
                        <td class="px-6 py-3 text-gray-500"><?= $act['description'] ?></td>
                        <td class="px-6 py-3 text-center">
                            <a href="/boarding/activities/delete?id=<?= $act['id'] ?>" class="text-red-500" onclick="return confirm('Hapus kegiatan ini?')"><i class="fa fa-trash"></i></a>
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
        <h3 class="text-xl font-bold mb-4">Tambah Kegiatan Baru</h3>
        <form action="/boarding/activities/store" method="POST">
            <?= \App\Core\Csrf::input() ?>
            
            <div class="mb-3">
                <label class="block text-xs font-bold mb-1">Nama Kegiatan</label>
                <input type="text" name="name" class="w-full p-2 border rounded" required placeholder="Contoh: Sholat Subuh Berjamaah">
            </div>

            <div class="mb-3">
                <label class="block text-xs font-bold mb-1">Hari</label>
                <select name="day" class="w-full p-2 border rounded">
                    <?php foreach($days as $d): ?>
                        <option value="<?= $d ?>"><?= $d ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-xs font-bold mb-1">Jam Mulai</label>
                    <input type="time" name="start_time" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Jam Selesai</label>
                    <input type="time" name="end_time" class="w-full p-2 border rounded" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold mb-1">Keterangan (Opsional)</label>
                <textarea name="description" class="w-full p-2 border rounded h-20"></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
