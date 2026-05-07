<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Data Petugas Poskestren</h1>
        <p class="text-slate-500 text-sm mt-1">Daftar staf yang bertugas di Poskestren.</p>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Filter -->
    <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-6 flex gap-3">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama / NIP..."
            class="flex-1 px-4 py-2 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500/50 bg-slate-50">
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-sm font-semibold">Cari</button>
        <?php if($search): ?><a href="/poskestren/staff" class="px-4 py-2 bg-red-50 text-red-500 rounded-xl text-sm font-semibold">Reset</a><?php endif; ?>
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">NIP</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Jabatan</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Kontak</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($staff)): ?>
                        <tr><td colspan="5" class="px-4 py-12 text-center text-slate-400">Belum ada data petugas.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($staff as $s): ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3 font-mono text-xs text-slate-500"><?= htmlspecialchars($s['nip'] ?? '-') ?></td>
                        <td class="px-4 py-3 font-semibold text-slate-800">
                            <?= htmlspecialchars($s['full_name']) ?>
                            <div class="text-xs text-slate-400"><?= $s['gender'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></div>
                        </td>
                        <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($s['position_name'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-slate-600">
                            <?php if(!empty($s['phone'])): ?>
                                <a href="https://wa.me/62<?= ltrim($s['phone'],'0') ?>" target="_blank" class="text-green-600 hover:underline text-xs"><i class="fa-brands fa-whatsapp mr-1"></i><?= $s['phone'] ?></a>
                            <?php else: ?><span class="text-slate-400 text-xs">-</span><?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= $s['status'] == 'ACTIVE' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' ?>"><?= $s['status'] ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <p class="text-xs text-slate-400 mt-3">* Data petugas diambil dari modul Kepegawaian. Tambah petugas melalui menu Kepegawaian → Data Staff.</p>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
