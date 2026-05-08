<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">

    <?php \App\Core\Session::flash(); ?>

    <?php if (isset($candidate)): ?>
    <!-- ══════════════════════════════════════════
         MODE CALON SANTRI (PPDB)
    ══════════════════════════════════════════ -->

    <!-- Hero Welcome -->
    <div class="relative bg-gradient-to-br from-green-700 via-green-600 to-emerald-500 rounded-2xl overflow-hidden mb-6 shadow-lg shadow-green-200">
        <div class="absolute inset-0 opacity-10">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="g" width="60" height="60" patternUnits="userSpaceOnUse"><circle cx="30" cy="30" r="20" fill="none" stroke="white" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(#g)"/></svg>
        </div>
        <div class="relative z-10 p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <p class="text-green-200 text-sm font-medium mb-1">Selamat datang,</p>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white"><?= htmlspecialchars($candidate['full_name']) ?></h1>
                <p class="text-green-100 text-sm mt-1">
                    <i class="fa-solid fa-road mr-1"></i> Jalur <?= htmlspecialchars($candidate['track_name'] ?? 'Reguler') ?>
                    &nbsp;·&nbsp;
                    <i class="fa-solid fa-hashtag mr-1"></i> REG-<?= $candidate['id'] ?>
                </p>
            </div>
            <div class="shrink-0">
                <?php
                $rawStatus = strtoupper(trim($candidate['registration_status'] ?? ''));
                $statusMap = [
                    'APPROVED' => ['LULUS SELEKSI',       'bg-white text-green-700'],
                    'LULUS'    => ['LULUS SELEKSI',       'bg-white text-green-700'],
                    'DITERIMA' => ['LULUS SELEKSI',       'bg-white text-green-700'],
                    'ACCEPTED' => ['LULUS SELEKSI',       'bg-white text-green-700'],
                    'PAID'     => ['SEDANG DIVERIFIKASI', 'bg-white text-blue-700'],
                    'VERIFIKASI'=>['SEDANG DIVERIFIKASI', 'bg-white text-blue-700'],
                    'PENDING'  => ['MENUNGGU PEMBAYARAN', 'bg-white/20 text-white border border-white/30'],
                    ''         => ['MENUNGGU PEMBAYARAN', 'bg-white/20 text-white border border-white/30'],
                ];
                [$statusLabel, $statusClass] = $statusMap[$rawStatus] ?? [$rawStatus, 'bg-white/20 text-white'];
                ?>
                <span class="inline-block px-4 py-2 rounded-xl text-xs font-extrabold tracking-wide <?= $statusClass ?>">
                    <?= $statusLabel ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <?php if (isset($progress)): ?>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
        <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-4">Progress Pendaftaran</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <?php $steps = [
                ['Pendaftaran', $progress['registered'], 'fa-pen-to-square', 'green'],
                ['Pembayaran',  $progress['paid'],       'fa-money-bill-wave','blue'],
                ['Dokumen',     $progress['document'],   'fa-folder-open',   'orange'],
                ['Verifikasi',  $progress['verified'],   'fa-user-check',    'purple'],
            ]; foreach ($steps as $i => [$lbl, $done, $icon, $color]): ?>
            <div class="flex items-center gap-3 p-3 rounded-xl <?= $done ? 'bg-green-50 border border-green-200' : 'bg-slate-50 border border-slate-200' ?>">
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 <?= $done ? 'bg-green-600 text-white' : 'bg-slate-200 text-slate-400' ?>">
                    <i class="fa-solid <?= $done ? 'fa-check' : $icon ?> text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-bold <?= $done ? 'text-green-800' : 'text-slate-500' ?>"><?= $lbl ?></p>
                    <p class="text-[10px] <?= $done ? 'text-green-600' : 'text-slate-400' ?>"><?= $done ? 'Selesai' : 'Belum' ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- CTA berdasarkan status -->
    <?php if ($rawStatus === 'PENDING' || $rawStatus === ''): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-circle-exclamation text-lg"></i>
            </div>
            <div>
                <p class="font-bold text-amber-800">Pembayaran Belum Dilakukan</p>
                <p class="text-sm text-amber-700 mt-0.5">Silakan lakukan pembayaran biaya pendaftaran untuk melanjutkan ke tahap verifikasi.</p>
            </div>
        </div>
        <a href="/student/payment" class="shrink-0 bg-amber-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-amber-700 transition text-sm">
            <i class="fa-solid fa-wallet mr-2"></i> Bayar Sekarang
        </a>
    </div>
    <?php elseif ($rawStatus === 'PAID' || $rawStatus === 'VERIFIKASI'): ?>
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-6 flex items-start gap-3">
        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center shrink-0 animate-pulse">
            <i class="fa-solid fa-hourglass-half text-lg"></i>
        </div>
        <div>
            <p class="font-bold text-blue-800">Sedang Diverifikasi</p>
            <p class="text-sm text-blue-700 mt-0.5">Panitia PPDB sedang memverifikasi data Anda. Proses ini memakan waktu maksimal 1×24 jam.</p>
        </div>
    </div>
    <?php elseif (in_array($rawStatus, ['APPROVED','LULUS','DITERIMA','ACCEPTED'])): ?>
    <div class="bg-green-50 border border-green-200 rounded-2xl p-5 mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-green-100 text-green-600 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-circle-check text-lg"></i>
            </div>
            <div>
                <p class="font-bold text-green-800">Selamat! Anda Diterima</p>
                <p class="text-sm text-green-700 mt-0.5">Anda dinyatakan diterima sebagai santri baru. Silakan cetak kartu ujian.</p>
            </div>
        </div>
        <a href="/student/exam-card" class="shrink-0 bg-green-700 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-green-800 transition text-sm">
            <i class="fa-solid fa-print mr-2"></i> Cetak Kartu Ujian
        </a>
    </div>
    <?php endif; ?>

    <!-- Menu Aksi Cepat -->
    <div class="grid grid-cols-3 gap-3">
        <?php $menus = [
            ['/student/payment',   'fa-file-invoice-dollar', 'blue',   'Pembayaran',  'Upload bukti transfer'],
            ['/student/documents', 'fa-folder-open',         'orange', 'Dokumen',     'Lengkapi berkas'],
            ['/student/profile',   'fa-address-card',        'purple', 'Data Santri', 'Lihat biodata'],
        ]; foreach ($menus as [$href, $icon, $color, $title, $desc]): ?>
        <a href="<?= $href ?>" class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-4 md:p-5 hover:shadow-md hover:border-<?= $color ?>-300 transition text-center">
            <div class="w-12 h-12 bg-<?= $color ?>-100 text-<?= $color ?>-600 rounded-xl flex items-center justify-center text-xl mx-auto mb-3 group-hover:scale-110 transition">
                <i class="fa-solid <?= $icon ?>"></i>
            </div>
            <p class="font-bold text-slate-800 text-sm"><?= $title ?></p>
            <p class="text-xs text-slate-400 mt-0.5 hidden md:block"><?= $desc ?></p>
        </a>
        <?php endforeach; ?>
    </div>

    <?php elseif (isset($student)): ?>
    <!-- ══════════════════════════════════════════
         MODE SISWA AKTIF
    ══════════════════════════════════════════ -->

    <!-- Hero Welcome -->
    <div class="relative bg-gradient-to-br from-green-700 via-green-600 to-emerald-500 rounded-2xl overflow-hidden mb-6 shadow-lg shadow-green-200">
        <div class="absolute inset-0 opacity-10">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="g2" width="60" height="60" patternUnits="userSpaceOnUse"><circle cx="30" cy="30" r="20" fill="none" stroke="white" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(#g2)"/></svg>
        </div>
        <div class="relative z-10 p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <p class="text-green-200 text-sm font-medium mb-1">Selamat datang,</p>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white"><?= htmlspecialchars($student['full_name']) ?></h1>
                <p class="text-green-100 text-sm mt-1">
                    <i class="fa-solid fa-graduation-cap mr-1"></i> Kelas <?= htmlspecialchars($student['class_name'] ?? '-') ?>
                    &nbsp;·&nbsp;
                    <i class="fa-solid fa-id-card mr-1"></i> NIS: <?= htmlspecialchars($student['nis'] ?? '-') ?>
                </p>
            </div>
            <div class="shrink-0">
                <span class="inline-flex items-center gap-2 bg-white/20 border border-white/30 text-white px-4 py-2 rounded-xl text-xs font-bold">
                    <span class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></span> AKTIF
                </span>
            </div>
        </div>
    </div>

    <!-- Alert tagihan -->
    <?php if (!empty($unpaid_bills) && $unpaid_bills > 0): ?>
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-red-100 text-red-600 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <p class="text-sm text-red-800 font-medium">
                Anda memiliki <strong><?= $unpaid_bills ?></strong> tagihan yang belum dibayar.
            </p>
        </div>
        <a href="/student/payment" class="shrink-0 bg-red-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-red-700 transition">Bayar</a>
    </div>
    <?php endif; ?>

    <!-- Menu Aksi Cepat -->
    <h2 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Menu Utama</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <?php $mainMenus = [
            ['/student/schedule',   'fa-calendar-days',        'blue',   'Jadwal',      'Pelajaran'],
            ['/student/attendance', 'fa-clipboard-check',      'green',  'Absensi',     'Rekap Kehadiran'],
            ['/student/grades',     'fa-star',                 'yellow', 'Nilai',       'Akademik'],
            ['/student/payment',    'fa-file-invoice-dollar',  'orange', 'Keuangan',    'Tagihan SPP'],
        ]; foreach ($mainMenus as [$href, $icon, $color, $title, $desc]): ?>
        <a href="<?= $href ?>" class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:shadow-md hover:border-<?= $color ?>-300 transition text-center">
            <div class="w-12 h-12 bg-<?= $color ?>-100 text-<?= $color ?>-600 rounded-xl flex items-center justify-center text-xl mx-auto mb-2 group-hover:scale-110 transition">
                <i class="fa-solid <?= $icon ?>"></i>
            </div>
            <p class="font-bold text-slate-800 text-sm"><?= $title ?></p>
            <p class="text-xs text-slate-400 mt-0.5"><?= $desc ?></p>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Menu Lainnya -->
    <h2 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Lainnya</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <?php $otherMenus = [
            ['/student/boarding',        'fa-house',                'teal',   'Asrama',       'Info Kamar'],
            ['/student/health',          'fa-heart-pulse',          'pink',   'Kesehatan',    'Riwayat Poskestren'],
            ['/student/discipline',      'fa-triangle-exclamation', 'red',    'Kedisiplinan', 'Pelanggaran & Prestasi'],
            ['/student/extracurricular', 'fa-person-running',       'indigo', 'Ekskul',       'Kegiatan Ekstra'],
            ['/student/announcements',   'fa-bullhorn',             'cyan',   'Pengumuman',   'Info Pesantren'],
            ['/student/letter',          'fa-envelope',             'violet', 'Surat',        'Keterangan'],
            ['/student/profile',         'fa-address-card',         'slate',  'Data Santri',  'Biodata Lengkap'],
            ['/student/resume',          'fa-file-lines',           'gray',   'Resume',       'Status Pendaftaran'],
        ]; foreach ($otherMenus as [$href, $icon, $color, $title, $desc]): ?>
        <a href="<?= $href ?>" class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:shadow-md transition text-center">
            <div class="w-10 h-10 bg-<?= $color ?>-100 text-<?= $color ?>-600 rounded-xl flex items-center justify-center text-lg mx-auto mb-2 group-hover:scale-110 transition">
                <i class="fa-solid <?= $icon ?>"></i>
            </div>
            <p class="font-bold text-slate-800 text-xs"><?= $title ?></p>
            <p class="text-[10px] text-slate-400 mt-0.5 hidden md:block"><?= $desc ?></p>
        </a>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>

</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
