<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Kasir Pembayaran</h1>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-md mt-10">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-700">Cari Data Siswa</h2>
            <p class="text-gray-500 text-sm">Masukkan NIS atau Nama Siswa untuk mengelola tagihan.</p>
        </div>

        <form action="/finance/billing" method="GET" class="flex gap-2">
            <input type="text" name="nis" placeholder="Masukkan NIS Siswa (Contoh: 2024001)" 
                   class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500" required>
            
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                <i class="fa-solid fa-search mr-2"></i> Cari
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-xs text-gray-400">Pastikan siswa sudah terdaftar di Data Santri.</p>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
