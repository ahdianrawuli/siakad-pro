<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight"><?= $title ?></h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Lengkapi data profil dan akses login guru.</p>
        </div>
        <a href="/school/teachers" class="px-4 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-all flex items-center gap-2 w-fit">
            <i class="fa-solid fa-arrow-left text-slate-400"></i> Kembali
        </a>
    </div>

    <form action="<?= isset($teacher) ? '/school/teachers/update' : '/school/teachers/store' ?>" method="POST">
        <?php if (class_exists('\App\Core\Csrf')) echo \App\Core\Csrf::input(); ?>
        <?php if (isset($teacher)): ?>
            <input type="hidden" name="id" value="<?= $teacher['id'] ?>">
        <?php endif; ?>

        <div class="max-w-4xl flex flex-col gap-6">

            <!-- Biodata -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50">
                    <h4 class="font-bold text-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-user text-slate-400"></i> A. Biodata Guru
                    </h4>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">NIP</label>
                        <input type="text" name="nip" value="<?= $teacher['nip'] ?? '' ?>"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="full_name" value="<?= $teacher['full_name'] ?? '' ?>"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jenis Kelamin</label>
                        <select name="gender" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                            <option value="L" <?= (isset($teacher) && $teacher['gender'] == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="P" <?= (isset($teacher) && $teacher['gender'] == 'P') ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Pendidikan Terakhir</label>
                        <select name="education" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                            <?php foreach (['S1', 'S2', 'S3', 'D3', 'SMA'] as $edu): ?>
                                <option value="<?= $edu ?>" <?= (isset($teacher) && $teacher['education'] == $edu) ? 'selected' : '' ?>><?= $edu ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nomor HP / WhatsApp</label>
                        <input type="text" name="phone" value="<?= $teacher['phone'] ?? '' ?>" placeholder="08xxxx"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Email</label>
                        <input type="email" name="email" value="<?= $teacher['email'] ?? '' ?>" placeholder="email@sekolah.com"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Alamat Lengkap</label>
                        <textarea name="address" rows="3"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"><?= $teacher['address'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>

            <?php if (!isset($teacher)): ?>
            <!-- Akun Login -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-blue-100 bg-blue-50">
                    <h4 class="font-bold text-blue-700 flex items-center gap-2">
                        <i class="fa-solid fa-key text-blue-400"></i> B. Akun Login Sistem
                    </h4>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Username</label>
                        <input type="text" name="username" placeholder="Gunakan NIP atau nama"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Password</label>
                        <input type="password" name="password" placeholder="Min. 6 karakter"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                    </div>
                </div>
                <div class="px-6 py-3 bg-amber-50 border-t border-amber-100 text-xs text-amber-700 font-medium">
                    <i class="fa-solid fa-circle-info mr-1"></i> Akun ini akan otomatis terdaftar di Manajemen User dengan role Guru.
                </div>
            </div>
            <?php endif; ?>

            <div class="flex justify-end">
                <button type="submit" class="px-8 py-2.5 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all text-sm flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Simpan Data Guru
                </button>
            </div>
        </div>
    </form>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
