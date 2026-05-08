<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Kelengkapan Dokumen';
$pageSubtitle = 'Upload dan pantau status dokumen pendaftaran Anda';
$pageBadge    = 'Dokumen Terupload: ' . count(array_filter($documents ?? [], fn($d) => $d !== null));
$pageBadgeIcon = 'fa-folder-open';
$infoItems    = [
    'Upload dokumen yang diperlukan untuk melengkapi berkas pendaftaran.',
    'Dokumen yang diterima: JPG, PNG, atau PDF (maks. 2MB).',
    'Status "Valid" berarti dokumen telah diverifikasi oleh admin.',
    'Status "Verifikasi" berarti dokumen sedang dalam proses pengecekan.',
    'Klik "Ganti" untuk mengganti dokumen yang sudah diupload.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php \App\Core\Session::flash(); ?>

    <?php
    $requiredDocs = [
        'KK'     => ['label' => 'Kartu Keluarga',  'icon' => 'fa-users-rectangle', 'desc' => 'Scan/foto KK yang jelas'],
        'AKTA'   => ['label' => 'Akta Kelahiran',   'icon' => 'fa-scroll',          'desc' => 'Akta kelahiran asli/fotokopi'],
        'IJAZAH' => ['label' => 'Ijazah / SKL',     'icon' => 'fa-certificate',     'desc' => 'Ijazah atau Surat Keterangan Lulus'],
        'FOTO'   => ['label' => 'Pas Foto Warna',   'icon' => 'fa-image',           'desc' => 'Foto terbaru latar merah/biru'],
    ];
    $uploaded = count(array_filter($documents ?? [], fn($d) => $d !== null));
    $total    = count($requiredDocs);
    $pct      = $total > 0 ? round($uploaded / $total * 100) : 0;
    ?>

    <!-- Progress bar -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-semibold text-slate-700">Progress Kelengkapan</span>
            <span class="text-sm font-bold text-green-700"><?= $uploaded ?>/<?= $total ?> dokumen</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-2.5">
            <div class="bg-green-600 h-2.5 rounded-full transition-all" style="width: <?= $pct ?>%"></div>
        </div>
        <p class="text-xs text-slate-400 mt-1"><?= $pct ?>% selesai</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php foreach ($requiredDocs as $code => $info):
            $doc = $documents[$code] ?? null;
            if ($doc) {
                $statusColor  = ['VALID'=>'bg-green-100 text-green-700','INVALID'=>'bg-red-100 text-red-700'][$doc['status']] ?? 'bg-yellow-100 text-yellow-700';
                $statusText   = ['VALID'=>'Valid','INVALID'=>'Ditolak'][$doc['status']] ?? 'Verifikasi';
                $borderColor  = ['VALID'=>'border-green-300','INVALID'=>'border-red-300'][$doc['status']] ?? 'border-yellow-300';
                $iconBg       = ['VALID'=>'bg-green-100 text-green-600','INVALID'=>'bg-red-100 text-red-500'][$doc['status']] ?? 'bg-yellow-100 text-yellow-600';
            } else {
                $statusColor = 'bg-slate-100 text-slate-500'; $statusText = 'Belum Ada';
                $borderColor = 'border-slate-200'; $iconBg = 'bg-slate-100 text-slate-400';
            }
        ?>
        <div class="bg-white rounded-2xl border <?= $borderColor ?> shadow-sm flex flex-col overflow-hidden hover:shadow-md transition">
            <div class="p-5 flex-1">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-11 h-11 <?= $iconBg ?> rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid <?= $info['icon'] ?>"></i>
                    </div>
                    <span class="text-[10px] uppercase font-bold px-2 py-1 rounded-full <?= $statusColor ?>"><?= $statusText ?></span>
                </div>
                <h4 class="font-bold text-slate-800 text-sm mb-1"><?= $info['label'] ?></h4>
                <p class="text-xs text-slate-400"><?= $info['desc'] ?></p>
                <?php if ($doc): ?>
                <a href="/uploads/documents/<?= $doc['file_path'] ?>" target="_blank"
                   class="inline-flex items-center gap-1 mt-3 text-xs text-green-700 font-semibold hover:underline">
                    <i class="fa-solid fa-eye"></i> Lihat File
                </a>
                <?php endif; ?>
            </div>
            <div class="px-4 pb-4">
                <form action="/student/documents/store" method="POST" enctype="multipart/form-data">
                    <?= \App\Core\Csrf::input() ?>
                    <input type="hidden" name="doc_type" value="<?= $code ?>">
                    <input type="file" name="doc_file" id="file_<?= $code ?>" class="hidden"
                           onchange="this.form.submit()" accept=".jpg,.jpeg,.png,.pdf">
                    <label for="file_<?= $code ?>"
                           class="cursor-pointer flex items-center justify-center gap-2 w-full py-2 rounded-xl text-xs font-bold transition
                           <?= $doc ? 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200' : 'bg-green-700 text-white hover:bg-green-800' ?>">
                        <i class="fa-solid <?= $doc ? 'fa-pen' : 'fa-upload' ?>"></i>
                        <?= $doc ? 'Ganti File' : 'Upload' ?>
                    </label>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
