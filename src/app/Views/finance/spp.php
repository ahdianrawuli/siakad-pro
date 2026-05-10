<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Data SPP</h3>
            <p class="text-slate-500 text-sm mt-1">Master jenis tagihan SPP bulanan.</p>
        </div>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md hover:bg-blue-700 transition flex items-center gap-2 w-fit">
            <i class="fa-solid fa-plus"></i> Tambah SPP
        </button>
    </div>
    <?php \App\Core\Session::flash(); ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase">Nama SPP</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-right">Nominal</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($sppList)): ?>
                    <tr><td colspan="3" class="px-5 py-12 text-center text-slate-400">Belum ada data SPP.</td></tr>
                <?php endif; ?>
                <?php foreach ($sppList as $s): ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3 font-semibold text-slate-800"><?= htmlspecialchars($s['name']) ?></td>
                    <td class="px-5 py-3 text-right font-mono font-bold text-slate-700">Rp <?= number_format($s['amount'], 0, ',', '.') ?></td>
                    <td class="px-5 py-3 text-center">
                        <a href="/finance/spp/delete?id=<?= $s['id'] ?>" onclick="return confirm('Hapus SPP ini?')"
                            class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white inline-flex items-center justify-center transition shadow-sm">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<div id="addModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700">Tambah Data SPP</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/finance/spp/store" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama SPP</label>
                <input type="text" name="name" placeholder="cth: SPP Bulanan MTs" required
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nominal (Rp)</label>
                <input type="number" name="amount" placeholder="cth: 350000" required
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>
<script>window.onclick=function(e){if(e.target==document.getElementById('addModal'))document.getElementById('addModal').classList.add('hidden');}</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
