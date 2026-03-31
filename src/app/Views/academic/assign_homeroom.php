<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <h3 class="text-3xl font-medium text-gray-700 mb-6">Atur Wali Kelas</h3>
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded shadow overflow-hidden">
        <div class="p-4 bg-blue-50 border-b text-blue-800 text-sm">
            <i class="fa-solid fa-info-circle mr-1"></i> 
            Wali Kelas yang ditunjuk akan mendapatkan akses menu khusus <b>"Kelas Saya"</b>.
        </div>
        
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-100 text-left text-xs font-bold uppercase text-gray-600 border-b">
                    <th class="px-5 py-3">Nama Kelas</th>
                    <th class="px-5 py-3">Wali Kelas Saat Ini</th>
                    <th class="px-5 py-3">Ganti Wali Kelas</th>
                    <th class="px-5 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classrooms as $c): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-5 py-4 font-bold text-gray-700">
                        <?= $c['name'] ?>
                    </td>
                    <td class="px-5 py-4">
                        <?php if($c['teacher_name']): ?>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">
                                <?= $c['teacher_name'] ?>
                            </span>
                        <?php else: ?>
                            <span class="text-gray-400 text-xs italic">Belum diset</span>
                        <?php endif; ?>
                    </td>
                    
                    <form action="/academic/homeroom-assign/update" method="POST">
                        <?= \App\Core\Csrf::input() ?>
                        <input type="hidden" name="classroom_id" value="<?= $c['id'] ?>">
                        
                        <td class="px-5 py-4">
                            <select name="teacher_id" class="w-full p-2 border rounded text-sm">
                                <option value="">-- Kosongkan / Pilih Guru --</option>
                                <?php foreach ($teachers as $t): ?>
                                    <option value="<?= $t['id'] ?>" <?= ($c['homeroom_teacher_id'] == $t['id']) ? 'selected' : '' ?>>
                                        <?= $t['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-xs font-bold hover:bg-blue-700 shadow">
                                Simpan
                            </button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
