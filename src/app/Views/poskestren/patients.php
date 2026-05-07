<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Data Pasien Poskestren</h1>
            <p class="text-slate-500 text-sm mt-1">Rekam medis dan riwayat kesehatan santri.</p>
        </div>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
            class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Rekam Medis
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Filter -->
    <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-6 flex gap-3">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama santri / keluhan..."
            class="flex-1 px-4 py-2 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500/50 bg-slate-50">
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-sm font-semibold">Cari</button>
        <?php if($search): ?><a href="/poskestren/patients" class="px-4 py-2 bg-red-50 text-red-500 rounded-xl text-sm font-semibold">Reset</a><?php endif; ?>
    </form>

    <!-- Tabel -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Nama Santri</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Keluhan</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Diagnosis</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Petugas</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($records)): ?>
                        <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400">Belum ada data rekam medis.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($records as $r): ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3 font-mono text-xs"><?= date('d M Y', strtotime($r['date'])) ?></td>
                        <td class="px-4 py-3 font-semibold text-slate-800"><?= htmlspecialchars($r['full_name'] ?? '-') ?><div class="text-xs text-slate-400"><?= $r['nis'] ?? '' ?></div></td>
                        <td class="px-4 py-3 text-slate-600 max-w-[200px] truncate"><?= htmlspecialchars($r['complaint']) ?></td>
                        <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($r['diagnosis'] ?? '-') ?></td>
                        <td class="px-4 py-3">
                            <?php $sc = ['RAWAT_JALAN'=>'bg-green-100 text-green-700','RAWAT_INAP'=>'bg-amber-100 text-amber-700','RUJUK_RS'=>'bg-red-100 text-red-700']; ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= $sc[$r['status']] ?? 'bg-slate-100 text-slate-600' ?>"><?= str_replace('_',' ',$r['status']) ?></span>
                        </td>
                        <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($r['officer_name'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="/poskestren/patients/delete" onsubmit="return confirm('Hapus data ini?')">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button class="text-red-500 hover:text-red-700 text-xs font-semibold"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="p-4 border-t border-slate-100 flex justify-between items-center text-sm">
            <span class="text-slate-500">Total: <?= $totalData ?> data</span>
            <div class="flex gap-1">
                <?php if ($currentPage > 1): ?><a href="?page=<?= $currentPage-1 ?>&search=<?= urlencode($search) ?>" class="px-3 py-1 border rounded-lg hover:bg-slate-50">‹</a><?php endif; ?>
                <span class="px-3 py-1 font-semibold"><?= $currentPage ?> / <?= $totalPages ?></span>
                <?php if ($currentPage < $totalPages): ?><a href="?page=<?= $currentPage+1 ?>&search=<?= urlencode($search) ?>" class="px-3 py-1 border rounded-lg hover:bg-slate-50">›</a><?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Modal Tambah -->
<div id="modalTambah" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-slate-800">Tambah Rekam Medis</h3>
            <button onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form method="POST" action="/poskestren/patients/store" class="p-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Santri</label>
                <select name="student_id" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/50">
                    <option value="">-- Pilih Santri --</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['full_name']) ?> (<?= $s['nis'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal</label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                    <select name="status" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/50">
                        <option value="RAWAT_JALAN">Rawat Jalan</option>
                        <option value="RAWAT_INAP">Rawat Inap</option>
                        <option value="RUJUK_RS">Rujuk RS</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Keluhan</label>
                <textarea name="complaint" required rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/50"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Diagnosis</label>
                <input type="text" name="diagnosis" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/50">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tindakan / Pengobatan</label>
                <textarea name="treatment" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/50"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Petugas</label>
                <select name="officer_id" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/50">
                    <option value="">-- Pilih Petugas --</option>
                    <?php foreach ($officers as $o): ?>
                        <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="px-4 py-2 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
