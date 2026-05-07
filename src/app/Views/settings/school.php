<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Identitas Sekolah</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola informasi profil dan identitas resmi pesantren.</p>
        </div>
        <div>
            <button form="form-school" type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 hover:shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <form id="form-school" action="/school/profile/update" method="POST" enctype="multipart/form-data">
        <?= \App\Core\Csrf::input() ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Kolom Kiri -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col gap-5">
                <h4 class="font-bold text-slate-700 flex items-center gap-2 border-b border-slate-100 pb-4">
                    <i class="fa-solid fa-school text-slate-400"></i> Informasi Umum
                </h4>

                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Sekolah / Pesantren</label>
                    <input type="text" name="school_name" value="<?= $config['school_name'] ?? '' ?>"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Alamat Lengkap</label>
                    <textarea name="school_address" rows="3"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"><?= $config['school_address'] ?? '' ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">No. Telepon</label>
                    <input type="text" name="school_phone" value="<?= $config['school_phone'] ?? '' ?>"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col gap-5">
                <h4 class="font-bold text-slate-700 flex items-center gap-2 border-b border-slate-100 pb-4">
                    <i class="fa-solid fa-globe text-slate-400"></i> Kontak & Branding
                </h4>

                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Email Resmi</label>
                    <input type="email" name="school_email" value="<?= $config['school_email'] ?? '' ?>"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Website</label>
                    <input type="text" name="school_website" value="<?= $config['school_website'] ?? '' ?>"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Logo Sekolah</label>
                    <?php if (!empty($config['school_logo'])): ?>
                        <div class="mb-3 p-3 bg-slate-50 border border-slate-200 rounded-xl inline-block">
                            <img src="/uploads/<?= $config['school_logo'] ?>" class="h-16 object-contain">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="school_logo"
                        class="w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                    <p class="text-xs text-slate-400 mt-1.5">Format: PNG/JPG. Transparan lebih baik.</p>
                </div>
            </div>
        </div>

    </form>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
