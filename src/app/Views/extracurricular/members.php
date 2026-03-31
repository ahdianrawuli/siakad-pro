<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6">
        <h3 class="text-3xl font-medium text-gray-700">Anggota Ekstrakurikuler</h3>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white p-4 rounded shadow mb-6">
        <form class="flex gap-4 items-end" method="GET" action="/extracurricular/members">
            <div class="w-full md:w-1/3">
                <label class="block text-sm font-bold mb-1">Pilih Ekstrakurikuler</label>
                <select name="id" class="w-full p-2 border rounded" onchange="this.form.submit()">
                    <option value="">-- Pilih --</option>
                    <?php foreach($ekskuls as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= $selectedEkskul == $e['id'] ? 'selected' : '' ?>>
                            <?= $e['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <?php if($selectedEkskul): ?>
    <div class="flex gap-6 flex-col md:flex-row">
        <div class="w-full md:w-2/3 bg-white p-4 rounded shadow">
            <h4 class="font-bold border-b pb-2 mb-4">Daftar Anggota</h4>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="p-2">NIS</th>
                        <th class="p-2">Nama Siswa</th>
                        <th class="p-2">Kelas</th>
                        <th class="p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($members as $m): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-2"><?= $m['nis'] ?></td>
                        <td class="p-2 font-bold"><?= $m['full_name'] ?></td>
                        <td class="p-2"><?= $m['class_name'] ?></td>
                        <td class="p-2">
                            <form action="/extracurricular/members/delete" method="POST" onsubmit="return confirm('Hapus siswa ini dari ekskul?')">
                                <input type="hidden" name="record_id" value="<?= $m['record_id'] ?>">
                                <input type="hidden" name="extracurricular_id" value="<?= $selectedEkskul ?>">
                                <button class="text-red-500 hover:text-red-700"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="w-full md:w-1/3 bg-white p-4 rounded shadow h-fit">
            <h4 class="font-bold border-b pb-2 mb-4">Tambah Anggota</h4>
            <form action="/extracurricular/members/add" method="POST">
                <input type="hidden" name="extracurricular_id" value="<?= $selectedEkskul ?>">
                <div class="mb-4">
                    <label class="block text-xs mb-1">Cari Siswa</label>
                    <select name="student_id" class="w-full p-2 border rounded" required>
                        <?php foreach($students as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= $s['nis'] ?> - <?= $s['full_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
                    + Masukkan ke Ekskul
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

