<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Master Pelanggaran</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola daftar aturan dan sanksi poin pelanggaran santri.</p>
            <div class="mt-3">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-list"></i> Total: <?= count($violations) ?> aturan
                </div>
            </div>
        </div>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')"
            class="px-4 py-2.5 bg-red-600 text-white rounded-xl text-sm font-semibold shadow-md hover:bg-red-700 transition flex items-center gap-2 w-fit">
            <i class="fa-solid fa-plus"></i> Tambah Aturan
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Filter + Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-3">
            <form method="GET" class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari kode atau nama..."
                    class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kode</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Pelanggaran</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Tingkat</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Poin</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($violations)): ?>
                        <tr><td colspan="5" class="px-5 py-16 text-center text-slate-400">Belum ada data.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($violations as $v):
                        $bg = $v['severity']==='BERAT' ? 'bg-red-50 text-red-700 border-red-200' : ($v['severity']==='SEDANG' ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : 'bg-green-50 text-green-700 border-green-200');
                    ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-4 font-mono text-xs font-bold text-slate-700"><?= htmlspecialchars($v['code']) ?></td>
                        <td class="px-5 py-4 font-semibold text-slate-800"><?= htmlspecialchars($v['name']) ?></td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border <?= $bg ?>"><?= $v['severity'] ?></span>
                        </td>
                        <td class="px-5 py-4 text-center font-bold text-red-600">-<?= $v['points'] ?></td>
                        <td class="px-5 py-4 text-center flex items-center justify-center gap-2">
                            <button onclick='openEdit(<?= json_encode($v) ?>)'
                                class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </button>
                            <a href="/discipline/master-violations/delete?id=<?= $v['id'] ?>"
                                onclick="return confirm('Hapus aturan ini?')"
                                class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition" title="Hapus">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Tambah -->
<div id="addModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700"><i class="fa-solid fa-plus text-slate-400 mr-2"></i>Tambah Aturan</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/discipline/master-violations/store" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Kode</label>
                <input type="text" name="code" required placeholder="PL-001" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
            <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Pelanggaran</label>
                <input type="text" name="name" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Tingkat</label>
                    <select name="severity" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="RINGAN">RINGAN</option><option value="SEDANG">SEDANG</option><option value="BERAT">BERAT</option>
                    </select></div>
                <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Poin</label>
                    <input type="number" name="points" required min="1" max="100" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-red-600 text-white py-2.5 rounded-xl font-bold hover:bg-red-700 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700"><i class="fa-solid fa-pen-to-square text-slate-400 mr-2"></i>Edit Aturan</h3>
            <button onclick="document.getElementById('editModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/discipline/master-violations/update" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="edit_id">
            <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Kode</label>
                <input type="text" name="code" id="edit_code" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
            <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Pelanggaran</label>
                <input type="text" name="name" id="edit_name" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Tingkat</label>
                    <select name="severity" id="edit_severity" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="RINGAN">RINGAN</option><option value="SEDANG">SEDANG</option><option value="BERAT">BERAT</option>
                    </select></div>
                <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Poin</label>
                    <input type="number" name="points" id="edit_points" required min="1" max="100" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(v) {
    document.getElementById('edit_id').value       = v.id;
    document.getElementById('edit_code').value     = v.code;
    document.getElementById('edit_name').value     = v.name;
    document.getElementById('edit_points').value   = v.points;
    document.getElementById('edit_severity').value = v.severity;
    document.getElementById('editModal').classList.remove('hidden');
}
window.onclick = function(e) {
    ['addModal','editModal'].forEach(function(id){
        if (e.target == document.getElementById(id)) document.getElementById(id).classList.add('hidden');
    });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
