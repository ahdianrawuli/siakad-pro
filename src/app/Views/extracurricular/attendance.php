<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6">
        <h3 class="text-3xl font-medium text-gray-700">Absensi Ekstrakurikuler</h3>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white p-4 rounded shadow mb-6">
        <form class="flex gap-4 items-end" method="GET" action="/extracurricular/attendance">
            <div class="w-64">
                <label class="block text-sm font-bold mb-1">Pilih Ekskul</label>
                <select name="id" class="w-full p-2 border rounded" onchange="this.form.submit()">
                    <option value="">-- Pilih --</option>
                    <?php foreach($ekskuls as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= $selectedEkskul == $e['id'] ? 'selected' : '' ?>><?= $e['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-48">
                <label class="block text-sm font-bold mb-1">Tanggal</label>
                <input type="date" name="date" value="<?= $date ?>" class="w-full p-2 border rounded" onchange="this.form.submit()">
            </div>
        </form>
    </div>

    <?php if($selectedEkskul && !empty($members)): ?>
    <form action="/extracurricular/attendance/save" method="POST" class="bg-white p-6 rounded shadow">
        <input type="hidden" name="extracurricular_id" value="<?= $selectedEkskul ?>">
        <input type="hidden" name="date" value="<?= $date ?>">

        <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-lg">Daftar Hadir</h4>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                <i class="fa fa-save"></i> Simpan Absensi
            </button>
        </div>

        <table class="w-full">
            <thead>
                <tr class="bg-gray-100 text-left border-b border-gray-300">
                    <th class="p-3 w-10">No</th>
                    <th class="p-3">Nama Siswa</th>
                    <th class="p-3">Kelas</th>
                    <th class="p-3 text-center">Hadir</th>
                    <th class="p-3 text-center">Sakit</th>
                    <th class="p-3 text-center">Izin</th>
                    <th class="p-3 text-center">Alpa</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($members as $m): 
                    $status = $existingAttendance[$m['student_id']] ?? 'HADIR'; // Default Hadir
                ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 text-center"><?= $no++ ?></td>
                    <td class="p-3 font-bold"><?= $m['full_name'] ?></td>
                    <td class="p-3"><?= $m['class_name'] ?></td>
                    <td class="p-3 text-center">
                        <input type="radio" name="status[<?= $m['student_id'] ?>]" value="HADIR" class="h-4 w-4 text-green-600" <?= $status=='HADIR'?'checked':'' ?>>
                    </td>
                    <td class="p-3 text-center">
                        <input type="radio" name="status[<?= $m['student_id'] ?>]" value="SAKIT" class="h-4 w-4 text-yellow-500" <?= $status=='SAKIT'?'checked':'' ?>>
                    </td>
                    <td class="p-3 text-center">
                        <input type="radio" name="status[<?= $m['student_id'] ?>]" value="IZIN" class="h-4 w-4 text-blue-500" <?= $status=='IZIN'?'checked':'' ?>>
                    </td>
                    <td class="p-3 text-center">
                        <input type="radio" name="status[<?= $m['student_id'] ?>]" value="ALPA" class="h-4 w-4 text-red-600" <?= $status=='ALPA'?'checked':'' ?>>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
    <?php elseif($selectedEkskul): ?>
        <div class="text-center p-10 text-gray-500">Belum ada anggota di ekstrakurikuler ini.</div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

