<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Kasir Pembayaran</h3>
        <p class="text-slate-500 text-sm mt-1 font-medium">Cari data siswa untuk mengelola tagihan.</p>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="max-w-xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-blue-100 text-blue-500 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <h4 class="text-lg font-extrabold text-slate-700">Cari Data Siswa</h4>
            <p class="text-slate-400 text-sm mt-1">Masukkan NIS atau Nama Siswa untuk mengelola tagihan.</p>
        </div>
        <form action="/finance/billing" method="GET" class="flex gap-2">
            <div class="flex-1 relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="nis" placeholder="Masukkan NIS Siswa (cth: 2024001)"
                    class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all text-sm">
                <i class="fa-solid fa-search mr-1"></i> Cari
            </button>
        </form>
        <p class="text-xs text-slate-400 text-center mt-4">Pastikan siswa sudah terdaftar di Data Santri.</p>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
