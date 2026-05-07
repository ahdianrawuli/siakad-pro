<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 md:p-6" x-data="{ modalOpen: false, editModalOpen: false, deleteModalOpen: false, currentId: '', currentTitle: '', currentContent: '', currentTarget: '', currentStatus: '' }">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Manajemen Pengumuman</h1>
            <p class="text-sm text-gray-500">Kelola dan broadcast pengumuman sekolah.</p>
        </div>
        <button @click="modalOpen = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2 shadow-sm">
            <i class="fa-solid fa-bullhorn"></i> Buat Pengumuman
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
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari judul / konten..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
                <select name="target" class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none w-full md:w-48 bg-white">
                    <option value="">Semua Target</option>
                    <option value="ALL" <?= $target === 'ALL' ? 'selected' : '' ?>>Semua Warga Sekolah</option>
                    <option value="STUDENTS" <?= $target === 'STUDENTS' ? 'selected' : '' ?>>Hanya Siswa</option>
                    <option value="PARENTS" <?= $target === 'PARENTS' ? 'selected' : '' ?>>Hanya Orang Tua</option>
                    <option value="TEACHERS" <?= $target === 'TEACHERS' ? 'selected' : '' ?>>Hanya Guru</option>
                    <option value="STAFF" <?= $target === 'STAFF' ? 'selected' : '' ?>>Hanya Staff</option>
                </select>
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
                        <th class="p-4 font-bold border-b border-gray-100 w-1/4">Judul Pengumuman</th>
                        <th class="p-4 font-bold border-b border-gray-100 w-1/3">Konten</th>
                        <th class="p-4 font-bold border-b border-gray-100 text-center">Target</th>
                        <th class="p-4 font-bold border-b border-gray-100 text-center">Status</th>
                        <th class="p-4 font-bold border-b border-gray-100 text-center">Tgl Dibuat</th>
                        <th class="p-4 font-bold border-b border-gray-100 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                    <?php if (empty($announcements)): ?>
                        <tr><td colspan="7" class="p-8 text-center text-gray-500">Tidak ada pengumuman ditemukan.</td></tr>
                    <?php else: ?>
                        <?php $no = ($currentPage - 1) * $limit + 1; foreach ($announcements as $a): ?>
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="p-4 text-center text-gray-500"><?= $no++ ?></td>
                                <td class="p-4 font-semibold text-gray-800"><?= htmlspecialchars($a['title']) ?></td>
                                <td class="p-4 text-gray-500 truncate max-w-xs" title="<?= htmlspecialchars($a['content']) ?>"><?= htmlspecialchars($a['content']) ?></td>
                                <td class="p-4 text-center">
                                    <?php
                                        $targetColors = [
                                            'ALL' => 'bg-indigo-100 text-indigo-700',
                                            'STUDENTS' => 'bg-blue-100 text-blue-700',
                                            'PARENTS' => 'bg-emerald-100 text-emerald-700',
                                            'TEACHERS' => 'bg-amber-100 text-amber-700',
                                            'STAFF' => 'bg-gray-100 text-gray-700'
                                        ];
                                        $color = $targetColors[$a['target_audience']] ?? 'bg-gray-100 text-gray-700';
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold <?= $color ?>">
                                        <?= $a['target_audience'] ?>
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <?php if($a['status'] === 'PUBLISHED'): ?>
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-green-100 text-green-700"><i class="fa-solid fa-check-circle mr-1"></i> PUBLISHED</span>
                                    <?php elseif($a['status'] === 'DRAFT'): ?>
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-amber-100 text-amber-700"><i class="fa-solid fa-clock mr-1"></i> DRAFT</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-gray-100 text-gray-500"><i class="fa-solid fa-archive mr-1"></i> ARCHIVED</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-center whitespace-nowrap text-gray-500">
                                    <?= date('d M Y H:i', strtotime($a['created_at'])) ?>
                                </td>
                                <td class="p-4 text-center">
                                    <button @click="editModalOpen = true; currentId = '<?= $a['id'] ?>'; currentTitle = '<?= addslashes($a['title']) ?>'; currentContent = '<?= addslashes(str_replace(["\r\n","\r","\n"], '\n', $a['content'])) ?>'; currentTarget = '<?= $a['target_audience'] ?>'; currentStatus = '<?= $a['status'] ?>'" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition mr-1" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button @click="deleteModalOpen = true; currentId = '<?= $a['id'] ?>'" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition" title="Hapus">
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
                    <a href="?page=<?= $i ?>&limit=<?= $limit ?>&search=<?= urlencode($search) ?>&target=<?= urlencode($target) ?>"
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
        <div @click.away="modalOpen = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Buat Pengumuman Baru</h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="/announcements/store" method="POST" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Pengumuman <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pesan / Konten <span class="text-red-500">*</span></label>
                        <textarea name="content" required rows="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ketik pengumuman disini..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Target Broadcast WA</label>
                            <select name="target_audience" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="ALL">Semua Warga Sekolah</option>
                                <option value="STUDENTS">Siswa Saja</option>
                                <option value="PARENTS">Orang Tua Saja</option>
                                <option value="TEACHERS">Guru Saja</option>
                                <option value="STAFF">Staff Saja</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="PUBLISHED">Publish & Kirim WA</option>
                                <option value="DRAFT">Simpan Saja (Draft)</option>
                            </select>
                        </div>
                    </div>
                    <div class="bg-blue-50 text-blue-800 p-3 rounded-lg text-xs flex gap-2">
                        <i class="fa-solid fa-circle-info mt-0.5"></i>
                        <p>Memilih status <strong>Publish & Kirim WA</strong> akan mengirimkan pesan otomatis ke semua nomor WhatsApp target yang aktif secara bersamaan.</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"><i class="fa-solid fa-paper-plane mr-2"></i> Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-cloak x-show="editModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50 backdrop-blur-sm">
        <div @click.away="editModalOpen = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Edit Pengumuman</h3>
                <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="/announcements/update" method="POST" class="p-6">
                <input type="hidden" name="id" x-model="currentId">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Pengumuman <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="currentTitle" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pesan / Konten <span class="text-red-500">*</span></label>
                        <textarea name="content" x-model="currentContent.replace(/\\n/g, '\n')" required rows="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Target Broadcast</label>
                            <select name="target_audience" x-model="currentTarget" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="ALL">Semua Warga Sekolah</option>
                                <option value="STUDENTS">Siswa Saja</option>
                                <option value="PARENTS">Orang Tua Saja</option>
                                <option value="TEACHERS">Guru Saja</option>
                                <option value="STAFF">Staff Saja</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" x-model="currentStatus" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="PUBLISHED">PUBLISHED</option>
                                <option value="DRAFT">DRAFT</option>
                                <option value="ARCHIVED">ARCHIVED</option>
                            </select>
                        </div>
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
            <h3 class="text-xl font-bold text-gray-800 mb-2">Hapus Pengumuman?</h3>
            <p class="text-sm text-gray-500 mb-6">Data yang dihapus tidak dapat dikembalikan. Apakah Anda yakin?</p>
            <form action="/announcements/delete" method="POST" class="flex justify-center gap-3">
                <input type="hidden" name="id" x-model="currentId">
                <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 w-full">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 w-full">Ya, Hapus</button>
            </form>
        </div>
    </div>

</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
