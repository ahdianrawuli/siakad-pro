<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Input Absensi Harian</h1>
            <p class="text-gray-500 text-sm">Silakan pilih kelas dan tanggal untuk memulai absensi.</p>
        </div>
        <a href="/student-affairs/attendance" class="text-blue-600 hover:text-blue-800 text-sm font-bold flex items-center">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Riwayat
        </a>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <form method="GET" action="/student-affairs/attendance/create" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Pilih Kelas</label>
                <select name="class_id" class="w-full p-2.5 border rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($classrooms as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $selectedClass == $c['id'] ? 'selected' : '' ?>>
                            <?= $c['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-full md:w-1/3">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Pilih Tanggal</label>
                <input type="date" name="date" value="<?= $selectedDate ?>" class="w-full p-2.5 border rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            <div class="w-full md:w-auto">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-blue-700 transition w-full md:w-auto shadow">
                    <i class="fa-solid fa-users-viewfinder mr-2"></i> Tampilkan Siswa
                </button>
            </div>
        </form>
    </div>

    <?php if ($selectedClass && !empty($students)): ?>
    <form action="/student-affairs/attendance/store" method="POST">
        <?= \App\Core\Csrf::input() ?>
        <input type="hidden" name="classroom_id" value="<?= $selectedClass ?>">
        <input type="hidden" name="date" value="<?= $selectedDate ?>">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 bg-gray-100 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-bold text-gray-700">Daftar Siswa</h3>
                <span class="text-xs font-mono bg-blue-100 text-blue-800 px-2 py-1 rounded">
                    <?= date('l, d F Y', strtotime($selectedDate)) ?>
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-xs uppercase text-gray-500">
                            <th class="px-6 py-3 font-bold">Nama Siswa</th>
                            <th class="px-6 py-3 font-bold text-center w-64">Status Kehadiran</th>
                            <th class="px-6 py-3 font-bold">Keterangan (Opsional)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($students as $s): 
                            $status = $existing[$s['id']]['status'] ?? 'H'; // Default Hadir
                            $note = $existing[$s['id']]['notes'] ?? '';
                        ?>
                        <tr class="hover:bg-blue-50 transition">
                            <td class="px-6 py-3">
                                <div class="font-bold text-gray-800"><?= $s['full_name'] ?></div>
                                <div class="text-xs text-gray-500"><?= $s['nis'] ?></div>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <div class="flex justify-center bg-gray-100 rounded-lg p-1 space-x-1">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendance[<?= $s['id'] ?>]" value="H" class="peer sr-only" <?= $status == 'H' ? 'checked' : '' ?>>
                                        <div class="px-3 py-1 rounded text-xs font-bold text-gray-500 peer-checked:bg-green-500 peer-checked:text-white transition">H</div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendance[<?= $s['id'] ?>]" value="S" class="peer sr-only" <?= $status == 'S' ? 'checked' : '' ?>>
                                        <div class="px-3 py-1 rounded text-xs font-bold text-gray-500 peer-checked:bg-yellow-400 peer-checked:text-white transition">S</div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendance[<?= $s['id'] ?>]" value="I" class="peer sr-only" <?= $status == 'I' ? 'checked' : '' ?>>
                                        <div class="px-3 py-1 rounded text-xs font-bold text-gray-500 peer-checked:bg-blue-500 peer-checked:text-white transition">I</div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendance[<?= $s['id'] ?>]" value="A" class="peer sr-only" <?= $status == 'A' ? 'checked' : '' ?>>
                                        <div class="px-3 py-1 rounded text-xs font-bold text-gray-500 peer-checked:bg-red-500 peer-checked:text-white transition">A</div>
                                    </label>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <input type="text" name="notes[<?= $s['id'] ?>]" value="<?= $note ?>" 
                                       placeholder="Cth: Sakit demam..." 
                                       class="w-full border-b border-gray-300 focus:border-blue-500 outline-none text-sm py-1 bg-transparent">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-green-700 shadow-lg transition">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Data Absensi
                </button>
            </div>
        </div>
    </form>
    <?php elseif ($selectedClass): ?>
        <div class="text-center py-12 bg-white rounded-lg border border-dashed border-gray-300">
            <p class="text-gray-500">Tidak ada siswa aktif di kelas ini.</p>
        </div>
    <?php endif; ?>

</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
