<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <h3 class="text-3xl font-medium text-gray-700 mb-6">Template Surat</h3>
    <?php \App\Core\Session::flash(); ?>
    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-100 text-left text-xs font-bold uppercase text-gray-600"><th class="px-5 py-3">Nama Surat</th><th class="px-5 py-3">Kode</th><th class="px-5 py-3 text-center">Aksi</th></tr>
            </thead>
            <tbody>
                <?php foreach($templates as $t): ?>
                <tr class="border-b">
                    <td class="px-5 py-4 font-bold"><?= $t['name'] ?></td>
                    <td class="px-5 py-4 font-mono text-xs"><?= $t['code'] ?></td>
                    <td class="px-5 py-4 text-center">
                        <a href="/settings/letters/edit?id=<?= $t['id'] ?>" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Edit Konten</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
