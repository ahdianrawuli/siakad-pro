<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-3xl font-medium text-gray-700">Silabus & RPP</h3>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-sm">
            <i class="fa fa-upload"></i> Upload Dokumen
        </button>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
            <form class="flex gap-2">
                <input type="text" name="search" value="<?= $search ?>" placeholder="Cari Dokumen..." class="p-2 border rounded text-sm w-64">
                <button class="bg-gray-800 text-white px-4 py-2 rounded text-sm">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Jenis</th>
                        <th class="px-6 py-3 text-left">Judul Dokumen</th>
                        <th class="px-6 py-3 text-left">Mapel & Tingkat</th>
                        <th class="px-6 py-3 text-left">Guru</th>
                        <th class="px-6 py-3 text-center">Download</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($documents)): ?>
                        <tr><td colspan="6" class="p-4 text-center text-gray-400">Belum ada dokumen diupload.</td></tr>
                    <?php endif; ?>

                    <?php foreach($documents as $doc): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded text-xs font-bold 
                                <?= $doc['type'] == 'RPP' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700' ?>">
                                <?= $doc['type'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-3 font-bold text-gray-700"><?= $doc['title'] ?></td>
                        <td class="px-6 py-3 text-xs">
                            <div><?= $doc['subject_name'] ?></div>
                            <div class="text-gray-500">Kelas: <?= $doc['grade_level'] ?></div>
                        </td>
                        <td class="px-6 py-3 text-gray-600 text-xs"><?= $doc['teacher_name'] ?></td>
                        <td class="px-6 py-3 text-center">
                            <a href="/academic/syllabus/download?file=<?= $doc['file_path'] ?>" class="text-blue-600 hover:text-blue-800 underline text-xs">
                                <i class="fa fa-download"></i> Unduh
                            </a>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <a href="/academic/syllabus/delete?id=<?= $doc['id'] ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Hapus dokumen ini?')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-lg shadow-xl">
        <h3 class="text-xl font-bold mb-4">Upload Dokumen Ajar</h3>
        <form action="/academic/syllabus/store" method="POST" enctype="multipart/form-data">
            <?= \App\Core\Csrf::input() ?>
            
            <?php if($user['role_id'] == 1 || $user['role_id'] == 2): ?>
                 <input type="hidden" name="teacher_id" value="<?= $user['id'] ?>">
            <?php else: ?>
                 <input type="hidden" name="teacher_id" value="<?= $user['id'] ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label class="block text-xs font-bold mb-1">Judul Dokumen</label>
                <input type="text" name="title" class="w-full p-2 border rounded" required placeholder="Contoh: RPP Matematika Bab 1">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-xs font-bold mb-1">Jenis Dokumen</label>
                    <select name="type" class="w-full p-2 border rounded bg-gray-50">
                        <option value="SILABUS">Silabus</option>
                        <option value="RPP">RPP / Modul Ajar</option>
                        <option value="PROTA">Program Tahunan</option>
                        <option value="PROSEM">Program Semester</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Tingkat Kelas</label>
                    <select name="grade_level" class="w-full p-2 border rounded">
                        <option value="7">Kelas 7</option>
                        <option value="8">Kelas 8</option>
                        <option value="9">Kelas 9</option>
                        <option value="10">Kelas 10</option>
                        <option value="11">Kelas 11</option>
                        <option value="12">Kelas 12</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-xs font-bold mb-1">Mata Pelajaran</label>
                    <select name="subject_id" class="w-full p-2 border rounded" required>
                        <?php foreach($subjects as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Tahun Ajaran</label>
                    <select name="academic_year_id" class="w-full p-2 border rounded">
                        <?php foreach($years as $y): ?>
                            <option value="<?= $y['id'] ?>"><?= $y['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold mb-1">File (PDF/DOC)</label>
                <input type="file" name="file" class="w-full p-2 border rounded" required accept=".pdf,.doc,.docx">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Upload</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>

