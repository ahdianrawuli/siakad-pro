<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <h3 class="text-3xl font-medium text-gray-700 mb-6">Monitoring Tahfidz</h3>
    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow h-fit">
            <h4 class="font-bold mb-4 border-b pb-2 text-blue-600">
                <i class="fa-solid fa-book-quran mr-2"></i> Setoran Hafalan
            </h4>
            <form action="/boarding/tahfidz/store" method="POST">
                <?= \App\Core\Csrf::input() ?>
                
                <div class="mb-3">
                    <label class="block text-xs font-bold uppercase mb-1">Santri</label>
                    <select name="student_id" class="w-full p-2 border rounded select2" required>
                        <?php foreach($students as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-bold uppercase mb-1">Tanggal</label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="w-full p-2 border rounded">
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-bold uppercase mb-1">Jenis Setoran</label>
                    <select name="type" class="w-full p-2 border rounded">
                        <option value="ZIYADAH">Ziyadah (Hafalan Baru)</option>
                        <option value="MUROJAAH">Murojaah (Mengulang)</option>
                        <option value="TILAWAH">Tilawah (Membaca Binadzor)</option>
                    </select>
                </div>

                <div class="flex gap-2 mb-3">
                    <div class="w-2/3">
                        <label class="block text-xs font-bold uppercase mb-1">Nama Surat</label>
                        <input type="text" name="surah_name" placeholder="Misal: An-Naba" class="w-full p-2 border rounded" required>
                    </div>
                    <div class="w-1/3">
                        <label class="block text-xs font-bold uppercase mb-1">Ayat</label>
                        <input type="text" name="verses" placeholder="1-10" class="w-full p-2 border rounded" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold uppercase mb-1">Kelancaran (Nilai)</label>
                    <div class="flex gap-4">
                        <label class="flex items-center"><input type="radio" name="grade" value="A" checked class="mr-2"> Mumtaz (Lancar)</label>
                        <label class="flex items-center"><input type="radio" name="grade" value="B" class="mr-2"> Jayyid (Sedang)</label>
                        <label class="flex items-center"><input type="radio" name="grade" value="C" class="mr-2"> Kurang</label>
                    </div>
                </div>
                
                <textarea name="note" class="w-full p-2 border rounded mb-3" placeholder="Catatan Musyrif..."></textarea>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700">Simpan Hafalan</button>
            </form>
        </div>

        <div class="md:col-span-2 bg-white rounded shadow overflow-hidden">
             <div class="p-4 bg-gray-50 border-b font-bold text-gray-700">Log Setoran Terakhir</div>
             <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-white text-left text-xs font-bold uppercase text-gray-600 border-b">
                        <th class="px-5 py-3">Tgl</th>
                        <th class="px-5 py-3">Santri</th>
                        <th class="px-5 py-3">Hafalan</th>
                        <th class="px-5 py-3 text-center">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($logs as $l): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-5 py-4 text-xs"><?= date('d/m', strtotime($l['date'])) ?></td>
                        <td class="px-5 py-4 font-bold"><?= $l['full_name'] ?></td>
                        <td class="px-5 py-4">
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-bold"><?= $l['type'] ?></span>
                            <span class="ml-1 font-bold text-gray-700"><?= $l['surah_name'] ?>: <?= $l['verses'] ?></span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="font-bold text-lg <?= $l['grade']=='A'?'text-green-600':($l['grade']=='B'?'text-blue-600':'text-red-600') ?>">
                                <?= $l['grade'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
             </table>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
