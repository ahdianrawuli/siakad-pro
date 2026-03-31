<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-3xl font-medium text-gray-700">Wali Kelas: <?= $class['name'] ?></h3>
            <p class="text-gray-500">Tingkat <?= $class['level'] ?> | Jurusan <?= $class['major'] ?></p>
        </div>
        <div class="flex gap-4">
            <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded text-center">
                <span class="block text-xl font-bold"><?= $class['total_students'] ?></span>
                <span class="text-xs">Total Siswa</span>
            </div>
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded text-center">
                <span class="block text-xl font-bold"><?= $class['total_male'] ?></span>
                <span class="text-xs">Putra</span>
            </div>
            <div class="bg-pink-100 text-pink-800 px-4 py-2 rounded text-center">
                <span class="block text-xl font-bold"><?= $class['total_female'] ?></span>
                <span class="text-xs">Putri</span>
            </div>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded shadow overflow-hidden">
        <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
            <h4 class="font-bold text-gray-700">Daftar Siswa Perwalian</h4>
            <div class="text-xs text-gray-500">
                <i class="fa-solid fa-circle-info mr-1"></i> Klik 'Rapor' untuk melihat nilai
            </div>
        </div>
        
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-white text-left text-xs font-bold uppercase text-gray-600 border-b">
                    <th class="px-5 py-3">NIS / Nama</th>
                    <th class="px-5 py-3 text-center">Absensi (S/A)</th>
                    <th class="px-5 py-3 text-center">Pelanggaran</th>
                    <th class="px-5 py-3 text-center">Aksi Akademik</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $s): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold mr-3 text-gray-600">
                                <?= substr($s['full_name'], 0, 1) ?>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800"><?= $s['full_name'] ?></p>
                                <p class="text-xs text-gray-500"><?= $s['nis'] ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <?php if($s['total_absent'] > 0): ?>
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-bold"><?= $s['total_absent'] ?> Hari</span>
                        <?php else: ?>
                            <span class="text-green-600 text-xs"><i class="fa-solid fa-check"></i> Rajin</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <?php if($s['total_violations'] > 0): ?>
                            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-bold"><?= $s['total_violations'] ?> Kasus</span>
                        <?php else: ?>
                            <span class="text-green-600 text-xs"><i class="fa-solid fa-check"></i> Nihil</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="/report/print?student_id=<?= $s['id'] ?>" target="_blank" class="bg-blue-600 text-white px-3 py-1 rounded text-xs font-bold hover:bg-blue-700" title="Cetak Rapor">
                                <i class="fa-solid fa-print"></i> Rapor
                            </a>
                            <a href="/settings/letters/print?template_id=1&student_id=<?= $s['id'] ?>" target="_blank" class="bg-gray-600 text-white px-3 py-1 rounded text-xs font-bold hover:bg-gray-700" title="Surat Keterangan Aktif">
                                <i class="fa-solid fa-file-lines"></i> SKA
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
