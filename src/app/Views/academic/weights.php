<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <h3 class="text-3xl font-medium text-gray-700 mb-6">Bobot Penilaian</h3>
    <?php \App\Core\Session::flash(); ?>

    <div class="max-w-xl bg-white p-8 rounded shadow">
        <div class="mb-6 border-b pb-4">
            <h4 class="font-bold text-lg text-gray-800">Tahun Ajaran: <span class="text-blue-600"><?= $year['name'] ?> (<?= $year['semester'] ?>)</span></h4>
            <p class="text-sm text-gray-500">Atur proporsi penilaian untuk perhitungan Rapor pada periode ini.</p>
        </div>

        <form action="/academic/weights/store" method="POST">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="academic_year_id" value="<?= $year['id'] ?>">

            <div class="grid grid-cols-1 gap-6">
                <div class="bg-blue-50 p-4 rounded border border-blue-200">
                    <label class="block font-bold text-blue-800 mb-2">Bobot Harian (UH + Tugas)</label>
                    <div class="flex items-center">
                        <input type="number" name="weight_daily" value="<?= $weight['weight_daily'] ?>" class="w-24 p-2 border rounded text-center font-bold text-lg" min="0" max="100" required>
                        <span class="ml-2 font-bold text-xl">%</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Diambil dari rata-rata UH1, UH2, dan Tugas.</p>
                </div>

                <div class="bg-yellow-50 p-4 rounded border border-yellow-200">
                    <label class="block font-bold text-yellow-800 mb-2">Bobot UTS (Tengah Semester)</label>
                    <div class="flex items-center">
                        <input type="number" name="weight_uts" value="<?= $weight['weight_uts'] ?>" class="w-24 p-2 border rounded text-center font-bold text-lg" min="0" max="100" required>
                        <span class="ml-2 font-bold text-xl">%</span>
                    </div>
                </div>

                <div class="bg-green-50 p-4 rounded border border-green-200">
                    <label class="block font-bold text-green-800 mb-2">Bobot UAS (Akhir Semester)</label>
                    <div class="flex items-center">
                        <input type="number" name="weight_uas" value="<?= $weight['weight_uas'] ?>" class="w-24 p-2 border rounded text-center font-bold text-lg" min="0" max="100" required>
                        <span class="ml-2 font-bold text-xl">%</span>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded font-bold shadow hover:bg-blue-700">
                    <i class="fa-solid fa-calculator mr-2"></i> Simpan Rumus Penilaian
                </button>
            </div>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
