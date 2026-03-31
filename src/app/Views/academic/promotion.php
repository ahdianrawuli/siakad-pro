<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800"><?= $title ?></h1>
        <p class="text-gray-600">Proses kenaikan kelas atau kelulusan santri secara massal.</p>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-gray-200">
        <form method="GET" action="/academic/promotion">
            <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Kelas Asal (Sumber Data)</label>
            <div class="flex gap-4">
                <select name="source_id" class="flex-1 p-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($classrooms as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (isset($sourceId) && $sourceId == $c['id']) ? 'selected' : '' ?>>
                            Kelas <?= $c['name'] ?> (<?= $c['level'] ?> - <?= $c['major'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                    Tampilkan Siswa
                </button>
            </div>
        </form>
    </div>

    <?php if(!empty($students)): ?>
    <form action="/academic/promotion/process" method="POST" id="promotionForm">
        <input type="hidden" name="source_id" value="<?= $sourceId ?>">

        <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-gray-200">
            <h3 class="font-bold text-lg text-gray-800 mb-4 border-b pb-2">Opsi Tindakan</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Tindakan</label>
                    <select name="action" id="actionSelect" class="w-full p-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="promote">Naikkan ke Kelas Berikutnya</option>
                        <option value="graduate">Luluskan (Pindah ke Alumni)</option>
                    </select>
                </div>

                <div id="targetClassBox">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Kelas Tujuan</label>
                    <select name="target_class" class="w-full p-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Kelas Tujuan --</option>
                        <?php foreach($classrooms as $c): ?>
                            <?php if($c['id'] != $sourceId): ?>
                            <option value="<?= $c['id'] ?>">
                                Kelas <?= $c['name'] ?> (<?= $c['level'] ?>)
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
            <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="font-bold text-gray-700">Daftar Siswa Kelas Asal</h3>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="selectAll" class="form-checkbox h-5 w-5 text-blue-600 rounded">
                    <span class="ml-2 text-sm font-bold text-gray-600">Pilih Semua</span>
                </label>
            </div>
            
            <table class="w-full text-left">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 w-10">#</th>
                        <th class="px-6 py-3">NIS</th>
                        <th class="px-6 py-3">Nama Lengkap</th>
                        <th class="px-6 py-3 text-center">Status Saat Ini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach($students as $s): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" name="student_ids[]" value="<?= $s['id'] ?>" class="student-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4 font-mono text-sm text-gray-600"><?= $s['nis'] ?></td>
                        <td class="px-6 py-4 font-bold text-gray-800"><?= $s['full_name'] ?></td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs font-bold bg-green-100 text-green-700 rounded-full">
                                <?= $s['status'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="p-6 bg-gray-50 border-t flex justify-end">
                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin memproses data terpilih?')" class="bg-green-600 text-white font-bold px-8 py-3 rounded-lg shadow hover:bg-green-700 transition flex items-center">
                    <i class="fa-solid fa-check-circle mr-2"></i> Proses Sekarang
                </button>
            </div>
        </div>
    </form>
    
    <?php elseif(isset($sourceId)): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-yellow-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Tidak ada siswa aktif ditemukan di kelas ini, atau kelas belum dipilih.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

</main>

<script>
    // 1. Script Select All Checkbox
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.student-checkbox');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }

    // 2. Script Show/Hide Kelas Tujuan berdasarkan Aksi
    const actionSelect = document.getElementById('actionSelect');
    const targetClassBox = document.getElementById('targetClassBox');

    if (actionSelect) {
        actionSelect.addEventListener('change', function() {
            if (this.value === 'graduate') {
                targetClassBox.style.display = 'none'; // Sembunyikan jika Lulus
                // Optional: Reset value agar tidak terkirim
                targetClassBox.querySelector('select').value = '';
                targetClassBox.querySelector('select').removeAttribute('required');
            } else {
                targetClassBox.style.display = 'block'; // Tampilkan jika Naik Kelas
                targetClassBox.querySelector('select').setAttribute('required', 'required');
            }
        });
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
