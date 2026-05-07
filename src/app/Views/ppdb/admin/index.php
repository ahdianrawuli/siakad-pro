<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6" x-data="{ viewColumns: false }">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Data Pendaftar PPDB</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Mengelola dan memverifikasi calon santri baru secara terpusat.</p>
            <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                <i class="fa-solid fa-users"></i> Total Pendaftar: <?= $totalData ?>
            </div>
        </div>

        <!-- Global Actions -->
        <div class="flex flex-wrap gap-2">
            <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan">
                <i class="fa-solid fa-circle-info text-sm"></i>
            </button>
        </div>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <!-- Banner Gelombang & Tahun Ajaran -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-500 rounded-2xl shadow-sm p-4 mb-6 flex flex-col md:flex-row justify-between items-center text-white border border-blue-400/30">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <i class="fa-solid fa-bullhorn text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-blue-100 uppercase tracking-widest">Tahun Ajaran Aktif</p>
                <h4 class="text-lg font-bold">2024 / 2025</h4>
            </div>
        </div>
        <div class="h-8 w-px bg-blue-400/50 hidden md:block mx-4"></div>
        <div class="flex items-center gap-3 mt-4 md:mt-0">
            <div>
                <p class="text-xs font-semibold text-blue-100 uppercase tracking-widest text-right">Gelombang PPDB</p>
                <h4 class="text-lg font-bold text-right">Gelombang 1 (Reguler & Prestasi)</h4>
            </div>
            <a href="/ppdb/periods" class="px-3 py-1.5 bg-white text-blue-600 rounded-lg text-xs font-bold hover:bg-blue-50 transition-colors whitespace-nowrap shadow-sm">
                Ubah Pengaturan <i class="fa-solid fa-gear ml-1"></i>
            </a>
        </div>
    </div>

    <div class="flex flex-col gap-6">

        <!-- Filter Section -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-filter text-slate-400"></i> Filter Pencarian
                </h4>
            </div>
            
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="limit" value="<?= $limit ?>">

                <div class="flex-1 min-w-[220px] relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari Nama / NISN / No. Reg..."
                        class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>

                <select name="status" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="">Semua Status</option>
                    <option value="PENDING"  <?= ($selectedStatus??'')==='PENDING'  ?'selected':'' ?>>Pending</option>
                    <option value="PAID"     <?= ($selectedStatus??'')==='PAID'     ?'selected':'' ?>>Lunas</option>
                    <option value="VERIFIED" <?= ($selectedStatus??'')==='VERIFIED' ?'selected':'' ?>>Terverifikasi</option>
                    <option value="ACCEPTED" <?= ($selectedStatus??'')==='ACCEPTED' ?'selected':'' ?>>Diterima</option>
                    <option value="REJECTED" <?= ($selectedStatus??'')==='REJECTED' ?'selected':'' ?>>Ditolak</option>
                </select>

                <select name="track_id" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="">Semua Jalur</option>
                    <?php foreach($tracks as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($selectedTrack??'')==$t['id'] ?'selected':'' ?>><?= htmlspecialchars($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
                <?php if(!empty($search) || !empty($selectedStatus) || !empty($selectedTrack)): ?>
                    <a href="/ppdb/registrations" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Data Table Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col relative z-0">
            <div class="overflow-x-auto custom-scrollbar pb-4">
                <table class="min-w-full whitespace-nowrap text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider sticky left-0 z-10 bg-slate-50">Profil Santri</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Identitas & Kontak</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Pendidikan Asal</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Jalur & Lokasi</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status Pembayaran</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Dokumen & Grup</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center sticky right-0 z-10 bg-slate-50 shadow-[-4px_0_15px_-3px_rgba(0,0,0,0.05)]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php if (empty($candidates)): ?>
                        <tr><td colspan="7" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Belum ada data pendaftar yang sesuai kriteria.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($candidates as $row): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors group">

                            <!-- Profil Utama -->
                            <td class="px-4 py-4 sticky left-0 z-10 bg-white group-hover:bg-slate-50/80 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shadow-inner">
                                        <?= substr($row['full_name'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <a href="/ppdb/registrations/detail?id=<?= $row['id'] ?>" class="font-extrabold text-slate-800 hover:text-blue-600 transition-colors"><?= $row['full_name'] ?></a>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-[10px] font-bold border border-slate-200 font-mono"><?= $row['registration_no'] ?></span>
                                            <span class="text-[10px] font-semibold <?= $row['gender'] == 'L' ? 'text-blue-500' : 'text-pink-500' ?>"><i class="fa-solid <?= $row['gender'] == 'L' ? 'fa-mars' : 'fa-venus' ?>"></i> <?= $row['gender'] == 'L' ? 'L' : 'P' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Identitas & Kontak -->
                            <td class="px-4 py-4 text-sm text-slate-600">
                                <div class="font-mono text-xs mb-1"><span class="text-slate-400">NISN:</span> <?= $row['nisn'] ?? 'Belum ada' ?></div>
                                <?php
                                    $ttl = ($row['birth_place'] ?? 'Belum diisi') . ', ' . ($row['birth_date'] ? date('d M Y', strtotime($row['birth_date'])) : '-');
                                ?>
                                <div class="truncate max-w-[200px] text-xs" title="<?= $ttl ?>"><i class="fa-regular fa-calendar text-slate-400 mr-1"></i> <?= $ttl ?></div>
                                <div class="mt-2">
                                    <a href="https://wa.me/62<?= ltrim($row['whatsapp_number'] ?? '', '0') ?>" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 text-green-700 hover:bg-green-100 rounded-lg text-xs font-bold transition-colors">
                                        <i class="fa-brands fa-whatsapp text-sm"></i> <?= $row['whatsapp_number'] ?? 'Belum ada' ?>
                                    </a>
                                </div>
                            </td>

                            <!-- Pendidikan Asal -->
                            <td class="px-4 py-4 text-sm text-slate-600">
                                <div class="font-bold text-slate-700"><?= $row['school_origin'] ?? 'Belum ada data' ?></div>
                                <div class="font-mono text-[10px] text-slate-400 mt-0.5">NPSN: <?= $row['npsn'] ?? '-' ?></div>
                                <div class="text-[10px] text-slate-500 mt-1 truncate max-w-[180px]">
                                    <i class="fa-solid fa-location-dot text-slate-400 mr-1"></i>
                                    <?= trim(($row['city'] ?? '') . ', ' . ($row['province'] ?? ''), ', ') ?: 'Alamat belum lengkap' ?>
                                </div>
                            </td>

                            <!-- Jalur & Lokasi -->
                            <td class="px-4 py-4 text-sm text-slate-600">
                                <div class="font-bold text-slate-700"><?= $row['track_name'] ?? 'Jalur Reguler' ?></div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] font-semibold bg-slate-100 px-2 py-0.5 rounded border border-slate-200">Level: <?= $row['track_level'] ?? '-' ?></span>
                                    <span class="text-[10px] font-semibold text-slate-500"><i class="fa-solid fa-building-columns mr-1"></i> <?= $row['exam_location'] ?? 'Offline' ?></span>
                                </div>
                            </td>

                            <!-- Status Pembayaran -->
                            <td class="px-4 py-4 text-sm">
                                <?php if(($row['payment_status'] ?? '') == 'VERIFIED'): ?>
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-bold border border-green-200">
                                        <i class="fa-solid fa-check-circle"></i> Lunas
                                    </div>
                                <?php elseif(($row['payment_status'] ?? '') == 'PENDING'): ?>
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-bold border border-amber-200">
                                        <i class="fa-solid fa-clock animate-spin-slow"></i> Validasi
                                    </div>
                                <?php elseif(($row['payment_status'] ?? '') == 'REJECTED'): ?>
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 rounded-full text-xs font-bold border border-red-200">
                                        <i class="fa-solid fa-circle-xmark"></i> Batal
                                    </div>
                                <?php else: ?>
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-bold border border-slate-200">
                                        <i class="fa-solid fa-wallet"></i> Menunggu Bayar
                                    </div>
                                <?php endif; ?>
                                <div class="font-mono text-[10px] text-slate-400 mt-2">Status: <?= $row['registration_status'] ?? 'PENDING' ?></div>
                            </td>

                            <!-- Dokumen & Group -->
                            <td class="px-4 py-4 text-sm">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs font-semibold text-slate-500">Status:</span>
                                    <?php
                                        $regStatus = $row['registration_status'] ?? 'PENDING';
                                        $statusClass = match($regStatus) {
                                            'ACCEPTED' => 'bg-blue-50 text-blue-600',
                                            'REJECTED' => 'bg-red-50 text-red-600',
                                            'VERIFIED', 'PAID' => 'bg-green-50 text-green-600',
                                            default => 'bg-slate-100 text-slate-500'
                                        };
                                    ?>
                                    <span class="px-2 py-0.5 <?= $statusClass ?> text-[10px] font-bold rounded"><?= $regStatus ?></span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-1 truncate max-w-[120px]" title="Sumber Info">Info: <?= $row['info_source'] ?? '-' ?></div>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-4 text-center sticky right-0 z-10 bg-white group-hover:bg-slate-50/80 transition-colors shadow-[-4px_0_15px_-3px_rgba(0,0,0,0.02)]">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Validasi Dokumen -->
                                    <button class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-colors shadow-sm" title="Validasi Dokumen">
                                        <i class="fa-solid fa-file-signature text-sm"></i>
                                    </button>

                                    <!-- Kelulusan -->
                                    <button class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white flex items-center justify-center transition-colors shadow-sm" title="Konfirmasi Kelulusan">
                                        <i class="fa-solid fa-user-check text-sm"></i>
                                    </button>

                                    <!-- Dropdown Action -->
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition-colors shadow-sm">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden z-50 text-left">
                                            <a href="/ppdb/registrations/detail?id=<?= $row['id'] ?>" class="block px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                                                <i class="fa-solid fa-eye w-5 text-center text-slate-400"></i> Lihat Detail
                                            </a>
                                            <a href="#" class="block px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                                                <i class="fa-solid fa-pen-to-square w-5 text-center text-slate-400"></i> Edit Data
                                            </a>
                                            <div class="border-t border-slate-100"></div>
                                            <button class="w-full text-left px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors">
                                                <i class="fa-solid fa-trash-can w-5 text-center"></i> Hapus Santri
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
                    <select onchange="window.location.href=updateQueryStringParameter(window.location.href, 'limit', this.value)" class="border border-slate-300 rounded-lg px-2 py-1 text-sm outline-none focus:ring-2 focus:ring-blue-500/50 bg-white font-medium">
                        <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10 entries</option>
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50 entries</option>
                    </select>
                </div>

                <?php if (isset($totalPages) && $totalPages > 1): ?>
                <div class="flex items-center gap-1.5">
                    <?php
                    $safeSearch = isset($search) ? urlencode($search) : '';
                    $safeStatus = isset($selectedStatus) ? $selectedStatus : '';
                    $safeTrack = isset($selectedTrack) ? $selectedTrack : '';
                    $qs = "&limit={$limit}&search={$safeSearch}&status={$safeStatus}&track_id={$safeTrack}";
                    ?>
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

<script>
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    }
    window.onclick = function(e) {
        if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden');
    }
</script>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Data Pendaftar PPDB</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Gunakan filter <strong class="text-slate-700">Nama/NISN</strong>, <strong class="text-slate-700">Status</strong>, dan <strong class="text-slate-700">Jalur</strong> untuk menyaring data.</li>
                    <li>Klik nama pendaftar untuk melihat detail lengkap dan mengubah status kelulusan.</li>
                    <li>Klik ikon <strong class="text-slate-700">titik tiga</strong> untuk aksi tambahan (edit/hapus).</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-signs-post text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Jalur & Periode PPDB</div><div class="text-[11px] text-slate-400">Dikelola di <strong>PPDB → Konfigurasi PPDB</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-graduation-cap text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Santri</div><div class="text-[11px] text-slate-400">Pendaftar yang diterima dapat dipindahkan ke <strong>Kesiswaan → Data Santri</strong>.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
