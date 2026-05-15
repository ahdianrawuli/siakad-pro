<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Data Pendaftar PPDB</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Mengelola dan memverifikasi calon santri baru secara terpusat.</p>
            <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                <i class="fa-solid fa-users"></i> Total Pendaftar: <?= $totalData ?>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="document.getElementById('addModal').classList.remove('hidden')"
                class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Manual
            </button>
            <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition border border-slate-200" title="Panduan">
                <i class="fa-solid fa-circle-info"></i>
            </button>
        </div>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <!-- Summary Cards -->
    <?php
    $statusCount = array_merge(['PENDING'=>0,'PAID'=>0,'VERIFIED'=>0,'ACCEPTED'=>0,'REJECTED'=>0], $statusCount ?? []);
    $cards = [
        ['PENDING',  'Menunggu',  'bg-slate-100 text-slate-600',  'fa-clock'],
        ['PAID',     'Lunas',     'bg-green-50 text-green-700',   'fa-circle-check'],
        ['VERIFIED', 'Terverif.', 'bg-blue-50 text-blue-700',     'fa-file-circle-check'],
        ['ACCEPTED', 'Diterima',  'bg-emerald-50 text-emerald-700','fa-user-check'],
        ['REJECTED', 'Ditolak',   'bg-red-50 text-red-700',       'fa-user-xmark'],
    ];
    ?>
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        <?php foreach ($cards as [$val, $label, $cls, $icon]): ?>
        <a href="?status=<?= $val ?>" class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3 hover:shadow-md transition <?= ($selectedStatus??'')===$val ? 'ring-2 ring-blue-500' : '' ?>">
            <div class="w-9 h-9 rounded-xl <?= $cls ?> flex items-center justify-center shrink-0">
                <i class="fa-solid <?= $icon ?> text-sm"></i>
            </div>
            <div>
                <div class="text-xl font-extrabold text-slate-800"><?= $statusCount[$val] ?></div>
                <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider"><?= $label ?></div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <div class="bg-gradient-to-r from-blue-600 to-blue-500 rounded-2xl shadow-sm p-4 mb-6 flex flex-col md:flex-row justify-between items-center text-white border border-blue-400/30">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <i class="fa-solid fa-bullhorn text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-blue-100 uppercase tracking-widest">Tahun Ajaran Aktif</p>
                <h4 class="text-lg font-bold"><?= htmlspecialchars($activeYear['name'] ?? '-') ?></h4>
            </div>
        </div>
        <div class="h-8 w-px bg-blue-400/50 hidden md:block mx-4"></div>
        <div class="flex items-center gap-3 mt-4 md:mt-0">
            <div>
                <p class="text-xs font-semibold text-blue-100 uppercase tracking-widest text-right">Gelombang PPDB</p>
                <h4 class="text-lg font-bold text-right"><?= $activeBatch ? htmlspecialchars($activeBatch['name']) : '<span class="text-blue-200 text-sm font-normal">Belum ada gelombang aktif</span>' ?></h4>
            </div>
            <a href="/ppdb/settings?tab=periode" class="px-3 py-1.5 bg-white text-blue-600 rounded-lg text-xs font-bold hover:bg-blue-50 transition-colors whitespace-nowrap shadow-sm">
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
        <!-- PENTING: tidak pakai overflow-hidden agar dropdown tidak terpotong -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col relative">
            <div class="overflow-x-auto custom-scrollbar pb-1">
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
                                    <!-- Ubah Status -->
                                    <button onclick="openStatusModal(<?= $row['id'] ?>, '<?= $row['registration_status'] ?? 'PENDING' ?>', '<?= addslashes($row['full_name']) ?>')"
                                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition shadow-sm" title="Ubah Status">
                                        <i class="fa-solid fa-arrows-rotate text-sm"></i>
                                    </button>

                                    <!-- Kirim WA -->
                                    <button onclick="openWaModal(<?= $row['id'] ?>, '<?= addslashes($row['full_name']) ?>', '<?= $row['whatsapp_number'] ?? '' ?>')"
                                        class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white flex items-center justify-center transition shadow-sm" title="Kirim WA">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                    </button>

                                    <!-- Dropdown -->
                                    <div class="relative">
                                        <button onclick="toggleDropdown(<?= $row['id'] ?>, this)"
                                            class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition shadow-sm">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
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

<!-- Floating Dropdown (di-render di body agar tidak terpotong tabel) -->
<div id="floatingDropdown" class="hidden fixed bg-white rounded-xl shadow-2xl border border-slate-100 w-48 text-left" style="z-index:99999;">
    <a id="ddDetail" href="#" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-blue-600">
        <i class="fa-solid fa-eye w-4 text-center text-slate-400"></i> Lihat Detail
    </a>
    <div class="border-t border-slate-100"></div>
    <button id="ddDelete" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 transition">
        <i class="fa-solid fa-trash-can w-4 text-center"></i> Hapus
    </button>
</div>

<!-- Modal Ubah Status -->
<div id="statusModal" class="hidden fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-arrows-rotate text-blue-500"></i> Ubah Status Pendaftar</h3>
            <button onclick="document.getElementById('statusModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/ppdb/registrations/set-status" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="statusId">
            <p class="text-sm text-slate-600">Pendaftar: <strong id="statusName" class="text-slate-800"></strong></p>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-2">Status Baru</label>
                <select name="status" id="statusSelect" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="PENDING">Pending</option>
                    <option value="PAID">Lunas</option>
                    <option value="VERIFIED">Terverifikasi</option>
                    <option value="ACCEPTED">Diterima ✓</option>
                    <option value="REJECTED">Ditolak ✗</option>
                </select>
            </div>
            <p class="text-xs text-slate-400"><i class="fa-brands fa-whatsapp text-green-500 mr-1"></i>Notifikasi WA otomatis dikirim jika status DITERIMA atau DITOLAK.</p>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('statusModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kirim WA -->
<div id="waModal" class="hidden fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-green-50 flex justify-between items-center">
            <h3 class="font-bold text-green-800 flex items-center gap-2"><i class="fa-brands fa-whatsapp text-green-600"></i> Kirim Pesan WhatsApp</h3>
            <button onclick="document.getElementById('waModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/ppdb/registrations/notify" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="waId">
            <p class="text-sm text-slate-600">Kepada: <strong id="waName" class="text-slate-800"></strong> (<span id="waPhone" class="font-mono text-green-700"></span>)</p>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Pesan</label>
                <textarea name="message" rows="4" placeholder="Ketik pesan WhatsApp..."
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500/50 outline-none resize-none" required></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('waModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-green-600 text-white py-2.5 rounded-xl font-bold hover:bg-green-700 shadow-md shadow-green-500/20 transition text-sm"><i class="fa-brands fa-whatsapp mr-1"></i> Kirim</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Manual -->
<div id="addModal" class="hidden fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-plus text-blue-500"></i> Tambah Pendaftar Manual</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/ppdb/registrations/store" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="full_name" placeholder="Nama lengkap calon santri" required
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jenis Kelamin</label>
                    <select name="gender" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">No. WhatsApp</label>
                    <input type="text" name="whatsapp_number" placeholder="08xx..." required
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jalur Pendaftaran</label>
                    <select name="ppdb_track_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                        <?php foreach ($tracks as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?> (<?= $t['level'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Form hapus (hidden) -->
<form id="deleteForm" action="/ppdb/registrations/delete" method="POST" class="hidden">
    <?= \App\Core\Csrf::input() ?>
    <input type="hidden" name="id" id="deleteId">
</form>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Data Pendaftar PPDB</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 max-h-[70vh] overflow-y-auto">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Tombol Aksi di Tabel</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-arrows-rotate text-xs"></i></div>
                        <div class="text-xs"><strong class="text-slate-700">Ubah Status</strong> — Ubah status pendaftar (Pending → Lunas → Terverifikasi → Diterima/Ditolak). Notif WA otomatis dikirim saat status DITERIMA atau DITOLAK.</div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="w-7 h-7 rounded-lg bg-green-50 text-green-600 flex items-center justify-center shrink-0"><i class="fa-brands fa-whatsapp text-xs"></i></div>
                        <div class="text-xs"><strong class="text-slate-700">Kirim WA</strong> — Kirim pesan WhatsApp manual ke nomor pendaftar.</div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-ellipsis-vertical text-xs"></i></div>
                        <div class="text-xs"><strong class="text-slate-700">Menu (⋮)</strong> — Lihat detail lengkap atau hapus pendaftar. Pendaftar berstatus DITERIMA tidak dapat dihapus.</div>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Alur Status Pendaftar</h4>
                <div class="flex flex-wrap gap-1.5 items-center text-xs">
                    <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded font-bold">PENDING</span>
                    <i class="fa-solid fa-arrow-right text-slate-300"></i>
                    <span class="px-2 py-1 bg-green-50 text-green-700 rounded font-bold">LUNAS</span>
                    <i class="fa-solid fa-arrow-right text-slate-300"></i>
                    <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded font-bold">TERVERIFIKASI</span>
                    <i class="fa-solid fa-arrow-right text-slate-300"></i>
                    <span class="px-2 py-1 bg-emerald-50 text-emerald-700 rounded font-bold">DITERIMA</span>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-graduation-cap text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Santri</div><div class="text-[11px] text-slate-400">Pendaftar DITERIMA dapat dipromosikan ke <strong>Kesiswaan → Data Santri</strong> via halaman Detail.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-signs-post text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Jalur & Periode PPDB</div><div class="text-[11px] text-slate-400">Dikelola di <strong>PPDB → Jalur Pendaftaran</strong> dan <strong>PPDB → Periode PPDB</strong>.</div></div>
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
// Floating dropdown
let _ddActive = null;
function toggleDropdown(id, btn) {
    const dd = document.getElementById('floatingDropdown');
    if (_ddActive === id && !dd.classList.contains('hidden')) {
        dd.classList.add('hidden'); _ddActive = null; return;
    }
    _ddActive = id;
    const rect = btn.getBoundingClientRect();
    dd.style.top  = (rect.bottom + window.scrollY + 4) + 'px';
    dd.style.left = (rect.right  + window.scrollX - 192) + 'px'; // 192 = w-48
    document.getElementById('ddDetail').href = '/ppdb/registrations/detail?id=' + id;
    document.getElementById('ddDelete').onclick = function() {
        dd.classList.add('hidden');
        confirmDelete(id, btn.closest('tr').querySelector('a[href*="detail"]')?.innerText || '');
    };
    dd.classList.remove('hidden');
}
document.addEventListener('click', function(e) {
    const dd = document.getElementById('floatingDropdown');
    if (!dd.contains(e.target) && !e.target.closest('[onclick*="toggleDropdown"]')) {
        dd.classList.add('hidden'); _ddActive = null;
    }
});

function openStatusModal(id, currentStatus, name) {
    document.getElementById('statusId').value = id;
    document.getElementById('statusName').innerText = name;
    document.getElementById('statusSelect').value = currentStatus;
    document.getElementById('statusModal').classList.remove('hidden');
}
function openWaModal(id, name, phone) {
    document.getElementById('waId').value = id;
    document.getElementById('waName').innerText = name;
    document.getElementById('waPhone').innerText = phone || '-';
    document.getElementById('waModal').classList.remove('hidden');
}
function confirmDelete(id, name) {
    if (confirm('Hapus pendaftar "' + name + '"? Tindakan ini tidak dapat dibatalkan.')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var sep = uri.indexOf('?') !== -1 ? "&" : "?";
    return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + sep + key + "=" + value;
}
window.onclick = function(e) {
    ['infoModal','statusModal','waModal','addModal'].forEach(function(id) {
        if (e.target == document.getElementById(id)) document.getElementById(id).classList.add('hidden');
    });
}
</script>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
