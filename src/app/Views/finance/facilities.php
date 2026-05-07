<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 md:p-6">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Sarana dan Prasarana</h1>
            <p class="text-sm text-gray-500">Manajemen inventaris, aset, bangunan, dan fasilitas yayasan.</p>
        </div>
        <div class="flex gap-2">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Aset
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 flex flex-col items-center justify-center text-center min-h-[400px]">
        <div class="w-20 h-20 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center text-3xl mb-4">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800">Modul Inventaris & Sarpras</h2>
        <p class="text-gray-500 mt-2 max-w-md">Sistem inventarisasi barang (kodifikasi aset) dan manajemen pemeliharaan sarana/prasarana sedang dalam proses pengembangan.</p>
        <a href="/dashboard" class="mt-6 text-blue-600 font-medium hover:underline flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>