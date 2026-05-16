<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Jenis Sholat</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola item sholat yang akan diabsen.</p>
        </div>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full text-left text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase w-10 text-center">#</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase">Nama Sholat</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">Kategori</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">Urutan</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">Status</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($types as $i => $t): ?>
                <tr class="hover:bg-slate-50/80">
                    <td class="px-5 py-3 text-center text-slate-400"><?= $i+1 ?></td>
                    <td class="px-5 py-3 font-bold text-slate-800"><?= htmlspecialchars($t['name']) ?></td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border <?= $t['category']==='WAJIB'?'bg-green-50 text-green-700 border-green-200':'bg-purple-50 text-purple-700 border-purple-200' ?>">
                            <?= $t['category'] ?>
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center text-slate-500"><?= $t['order_num'] ?></td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $t['is_active']?'bg-green-100 text-green-700':'bg-red-100 text-red-700' ?>">
                            <?= $t['is_active']?'Aktif':'Nonaktif' ?>
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <a href="/boarding/prayer/types/delete?id=<?= $t['id'] ?>" onclick="return confirm('Hapus?')"
                            class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white inline-flex items-center justify-center transition">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Modal Tambah -->
<div id="addModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700"><i class="fa-solid fa-plus text-slate-400 mr-2"></i>Tambah Jenis Sholat</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/boarding/prayer/types/store" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Sholat</label>
                <input type="text" name="name" required placeholder="cth: Witir" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Kategori</label>
                    <select name="category" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
                        <option value="WAJIB">Wajib</option><option value="SUNNAH">Sunnah</option>
                    </select></div>
                <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Urutan</label>
                    <input type="number" name="order_num" value="10" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none"></div>
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
