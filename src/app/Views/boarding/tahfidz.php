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
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Monitoring Tahfidz</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Pencatatan setoran hafalan santri harian.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-bold border border-green-100">
                    <i class="fa-solid fa-book-quran"></i> Total Log: <?= $totalData ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Setoran -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 h-fit">
            <h4 class="font-bold text-blue-700 flex items-center gap-2 border-b border-slate-100 pb-4 mb-5">
                <i class="fa-solid fa-book-quran text-blue-400"></i> Setoran Hafalan
            </h4>
            <form action="/boarding/tahfidz/store" method="POST" class="space-y-4">
                <?= \App\Core\Csrf::input() ?>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Santri</label>
                    <select name="student_id" class="select2-student w-full" required>
                        <option value="">-- Cari & Pilih Santri --</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?> (<?= $s['nis'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal</label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jenis Setoran</label>
                    <select name="type" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="ZIYADAH">Ziyadah (Hafalan Baru)</option>
                        <option value="MUROJAAH">Murojaah (Mengulang)</option>
                        <option value="TILAWAH">Tilawah (Binadzor)</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Surat</label>
                        <input type="text" name="surah_name" placeholder="cth: An-Naba"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Ayat</label>
                        <input type="text" name="verses" placeholder="cth: 1-10"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2">Kelancaran</label>
                    <div class="flex gap-3">
                        <?php foreach (['A' => 'Mumtaz', 'B' => 'Jayyid', 'C' => 'Kurang'] as $val => $label): ?>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="grade" value="<?= $val ?>" <?= $val == 'A' ? 'checked' : '' ?> class="peer sr-only">
                            <div class="text-center py-2 rounded-xl border text-xs font-bold text-slate-400 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 border-slate-200 transition-all">
                                <?= $val ?> — <?= $label ?>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Catatan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <textarea name="note" rows="2" placeholder="cth: Perlu perbaikan makhroj huruf..."
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"></textarea>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all text-sm">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Hafalan
                </button>
            </form>
        </div>

        <!-- Tabel Log -->
        <div class="lg:col-span-2 flex flex-col gap-6">

            <!-- Filter -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="limit" value="<?= $limit ?>">
                    <div class="flex-1 min-w-[180px] relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama santri atau surat..."
                            class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    </div>
                    <select name="type" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="">Semua Jenis</option>
                        <option value="ZIYADAH"  <?= $typeFilter == 'ZIYADAH'  ? 'selected' : '' ?>>Ziyadah</option>
                        <option value="MUROJAAH" <?= $typeFilter == 'MUROJAAH' ? 'selected' : '' ?>>Murojaah</option>
                        <option value="TILAWAH"  <?= $typeFilter == 'TILAWAH'  ? 'selected' : '' ?>>Tilawah</option>
                    </select>
                    <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
                    <?php if (!empty($search) || !empty($typeFilter)): ?>
                        <a href="/boarding/tahfidz" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
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
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tgl</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Santri</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Hafalan</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if (empty($logs)): ?>
                                <tr><td colspan="4" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Belum ada log setoran.</td></tr>
                            <?php endif; ?>
                            <?php
                            $typeColors = [
                                'ZIYADAH'  => 'bg-green-50 text-green-700 border-green-200',
                                'MUROJAAH' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'TILAWAH'  => 'bg-purple-50 text-purple-700 border-purple-200',
                            ];
                            ?>
                            <?php foreach ($logs as $l): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                                <td class="px-5 py-4 font-mono text-xs text-slate-500"><?= date('d/m/Y', strtotime($l['date'])) ?></td>
                                <td class="px-5 py-4">
                                    <div class="font-extrabold text-slate-800"><?= $l['full_name'] ?></div>
                                    <div class="text-[10px] text-slate-400 font-mono"><?= $l['nis'] ?></div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border <?= $typeColors[$l['type']] ?? 'bg-slate-100 text-slate-600 border-slate-200' ?>">
                                        <?= $l['type'] ?>
                                    </span>
                                    <span class="ml-2 font-bold text-slate-700 text-xs"><?= $l['surah_name'] ?>: <?= $l['verses'] ?></span>
                                    <?php if ($l['note']): ?>
                                        <div class="text-[10px] text-slate-400 italic mt-0.5"><?= $l['note'] ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="text-xl font-extrabold <?= $l['grade']=='A' ? 'text-green-600' : ($l['grade']=='B' ? 'text-blue-600' : 'text-red-500') ?>">
                                        <?= $l['grade'] ?>
                                    </span>
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
                        <?php $qs = "&limit=$limit&search=" . urlencode($search) . "&type=$typeFilter"; ?>
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
    </div>
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Monitoring Tahfidz</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Jenis Setoran</h4>
                <div class="flex flex-wrap gap-2">
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-green-50 text-green-700 border border-green-200">ZIYADAH — Hafalan Baru</span>
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-200">MUROJAAH — Mengulang</span>
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-purple-50 text-purple-700 border border-purple-200">TILAWAH — Membaca Binadzor</span>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Skala Nilai</h4>
                <div class="flex flex-wrap gap-2 text-xs font-bold">
                    <span class="text-green-600">A — Mumtaz (Lancar)</span>
                    <span class="text-blue-600">B — Jayyid (Sedang)</span>
                    <span class="text-red-500">C — Kurang</span>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-file-invoice text-orange-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Rapor Asrama</div><div class="text-[11px] text-slate-400">Nilai tahfidz dirangkum di <strong>Laporan → Rapor Asrama</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-book-open text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Jurnal Kitab</div><div class="text-[11px] text-slate-400">Kegiatan tilawah berkaitan dengan <strong>Akademik → Jurnal Kitab</strong>.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-student').select2({ placeholder: '-- Cari & Pilih Santri --', allowClear: true });
    });
    function updateQS(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var sep = uri.indexOf('?') !== -1 ? "&" : "?";
        return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + sep + key + "=" + value;
    }
    window.onclick = function(e) { if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden'); }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
