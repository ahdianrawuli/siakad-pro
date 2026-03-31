<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6">
        <h3 class="text-3xl font-medium text-gray-700">Absensi Pegawai</h3>
        <p class="text-gray-500 text-sm">Pencatatan kehadiran Guru dan Staff.</p>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 bg-gray-50 border-b flex flex-wrap gap-4 justify-between items-center">
            <form class="flex gap-2 items-center" id="filterForm">
                <input type="date" name="date" value="<?= $date ?>" class="p-2 border rounded text-sm" onchange="document.getElementById('filterForm').submit()">
                
                <select name="role" class="p-2 border rounded text-sm" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Semua Peran</option>
                    <option value="guru" <?= $roleFilter == 'guru' ? 'selected' : '' ?>>Guru</option>
                    <option value="staff" <?= $roleFilter == 'staff' ? 'selected' : '' ?>>Staff / Karyawan</option>
                </select>
            </form>
            <div class="text-sm text-gray-500">
                Tanggal: <strong><?= date('d F Y', strtotime($date)) ?></strong>
            </div>
        </div>

        <form action="/staff/attendance/store" method="POST">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="date" value="<?= $date ?>">

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama Pegawai</th>
                            <th class="px-4 py-3 text-left">Jabatan</th>
                            <th class="px-4 py-3 text-center">Status Kehadiran</th>
                            <th class="px-4 py-3 text-left">Catatan</th>
                            <th class="px-4 py-3 text-center">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($users as $u): ?>
                        <?php 
                            $att = $attMap[$u['id']] ?? null; 
                            $status = $att['status'] ?? '';
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-800"><?= $u['name'] ?></div>
                                <div class="text-xs text-gray-500 uppercase"><?= $u['role_slug'] ?></div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <?= $u['position_name'] ?? '-' ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-2">
                                    <label class="cursor-pointer flex items-center gap-1 bg-green-50 px-2 py-1 rounded border <?= $status == 'HADIR' ? 'border-green-500 bg-green-100' : 'border-transparent' ?>">
                                        <input type="radio" name="attendance[<?= $u['id'] ?>]" value="HADIR" <?= $status == 'HADIR' || $status == '' ? 'checked' : '' ?>> Hadir
                                    </label>
                                    <label class="cursor-pointer flex items-center gap-1 bg-blue-50 px-2 py-1 rounded border <?= $status == 'IZIN' ? 'border-blue-500 bg-blue-100' : 'border-transparent' ?>">
                                        <input type="radio" name="attendance[<?= $u['id'] ?>]" value="IZIN" <?= $status == 'IZIN' ? 'checked' : '' ?>> Izin
                                    </label>
                                    <label class="cursor-pointer flex items-center gap-1 bg-yellow-50 px-2 py-1 rounded border <?= $status == 'SAKIT' ? 'border-yellow-500 bg-yellow-100' : 'border-transparent' ?>">
                                        <input type="radio" name="attendance[<?= $u['id'] ?>]" value="SAKIT" <?= $status == 'SAKIT' ? 'checked' : '' ?>> Sakit
                                    </label>
                                    <label class="cursor-pointer flex items-center gap-1 bg-red-50 px-2 py-1 rounded border <?= $status == 'ALPA' ? 'border-red-500 bg-red-100' : 'border-transparent' ?>">
                                        <input type="radio" name="attendance[<?= $u['id'] ?>]" value="ALPA" <?= $status == 'ALPA' ? 'checked' : '' ?>> Alpa
                                    </label>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="notes[<?= $u['id'] ?>]" value="<?= $att['notes'] ?? '' ?>" class="w-full border rounded px-2 py-1 text-xs" placeholder="Keterangan...">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <?php if($att): ?>
                                    <a href="/staff/attendance/delete?user_id=<?= $u['id'] ?>&date=<?= $date ?>" class="text-red-400 hover:text-red-600 text-xs" onclick="return confirm('Reset absensi orang ini?')"><i class="fa fa-undo"></i> Reset</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 bg-gray-50 border-t flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold shadow hover:bg-blue-700">
                    <i class="fa fa-save"></i> Simpan Semua Absensi
                </button>
            </div>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>

