<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6" x-data="{ modalOpen: false, editModalOpen: false, deleteModalOpen: false, currentId: '', currentName: '', currentDesc: '' }">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Manajemen Roles</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola kelompok hak akses dan perizinan sistem.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-shield-halved"></i> Total Role: <?= $totalData ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <button @click="modalOpen = true"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2 w-fit">
            <i class="fa-solid fa-plus"></i> Tambah Role
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="flex flex-col gap-6">

        <!-- Filter -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <div class="flex-1 min-w-[200px] relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama role..."
                        class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
                <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
                <?php if (!empty($search)): ?>
                    <a href="/settings/roles" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full whitespace-nowrap text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-12 text-center">No</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Role</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Slug / Kode</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Hak Akses</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($roles)): ?>
                        <tr><td colspan="6" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Data tidak ditemukan.</td></tr>
                    <?php else: ?>
                        <?php $no = ($currentPage - 1) * $limit + 1; foreach ($roles as $r): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                                <td class="px-5 py-4 text-center text-slate-500 font-semibold"><?= $no++ ?></td>
                                <td class="px-5 py-4 font-extrabold text-slate-800"><?= htmlspecialchars($r['name']) ?></td>
                                <td class="px-5 py-4 text-slate-500 font-mono text-xs"><?= htmlspecialchars($r['slug']) ?></td>
                                <td class="px-5 py-4 text-slate-500 text-sm"><?= htmlspecialchars($r['description'] ?? '-') ?></td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <form action="/settings/roles/toggle" method="POST" class="inline">
                                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                            <button type="submit" <?= in_array($r['slug'], ['super-admin', 'admin']) ? 'disabled' : '' ?>
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold border transition
                                                <?= ($r['status'] ?? 'active') === 'active' ? 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100' : 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100' ?>
                                                <?= in_array($r['slug'], ['super-admin', 'admin']) ? 'opacity-50 cursor-not-allowed' : '' ?>">
                                                <?= strtoupper($r['status'] ?? 'active') ?>
                                            </button>
                                        </form>
                                        <a href="/settings/roles/permissions?id=<?= $r['id'] ?>"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg text-[10px] font-bold hover:bg-indigo-100 transition">
                                            <i class="fa-solid fa-list-check"></i> Edit Permissions
                                        </a>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <?php if (empty($r['is_protected'])): ?>
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button @click="editModalOpen = true; currentId = '<?= $r['id'] ?>'; currentName = '<?= addslashes(htmlspecialchars($r['name'])) ?>'; currentDesc = '<?= addslashes(htmlspecialchars($r['description'] ?? '')) ?>'"
                                            class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Edit">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </button>
                                        <button @click="deleteModalOpen = true; currentId = '<?= $r['id'] ?>'"
                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Hapus">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </div>
                                    <?php else: ?>
                                        <span class="text-slate-400 text-xs italic">System Locked</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Show:</span>
                    <select onchange="window.location.href=updateQS(window.location.href,'limit',this.value)"
                        class="border border-slate-300 rounded-lg px-2 py-1 text-sm outline-none bg-white font-medium">
                        <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10 entries</option>
                        <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25 entries</option>
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50 entries</option>
                    </select>
                    <span class="text-xs text-slate-500">
                        <?= min($totalData, ($currentPage-1)*$limit+1) ?>–<?= min($totalData, $currentPage*$limit) ?> dari <?= $totalData ?> entri
                    </span>
                </div>
                <?php if ($totalPages > 1): ?>
                <div class="flex items-center gap-1.5">
                    <?php $qs = "&limit=$limit&search=" . urlencode($search); ?>
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors shadow-sm"><i class="fa-solid fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <span class="text-xs font-bold text-slate-600 px-2">Hal <?= $currentPage ?> / <?= $totalPages ?></span>
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors shadow-sm"><i class="fa-solid fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div x-cloak x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div @click.away="modalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-plus text-slate-400"></i> Tambah Role Baru</h3>
                <button @click="modalOpen = false" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="/settings/roles/store" method="POST" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Role</label>
                    <input type="text" name="name" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="modalOpen = false" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-cloak x-show="editModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div @click.away="editModalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-pen-to-square text-slate-400"></i> Edit Role</h3>
                <button @click="editModalOpen = false" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="/settings/roles/update" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="id" x-model="currentId">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Role</label>
                    <input type="text" name="name" x-model="currentName" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Deskripsi</label>
                    <textarea name="description" x-model="currentDesc" rows="3" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="editModalOpen = false" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-cloak x-show="deleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div @click.away="deleteModalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6">
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-triangle-exclamation text-2xl text-red-600"></i>
            </div>
            <h3 class="text-lg font-extrabold text-slate-800 mb-2">Hapus Role?</h3>
            <p class="text-sm text-slate-500 mb-6">Data yang dihapus tidak dapat dikembalikan. Role tidak dapat dihapus jika masih ada pengguna yang terhubung.</p>
            <form action="/settings/roles/delete" method="POST" class="flex gap-3">
                <input type="hidden" name="id" x-model="currentId">
                <button type="button" @click="deleteModalOpen = false" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-red-600 text-white py-2.5 rounded-xl font-bold hover:bg-red-700 shadow-md shadow-red-500/20 transition text-sm">Hapus</button>
            </form>
        </div>
    </div>

</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Manajemen Role</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Klik <strong class="text-slate-700">Tambah Role</strong> untuk membuat kelompok hak akses baru.</li>
                    <li>Klik <strong class="text-slate-700">Edit Permissions</strong> untuk mengatur menu yang dapat diakses oleh role tersebut.</li>
                    <li>Klik badge <strong class="text-slate-700">ACTIVE/INACTIVE</strong> untuk mengaktifkan atau menonaktifkan role.</li>
                    <li>Role <strong class="text-slate-700">super-admin</strong> dan <strong class="text-slate-700">admin</strong> tidak dapat diedit atau dihapus.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-users text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Manajemen User</div><div class="text-[11px] text-slate-400">Role diterapkan ke pengguna di menu <strong>Pengaturan → Manajemen User</strong>.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<script>
    function updateQS(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var sep = uri.indexOf('?') !== -1 ? "&" : "?";
        return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + sep + key + "=" + value;
    }
    window.onclick = function(e) {
        if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden');
    }
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>