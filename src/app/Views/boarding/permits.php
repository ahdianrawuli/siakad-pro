<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <h3 class="text-3xl font-medium text-gray-700 mb-6">Perizinan Santri</h3>
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white p-6 rounded shadow mb-6">
        <h4 class="font-bold mb-4">Input Izin Baru</h4>
        <form action="/boarding/permits/store" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <?= \App\Core\Csrf::input() ?>
            <div class="col-span-2">
                <label class="text-xs font-bold uppercase">Santri</label>
                <select name="student_id" class="w-full p-2 border rounded text-sm select2">
                    <?php foreach($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-xs font-bold uppercase">Jenis</label>
                <select name="type" class="w-full p-2 border rounded text-sm">
                    <option value="KELUAR">Keluar Sebentar</option>
                    <option value="PULANG">Pulang Kampung</option>
                    <option value="SAKIT">Sakit/Rawat</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-bold uppercase">Alasan</label>
                <input type="text" name="reason" class="w-full p-2 border rounded text-sm" required>
            </div>
            <div class="hidden">
                 <input type="hidden" name="start_date" value="<?= date('Y-m-d H:i') ?>">
                 <input type="hidden" name="end_date" value="<?= date('Y-m-d H:i', strtotime('+1 day')) ?>">
            </div>
            <button type="submit" class="bg-blue-600 text-white py-2 rounded text-sm font-bold">Buat Izin</button>
        </form>
    </div>

    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-50 text-xs uppercase font-bold text-gray-600">
                    <th class="px-5 py-3 text-left">Santri</th>
                    <th class="px-5 py-3 text-left">Jenis/Alasan</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($permits as $p): ?>
                <tr class="border-b">
                    <td class="px-5 py-4">
                        <p class="font-bold"><?= $p['full_name'] ?></p>
                        <p class="text-xs text-gray-500"><?= $p['dorm_name'] ?? 'Non-Asrama' ?></p>
                    </td>
                    <td class="px-5 py-4">
                        <span class="bg-gray-200 px-2 py-1 rounded text-xs font-bold"><?= $p['type'] ?></span>
                        <p class="text-sm mt-1"><?= $p['reason'] ?></p>
                        <p class="text-xs text-gray-400"><?= substr($p['start_date'], 0, 16) ?></p>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-2 py-1 rounded text-xs font-bold 
                            <?= $p['status']=='APPROVED'?'bg-green-100 text-green-800':($p['status']=='PENDING'?'bg-yellow-100 text-yellow-800':'bg-red-100 text-red-800') ?>">
                            <?= $p['status'] ?>
                        </span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <?php if($p['status'] == 'PENDING'): ?>
                            <a href="/boarding/permits/approve?id=<?= $p['id'] ?>&action=APPROVE" class="text-green-600 hover:underline text-xs mr-2">Setujui</a>
                            <a href="/boarding/permits/approve?id=<?= $p['id'] ?>&action=REJECT" class="text-red-600 hover:underline text-xs">Tolak</a>
                        <?php elseif($p['status'] == 'APPROVED'): ?>
                            <a href="/boarding/permits/approve?id=<?= $p['id'] ?>&action=RETURN" class="bg-blue-600 text-white px-2 py-1 rounded text-xs">Catat Kembali</a>
                        <?php else: ?>
                            <span class="text-gray-400 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
