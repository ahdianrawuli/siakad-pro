<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Resume Pendaftaran</h1>
        <p class="text-slate-500 text-sm mt-1">Ringkasan status pendaftaran Anda.</p>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <?php if (isset($candidate) && $candidate): ?>
    <div class="space-y-4">

        <!-- Info Pendaftaran -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2"><i class="fa-solid fa-id-card text-blue-500"></i> Informasi Pendaftaran</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div class="flex justify-between border-b border-slate-50 pb-2">
                    <span class="text-slate-500">No. Pendaftaran</span>
                    <span class="font-bold font-mono text-blue-600"><?= htmlspecialchars($candidate['registration_no']) ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-50 pb-2">
                    <span class="text-slate-500">Nama Lengkap</span>
                    <span class="font-semibold"><?= htmlspecialchars($candidate['full_name']) ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-50 pb-2">
                    <span class="text-slate-500">Jalur Pendaftaran</span>
                    <span class="font-semibold"><?= htmlspecialchars($candidate['track_name'] ?? 'Reguler') ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-50 pb-2">
                    <span class="text-slate-500">Tanggal Daftar</span>
                    <span class="font-semibold"><?= date('d M Y', strtotime($candidate['created_at'])) ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-50 pb-2">
                    <span class="text-slate-500">Status</span>
                    <?php
                    $sc = ['PENDING'=>'bg-amber-100 text-amber-700','PAID'=>'bg-blue-100 text-blue-700','VERIFIED'=>'bg-green-100 text-green-700','ACCEPTED'=>'bg-green-100 text-green-700','REJECTED'=>'bg-red-100 text-red-700'];
                    $s = $candidate['registration_status'];
                    ?>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= $sc[$s] ?? 'bg-slate-100 text-slate-600' ?>"><?= $s ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-50 pb-2">
                    <span class="text-slate-500">Asal Sekolah</span>
                    <span class="font-semibold"><?= htmlspecialchars($candidate['school_origin'] ?? '-') ?></span>
                </div>
            </div>
        </div>

        <!-- Progress Steps -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2"><i class="fa-solid fa-list-check text-blue-500"></i> Progress Pendaftaran</h3>
            <?php
            $steps = [
                ['label' => 'Formulir Terisi', 'done' => true],
                ['label' => 'Pembayaran Pendaftaran', 'done' => in_array($payment['status'] ?? '', ['VERIFIED'])],
                ['label' => 'Upload Dokumen', 'done' => count($docs) > 0],
                ['label' => 'Verifikasi Admin', 'done' => in_array($candidate['registration_status'], ['VERIFIED','ACCEPTED'])],
                ['label' => 'Diterima', 'done' => $candidate['registration_status'] === 'ACCEPTED'],
            ];
            ?>
            <div class="space-y-3">
                <?php foreach ($steps as $i => $step): ?>
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 <?= $step['done'] ? 'bg-green-500 text-white' : 'bg-slate-100 text-slate-400' ?>">
                        <?= $step['done'] ? '<i class="fa-solid fa-check"></i>' : ($i+1) ?>
                    </div>
                    <span class="text-sm <?= $step['done'] ? 'text-slate-800 font-semibold' : 'text-slate-400' ?>"><?= $step['label'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Pembayaran -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2"><i class="fa-solid fa-receipt text-blue-500"></i> Status Pembayaran</h3>
            <?php if ($payment): ?>
            <div class="text-sm space-y-2">
                <div class="flex justify-between"><span class="text-slate-500">Tanggal Bayar</span><span class="font-semibold"><?= date('d M Y', strtotime($payment['payment_date'])) ?></span></div>
                <div class="flex justify-between"><span class="text-slate-500">Jumlah</span><span class="font-semibold">Rp <?= number_format($payment['amount'],0,',','.') ?></span></div>
                <div class="flex justify-between"><span class="text-slate-500">Status</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= $payment['status']=='VERIFIED' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>"><?= $payment['status'] ?></span>
                </div>
            </div>
            <?php else: ?>
            <p class="text-sm text-slate-400">Belum ada data pembayaran. <a href="/student/payment" class="text-blue-600 font-semibold hover:underline">Upload bukti bayar →</a></p>
            <?php endif; ?>
        </div>

        <!-- Dokumen -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2"><i class="fa-solid fa-folder-open text-blue-500"></i> Dokumen Terupload (<?= count($docs) ?>)</h3>
            <?php if (empty($docs)): ?>
            <p class="text-sm text-slate-400">Belum ada dokumen. <a href="/student/documents" class="text-blue-600 font-semibold hover:underline">Upload dokumen →</a></p>
            <?php else: ?>
            <div class="space-y-2">
                <?php foreach ($docs as $d): ?>
                <div class="flex justify-between items-center text-sm border-b border-slate-50 pb-2">
                    <span class="font-semibold text-slate-700"><i class="fa-solid fa-file text-slate-400 mr-2"></i><?= htmlspecialchars($d['doc_type']) ?></span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= $d['status']=='APPROVED' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>"><?= $d['status'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <?php elseif (isset($student) && $student): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <p class="text-slate-600">Anda terdaftar sebagai siswa aktif di kelas <strong><?= htmlspecialchars($student['class_name'] ?? '-') ?></strong>.</p>
    </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
