<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    
    <?php if (isset($candidate)): ?>
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-800">Overview</h1>
                <p class="text-sm md:text-base text-gray-600">
                    Halo, <span class="font-bold text-[#2E603E]"><?= htmlspecialchars($candidate['full_name']) ?></span>
                </p>
            </div>
            <div class="inline-block bg-gray-200 rounded-full px-3 py-1 text-xs font-mono text-gray-600 self-start md:self-auto">
                ID: REG-<?= $candidate['id'] ?>
            </div>
        </div>

        <?php \App\Core\Session::flash(); ?>

        <?php 
            // Logika Status (Tetap sama seperti sebelumnya)
            $rawStatus = strtoupper(trim($candidate['registration_status'] ?? ''));

            $judulStatus = "Status Pendaftaran";
            $bgCard = "bg-white";
            $borderCard = "border-gray-200";
            $textStatus = "text-gray-700";
            $iconJudul = "fa-list-check";

            if ($rawStatus == 'APPROVED' || $rawStatus == 'LULUS' || $rawStatus == 'DITERIMA' || $rawStatus == 'ACCEPTED') {
                $judulStatus = "LULUS SELEKSI";
                $bgCard = "bg-green-50"; 
                $borderCard = "border-green-200";
                $textStatus = "text-green-800";
                $iconJudul = "fa-certificate";
            } elseif ($rawStatus == 'PAID' || $rawStatus == 'BAYAR' || $rawStatus == 'VERIFIKASI') {
                $judulStatus = "SEDANG DIVERIFIKASI";
                $bgCard = "bg-blue-50";
                $borderCard = "border-blue-200";
                $textStatus = "text-blue-800";
                $iconJudul = "fa-hourglass-half";
            } elseif ($rawStatus == 'PENDING' || $rawStatus == '') {
                $judulStatus = "MENUNGGU PEMBAYARAN";
                $bgCard = "bg-orange-50";
                $borderCard = "border-orange-200";
                $textStatus = "text-orange-800";
                $iconJudul = "fa-circle-exclamation";
            }
        ?>

        <div class="<?= $bgCard ?> border <?= $borderCard ?> p-5 rounded-xl shadow-sm mb-6 relative overflow-hidden">
            <i class="fa-solid <?= $iconJudul ?> absolute -right-4 -bottom-4 text-8xl opacity-10 pointer-events-none <?= $textStatus ?>"></i>

            <div class="relative z-10">
                <h3 class="font-bold <?= $textStatus ?> text-sm uppercase tracking-wider mb-1 opacity-80">Status Saat Ini</h3>
                <div class="text-2xl md:text-3xl font-extrabold <?= $textStatus ?> mb-4 flex items-center">
                    <?= $judulStatus ?>
                </div>

                <?php if(isset($progress)): ?>
                <div class="my-4 bg-white/60 p-4 rounded-lg">
                    <h4 class="text-sm font-bold text-gray-800 mb-3">Progress Pendaftaran:</h4>
                    <div class="flex flex-col md:flex-row gap-4 md:gap-8 text-sm">
                        <div class="flex items-center gap-2">
                            <?php if($progress['registered']): ?>
                                <i class="fa-solid fa-circle-check text-green-600 text-lg"></i>
                            <?php else: ?>
                                <i class="fa-solid fa-circle-xmark text-red-500 text-lg"></i>
                            <?php endif; ?>
                            <span class="text-gray-800 font-medium">1. Pendaftaran</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <?php if($progress['paid']): ?>
                                <i class="fa-solid fa-circle-check text-green-600 text-lg"></i>
                            <?php else: ?>
                                <i class="fa-solid fa-circle-xmark text-red-500 text-lg"></i>
                            <?php endif; ?>
                            <span class="text-gray-800 font-medium">2. Pembayaran</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <?php if($progress['document']): ?>
                                <i class="fa-solid fa-circle-check text-green-600 text-lg"></i>
                            <?php else: ?>
                                <i class="fa-solid fa-circle-xmark text-red-500 text-lg"></i>
                            <?php endif; ?>
                            <span class="text-gray-800 font-medium">3. Dokumen</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <?php if($progress['verified']): ?>
                                <i class="fa-solid fa-circle-check text-green-600 text-lg"></i>
                            <?php else: ?>
                                <i class="fa-solid fa-circle-xmark text-red-500 text-lg"></i>
                            <?php endif; ?>
                            <span class="text-gray-800 font-medium">4. Verifikasi Akhir</span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-2">
                    <?php if ($rawStatus == 'PENDING' || $rawStatus == ''): ?>
                        <p class="text-sm text-gray-700 mb-4 max-w-lg">
                            Formulir Anda belum diproses. Silakan lakukan pembayaran biaya pendaftaran untuk melanjutkan ke tahap verifikasi.
                        </p>
                        <a href="/student/payment" class="block w-full md:w-auto text-center bg-orange-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-orange-700 shadow-lg shadow-orange-200 transition transform active:scale-95">
                            <i class="fa-solid fa-wallet mr-2"></i> Bayar Sekarang
                        </a>
                    
                    <?php elseif ($rawStatus == 'PAID' || $rawStatus == 'BAYAR' || $rawStatus == 'VERIFIKASI'): ?>
                        <p class="text-sm text-gray-700">
                            Terima kasih. Bukti pembayaran telah kami terima. Panitia PPDB sedang memverifikasi data Anda (1x24 Jam).
                        </p>
                    
                    <?php elseif ($rawStatus == 'APPROVED' || $rawStatus == 'LULUS' || $rawStatus == 'DITERIMA' || $rawStatus == 'ACCEPTED'): ?>
                        <p class="text-sm text-green-800 mb-4 max-w-lg font-medium">
                            Selamat! Anda dinyatakan diterima sebagai santri baru. Silakan unduh kartu ujian/bukti kelulusan di bawah ini.
                        </p>
                        <a href="/student/exam-card" class="block w-full md:w-auto text-center bg-[#2E603E] text-white px-6 py-3 rounded-lg font-bold hover:bg-[#254e32] shadow-lg shadow-green-200 transition transform active:scale-95">
                            <i class="fa-solid fa-print mr-2"></i> Cetak Kartu Ujian
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-6">
            
            <a href="/student/payment" class="group bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition active:bg-gray-50">
                <div class="flex flex-col items-center text-center">
                    <div class="w-12 h-12 md:w-14 md:h-14 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl md:text-2xl mb-3 group-hover:scale-110 transition">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 text-sm md:text-lg">Pembayaran</h3>
                    <p class="text-xs text-gray-500 mt-1 hidden md:block">Upload bukti transfer</p>
                </div>
            </a>
            
            <a href="/student/documents" class="group bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition active:bg-gray-50">
                <div class="flex flex-col items-center text-center">
                    <div class="w-12 h-12 md:w-14 md:h-14 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-xl md:text-2xl mb-3 group-hover:scale-110 transition">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 text-sm md:text-lg">Dokumen</h3>
                    <p class="text-xs text-gray-500 mt-1 hidden md:block">Lengkapi berkas KK/Akta</p>
                </div>
            </a>

            <a href="/student/profile" class="group bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition active:bg-gray-50">
                <div class="flex flex-col items-center text-center">
                    <div class="w-12 h-12 md:w-14 md:h-14 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xl md:text-2xl mb-3 group-hover:scale-110 transition">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 text-sm md:text-lg">Biodata</h3>
                    <p class="text-xs text-gray-500 mt-1 hidden md:block">Cek data diri Anda</p>
                </div>
            </a>

        </div>

    <?php elseif (isset($student)): ?>
         <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Dashboard Siswa</h1>
            <p class="text-sm text-gray-600">Selamat datang, <span class="font-bold text-blue-600"><?= htmlspecialchars($student['full_name']) ?></span></p>
         </div>
         
         <div class="bg-white p-5 rounded-xl shadow border-l-4 border-blue-500 flex items-start space-x-4 mb-6">
             <div class="bg-blue-100 p-3 rounded-full text-blue-600">
                <i class="fa-solid fa-user-graduate text-xl"></i>
             </div>
             <div>
                 <h3 class="font-bold text-lg text-gray-800">Status Akademik: AKTIF</h3>
                 <p class="text-sm text-gray-600 mt-1">
                    Anda tercatat sebagai siswa aktif di kelas <span class="font-bold bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs"><?= $student['class_name'] ?? '-' ?></span>
                 </p>
                 <?php if (!empty($unpaid_bills) && $unpaid_bills > 0): ?>
                 <p class="text-sm text-red-600 mt-1 font-medium">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                    Anda memiliki <strong><?= $unpaid_bills ?></strong> tagihan yang belum dibayar.
                    <a href="/finance/billing?nis=<?= $student['nis'] ?>" class="underline">Bayar sekarang</a>
                 </p>
                 <?php endif; ?>
             </div>
         </div>

         <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
             <a href="/student/schedule" class="group bg-white p-4 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition text-center">
                 <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl mx-auto mb-2 group-hover:scale-110 transition">
                     <i class="fa-solid fa-calendar-days"></i>
                 </div>
                 <p class="font-bold text-gray-800 text-sm">Jadwal</p>
                 <p class="text-xs text-gray-500 mt-0.5">Pelajaran</p>
             </a>
             <a href="/student/attendance" class="group bg-white p-4 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition text-center">
                 <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xl mx-auto mb-2 group-hover:scale-110 transition">
                     <i class="fa-solid fa-calendar-check"></i>
                 </div>
                 <p class="font-bold text-gray-800 text-sm">Absensi</p>
                 <p class="text-xs text-gray-500 mt-0.5">Rekap Kehadiran</p>
             </a>
             <a href="/student/grades" class="group bg-white p-4 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition text-center">
                 <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center text-xl mx-auto mb-2 group-hover:scale-110 transition">
                     <i class="fa-solid fa-star"></i>
                 </div>
                 <p class="font-bold text-gray-800 text-sm">Nilai</p>
                 <p class="text-xs text-gray-500 mt-0.5">Akademik</p>
             </a>
             <a href="/finance/billing?nis=<?= $student['nis'] ?>" class="group bg-white p-4 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition text-center">
                 <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-xl mx-auto mb-2 group-hover:scale-110 transition">
                     <i class="fa-solid fa-file-invoice-dollar"></i>
                 </div>
                 <p class="font-bold text-gray-800 text-sm">Keuangan</p>
                 <p class="text-xs text-gray-500 mt-0.5">Tagihan SPP</p>
             </a>
         </div>
         
         <?php endif; ?>

</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
