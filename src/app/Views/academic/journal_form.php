<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto">
        <h3 class="text-2xl font-bold text-gray-700 mb-6">Input Jurnal & Absensi</h3>
        
        <form action="/academic/journals/store" method="POST" class="bg-white shadow rounded-lg overflow-hidden">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">
            
            <div class="p-6 border-b bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Pertemuan</label>
                        <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="w-full p-2 border rounded shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Materi / Topik Bahasan</label>
                        <input type="text" name="topic" class="w-full p-2 border rounded shadow-sm" placeholder="Contoh: Bab 3 - Persamaan Kuadrat" required>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Kejadian (Opsional)</label>
                        <textarea name="notes" rows="2" class="w-full p-2 border rounded shadow-sm" placeholder="Siswa X tidur di kelas, Proyektor rusak, dll"></textarea>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <h4 class="font-bold text-gray-700 mb-4 flex items-center">
                    <i class="fa-solid fa-users-viewfinder mr-2"></i> Absensi Kelas
                </h4>
                
                <table class="min-w-full border rounded">
                    <thead class="bg-gray-100 text-xs uppercase font-bold text-gray-600">
                        <tr>
                            <th class="px-4 py-2 text-left">Nama Siswa</th>
                            <th class="px-2 py-2 text-center w-16 bg-green-50">Hadir</th>
                            <th class="px-2 py-2 text-center w-16 bg-yellow-50">Sakit</th>
                            <th class="px-2 py-2 text-center w-16 bg-blue-50">Izin</th>
                            <th class="px-2 py-2 text-center w-16 bg-red-50">Alpha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($students as $s): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 font-bold text-sm"><?= $s['full_name'] ?></td>
                            <td class="text-center bg-green-50">
                                <input type="radio" name="attendance[<?= $s['id'] ?>]" value="H" checked class="h-4 w-4 text-green-600">
                            </td>
                            <td class="text-center bg-yellow-50">
                                <input type="radio" name="attendance[<?= $s['id'] ?>]" value="S" class="h-4 w-4 text-yellow-600">
                            </td>
                            <td class="text-center bg-blue-50">
                                <input type="radio" name="attendance[<?= $s['id'] ?>]" value="I" class="h-4 w-4 text-blue-600">
                            </td>
                            <td class="text-center bg-red-50">
                                <input type="radio" name="attendance[<?= $s['id'] ?>]" value="A" class="h-4 w-4 text-red-600">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-gray-50 text-right border-t">
                <a href="/academic/journals/history?schedule_id=<?= $schedule['id'] ?>" class="text-gray-600 mr-4 font-bold text-sm">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded shadow hover:bg-blue-700 font-bold">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Jurnal
                </button>
            </div>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
