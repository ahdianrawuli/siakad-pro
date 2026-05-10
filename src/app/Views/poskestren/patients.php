<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Poskestren</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Rekam medis dan riwayat kesehatan santri oleh petugas kesehatan.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-bold border border-green-100">
                    <i class="fa-solid fa-stethoscope"></i> <?= $totalData ?> Rekam Medis
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition border border-slate-200" title="Panduan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition flex items-center gap-2 w-fit">
            <i class="fa-solid fa-plus"></i> Tambah Rekam Medis
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Filter -->
    <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-6 flex flex-wrap gap-3">
        <div class="flex-1 min-w-[200px] relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama santri / keluhan..."
                class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/50">
        </div>
        <select name="status" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            <option value="">Semua Status</option>
            <option value="RAWAT_JALAN" <?= ($status??'')==='RAWAT_JALAN'?'selected':'' ?>>Rawat Jalan</option>
            <option value="RAWAT_INAP"  <?= ($status??'')==='RAWAT_INAP' ?'selected':'' ?>>Rawat Inap</option>
            <option value="RUJUK_RS"    <?= ($status??'')==='RUJUK_RS'   ?'selected':'' ?>>Rujuk RS</option>
        </select>
        <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition">Cari</button>
        <?php if ($search || $status): ?>
            <a href="/poskestren/patients" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
        <?php endif; ?>
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
                <select name="student_id" id="selectSantriPoskestren" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none">
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
<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Poskestren</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-4 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Fungsi Halaman Ini</h4>
                <p class="text-slate-500 text-xs">Halaman ini khusus untuk <strong class="text-slate-700">petugas kesehatan/dokter</strong> mencatat rekam medis lengkap: keluhan, diagnosa, tindakan/obat, dan status rawat.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Perbedaan dengan Kesehatan Asrama</h4>
                <div class="space-y-2">
                    <div class="flex items-start gap-3 p-2.5 bg-blue-50 rounded-xl border border-blue-100 text-xs">
                        <i class="fa-solid fa-stethoscope text-blue-500 mt-0.5 shrink-0"></i>
                        <div><strong class="text-slate-700">Poskestren (halaman ini)</strong> — Input lengkap oleh petugas medis: diagnosa, obat, petugas dipilih dari daftar staff.</div>
                    </div>
                    <div class="flex items-start gap-3 p-2.5 bg-teal-50 rounded-xl border border-teal-100 text-xs">
                        <i class="fa-solid fa-house-medical text-teal-500 mt-0.5 shrink-0"></i>
                        <div><strong class="text-slate-700">Kesehatan Asrama</strong> — Laporan awal oleh wali asrama: keluhan & tindakan awal saja, tanpa diagnosa.</div>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Status Rawat</h4>
                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full font-bold">Rawat Jalan</span>
                    <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-full font-bold">Rawat Inap</span>
                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full font-bold">Rujuk RS</span>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">4</span> Relasi ke Menu Lain</h4>
                <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                    <i class="fa-solid fa-heart-pulse text-pink-400 w-4 text-center"></i>
                    <div>Data kesehatan tampil di <strong class="text-slate-700">Portal Siswa → Kesehatan</strong> dan <strong class="text-slate-700">Portal Orang Tua → Kesehatan</strong>.</div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#selectSantriPoskestren').select2({ placeholder: '-- Cari santri...', allowClear: true, dropdownParent: $('#modalTambah'), width: '100%' });
});
window.onclick = function(e) {
    ['infoModal','modalTambah'].forEach(function(id) {
        if (e.target == document.getElementById(id)) document.getElementById(id).classList.add('hidden');
    });
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
