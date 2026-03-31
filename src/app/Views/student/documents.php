<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-6">Kelengkapan Dokumen</h1>
    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6">
        <?php 
        $requiredDocs = [
            'KK' => ['label' => 'Kartu Keluarga', 'icon' => 'fa-users-rectangle'],
            'AKTA' => ['label' => 'Akta Kelahiran', 'icon' => 'fa-scroll'],
            'IJAZAH' => ['label' => 'Ijazah / SKL', 'icon' => 'fa-certificate'],
            'FOTO' => ['label' => 'Pas Foto Warna', 'icon' => 'fa-image']
        ];
        ?>

        <?php foreach ($requiredDocs as $code => $info): ?>
            <?php $doc = $documents[$code] ?? null; ?>
            <?php 
                $statusColor = 'bg-gray-100 text-gray-500';
                $statusText = 'Belum Ada';
                $borderColor = 'border-gray-200';
                $iconColor = 'text-gray-300';

                if($doc) {
                    if($doc['status'] == 'VALID') {
                        $statusColor = 'bg-green-100 text-green-700';
                        $statusText = 'Valid';
                        $borderColor = 'border-green-300 ring-1 ring-green-100';
                        $iconColor = 'text-green-500';
                    } elseif($doc['status'] == 'INVALID') {
                        $statusColor = 'bg-red-100 text-red-700';
                        $statusText = 'Ditolak';
                        $borderColor = 'border-red-300';
                        $iconColor = 'text-red-500';
                    } else {
                        $statusColor = 'bg-yellow-100 text-yellow-700';
                        $statusText = 'Verifikasi';
                        $borderColor = 'border-yellow-300';
                        $iconColor = 'text-yellow-500';
                    }
                }
            ?>
            
            <div class="bg-white rounded-xl shadow-sm border <?= $borderColor ?> flex flex-col h-full relative overflow-hidden transition hover:shadow-md">
                
                <div class="absolute top-0 right-0 p-2">
                     <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full <?= $statusColor ?>">
                        <?= $statusText ?>
                     </span>
                </div>

                <div class="p-4 flex-1 flex flex-col items-center text-center mt-2">
                    <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                        <i class="fa-solid <?= $info['icon'] ?> text-xl <?= $iconColor ?>"></i>
                    </div>
                    
                    <h4 class="font-bold text-gray-800 text-sm leading-tight mb-1"><?= $info['label'] ?></h4>
                    
                    <?php if($doc): ?>
                        <a href="/uploads/documents/<?= $doc['file_path'] ?>" target="_blank" class="text-xs text-blue-500 hover:text-blue-700 underline truncate max-w-[120px]">
                            Lihat File
                        </a>
                    <?php else: ?>
                        <p class="text-[10px] text-gray-400">Wajib diupload</p>
                    <?php endif; ?>
                </div>
                
                <div class="p-3 border-t border-gray-100 bg-gray-50">
                    <form action="/student/documents/store" method="POST" enctype="multipart/form-data">
                        <?= \App\Core\Csrf::input() ?>
                        <input type="hidden" name="doc_type" value="<?= $code ?>">
                        <input type="file" name="doc_file" id="file_<?= $code ?>" class="hidden" onchange="this.form.submit()" accept=".jpg,.jpeg,.png,.pdf">
                        
                        <label for="file_<?= $code ?>" class="cursor-pointer block w-full text-center py-2 rounded-lg text-xs font-bold transition
                            <?= $doc ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-100' : 'bg-blue-600 text-white hover:bg-blue-700 shadow-sm' ?>">
                            <?= $doc ? '<i class="fa-solid fa-pen mr-1"></i> Ganti' : '<i class="fa-solid fa-upload mr-1"></i> Upload' ?>
                        </label>
                    </form>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
