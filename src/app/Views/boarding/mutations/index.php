<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-3xl font-medium text-gray-700">Mutasi Kamar</h3>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-sm">
            <i class="fa fa-exchange-alt"></i> Proses Mutasi
        </button>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 bg-gray-50 border-b font-bold text-gray-700 text-sm uppercase">
            Riwayat Perpindahan Kamar
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Tanggal</th>
                        <th class="px-6 py-3 text-left">Santri</th>
                        <th class="px-6 py-3 text-left">Asrama Lama</th>
                        <th class="px-6 py-3 text-center"><i class="fa fa-arrow-right"></i></th>
                        <th class="px-6 py-3 text-left">Asrama Baru</th>
                        <th class="px-6 py-3 text-left">Alasan</th>
                        <th class="px-6 py-3 text-left">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach($mutations as $m): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-gray-600"><?= date('d/m/Y', strtotime($m['mutation_date'])) ?></td>
                        <td class="px-6 py-3 font-bold"><?= $m['full_name'] ?></td>
                        <td class="px-6 py-3 text-red-600"><?= $m['old_dorm'] ?? '-' ?></td>
                        <td class="px-6 py-3 text-center text-gray-400"><i class="fa fa-arrow-right"></i></td>
                        <td class="px-6 py-3 text-green-600 font-bold"><?= $m['new_dorm'] ?></td>
                        <td class="px-6 py-3 text-gray-500 italic"><?= $m['reason'] ?></td>
                        <td class="px-6 py-3 text-xs"><?= $m['admin_name'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-lg shadow-xl">
        <h3 class="text-xl font-bold mb-4">Form Mutasi Kamar</h3>
        <form action="/boarding/mutations/store" method="POST">
            <?= \App\Core\Csrf::input() ?>
            
            <div class="mb-3">
                <label class="block text-xs font-bold mb-1">Santri</label>
                <select name="student_id" class="w-full p-2 border rounded select2" required style="width: 100%">
                    <option value="">-- Pilih Santri --</option>
                    <?php foreach($students as $s): ?>
                        <option value="<?= $s['id'] ?>">
                            <?= $s['full_name'] ?> (<?= $s['current_dorm'] ?? 'Belum ada kamar' ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="block text-xs font-bold mb-1">Pindah ke Asrama (Tujuan)</label>
                <select name="new_dorm_id" class="w-full p-2 border rounded" required>
                    <option value="">-- Pilih Kamar Baru --</option>
                    <?php foreach($dorms as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= $d['name'] ?> (Kapasitas: <?= $d['capacity'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="block text-xs font-bold mb-1">Tanggal Pindah</label>
                <input type="date" name="mutation_date" value="<?= date('Y-m-d') ?>" class="w-full p-2 border rounded">
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold mb-1">Alasan Pindah</label>
                <textarea name="reason" class="w-full p-2 border rounded h-20" placeholder="Contoh: Sakit, tidak betah, promosi..." required></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Proses Pindah</button>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() { 
        $('.select2').select2({ dropdownParent: $('#addModal') }); 
    });
</script>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
