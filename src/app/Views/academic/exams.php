<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <h3 class="text-3xl font-medium text-gray-700 mb-6">Bank Soal & Arsip Ujian</h3>
    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded shadow h-fit">
            <h4 class="font-bold mb-4 border-b pb-2">Upload Soal Baru</h4>
            <form action="/academic/exams/store" method="POST" enctype="multipart/form-data">
                <?= \App\Core\Csrf::input() ?>
                
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Mata Pelajaran</label>
                    <select name="subject_id" class="w-full p-2 border rounded" required>
                        <?php foreach($subjects as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Judul / Materi</label>
                    <input type="text" name="title" class="w-full p-2 border rounded" placeholder="Contoh: Soal UAS Semester 1" required>
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tipe Ujian</label>
                    <select name="type" class="w-full p-2 border rounded">
                        <option value="LATIHAN">Latihan / PR</option>
                        <option value="QUIZ">Kuis Harian</option>
                        <option value="UTS">UTS</option>
                        <option value="UAS">UAS</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">File Dokumen (PDF/Word)</label>
                    <input type="file" name="file" class="w-full text-sm border p-2 rounded" required>
                    <p class="text-xs text-gray-400 mt-1">* Maks 2MB</p>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700">
                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Upload
                </button>
            </form>
        </div>

        <div class="md:col-span-2 bg-white rounded shadow overflow-hidden">
            <div class="p-4 bg-gray-50 border-b font-bold text-gray-700">Repository Soal</div>
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-white text-left text-xs font-bold text-gray-600 uppercase border-b">
                        <th class="px-5 py-3">Mapel & Judul</th>
                        <th class="px-5 py-3">Tipe</th>
                        <th class="px-5 py-3">Uploader</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exams as $e): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <span class="block font-bold text-blue-600"><?= $e['subject_name'] ?></span>
                            <span class="text-sm text-gray-800"><?= $e['title'] ?></span>
                            <span class="block text-xs text-gray-400"><?= date('d M Y', strtotime($e['created_at'])) ?></span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded font-bold"><?= $e['type'] ?></span>
                        </td>
                        <td class="px-5 py-4 text-sm">
                            <?= $e['teacher_name'] ?>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="/uploads/exams/<?= $e['file_path'] ?>" target="_blank" class="text-blue-600 hover:text-blue-800 border border-blue-600 px-3 py-1 rounded text-xs font-bold">
                                <i class="fa-solid fa-download mr-1"></i> Unduh
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($exams)): ?>
                        <tr><td colspan="4" class="p-6 text-center text-gray-500">Belum ada soal yang diupload.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
