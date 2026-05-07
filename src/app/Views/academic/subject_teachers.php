<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
    <div class="mb-8 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Guru Mata Pelajaran</h3>
        <p class="text-slate-500 text-sm mt-1 font-medium">Pemetaan dan penugasan guru pengampu mata pelajaran.</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 flex flex-col items-center justify-center text-center min-h-[350px]">
        <div class="w-20 h-20 bg-blue-50 text-blue-400 rounded-full flex items-center justify-center text-3xl mb-4">
            <i class="fa-solid fa-chalkboard-user"></i>
        </div>
        <h4 class="text-lg font-extrabold text-slate-700">Modul Dalam Pengembangan</h4>
        <p class="text-slate-400 mt-2 max-w-md text-sm">Fitur pemetaan guru mata pelajaran ke setiap rombel/kelas sedang dalam tahap pengembangan.</p>
        <a href="/dashboard" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-200 transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
