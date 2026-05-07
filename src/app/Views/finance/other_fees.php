<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 md:p-6">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Biaya Lain lain</h1>
        <p class="text-sm text-gray-500">Manajemen tagihan dan biaya tambahan siswa di luar SPP.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 flex flex-col items-center justify-center text-center min-h-[400px]">
        <div class="w-20 h-20 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center text-3xl mb-4">
            <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800">Modul Biaya Lain-lain</h2>
        <p class="text-gray-500 mt-2 max-w-md">Fitur ini masih dalam tahap pengembangan. Nantinya Anda dapat mengelola berbagai jenis biaya tambahan seperti uang gedung, seragam, buku, dan denda di sini.</p>
        <a href="/dashboard" class="mt-6 bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg transition">Kembali ke Dashboard</a>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>