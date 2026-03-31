<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Data Orang Tua</h1>
            <p class="text-gray-500">Siswa: <span class="font-bold"><?= htmlspecialchars($student['full_name']) ?></span></p>
        </div>
        <a href="/student-affairs/parents" class="text-blue-600 hover:underline flex items-center">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <form action="/student-affairs/parents/update" method="POST">
        <input type="hidden" name="id" value="<?= $student['id'] ?>">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-blue-500">
                <h3 class="font-bold text-lg text-gray-700 mb-4 flex items-center">
                    <i class="fa-solid fa-person mr-2 text-blue-500"></i> Data Ayah
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1">Nama Ayah</label>
                        <input type="text" name="father_name" value="<?= htmlspecialchars($student['father_name'] ?? '') ?>" class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1">Pekerjaan</label>
                        <input type="text" name="father_job" value="<?= htmlspecialchars($student['father_job'] ?? '') ?>" class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1">No. HP / WhatsApp</label>
                        <input type="text" name="father_phone" value="<?= htmlspecialchars($student['father_phone'] ?? '') ?>" class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-pink-500">
                <h3 class="font-bold text-lg text-gray-700 mb-4 flex items-center">
                    <i class="fa-solid fa-person-dress mr-2 text-pink-500"></i> Data Ibu
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1">Nama Ibu</label>
                        <input type="text" name="mother_name" value="<?= htmlspecialchars($student['mother_name'] ?? '') ?>" class="w-full p-2 border rounded focus:ring-2 focus:ring-pink-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1">Pekerjaan</label>
                        <input type="text" name="mother_job" value="<?= htmlspecialchars($student['mother_job'] ?? '') ?>" class="w-full p-2 border rounded focus:ring-2 focus:ring-pink-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1">No. HP / WhatsApp</label>
                        <input type="text" name="mother_phone" value="<?= htmlspecialchars($student['mother_phone'] ?? '') ?>" class="w-full p-2 border rounded focus:ring-2 focus:ring-pink-500 outline-none">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-green-500 lg:col-span-2">
                <h3 class="font-bold text-lg text-gray-700 mb-4 flex items-center">
                    <i class="fa-solid fa-user-shield mr-2 text-green-500"></i> Data Wali (Opsional)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1">Nama Wali</label>
                        <input type="text" name="guardian_name" value="<?= htmlspecialchars($student['guardian_name'] ?? '') ?>" class="w-full p-2 border rounded focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1">Hubungan Keluarga</label>
                        <input type="text" name="guardian_relation" value="<?= htmlspecialchars($student['guardian_relation'] ?? '') ?>" placeholder="Misal: Paman/Bibi" class="w-full p-2 border rounded focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1">No. HP Wali</label>
                        <input type="text" name="guardian_phone" value="<?= htmlspecialchars($student['guardian_phone'] ?? '') ?>" class="w-full p-2 border rounded focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-bold text-gray-600 mb-1">Alamat Lengkap Wali</label>
                    <textarea name="guardian_address" rows="2" class="w-full p-2 border rounded focus:ring-2 focus:ring-green-500 outline-none"><?= htmlspecialchars($student['guardian_address'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white font-bold px-8 py-3 rounded-lg shadow hover:bg-blue-700 transition">
                <i class="fa-solid fa-save mr-2"></i> Simpan Semua Perubahan
            </button>
        </div>
    </form>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
