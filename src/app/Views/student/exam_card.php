<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6 print:bg-white print:p-0">

    <div class="mb-6 flex justify-between items-center print:hidden">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kartu Ujian</h1>
            <p class="text-gray-600 text-sm">Harap cetak dan bawa kartu ini saat ujian berlangsung.</p>
        </div>
        <button onclick="window.print()" class="bg-[#2E603E] text-white px-6 py-2 rounded shadow hover:bg-[#254e32] font-bold flex items-center">
            <i class="fa-solid fa-print mr-2"></i> Cetak Sekarang
        </button>
    </div>

    <div class="max-w-3xl mx-auto bg-white border-2 border-gray-800 p-8 rounded-none shadow-lg print:shadow-none print:w-full print:max-w-none print:border-2 print:border-black">
        
        <div class="flex items-center border-b-2 border-gray-800 pb-4 mb-6">
            <div class="w-20 h-20 bg-green-700 flex items-center justify-center text-white text-3xl font-serif rounded">
                <i class="fa-solid fa-mosque"></i>
            </div>
            <div class="ml-6 flex-1">
                <h2 class="text-2xl font-bold uppercase tracking-wide text-gray-900">KARTU PESERTA UJIAN</h2>
                <p class="text-sm font-bold text-green-700 uppercase tracking-widest">PONDOK PESANTREN DIGITAL</p>
                <p class="text-xs text-gray-500 mt-1">Jl. Pesantren No. 1, Kota Coding, Indonesia</p>
            </div>
            <div class="text-right">
                <div class="border-2 border-green-700 text-green-700 px-3 py-1 font-bold text-sm transform -rotate-6 inline-block">
                    VERIFIED
                </div>
            </div>
        </div>

        <div class="flex gap-8">
            <div class="w-32 h-40 bg-gray-100 border border-gray-300 flex items-center justify-center overflow-hidden relative">
                <?php if (!empty($student['photo'])): ?>
                    <img src="/uploads/documents/<?= $student['photo'] ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="text-center text-gray-400">
                        <i class="fa-solid fa-user text-4xl mb-2"></i>
                        <p class="text-[10px]">FOTO 3x4</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex-1 text-sm text-gray-800 space-y-3">
                <div class="grid grid-cols-3 border-b border-gray-200 pb-1">
                    <span class="font-bold text-gray-500 uppercase text-xs">Nomor Peserta</span>
                    <span class="col-span-2 font-mono font-bold text-lg"><?= $student['nis'] ?></span>
                </div>
                <div class="grid grid-cols-3 border-b border-gray-200 pb-1">
                    <span class="font-bold text-gray-500 uppercase text-xs">Nama Lengkap</span>
                    <span class="col-span-2 font-bold uppercase"><?= $student['full_name'] ?></span>
                </div>
                <div class="grid grid-cols-3 border-b border-gray-200 pb-1">
                    <span class="font-bold text-gray-500 uppercase text-xs">Kelas / Jalur</span>
                    <span class="col-span-2"><?= $student['class_name'] ?></span>
                </div>
                <div class="grid grid-cols-3 border-b border-gray-200 pb-1">
                    <span class="font-bold text-gray-500 uppercase text-xs">Lokasi Ujian</span>
                    <span class="col-span-2"><?= $exam['location'] ?></span>
                </div>
                <div class="grid grid-cols-3 border-b border-gray-200 pb-1">
                    <span class="font-bold text-gray-500 uppercase text-xs">Tanggal</span>
                    <span class="col-span-2"><?= $exam['dates'] ?></span>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-4 flex justify-between items-end">
            <div class="text-xs text-gray-500 italic max-w-sm">
                * Kartu ini wajib dibawa saat pelaksanaan ujian.<br>
                * Peserta wajib hadir 15 menit sebelum ujian dimulai.<br>
                * Dilarang membawa alat komunikasi ke dalam ruangan.
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-600 mb-12">Panitia Pelaksana,</p>
                <p class="font-bold text-sm border-b border-gray-800 pb-1 px-4">Admin Akademik</p>
                <p class="text-[10px] text-gray-400 mt-1">Dicetak pada: <?= date('d/m/Y H:i') ?></p>
            </div>
        </div>

    </div>

</main>

<style>
    @media print {
        @page { margin: 0; size: auto; }
        body * { visibility: hidden; }
        main { margin: 0; padding: 0; background: white; position: absolute; left: 0; top: 0; width: 100%; height: 100%; visibility: visible; }
        main .max-w-3xl { visibility: visible; position: absolute; left: 50%; top: 50px; transform: translateX(-50%); width: 100%; max-width: 800px; border: 2px solid black !important; box-shadow: none !important; }
        main .max-w-3xl * { visibility: visible; }
        .print\:hidden { display: none !important; }
    }
</style>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
