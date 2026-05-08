<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Resume Pendaftaran';
$pageSubtitle = 'Ringkasan status dan progress pendaftaran Anda';
$pageBadgeIcon = 'fa-file-lines';
$infoItems    = [
    'Halaman ini menampilkan ringkasan lengkap status pendaftaran Anda.',
    'Ikuti setiap tahap progress hingga status "Diterima".',
    'Hubungi panitia PPDB jika ada kendala dalam proses pendaftaran.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php \App\Core\Session::flash(); ?>

    <?php if (isset($candidate) && $candidate): ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Kiri: Profil + Status -->
        <div class="space-y-5">
            <!-- Kartu Profil -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="h-16 bg-gradient-to-r from-green-700 to-emerald-600"></div>
                <div class="px-5 pb-5 -mt-8 text-center">
                    <div class="w-16 h-16 bg-white rounded-full border-4 border-white shadow-md flex items-center justify-center mx-auto mb-3">
                        <span class="text-2xl font-extrabold text-green-700"><?= strtoupper(substr($candidate['full_name'], 0, 1)) ?></span>
                    </div>
                    <h3 class="font-bold text-slate-800"><?= htmlspecialchars($candidate['full_name']) ?></h3>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">REG-<?= $candidate['id'] ?></p>
                    <?php
                    $sc = ['PENDING'=>'bg-amber-100 text-amber-700','PAID'=>'bg-blue-100 text-blue-700','VERIFIED'=>'bg-green-100 text-green-700','ACCEPTED'=>'bg-green-100 text-green-700','REJECTED'=>'bg-red-100 text-red-700'];
                    $s  = $candidate['registration_status'];
                    ?>
                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-bold <?= $sc[$s] ?? 'bg-slate-100 text-slate-600' ?>"><?= $s ?></span>
                </div>
            </div>

            <!-- Info Singkat -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3 text-sm">
                <?php $rows = [
                    ['No. Daftar',   $candidate['registration_no'] ?? '-', 'font-mono text-green-700 font-bold'],
                    ['Jalur',        $candidate['track_name'] ?? 'Reguler', ''],
                    ['Tanggal Daftar', date('d M Y', strtotime($candidate['created_at'])), ''],
                    ['Asal Sekolah', $candidate['school_origin'] ?? '-', ''],
                ]; foreach ($rows as [$lbl, $val, $cls]): ?>
                <div class="flex justify-between items-start gap-2 border-b border-slate-50 pb-2 last:border-0 last:pb-0">
                    <span class="text-slate-400 shrink-0"><?= $lbl ?></span>
                    <span class="font-semibold text-slate-800 text-right <?= $cls ?>"><?= htmlspecialchars($val) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Kanan: Progress + Detail -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Progress Steps -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h4 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-green-600"></i> Progress Pendaftaran
                </h4>
                <?php
                $steps = [
                    ['label' => 'Formulir Terisi',         'done' => true,                                                                    'icon' => 'fa-pen-to-square'],
                    ['label' => 'Pembayaran Pendaftaran',   'done' => in_array($payment['status'] ?? '', ['VERIFIED']),                        'icon' => 'fa-money-bill-wave'],
                    ['label' => 'Upload Dokumen',           'done' => count($docs ?? []) > 0,                                                  'icon' => 'fa-folder-open'],
                    ['label' => 'Verifikasi Admin',         'done' => in_array($candidate['registration_status'], ['VERIFIED','ACCEPTED']),    'icon' => 'fa-user-check'],
                    ['label' => 'Diterima',                 'done' => $candidate['registration_status'] === 'ACCEPTED',                        'icon' => 'fa-circle-check'],
                ];
                $doneCount = count(array_filter($steps, fn($s) => $s['done']));
                $pct = round($doneCount / count($steps) * 100);
                ?>
                <div class="mb-4">
                    <div class="flex justify-between text-xs text-slate-500 mb-1">
                        <span><?= $doneCount ?>/<?= count($steps) ?> tahap selesai</span>
                        <span class="font-bold text-green-700"><?= $pct ?>%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full transition-all" style="width: <?= $pct ?>%"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <?php foreach ($steps as $i => $step): ?>
                    <div class="flex items-center gap-3 p-3 rounded-xl <?= $step['done'] ? 'bg-green-50' : 'bg-slate-50' ?>">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                            <?= $step['done'] ? 'bg-green-600 text-white' : 'bg-slate-200 text-slate-400' ?>">
                            <?= $step['done'] ? '<i class="fa-solid fa-check"></i>' : ($i + 1) ?>
                        </div>
                        <div class="flex-1">
                            <span class="text-sm font-semibold <?= $step['done'] ? 'text-slate-800' : 'text-slate-400' ?>"><?= $step['label'] ?></span>
                        </div>
                        <i class="fa-solid <?= $step['icon'] ?> text-sm <?= $step['done'] ? 'text-green-500' : 'text-slate-300' ?>"></i>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pembayaran -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h4 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-orange-500"></i> Status Pembayaran
                </h4>
                <?php if ($payment ?? null): ?>
                <div class="grid grid-cols-3 gap-3 text-sm">
                    <div class="bg-slate-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-slate-400 mb-1">Tanggal</p>
                        <p class="font-semibold text-slate-800"><?= date('d M Y', strtotime($payment['payment_date'])) ?></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-slate-400 mb-1">Jumlah</p>
                        <p class="font-semibold text-slate-800">Rp <?= number_format($payment['amount'], 0, ',', '.') ?></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-slate-400 mb-1">Status</p>
                        <span class="text-xs font-bold px-2 py-1 rounded-full <?= $payment['status'] === 'VERIFIED' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>"><?= $payment['status'] ?></span>
                    </div>
                </div>
                <?php else: ?>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                    <p class="text-sm text-amber-800">Belum ada pembayaran.
                        <a href="/student/payment" class="font-bold underline">Upload bukti bayar →</a>
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Dokumen -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h4 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-folder-open text-blue-500"></i> Dokumen (<?= count($docs ?? []) ?> terupload)
                </h4>
                <?php if (empty($docs ?? [])): ?>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center gap-3">
                    <i class="fa-solid fa-circle-info text-blue-500"></i>
                    <p class="text-sm text-blue-800">Belum ada dokumen.
                        <a href="/student/documents" class="font-bold underline">Upload dokumen →</a>
                    </p>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-2 gap-2">
                    <?php foreach ($docs as $d): ?>
                    <div class="flex items-center justify-between bg-slate-50 rounded-xl px-3 py-2 text-sm">
                        <span class="font-medium text-slate-700 flex items-center gap-2">
                            <i class="fa-solid fa-file text-slate-400"></i>
                            <?= htmlspecialchars($d['doc_type']) ?>
                        </span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full <?= ($d['status'] ?? '') === 'APPROVED' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>"><?= $d['status'] ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <?php elseif (isset($student) && $student): ?>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xl">
            <i class="fa-solid fa-user-graduate"></i>
        </div>
        <div>
            <p class="font-bold text-slate-800"><?= htmlspecialchars($student['full_name']) ?></p>
            <p class="text-sm text-slate-500">Siswa aktif — Kelas <strong><?= htmlspecialchars($student['class_name'] ?? '-') ?></strong></p>
        </div>
    </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
