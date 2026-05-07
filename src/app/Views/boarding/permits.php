<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single { border-color: #e2e8f0; height: 42px; padding-top: 6px; border-radius: 0.75rem; background-color: #f8fafc; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
    .select2-container { width: 100% !important; }
    .select2-dropdown { border-color: #e2e8f0; border-radius: 0.75rem; overflow: hidden; }
    .select2-search--dropdown .select2-search__field { border-radius: 0.5rem; border-color: #e2e8f0; padding: 6px 10px; }
</style>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Perizinan Santri</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola izin keluar dan kepulangan santri asrama.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-door-open"></i> Total Izin: <?= $totalData ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2 w-fit">
            <i class="fa-solid fa-plus"></i> Buat Izin Baru
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
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama atau NIS santri..."
                        class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
                <select name="status" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="">Semua Status</option>
                    <option value="PENDING"  <?= $statusFilter == 'PENDING'  ? 'selected' : '' ?>>Menunggu</option>
                    <option value="APPROVED" <?= $statusFilter == 'APPROVED' ? 'selected' : '' ?>>Disetujui</option>
                    <option value="REJECTED" <?= $statusFilter == 'REJECTED' ? 'selected' : '' ?>>Ditolak</option>
                    <option value="RETURNED" <?= $statusFilter == 'RETURNED' ? 'selected' : '' ?>>Kembali</option>
                </select>
                <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
                <?php if (!empty($search) || !empty($statusFilter)): ?>
                    <a href="/boarding/permits" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
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
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Santri</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Jenis / Alasan</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php if (empty($permits)): ?>
                            <tr><td colspan="5" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Belum ada data perizinan.</td></tr>
                        <?php endif; ?>

                        <?php
                        $typeColors = [
                            'KELUAR' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'PULANG' => 'bg-orange-50 text-orange-700 border-orange-200',
                            'SAKIT'  => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                        ];
                        $statusColors = [
                            'PENDING'  => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'APPROVED' => 'bg-green-50 text-green-700 border-green-200',
                            'REJECTED' => 'bg-red-50 text-red-700 border-red-200',
                            'RETURNED' => 'bg-slate-100 text-slate-600 border-slate-200',
                        ];
                        ?>
                        <?php foreach ($permits as $p): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-800"><?= $p['full_name'] ?></div>
                                <div class="text-[10px] text-slate-400 mt-0.5"><?= $p['dorm_name'] ?? 'Non-Asrama' ?></div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border <?= $typeColors[$p['type']] ?? 'bg-slate-100 text-slate-600 border-slate-200' ?>">
                                    <?= $p['type'] ?>
                                </span>
                                <div class="text-xs text-slate-600 mt-1"><?= $p['reason'] ?></div>
                            </td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-500"><?= substr($p['start_date'], 0, 16) ?></td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 text-[10px] font-bold rounded-full border <?= $statusColors[$p['status']] ?? 'bg-slate-100 text-slate-600 border-slate-200' ?>">
                                    <?= $p['status'] ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <?php if ($p['status'] == 'PENDING'): ?>
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="/boarding/permits/approve?id=<?= $p['id'] ?>&action=APPROVE"
                                            class="px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-lg text-[11px] font-bold border border-green-200 transition-colors">
                                            Setujui
                                        </a>
                                        <a href="/boarding/permits/approve?id=<?= $p['id'] ?>&action=REJECT"
                                            class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg text-[11px] font-bold border border-red-200 transition-colors">
                                            Tolak
                                        </a>
                                    </div>
                                <?php elseif ($p['status'] == 'APPROVED'): ?>
                                    <a href="/boarding/permits/approve?id=<?= $p['id'] ?>&action=RETURN"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-[11px] font-bold hover:bg-blue-700 transition-colors shadow-sm">
                                        <i class="fa-solid fa-rotate-left"></i> Catat Kembali
                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-400 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
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
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50 entries</option>
                    </select>
                </div>
                <?php if ($totalPages > 1): ?>
                <div class="flex items-center gap-1.5">
                    <?php $qs = "&limit=$limit&search=" . urlencode($search) . "&status=$statusFilter"; ?>
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
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Perizinan Santri</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 overflow-y-auto max-h-[70vh]">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Alur Perizinan</h4>
                <div class="flex items-center gap-2 flex-wrap text-xs font-bold">
                    <span class="px-2.5 py-1 bg-yellow-50 text-yellow-700 rounded-lg border border-yellow-200">PENDING</span>
                    <i class="fa-solid fa-arrow-right text-slate-400"></i>
                    <span class="px-2.5 py-1 bg-green-50 text-green-700 rounded-lg border border-green-200">APPROVED</span>
                    <i class="fa-solid fa-arrow-right text-slate-400"></i>
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg border border-slate-200">RETURNED</span>
                </div>
                <p class="text-slate-400 text-xs mt-2">Izin yang ditolak akan berstatus <strong>REJECTED</strong>.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Jenis Izin</h4>
                <div class="flex flex-wrap gap-2">
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-200">KELUAR — Keluar Sebentar</span>
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-orange-50 text-orange-700 border border-orange-200">PULANG — Pulang Kampung</span>
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-yellow-50 text-yellow-700 border border-yellow-200">SAKIT — Sakit/Rawat</span>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-building text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Manajemen Asrama</div><div class="text-[11px] text-slate-400">Data kamar santri diambil dari <strong>Asrama → Manajemen Asrama</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-person-walking-arrow-right text-orange-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Mutasi Asrama</div><div class="text-[11px] text-slate-400">Kepulangan permanen dicatat di menu <strong>Asrama → Mutasi</strong>.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<!-- Modal Buat Izin -->
<div id="addModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-door-open text-slate-400"></i> Buat Izin Baru</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/boarding/permits/store" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="start_date" value="<?= date('Y-m-d H:i') ?>">
            <input type="hidden" name="end_date" value="<?= date('Y-m-d H:i', strtotime('+1 day')) ?>">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Pilih Santri</label>
                <select name="student_id" class="select2-student w-full" required>
                    <option value="">-- Cari & Pilih Santri --</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?> (<?= $s['nis'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jenis Izin</label>
                <select name="type" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="KELUAR">Keluar Sebentar</option>
                    <option value="PULANG">Pulang Kampung</option>
                    <option value="SAKIT">Sakit / Rawat</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Alasan</label>
                <input type="text" name="reason" placeholder="cth: Menjenguk orang tua sakit"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Buat Izin</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-student').select2({ placeholder: '-- Cari & Pilih Santri --', allowClear: true, dropdownParent: $('#addModal') });
    });
    function updateQS(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var sep = uri.indexOf('?') !== -1 ? "&" : "?";
        return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + sep + key + "=" + value;
    }
    window.onclick = function(e) {
        ['addModal','infoModal'].forEach(function(id) {
            if (e.target == document.getElementById(id)) document.getElementById(id).classList.add('hidden');
        });
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
