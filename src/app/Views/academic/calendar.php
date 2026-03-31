<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow h-fit">
            <h4 class="font-bold mb-4">Tambah Event</h4>
            <form action="/academic/calendar/store" method="POST">
                <?= \App\Core\Csrf::input() ?>
                <input type="text" name="title" class="w-full p-2 border rounded mb-2" placeholder="Nama Kegiatan" required>
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <input type="date" name="start_date" class="border p-2 rounded" required>
                    <input type="date" name="end_date" class="border p-2 rounded" required>
                </div>
                <select name="type" class="w-full p-2 border rounded mb-2">
                    <option value="KEGIATAN">Kegiatan Sekolah</option>
                    <option value="LIBUR">Hari Libur</option>
                    <option value="UJIAN">Ujian</option>
                </select>
                <input type="color" name="color" value="#3788d8" class="w-full h-10 border rounded mb-4">
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Simpan</button>
            </form>
        </div>
        <div class="md:col-span-2 bg-white p-6 rounded shadow">
            <h4 class="font-bold mb-4">Agenda Tahun Ini</h4>
            <ul class="space-y-3">
                <?php foreach($events as $e): ?>
                <li class="flex border-l-4 pl-4 py-2 bg-gray-50 items-center" style="border-color: <?= $e['color'] ?>">
                    <div class="flex-1">
                        <p class="font-bold"><?= $e['title'] ?></p>
                        <p class="text-xs text-gray-500"><?= date('d M Y', strtotime($e['start_date'])) ?> s.d. <?= date('d M Y', strtotime($e['end_date'])) ?></p>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded bg-gray-200"><?= $e['type'] ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
