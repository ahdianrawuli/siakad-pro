<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800"><?= $title ?></h1>
    </div>

    <div class="max-w-2xl bg-white p-8 rounded-lg shadow-sm">
        <form action="<?= isset($alumni) ? '/school/alumni/update' : '/school/alumni/store' ?>" method="POST">
            <?php if(isset($alumni)): ?><input type="hidden" name="id" value="<?= $alumni['id'] ?>"><?php endif; ?>

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="full_name" value="<?= $alumni['full_name'] ?? '' ?>" class="w-full p-2 border rounded" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">NIS</label>
                        <input type="text" name="nis" value="<?= $alumni['nis'] ?? '' ?>" class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tahun Lulus</label>
                        <input type="number" name="graduation_year" value="<?= $alumni['graduation_year'] ?? date('Y') ?>" class="w-full p-2 border rounded" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Aktivitas Sekarang</label>
                    <select name="activity" class="w-full p-2 border rounded">
                        <option value="KULIAH" <?= (isset($alumni) && $alumni['activity'] == 'KULIAH') ? 'selected' : '' ?>>Kuliah</option>
                        <option value="KERJA" <?= (isset($alumni) && $alumni['activity'] == 'KERJA') ? 'selected' : '' ?>>Bekerja</option>
                        <option value="USAHA" <?= (isset($alumni) && $alumni['activity'] == 'USAHA') ? 'selected' : '' ?>>Wirausaha</option>
                        <option value="LAINNYA" <?= (isset($alumni) && $alumni['activity'] == 'LAINNYA') ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Detail (Nama Kampus / Perusahaan / Jenis Usaha)</label>
                    <textarea name="detail_activity" class="w-full p-2 border rounded" rows="2"><?= $alumni['detail_activity'] ?? '' ?></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">No. HP</label>
                        <input type="text" name="phone" value="<?= $alumni['phone'] ?? '' ?>" class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="<?= $alumni['email'] ?? '' ?>" class="w-full p-2 border rounded">
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded hover:bg-blue-700">Simpan Data Alumni</button>
            </div>
        </form>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
