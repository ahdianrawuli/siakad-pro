<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="flex justify-between mb-4">
        <h3 class="text-2xl font-bold">Edit Template: <?= $template['name'] ?></h3>
        <a href="/settings/letters" class="text-blue-600">Kembali</a>
    </div>
    <div class="bg-white p-6 rounded shadow">
        <div class="mb-4 bg-blue-50 p-4 rounded text-sm text-blue-800">
            <b>Placeholder Tersedia:</b> {nama}, {nis}, {kelas}, {tempat_lahir}, {tgl_lahir}, {alamat}
        </div>
        <form action="/settings/letters/update" method="POST">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" value="<?= $template['id'] ?>">
            <div class="mb-4">
                <label class="block font-bold mb-2">Nama Surat</label>
                <input type="text" name="name" value="<?= $template['name'] ?>" class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block font-bold mb-2">Isi Surat (HTML Allowed)</label>
                <textarea name="content" rows="15" class="w-full p-4 border rounded font-mono text-sm"><?= $template['content'] ?></textarea>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold">Simpan Template</button>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
