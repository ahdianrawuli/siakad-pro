<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 md:p-6">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Silabus Pembelajaran</h1>
            <p class="text-sm text-gray-500">Arsip kurikulum dan rencana program pembelajaran (RPP) guru.</p>
        </div>
        <div class="flex gap-2">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center">
                <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Unggah Silabus
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 flex flex-col items-center justify-center text-center min-h-[400px]">
        <div class="w-20 h-20 bg-teal-50 text-teal-500 rounded-full flex items-center justify-center text-3xl mb-4">
            <i class="fa-solid fa-book"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800">Modul Silabus & RPP</h2>
        <p class="text-gray-500 mt-2 max-w-md">Fitur baru untuk pengarsipan dan pengesahan rencana kerja mengajar oleh kepala bidang akademik sedang dirancang.</p>
        <a href="/dashboard" class="mt-6 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-6 py-2 rounded-lg transition">Kembali</a>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>