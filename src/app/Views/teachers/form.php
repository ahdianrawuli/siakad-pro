<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800"><?= $title ?></h1>
            <p class="text-gray-500">Lengkapi data profil dan akses login guru.</p>
        </div>
        <a href="/student-affairs/teachers" class="text-blue-600 hover:underline flex items-center">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="max-w-4xl">
        <form action="<?= isset($teacher) ? '/student-affairs/teachers/update' : '/student-affairs/teachers/store' ?>" method="POST">
            <?php if(class_exists('\App\Core\Csrf')) echo \App\Core\Csrf::input(); ?>
            <?php if(isset($teacher)): ?>
                <input type="hidden" name="id" value="<?= $teacher['id'] ?>">
            <?php endif; ?>

            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-4 border-b bg-gray-50 font-bold text-gray-700">A. Biodata Guru</div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">NIP</label>
                            <input type="text" name="nip" value="<?= $teacher['nip'] ?? '' ?>" class="w-full p-3 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="full_name" value="<?= $teacher['full_name'] ?? '' ?>" class="w-full p-3 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Kelamin</label>
                            <select name="gender" class="w-full p-3 border rounded-lg outline-none">
                                <option value="L" <?= (isset($teacher) && $teacher['gender'] == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= (isset($teacher) && $teacher['gender'] == 'P') ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Pendidikan Terakhir</label>
                            <select name="education" class="w-full p-3 border rounded-lg outline-none">
                                <?php foreach(['S1', 'S2', 'S3', 'D3', 'SMA'] as $edu): ?>
                                    <option value="<?= $edu ?>" <?= (isset($teacher) && $teacher['education'] == $edu) ? 'selected' : '' ?>><?= $edu ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nomor HP/WhatsApp</label>
                            <input type="text" name="phone" value="<?= $teacher['phone'] ?? '' ?>" class="w-full p-3 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500" placeholder="08xxxx">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="<?= $teacher['email'] ?? '' ?>" class="w-full p-3 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500" placeholder="email@sekolah.com">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Lengkap</label>
                            <textarea name="address" rows="3" class="w-full p-3 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500"><?= $teacher['address'] ?? '' ?></textarea>
                        </div>
                    </div>
                </div>

                <?php if(!isset($teacher)): ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-4 border-b bg-blue-50 font-bold text-blue-700">B. Akun Login Sistem</div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Username</label>
                            <input type="text" name="username" class="w-full p-3 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500" required placeholder="Gunakan NIP atau nama">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                            <input type="password" name="password" class="w-full p-3 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500" required placeholder="Min. 6 karakter">
                        </div>
                    </div>
                    <div class="p-4 bg-yellow-50 text-xs text-yellow-700">
                        * Akun ini akan otomatis terdaftar di Manajemen User dengan role Guru.
                    </div>
                </div>
                <?php endif; ?>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white font-bold px-10 py-3 rounded-lg shadow hover:bg-blue-700 transition">
                        <i class="fa-solid fa-save mr-2"></i> Simpan Data Guru
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
