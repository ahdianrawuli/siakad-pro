<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$name = $student['full_name'] ?? $student['name'] ?? ($candidate['full_name'] ?? 'Santri');
$pageTitle    = 'Data Santri';
$pageSubtitle = 'Biodata lengkap dan informasi pribadi';
$pageBadgeIcon = 'fa-address-card';
$infoItems    = [
    'Halaman ini menampilkan biodata lengkap Anda.',
    'Data ditampilkan sesuai yang tercatat di sistem pesantren.',
    'Jika ada data yang tidak sesuai, hubungi admin atau wali kelas.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php \App\Core\Session::flash(); ?>

    <?php if (isset($is_candidate) && $is_candidate): ?>
    <!-- ── MODE CALON SANTRI ── -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <!-- Sidebar Profil -->
        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="h-20 bg-gradient-to-r from-green-700 to-emerald-600"></div>
                <div class="px-5 pb-5 -mt-10 text-center">
                    <div class="w-20 h-20 bg-white rounded-full border-4 border-white shadow-md flex items-center justify-center mx-auto mb-3">
                        <span class="text-3xl font-extrabold text-green-700"><?= strtoupper(substr($name, 0, 1)) ?></span>
                    </div>
                    <h3 class="font-bold text-slate-800"><?= htmlspecialchars($name) ?></h3>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">NISN: <?= htmlspecialchars($candidate['nisn'] ?? '-') ?></p>
                    <span class="inline-block mt-2 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                        <i class="fa-solid fa-road mr-1"></i><?= htmlspecialchars($candidate['track_name'] ?? 'Reguler') ?>
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-bold text-slate-400 uppercase mb-3">Akun Login</p>
                <div class="space-y-2 text-sm">
                    <div class="bg-slate-50 rounded-xl px-3 py-2">
                        <p class="text-xs text-slate-400">Username</p>
                        <p class="font-mono font-bold text-slate-800"><?= htmlspecialchars($candidate['account_username'] ?? '-') ?></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl px-3 py-2">
                        <p class="text-xs text-slate-400">Email</p>
                        <p class="font-medium text-slate-700 truncate"><?= htmlspecialchars($candidate['account_email'] ?? '-') ?></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl px-3 py-2">
                        <p class="text-xs text-slate-400">Tanggal Daftar</p>
                        <p class="font-medium text-slate-700"><?= !empty($candidate['created_at']) ? date('d F Y', strtotime($candidate['created_at'])) : '-' ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-sm text-amber-800 flex items-start gap-2">
                <i class="fa-solid fa-circle-info mt-0.5 shrink-0"></i>
                <p>Jika ada kesalahan data, hubungi panitia melalui WhatsApp atau Pusat Bantuan.</p>
            </div>
        </div>

        <!-- Konten Biodata -->
        <div class="md:col-span-2 space-y-5">

            <!-- Informasi Pribadi -->
            <?php $fields = [
                ['NIK',           $candidate['nik'] ?? '-'],
                ['Tempat Lahir',  $candidate['birth_place'] ?? '-'],
                ['Tanggal Lahir', !empty($candidate['birth_date']) ? date('d-m-Y', strtotime($candidate['birth_date'])) : '-'],
                ['Jenis Kelamin', ($candidate['gender'] ?? '') == 'L' ? 'Laki-laki' : 'Perempuan'],
                ['No. WhatsApp',  $candidate['phone'] ?? '-'],
            ]; ?>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h4 class="font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                    <i class="fa-solid fa-address-card text-green-600"></i> Informasi Pribadi
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php foreach ($fields as [$lbl, $val]): ?>
                    <div class="bg-slate-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-slate-400 mb-0.5"><?= $lbl ?></p>
                        <p class="font-semibold text-slate-800 text-sm"><?= htmlspecialchars($val) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Data Orang Tua & Sekolah -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h4 class="font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                    <i class="fa-solid fa-users text-blue-500"></i> Data Orang Tua & Sekolah
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                    <div class="bg-slate-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-slate-400 mb-0.5">Nama Ayah</p>
                        <p class="font-semibold text-slate-800 text-sm"><?= htmlspecialchars($candidate['father_name'] ?? '-') ?></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-slate-400 mb-0.5">Nama Ibu</p>
                        <p class="font-semibold text-slate-800 text-sm"><?= htmlspecialchars($candidate['mother_name'] ?? '-') ?></p>
                    </div>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                    <p class="text-xs text-blue-600 font-bold mb-0.5">Asal Sekolah</p>
                    <p class="font-bold text-slate-800"><?= htmlspecialchars($candidate['previous_school'] ?? '-') ?></p>
                    <p class="text-xs text-slate-500"><?= htmlspecialchars($candidate['school_address'] ?? '') ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php elseif (isset($student)): ?>
    <!-- ── MODE SISWA AKTIF ── -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <!-- Sidebar Profil -->
        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="h-20 bg-gradient-to-r from-green-700 to-emerald-600"></div>
                <div class="px-5 pb-5 -mt-10 text-center">
                    <div class="w-20 h-20 bg-white rounded-full border-4 border-white shadow-md flex items-center justify-center mx-auto mb-3">
                        <span class="text-3xl font-extrabold text-green-700"><?= strtoupper(substr($name, 0, 1)) ?></span>
                    </div>
                    <h3 class="font-bold text-slate-800"><?= htmlspecialchars($name) ?></h3>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">NIS: <?= htmlspecialchars($student['nis'] ?? '-') ?></p>
                    <div class="flex flex-wrap justify-center gap-2 mt-2">
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full"><?= htmlspecialchars($student['class_name'] ?? '-') ?></span>
                        <?php if (!empty($student['dorm_name'])): ?>
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded-full"><?= htmlspecialchars($student['dorm_name']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Identitas Singkat -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-bold text-slate-400 uppercase mb-3">Identitas</p>
                <?php $ids = [
                    ['NISN', $student['nisn'] ?? '-'],
                    ['NIK',  $student['nik']  ?? '-'],
                    ['No. KK', $student['no_kk'] ?? '-'],
                ]; foreach ($ids as [$lbl, $val]): ?>
                <div class="bg-slate-50 rounded-xl px-3 py-2 mb-2 last:mb-0">
                    <p class="text-xs text-slate-400"><?= $lbl ?></p>
                    <p class="font-mono font-semibold text-slate-800 text-sm"><?= htmlspecialchars($val) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Konten Biodata -->
        <div class="md:col-span-2 space-y-5">

            <!-- Data Pribadi -->
            <?php $personal = [
                ['Nama Lengkap',   $student['full_name'] ?? $student['name'] ?? '-'],
                ['Tempat Lahir',   $student['place_of_birth'] ?? '-'],
                ['Tanggal Lahir',  !empty($student['date_of_birth']) ? date('d-m-Y', strtotime($student['date_of_birth'])) : '-'],
                ['Jenis Kelamin',  ($student['gender'] ?? '') == 'L' ? 'Laki-laki' : 'Perempuan'],
                ['Alamat',         $student['address'] ?? '-'],
                ['Anak Ke',        $student['birth_order'] ?? '-'],
                ['Jml Bersaudara', $student['number_of_siblings'] ?? '-'],
            ]; ?>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h4 class="font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                    <i class="fa-solid fa-user text-green-600"></i> Data Pribadi
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php foreach ($personal as [$lbl, $val]): ?>
                    <div class="bg-slate-50 rounded-xl px-4 py-3 <?= $lbl === 'Alamat' ? 'sm:col-span-2' : '' ?>">
                        <p class="text-xs text-slate-400 mb-0.5"><?= $lbl ?></p>
                        <p class="font-semibold text-slate-800 text-sm"><?= htmlspecialchars($val) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Riwayat Pendidikan -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h4 class="font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                    <i class="fa-solid fa-school text-blue-500"></i> Riwayat Pendidikan
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php $edu = [
                        ['Sekolah Asal',      $student['school_origin'] ?? '-'],
                        ['NPSN Sekolah Asal', $student['school_origin_npsn'] ?? '-'],
                    ]; foreach ($edu as [$lbl, $val]): ?>
                    <div class="bg-slate-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-slate-400 mb-0.5"><?= $lbl ?></p>
                        <p class="font-semibold text-slate-800 text-sm"><?= htmlspecialchars($val) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Data Orang Tua -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <?php foreach ([
                    ['Ayah', 'fa-user-tie', 'indigo', $father ?? []],
                    ['Ibu',  'fa-person-dress', 'pink', $mother ?? []],
                ] as [$label, $icon, $color, $parent]): ?>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <h4 class="font-bold text-slate-700 mb-3 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="fa-solid <?= $icon ?> text-<?= $color ?>-500"></i> <?= $label ?>
                    </h4>
                    <div class="space-y-2">
                        <?php $pFields = [
                            ['Nama',       $parent['name'] ?? '-'],
                            ['NIK',        $parent['nik'] ?? '-'],
                            ['No. HP',     $parent['phone_number'] ?? '-'],
                            ['Pekerjaan',  $parent['job'] ?? '-'],
                            ['Pendidikan', $parent['education'] ?? '-'],
                        ]; foreach ($pFields as [$lbl, $val]): ?>
                        <div class="flex justify-between items-start gap-2 text-sm border-b border-slate-50 pb-1.5 last:border-0">
                            <span class="text-slate-400 shrink-0"><?= $lbl ?></span>
                            <span class="font-medium text-slate-800 text-right"><?= htmlspecialchars($val) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
