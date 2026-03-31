<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-3xl font-medium text-gray-700">Dispensasi KBM</h3>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-sm">
            <i class="fa fa-plus"></i> Buat Izin Baru
        </button>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 bg-gray-50 border-b">
            <form class="flex gap-2">
                <input type="text" name="search" value="<?= $search ?>" placeholder="Cari Santri / Jenis Izin..." class="p-2 border rounded text-sm w-64">
                <button class="bg-gray-800 text-white px-4 py-2 rounded text-sm">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Tanggal</th>
                        <th class="px-6 py-3 text-left">Santri</th>
                        <th class="px-6 py-3 text-left">Jenis Dispensasi</th>
                        <th class="px-6 py-3 text-left">Alasan</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($permits)): ?>
                        <tr><td colspan="6" class="p-4 text-center text-gray-400">Tidak ada data dispensasi.</td></tr>
                    <?php endif; ?>

                    <?php foreach($permits as $p): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-mono"><?= date('d/m/Y', strtotime($p['date'])) ?></td>
                        <td class="px-6 py-3">
                            <div class="font-bold text-gray-800"><?= $p['full_name'] ?></div>
                            <div class="text-xs text-gray-500"><?= $p['class_name'] ?? '-' ?></div>
                        </td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded text-xs font-bold 
                                <?= $p['type'] == 'LOMBA' ? 'bg-purple-100 text-purple-700' : 
                                   ($p['type'] == 'SAKIT' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-200') ?>">
                                <?= $p['type'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-600"><?= $p['reason'] ?></td>
                        <td class="px-6 py-3 text-center">
                            <span class="text-xs font-bold text-green-600 border border-green-200 bg-green-50 px-2 py-1 rounded">
                                APPROVED
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <a href="/academic/kbm-permits/delete?id=<?= $p['id'] ?>" class="text-red-500" onclick="return confirm('Hapus izin ini?')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-lg shadow-xl">
        <h3 class="text-xl font-bold mb-4">Input Dispensasi KBM</h3>
        <form action="/academic/kbm-permits/store" method="POST">
            <?= \App\Core\Csrf::input() ?>
            
            <div class="mb-3">
                <label class="block text-xs font-bold mb-1">Pilih Santri</label>
                <select name="student_id" class="w-full p-2 border rounded select2" required style="width: 100%">
                    <option value="">-- Cari Santri --</option>
                    <?php foreach($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?> (<?= $s['nis'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-xs font-bold mb-1">Tanggal Izin</label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Tahun Ajaran</label>
                    <select name="academic_year_id" class="w-full p-2 border rounded">
                        <?php foreach($years as $y): ?>
                            <option value="<?= $y['id'] ?>"><?= $y['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-xs font-bold mb-1">Jenis Dispensasi</label>
                <select name="type" class="w-full p-2 border rounded bg-gray-50">
                    <option value="SAKIT">Sakit (UKS/Rumah)</option>
                    <option value="LOMBA">Lomba / Tugas Sekolah</option>
                    <option value="DISPENSASI">Dispensasi Keluarga</option>
                    <option value="SKORSING">Skorsing</option>
                    <option value="IZIN">Izin Lainnya</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold mb-1">Alasan Detail</label>
                <textarea name="reason" class="w-full p-2 border rounded h-20" placeholder="Contoh: Mengikuti Olimpiade Matematika tingkat Kabupaten..." required></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Izin</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() { 
        $('.select2').select2({ dropdownParent: $('#addModal') }); 
    });
</script>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>

