<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Perpustakaan</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Tracking peminjaman dan pengembalian buku perpustakaan.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-book-open"></i> Total Transaksi: <?= $totalData ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2 w-fit">
            <i class="fa-solid fa-plus"></i> Catat Peminjaman
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <?php foreach ([
            ['Dipinjam',     $stats['dipinjam'],  'blue',   'fa-book'],
            ['Dikembalikan', $stats['kembali'],   'green',  'fa-check-circle'],
            ['Terlambat',    $stats['terlambat'], 'red',    'fa-clock'],
            ['Total Buku',   count($books),       'purple', 'fa-book-open'],
        ] as [$label, $val, $color, $icon]): ?>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider"><?= $label ?></span>
                <div class="w-8 h-8 rounded-xl bg-<?= $color ?>-100 text-<?= $color ?>-600 flex items-center justify-center">
                    <i class="fa-solid <?= $icon ?> text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-800"><?= $val ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="flex flex-col gap-6">
        <!-- Filter -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <div class="flex-1 min-w-[200px] relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama santri, NIS, atau judul buku..."
                        class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
                <select name="status" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="">Semua Status</option>
                    <option value="DIPINJAM"     <?= $status==='DIPINJAM'     ?'selected':'' ?>>Dipinjam</option>
                    <option value="DIKEMBALIKAN" <?= $status==='DIKEMBALIKAN' ?'selected':'' ?>>Dikembalikan</option>
                    <option value="TERLAMBAT"    <?= $status==='TERLAMBAT'    ?'selected':'' ?>>Terlambat</option>
                </select>
                <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
                <?php if (!empty($search) || !empty($status)): ?>
                    <a href="/library" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full whitespace-nowrap text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Santri</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Buku</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tgl Pinjam</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Batas Kembali</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tgl Kembali</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php if (empty($loans)): ?>
                            <tr><td colspan="7" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Belum ada data peminjaman.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($loans as $l):
                            $badge = match($l['status']) {
                                'DIPINJAM'     => 'bg-blue-50 text-blue-700 border-blue-200',
                                'DIKEMBALIKAN' => 'bg-green-50 text-green-700 border-green-200',
                                'TERLAMBAT'    => 'bg-red-50 text-red-700 border-red-200',
                                default        => 'bg-slate-100 text-slate-600 border-slate-200',
                            };
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-800"><?= htmlspecialchars($l['full_name']) ?></div>
                                <div class="text-[10px] text-slate-400 font-mono"><?= $l['nis'] ?></div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-700"><?= htmlspecialchars($l['book_title']) ?></div>
                                <div class="text-[10px] text-slate-400 font-mono"><?= $l['book_code'] ?></div>
                            </td>
                            <td class="px-5 py-4 text-slate-600 text-xs font-mono"><?= date('d/m/Y', strtotime($l['loan_date'])) ?></td>
                            <td class="px-5 py-4 text-slate-600 text-xs font-mono"><?= date('d/m/Y', strtotime($l['due_date'])) ?></td>
                            <td class="px-5 py-4 text-slate-600 text-xs font-mono"><?= $l['return_date'] ? date('d/m/Y', strtotime($l['return_date'])) : '-' ?></td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border <?= $badge ?>"><?= $l['status'] ?></span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <?php if ($l['status'] !== 'DIKEMBALIKAN'): ?>
                                <button onclick="openReturnModal(<?= $l['id'] ?>)"
                                    class="w-8 h-8 rounded-lg bg-green-50 text-green-500 hover:bg-green-500 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Kembalikan">
                                    <i class="fa-solid fa-rotate-left text-sm"></i>
                                </button>
                                <?php else: ?>
                                <span class="text-slate-300 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Show:</span>
                    <select onchange="window.location.href=updateQS(window.location.href,'limit',this.value)"
                        class="border border-slate-300 rounded-lg px-2 py-1 text-sm outline-none bg-white font-medium">
                        <option value="10" <?= $limit==10?'selected':'' ?>>10 entries</option>
                        <option value="25" <?= $limit==25?'selected':'' ?>>25 entries</option>
                        <option value="50" <?= $limit==50?'selected':'' ?>>50 entries</option>
                    </select>
                    <span class="text-xs text-slate-500"><?= min($totalData,($currentPage-1)*$limit+1) ?>–<?= min($totalData,$currentPage*$limit) ?> dari <?= $totalData ?></span>
                </div>
                <?php if ($totalPages > 1): ?>
                <div class="flex items-center gap-1.5">
                    <?php $qs = "&limit=$limit&search=".urlencode($search)."&status=".urlencode($status); ?>
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage-1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-blue-600 transition-colors shadow-sm"><i class="fa-solid fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <span class="text-xs font-bold text-slate-600 px-2">Hal <?= $currentPage ?> / <?= $totalPages ?></span>
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage+1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-blue-600 transition-colors shadow-sm"><i class="fa-solid fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Modal Catat Peminjaman -->
<div id="addModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-book text-slate-400"></i> Catat Peminjaman</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/library/store" method="POST" class="p-6 overflow-y-auto space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Santri</label>
                <select name="student_id" id="studentLibSelect" class="w-full" required>
                    <option value="">-- Cari santri... --</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['nis'] ?> — <?= htmlspecialchars($s['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Buku</label>
                <select name="book_id" id="bookSelect" class="w-full" required>
                    <option value="">-- Cari buku... --</option>
                    <?php foreach ($books as $b): ?>
                        <option value="<?= $b['id'] ?>">[<?= $b['code'] ?>] <?= htmlspecialchars($b['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal Pinjam</label>
                <input type="date" name="loan_date" value="<?= date('Y-m-d') ?>"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                <p class="text-[10px] text-slate-400 mt-1">Batas pengembalian otomatis 14 hari dari tanggal pinjam.</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Catatan</label>
                <input type="text" name="notes" placeholder="Opsional..."
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kembalikan -->
<div id="returnModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-rotate-left text-green-500"></i> Kembalikan Buku</h3>
            <button onclick="document.getElementById('returnModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/library/return" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="return_id">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal Pengembalian</label>
                <input type="date" name="return_date" value="<?= date('Y-m-d') ?>"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('returnModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-green-600 text-white py-2.5 rounded-xl font-bold hover:bg-green-700 shadow-md shadow-green-500/20 transition text-sm">Konfirmasi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Perpustakaan</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Klik <strong class="text-slate-700">Catat Peminjaman</strong> untuk mencatat santri yang meminjam buku.</li>
                    <li>Batas pengembalian otomatis <strong class="text-slate-700">14 hari</strong> dari tanggal pinjam.</li>
                    <li>Klik ikon <strong class="text-slate-700">kembalikan</strong> saat buku dikembalikan. Status otomatis jadi <strong class="text-red-600">TERLAMBAT</strong> jika melewati batas.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-graduation-cap text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Santri</div><div class="text-[11px] text-slate-400">Peminjam diambil dari <strong>Kesiswaan → Data Santri</strong>.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
    .select2-container--default .select2-selection--single { border-color: #e2e8f0; height: 42px; padding-top: 6px; border-radius: 0.75rem; background-color: #f8fafc; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
    .select2-dropdown { border-color: #e2e8f0; border-radius: 0.75rem; overflow: hidden; }
    .select2-search--dropdown .select2-search__field { border-radius: 0.5rem; border-color: #e2e8f0; padding: 6px 10px; }
    .select2-container { width: 100% !important; }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $('#studentLibSelect').select2({ placeholder: '-- Cari santri...', allowClear: true, dropdownParent: $('#addModal') });
    $('#bookSelect').select2({ placeholder: '-- Cari buku...', allowClear: true, dropdownParent: $('#addModal') });

    function openReturnModal(id) {
        document.getElementById('return_id').value = id;
        document.getElementById('returnModal').classList.remove('hidden');
    }
    function updateQS(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var sep = uri.indexOf('?') !== -1 ? "&" : "?";
        return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + sep + key + "=" + value;
    }
    window.onclick = function(e) {
        ['addModal','returnModal','infoModal'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el && e.target == el) el.classList.add('hidden');
        });
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
