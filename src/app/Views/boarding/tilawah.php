<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 md:p-6">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Absen Tilawah</h1>
            <p class="text-sm text-gray-500">Pencatatan dan pemantauan aktivitas tilawah harian santri.</p>
        </div>
        <div class="flex gap-2">
            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center">
                <i class="fa-solid fa-clock mr-2"></i> Rekap Hari Ini
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center min-h-[300px] text-center">
        <div class="w-20 h-20 bg-green-50 text-green-500 rounded-full flex items-center justify-center text-3xl mb-4">
            <i class="fa-solid fa-book-quran"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800">Sistem Absensi Tilawah</h2>
        <p class="text-gray-500 mt-2 max-w-md">Modul absensi baca Al-Quran santri asrama masih dalam antrean pengembangan. Silakan kembali lagi nanti.</p>
        <a href="/dashboard" class="mt-6 text-blue-600 hover:underline font-medium"><i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Dashboard</a>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>