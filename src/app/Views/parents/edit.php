<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Edit Data Orang Tua</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Siswa: <span class="font-bold text-slate-700"><?= htmlspecialchars($student['full_name']) ?></span></p>
        </div>
        <div class="flex gap-2">
            <a href="/student-affairs/parents" class="px-4 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-all flex items-center gap-2 w-fit">
                <i class="fa-solid fa-arrow-left text-slate-400"></i> Kembali
            </a>
            <button form="form-parents" type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </div>

    <form id="form-parents" action="/student-affairs/parents/update" method="POST">
        <?php if (class_exists('\App\Core\Csrf')) echo \App\Core\Csrf::input(); ?>
        <input type="hidden" name="id" value="<?= $student['id'] ?>">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Data Ayah -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-blue-100 bg-blue-50">
                    <h4 class="font-bold text-blue-700 flex items-center gap-2">
                        <i class="fa-solid fa-person text-blue-400"></i> Data Ayah
                    </h4>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Ayah</label>
                        <input type="text" name="father_name" value="<?= htmlspecialchars($student['father_name'] ?? '') ?>" placeholder="cth: Budi Santoso"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Pekerjaan</label>
                        <input type="text" name="father_job" value="<?= htmlspecialchars($student['father_job'] ?? '') ?>" placeholder="cth: Wiraswasta"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">No. HP / WhatsApp</label>
                        <input type="text" name="father_phone" value="<?= htmlspecialchars($student['father_phone'] ?? '') ?>" placeholder="cth: 08123456789"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Data Ibu -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-pink-100 bg-pink-50">
                    <h4 class="font-bold text-pink-700 flex items-center gap-2">
                        <i class="fa-solid fa-person-dress text-pink-400"></i> Data Ibu
                    </h4>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Ibu</label>
                        <input type="text" name="mother_name" value="<?= htmlspecialchars($student['mother_name'] ?? '') ?>" placeholder="cth: Siti Rahayu"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Pekerjaan</label>
                        <input type="text" name="mother_job" value="<?= htmlspecialchars($student['mother_job'] ?? '') ?>" placeholder="cth: Ibu Rumah Tangga"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">No. HP / WhatsApp</label>
                        <input type="text" name="mother_phone" value="<?= htmlspecialchars($student['mother_phone'] ?? '') ?>" placeholder="cth: 08198765432"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500/50 outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Data Wali -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden lg:col-span-2">
                <div class="p-5 border-b border-green-100 bg-green-50">
                    <h4 class="font-bold text-green-700 flex items-center gap-2">
                        <i class="fa-solid fa-user-shield text-green-400"></i> Data Wali <span class="text-xs font-normal text-green-500">(Opsional)</span>
                    </h4>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Wali</label>
                            <input type="text" name="guardian_name" value="<?= htmlspecialchars($student['guardian_name'] ?? '') ?>" placeholder="cth: Ahmad Yusuf"
                                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500/50 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1.5">Hubungan Keluarga</label>
                            <input type="text" name="guardian_relation" value="<?= htmlspecialchars($student['guardian_relation'] ?? '') ?>" placeholder="cth: Paman / Bibi"
                                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500/50 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1.5">No. HP Wali</label>
                            <input type="text" name="guardian_phone" value="<?= htmlspecialchars($student['guardian_phone'] ?? '') ?>" placeholder="cth: 08111222333"
                                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500/50 outline-none transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Alamat Lengkap Wali</label>
                        <textarea name="guardian_address" rows="2" placeholder="cth: Jl. Merdeka No. 10, Bukittinggi"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500/50 outline-none transition-all resize-none"><?= htmlspecialchars($student['guardian_address'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
