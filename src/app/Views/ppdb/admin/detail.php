<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-4">
            <a href="/ppdb/registrations" class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition shrink-0">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Detail Pendaftar</h3>
                <p class="text-slate-500 text-sm mt-0.5 font-medium">
                    <?= htmlspecialchars($candidate['full_name']) ?>
                    &bull; <span class="font-mono text-xs"><?= $candidate['registration_no'] ?></span>
                </p>
            </div>
        </div>
        <?php
        $statusBadge = match($candidate['registration_status']) {
            'ACCEPTED' => 'bg-green-50 text-green-700 border-green-200',
            'REJECTED' => 'bg-red-50 text-red-700 border-red-200',
            'PAID'     => 'bg-blue-50 text-blue-700 border-blue-200',
            'VERIFIED' => 'bg-teal-50 text-teal-700 border-teal-200',
            default    => 'bg-yellow-50 text-yellow-700 border-yellow-200',
        };
        ?>
        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold border <?= $statusBadge ?>">
            <?= $candidate['registration_status'] ?>
        </span>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white rounded-lg shadow p-6 border-t-4 border-blue-600">
                <div class="text-center mb-4">
                    <div class="w-20 h-20 bg-gray-200 rounded-full mx-auto flex items-center justify-center text-2xl font-bold text-gray-500">
                        <?= substr($candidate['full_name'], 0, 1) ?>
                    </div>
                    <h2 class="text-xl font-bold mt-2"><?= $candidate['full_name'] ?></h2>
                    <p class="text-sm text-gray-500"><?= $candidate['registration_no'] ?></p>
                </div>
                <div class="border-t pt-4 text-sm space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jalur:</span>
                        <span class="font-bold"><?= $candidate['level'] ?> - <?= $candidate['track_name'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">JK:</span>
                        <span><?= $candidate['gender'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Lokasi Ujian:</span>
                        <span class="font-bold text-purple-600"><?= $candidate['exam_location'] ?></span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="font-bold text-gray-700 mb-4 border-b pb-2">Status Akhir</h4>
                <div class="text-center mb-4">
                    <span class="text-2xl font-bold <?= $candidate['registration_status'] == 'ACCEPTED' ? 'text-green-600' : 'text-gray-600' ?>">
                        <?= $candidate['registration_status'] ?>
                    </span>
                </div>
                <form action="/ppdb/verify/graduation" method="POST">
                    <?= \App\Core\Csrf::input() ?>
                    <input type="hidden" name="candidate_id" value="<?= $candidate['id'] ?>">
                    
                    <label class="block text-xs font-bold text-gray-500 mb-1">Update Status:</label>
                    <select name="status" class="w-full p-2 border rounded mb-3 text-sm">
                        <option value="PENDING" <?= $candidate['registration_status']=='PENDING'?'selected':'' ?>>PENDING (Menunggu)</option>
                        <option value="ACCEPTED" <?= $candidate['registration_status']=='ACCEPTED'?'selected':'' ?>>LULUS (Diterima)</option>
                        <option value="REJECTED" <?= $candidate['registration_status']=='REJECTED'?'selected':'' ?>>TIDAK LULUS</option>
                    </select>
                    <button type="submit" class="w-full bg-blue-800 text-white py-2 rounded text-sm hover:bg-blue-900">
                        <i class="fa-solid fa-save mr-1"></i> Simpan Keputusan
                    </button>
                </form>
		<?php if($candidate['registration_status'] == 'ACCEPTED'): ?>
                    <hr class="my-4">
                    <form action="/ppdb/promote" method="POST" onsubmit="return confirm('Yakin ingin memindahkan data ini ke Data Induk Siswa?')">
                         <?= \App\Core\Csrf::input() ?>
                         <input type="hidden" name="candidate_id" value="<?= $candidate['id'] ?>">
                         <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded text-sm hover:bg-purple-700 shadow-lg">
                             <i class="fa-solid fa-user-plus mr-1"></i> Generate Siswa Aktif
                         </button>
                         <p class="text-xs text-gray-500 mt-2 text-center">Tindakan ini akan membuat NIS & Data Induk</p>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
            <div x-data="{ tab: 'biodata' }">
                <div class="flex border-b mb-4">
                    <button @click="tab = 'biodata'" :class="{'border-b-2 border-blue-500 text-blue-600': tab==='biodata'}" class="px-4 py-2 font-medium text-sm text-gray-600">Biodata</button>
                    <button @click="tab = 'payment'" :class="{'border-b-2 border-blue-500 text-blue-600': tab==='payment'}" class="px-4 py-2 font-medium text-sm text-gray-600">Pembayaran</button>
                    <button @click="tab = 'document'" :class="{'border-b-2 border-blue-500 text-blue-600': tab==='document'}" class="px-4 py-2 font-medium text-sm text-gray-600">Dokumen</button>
                </div>

                <div x-show="tab === 'biodata'">
                    <h4 class="font-bold text-gray-700 mb-4">Informasi Lengkap</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">NISN</p>
                            <p class="font-semibold"><?= $candidate['nisn'] ?? '-' ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Tempat, Tgl Lahir</p>
                            <p class="font-semibold"><?= $candidate['birth_place'] ?>, <?= $candidate['birth_date'] ?></p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-500">Alamat</p>
                            <p class="font-semibold"><?= $candidate['address'] ?></p>
                        </div>
                        <div class="col-span-2 border-t pt-2 mt-2">
                            <h5 class="font-bold text-blue-600 mb-2">Data Orang Tua</h5>
                        </div>
                        <div>
                            <p class="text-gray-500">Nama Ayah</p>
                            <p class="font-semibold"><?= $candidate['father_name'] ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500">No HP Ayah</p>
                            <p class="font-semibold"><?= $candidate['father_phone'] ?></p>
                        </div>
                        <div class="col-span-2 border-t pt-2 mt-2">
                            <h5 class="font-bold text-blue-600 mb-2">Sekolah Asal</h5>
                            <p class="font-semibold"><?= $candidate['school_origin'] ?></p>
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'payment'" style="display: none;">
                    <h4 class="font-bold text-gray-700 mb-4">Riwayat Pembayaran</h4>
                    
                    <?php if(empty($payments)): ?>
                        <p class="text-gray-500 italic">Belum ada data pembayaran.</p>
                    <?php else: ?>
                        <?php foreach($payments as $pay): ?>
                            <div class="border rounded p-4 mb-4 <?= $pay['status'] == 'PENDING' ? 'bg-yellow-50 border-yellow-200' : '' ?>">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-bold text-lg">Rp <?= number_format($pay['amount'], 0, ',', '.') ?></p>
                                        <p class="text-xs text-gray-500">Tgl: <?= $pay['payment_date'] ?></p>
                                    </div>
                                    <span class="text-xs font-bold px-2 py-1 rounded <?= $pay['status']=='VERIFIED'?'bg-green-100 text-green-800':($pay['status']=='REJECTED'?'bg-red-100 text-red-800':'bg-yellow-100 text-yellow-800') ?>">
                                        <?= $pay['status'] ?>
                                    </span>
                                </div>
                                
                                <div class="mt-3">
                                    <a href="/uploads/payments/<?= $pay['proof_file'] ?>" target="_blank" class="text-blue-600 text-sm hover:underline"><i class="fa-solid fa-paperclip"></i> Lihat Bukti Transfer</a>
                                </div>

                                <?php if($pay['status'] == 'PENDING'): ?>
                                <div class="mt-4 pt-4 border-t flex space-x-2">
                                    <form action="/ppdb/verify/payment" method="POST" class="flex-1">
                                        <?= \App\Core\Csrf::input() ?>
                                        <input type="hidden" name="payment_id" value="<?= $pay['id'] ?>">
                                        <input type="hidden" name="candidate_id" value="<?= $candidate['id'] ?>">
                                        <input type="hidden" name="action" value="ACCEPT">
                                        <button type="submit" class="w-full bg-green-600 text-white py-1 rounded text-sm hover:bg-green-700">Terima</button>
                                    </form>
                                    <form action="/ppdb/verify/payment" method="POST" class="flex-1">
                                        <?= \App\Core\Csrf::input() ?>
                                        <input type="hidden" name="payment_id" value="<?= $pay['id'] ?>">
                                        <input type="hidden" name="candidate_id" value="<?= $candidate['id'] ?>">
                                        <input type="hidden" name="action" value="REJECT">
                                        <button type="submit" onclick="return prompt('Alasan Penolakan:')" class="w-full bg-red-600 text-white py-1 rounded text-sm hover:bg-red-700">Tolak</button>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div x-show="tab === 'document'" style="display: none;">
                    <h4 class="font-bold text-gray-700 mb-4">Validasi Dokumen</h4>
                    <div class="space-y-4">
                        <?php 
                        $types = ['KK', 'AKTA', 'IJAZAH', 'FOTO'];
                        foreach($types as $type): 
                            $doc = $documents[$type] ?? null;
                        ?>
                        <div class="flex items-center justify-between p-3 border rounded hover:bg-gray-50">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center text-gray-500 mr-3">
                                    <i class="fa-solid fa-file"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-sm"><?= $type ?></p>
                                    <?php if($doc): ?>
                                        <a href="/uploads/documents/<?= $doc['file_path'] ?>" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat File</a>
                                    <?php else: ?>
                                        <span class="text-xs text-red-500">Belum diupload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div>
                                <?php if($doc): ?>
                                    <?php if($doc['status'] == 'PENDING'): ?>
                                        <div class="flex space-x-1">
                                            <form action="/ppdb/verify/document" method="POST">
                                                <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                                                <input type="hidden" name="candidate_id" value="<?= $candidate['id'] ?>">
                                                <input type="hidden" name="action" value="VALID">
                                                <button type="submit" class="bg-green-100 text-green-700 p-2 rounded hover:bg-green-200" title="Valid"><i class="fa-solid fa-check"></i></button>
                                            </form>
                                            <form action="/ppdb/verify/document" method="POST">
                                                <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                                                <input type="hidden" name="candidate_id" value="<?= $candidate['id'] ?>">
                                                <input type="hidden" name="action" value="INVALID">
                                                <button type="submit" class="bg-red-100 text-red-700 p-2 rounded hover:bg-red-200" title="Invalid"><i class="fa-solid fa-times"></i></button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs font-bold px-2 py-1 rounded <?= $doc['status']=='VALID'?'bg-green-100 text-green-800':'bg-red-100 text-red-800' ?>">
                                            <?= $doc['status'] ?>
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
