<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <h3 class="text-2xl font-bold mb-6">Jurnal Ngaji Kitab (Sorogan/Bandongan)</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow h-fit">
            <form action="/academic/kitab/store" method="POST">
                <?= \App\Core\Csrf::input() ?>
                <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="w-full mb-3 p-2 border rounded">
                <input type="text" name="class_name" placeholder="Nama Halaqah (Misal: Ulya 1)" class="w-full mb-3 p-2 border rounded" required>
                <input type="text" name="kitab_name" placeholder="Nama Kitab (Misal: Fathul Qorib)" class="w-full mb-3 p-2 border rounded" required>
                <div class="flex gap-2 mb-3">
                    <input type="number" name="start_page" placeholder="Hal Awal" class="w-1/2 p-2 border rounded">
                    <input type="number" name="end_page" placeholder="Hal Akhir" class="w-1/2 p-2 border rounded">
                </div>
                <input type="text" name="chapter" placeholder="Bab / Fashl" class="w-full mb-3 p-2 border rounded">
                <textarea name="notes" placeholder="Catatan..." class="w-full mb-3 p-2 border rounded"></textarea>
                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded font-bold">Simpan</button>
            </form>
        </div>
        <div class="md:col-span-2 bg-white shadow rounded overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-100 text-left text-xs font-bold uppercase text-gray-600"><th class="px-5 py-3">Tgl</th><th class="px-5 py-3">Halaqah</th><th class="px-5 py-3">Kitab & Bahasan</th></tr>
                </thead>
                <tbody>
                    <?php foreach($journals as $j): ?>
                    <tr class="border-b">
                        <td class="px-5 py-4"><?= date('d/m', strtotime($j['date'])) ?></td>
                        <td class="px-5 py-4 font-bold"><?= $j['class_name'] ?></td>
                        <td class="px-5 py-4">
                            <span class="text-blue-600 font-bold"><?= $j['kitab_name'] ?></span>
                            <div class="text-sm">Hal: <?= $j['start_page'] ?> - <?= $j['end_page'] ?></div>
                            <div class="text-xs text-gray-500">Bab: <?= $j['chapter'] ?></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
