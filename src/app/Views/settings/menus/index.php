<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 md:p-6" x-data="{ modalOpen: false, editModalOpen: false, deleteModalOpen: false, currentId: '', currentTitle: '', currentUrl: '', currentIcon: '', currentOrder: '', currentParent: '' }">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Manajemen Menu</h1>
            <p class="text-sm text-gray-500">Kelola struktur navigasi sidebar aplikasi.</p>
        </div>
        <button @click="modalOpen = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2 shadow-sm">
            <i class="fa-solid fa-plus"></i> Tambah Menu
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row gap-4 justify-between">
            <form action="" method="GET" class="w-full flex flex-col md:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari judul menu..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
                <select name="limit" class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none w-full md:w-32 bg-white">
                    <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10 baris</option>
                    <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25 baris</option>
                    <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50 baris</option>
                </select>
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium transition whitespace-nowrap border border-gray-200">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="p-4 font-bold border-b border-gray-100 w-16 text-center">No</th>
                        <th class="p-4 font-bold border-b border-gray-100">Judul Menu</th>
                        <th class="p-4 font-bold border-b border-gray-100">Parent</th>
                        <th class="p-4 font-bold border-b border-gray-100">URL / Path</th>
                        <th class="p-4 font-bold border-b border-gray-100 text-center">Urutan</th>
                        <th class="p-4 font-bold border-b border-gray-100 text-center">Status</th>
                        <th class="p-4 font-bold border-b border-gray-100 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                    <?php if (empty($menus)): ?>
                        <tr><td colspan="7" class="p-8 text-center text-gray-500">Tidak ada menu ditemukan.</td></tr>
                    <?php else: ?>
                        <?php $no = ($currentPage - 1) * $limit + 1; foreach ($menus as $m): ?>
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="p-4 text-center text-gray-500"><?= $no++ ?></td>
                                <td class="p-4 font-semibold text-gray-800">
                                    <i class="fa-solid fa-<?= $m['icon'] ?> mr-2 text-gray-400"></i>
                                    <?= htmlspecialchars($m['title']) ?>
                                </td>
                                <td class="p-4 text-gray-500">
                                    <?= $m['parent_name'] ? htmlspecialchars($m['parent_name']) : '<span class="text-xs bg-gray-100 px-2 py-1 rounded">MAIN MENU</span>' ?>
                                </td>
                                <td class="p-4 text-gray-500 font-mono text-xs"><?= htmlspecialchars($m['url']) ?></td>
                                <td class="p-4 text-center"><?= $m['order_num'] ?></td>
                                <td class="p-4 text-center">
                                    <form action="/settings/menus/toggle" method="POST" class="inline-block">
                                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                        <button type="submit" class="px-3 py-1 rounded-full text-xs font-bold transition-colors <?= $m['is_active'] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' ?>">
                                            <?= $m['is_active'] ? 'AKTIF' : 'NONAKTIF' ?>
                                        </button>
                                    </form>
                                </td>
                                <td class="p-4 text-center">
                                    <button @click="editModalOpen = true; currentId = '<?= $m['id'] ?>'; currentTitle = '<?= htmlspecialchars($m['title']) ?>'; currentUrl = '<?= htmlspecialchars($m['url']) ?>'; currentIcon = '<?= htmlspecialchars($m['icon']) ?>'; currentOrder = '<?= $m['order_num'] ?>'; currentParent = '<?= $m['parent_id'] ?>'" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition mr-1" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button @click="deleteModalOpen = true; currentId = '<?= $m['id'] ?>'" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-b-xl">
            <span class="text-sm text-gray-500">
                Menampilkan <?= min($totalData, ($currentPage - 1) * $limit + 1) ?> sampai <?= min($totalData, $currentPage * $limit) ?> dari <?= $totalData ?> entri
            </span>
            <div class="flex gap-1">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&limit=<?= $limit ?>&search=<?= urlencode($search) ?>"
                       class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition <?= $i === $currentPage ? 'bg-blue-600 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Create Modal -->
    <div x-cloak x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50 backdrop-blur-sm">
        <div @click.away="modalOpen = false" class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Tambah Menu Baru</h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="/settings/menus/store" method="POST" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Menu <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parent Menu</label>
                        <select name="parent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Main Menu (Kosongkan jika bukan submenu) --</option>
                            <?php foreach($parents as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL Path</label>
                            <input type="text" name="url" placeholder="/path/to/page" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Icon (FontAwesome)</label>
                            <input type="text" name="icon" placeholder="circle" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Urutan (Order)</label>
                            <input type="number" name="order_num" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-center mt-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">Menu Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-cloak x-show="editModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50 backdrop-blur-sm">
        <div @click.away="editModalOpen = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Edit Menu</h3>
                <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="/settings/menus/update" method="POST" class="p-6">
                <input type="hidden" name="id" x-model="currentId">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Menu <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="currentTitle" required class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parent Menu</label>
                        <select name="parent_id" x-model="currentParent" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Main Menu --</option>
                            <?php foreach($parents as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL Path</label>
                            <input type="text" name="url" x-model="currentUrl" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                            <input type="text" name="icon" x-model="currentIcon" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                        <input type="number" name="order_num" x-model="currentOrder" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-cloak x-show="deleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50 backdrop-blur-sm">
        <div @click.away="deleteModalOpen = false" class="bg-white rounded-xl shadow-xl w-full max-w-sm overflow-hidden transform transition-all text-center p-6">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-triangle-exclamation text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Hapus Menu?</h3>
            <p class="text-sm text-gray-500 mb-6">Penghapusan menu juga akan menghapus seluruh permission yang terkait dengannya. Lanjutkan?</p>
            <form action="/settings/menus/delete" method="POST" class="flex justify-center gap-3">
                <input type="hidden" name="id" x-model="currentId">
                <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 w-full">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 w-full">Hapus</button>
            </form>
        </div>
    </div>

</main>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>