<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <h3 class="text-3xl font-medium text-gray-700 mb-6">Identitas Sekolah</h3>
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white p-6 rounded shadow-md">
        <form action="/settings/school/update" method="POST" enctype="multipart/form-data">
            <?= \App\Core\Csrf::input() ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Sekolah / Pesantren</label>
                        <input type="text" name="school_name" value="<?= $config['school_name'] ?? '' ?>" class="w-full p-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Lengkap</label>
                        <textarea name="school_address" rows="3" class="w-full p-2 border rounded"><?= $config['school_address'] ?? '' ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">No. Telepon</label>
                        <input type="text" name="school_phone" value="<?= $config['school_phone'] ?? '' ?>" class="w-full p-2 border rounded">
                    </div>
                </div>

                <div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email Resmi</label>
                        <input type="email" name="school_email" value="<?= $config['school_email'] ?? '' ?>" class="w-full p-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Website</label>
                        <input type="text" name="school_website" value="<?= $config['school_website'] ?? '' ?>" class="w-full p-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Logo Sekolah</label>
                        <?php if(!empty($config['school_logo'])): ?>
                            <img src="/uploads/<?= $config['school_logo'] ?>" class="h-16 mb-2 border">
                        <?php endif; ?>
                        <input type="file" name="school_logo" class="w-full text-sm">
                        <p class="text-xs text-gray-500 mt-1">Format: PNG/JPG (Transparan lebih baik)</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t pt-4 text-right">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
