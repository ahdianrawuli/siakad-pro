<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <h3 class="text-3xl font-medium text-gray-700 mb-6">Poskestren (Klinik Santri)</h3>
    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow h-fit">
            <h4 class="font-bold mb-4 border-b pb-2 text-green-700">
                <i class="fa-solid fa-stethoscope mr-2"></i> Periksa Santri
            </h4>
            <form action="/boarding/health/store" method="POST">
                <?= \App\Core\Csrf::input() ?>
                
                <div class="mb-3">
                    <label class="block text-xs font-bold uppercase mb-1">Pasien (Santri)</label>
                    <select name="student_id" class="w-full p-2 border rounded select2" required>
                        <?php foreach($students as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="block text-xs font-bold uppercase mb-1">Tanggal</label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="w-full p-2 border rounded">
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-bold uppercase mb-1">Keluhan</label>
                    <textarea name="complaint" class="w-full p-2 border rounded" placeholder="Pusing, mual, panas..." required></textarea>
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-bold uppercase mb-1">Diagnosa & Tindakan</label>
                    <input type="text" name="diagnosis" class="w-full p-2 border rounded mb-2" placeholder="Diagnosa (Misal: Flu)">
                    <textarea name="treatment" class="w-full p-2 border rounded" placeholder="Obat: Paracetamol 3x1..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold uppercase mb-1">Status Rawat</label>
                    <select name="status" class="w-full p-2 border rounded">
                        <option value="RAWAT_JALAN">Rawat Jalan (Balik Asrama)</option>
                        <option value="RAWAT_INAP">Rawat Inap (Di Poskestren)</option>
                        <option value="RUJUK_RS">Rujuk ke RS/Puskesmas</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded font-bold hover:bg-green-700">Simpan Data Medis</button>
            </form>
        </div>

        <div class="md:col-span-2 bg-white rounded shadow overflow-hidden">
            <div class="p-4 bg-gray-50 border-b font-bold text-gray-700">Riwayat Kunjungan Terakhir</div>
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-white text-left text-xs font-bold uppercase text-gray-600 border-b">
                        <th class="px-5 py-3">Tgl</th>
                        <th class="px-5 py-3">Santri</th>
                        <th class="px-5 py-3">Keluhan/Diagnosa</th>
                        <th class="px-5 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($records as $r): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-5 py-4 text-xs"><?= date('d/m', strtotime($r['date'])) ?></td>
                        <td class="px-5 py-4 font-bold"><?= $r['full_name'] ?></td>
                        <td class="px-5 py-4">
                            <span class="text-red-600 font-bold text-xs"><?= $r['complaint'] ?></span>
                            <p class="text-sm text-gray-800"><?= $r['diagnosis'] ?> - <span class="text-gray-500 italic"><?= $r['treatment'] ?></span></p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <?php if($r['status'] == 'RAWAT_INAP'): ?>
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-bold">Inap</span>
                            <?php elseif($r['status'] == 'RUJUK_RS'): ?>
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-bold">Rujuk</span>
                            <?php else: ?>
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Jalan</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
