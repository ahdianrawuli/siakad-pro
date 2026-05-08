<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-50 to-slate-100 p-4 md:p-6">

    <!-- Hero Header -->
    <div class="mb-6 relative overflow-hidden bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 p-6 md:p-8 rounded-3xl shadow-2xl text-white">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
        </div>
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-green-100 text-sm font-medium mb-1"><i class="fa-solid fa-gear mr-1"></i> Pengaturan</p>
                <h2 class="text-2xl md:text-3xl font-black tracking-tight">Identitas Sekolah</h2>
                <p class="text-green-100 text-sm mt-1">Kelola informasi profil dan identitas resmi pesantren.</p>
            </div>
            <button form="form-school" type="submit"
                class="self-start md:self-auto inline-flex items-center gap-2 px-6 py-3 bg-white text-green-700 font-bold rounded-2xl shadow-lg hover:bg-green-50 transition text-sm">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <form id="form-school" action="/school/profile/update" method="POST" enctype="multipart/form-data">
        <?= \App\Core\Csrf::input() ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Kolom Kiri -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-slate-100 px-6 py-4 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class="fa-solid fa-school text-green-600"></i>
                    </div>
                    <h4 class="font-bold text-slate-700">Informasi Umum</h4>
                </div>
                <div class="p-6 flex flex-col gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Sekolah / Pesantren</label>
                        <input type="text" name="school_name" value="<?= $config['school_name'] ?? '' ?>"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500/40 focus:border-green-400 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Lengkap</label>
                        <textarea name="school_address" rows="3"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500/40 focus:border-green-400 outline-none transition-all resize-none"><?= $config['school_address'] ?? '' ?></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">No. Telepon</label>
                        <input type="text" name="school_phone" value="<?= $config['school_phone'] ?? '' ?>"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500/40 focus:border-green-400 outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-slate-100 px-6 py-4 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <i class="fa-solid fa-globe text-emerald-600"></i>
                    </div>
                    <h4 class="font-bold text-slate-700">Kontak & Branding</h4>
                </div>
                <div class="p-6 flex flex-col gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email Resmi</label>
                        <input type="email" name="school_email" value="<?= $config['school_email'] ?? '' ?>"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500/40 focus:border-green-400 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Website</label>
                        <input type="text" name="school_website" value="<?= $config['school_website'] ?? '' ?>"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500/40 focus:border-green-400 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Logo Sekolah</label>
                        <?php if (!empty($config['school_logo'])): ?>
                            <div class="mb-3 p-3 bg-slate-50 border border-slate-200 rounded-xl inline-block">
                                <img src="/uploads/<?= $config['school_logo'] ?>" class="h-16 object-contain">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="school_logo"
                            class="w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-all">
                        <p class="text-xs text-slate-400 mt-1.5">Format: PNG/JPG. Transparan lebih baik.</p>
                    </div>
                </div>
            </div>

        </div>
    </form>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
