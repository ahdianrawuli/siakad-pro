<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-2xl font-extrabold text-slate-800">Kalender Akademik</h3>
                    <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                        class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                        <i class="fa-solid fa-circle-info text-sm"></i>
                    </button>
                </div>
                <p class="text-slate-500 text-sm mt-0.5">Kelola agenda tahun ajaran aktif.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm whitespace-nowrap">
                <i class="fa-solid fa-plus"></i> Tambah Event
            </button>
            <button onclick="document.getElementById('modalGenerate').classList.remove('hidden')"
                class="flex items-center gap-2 bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-emerald-700 shadow-md shadow-emerald-500/20 transition text-sm whitespace-nowrap">
                <i class="fa-solid fa-file-pdf"></i> Generate Kalender
            </button>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Search & Filter -->
    <form method="GET" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-5 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-semibold text-slate-500 mb-1">Cari</label>
            <div class="relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                    placeholder="Nama kegiatan..."
                    class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 outline-none">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Tipe</label>
            <select name="type" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/30 outline-none">
                <option value="">Semua Tipe</option>
                <option value="KEGIATAN" <?= $filter==='KEGIATAN'?'selected':'' ?>>Kegiatan</option>
                <option value="LIBUR"    <?= $filter==='LIBUR'   ?'selected':'' ?>>Hari Libur</option>
                <option value="UJIAN"    <?= $filter==='UJIAN'   ?'selected':'' ?>>Ujian</option>
            </select>
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
            <i class="fa-solid fa-filter mr-1"></i> Filter
        </button>
        <?php if ($search || $filter): ?>
        <a href="/academic/calendar" class="px-4 py-2 rounded-lg text-sm border border-slate-200 text-slate-600 hover:bg-slate-50 transition">Reset</a>
        <?php endif; ?>
    </form>

    <!-- Tabel -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <span class="text-sm font-semibold text-slate-700">
                <i class="fa-solid fa-timeline text-slate-400 mr-2"></i>Daftar Agenda
            </span>
            <span class="text-xs text-slate-400"><?= $total ?> event</span>
        </div>

        <?php if (empty($events)): ?>
        <div class="p-16 text-center text-slate-400">
            <i class="fa-solid fa-calendar-xmark text-4xl mb-3 block"></i>
            Tidak ada agenda ditemukan.
        </div>
        <?php else: ?>
        <?php
        $typeConfig = [
            'UJIAN'    => ['bg-red-100 text-red-700',   'Ujian'],
            'LIBUR'    => ['bg-green-100 text-green-700','Hari Libur'],
            'KEGIATAN' => ['bg-blue-100 text-blue-700',  'Kegiatan'],
        ];
        ?>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">#</th>
                        <th class="px-6 py-3 text-left">Nama Kegiatan</th>
                        <th class="px-6 py-3 text-left">Mulai</th>
                        <th class="px-6 py-3 text-left">Selesai</th>
                        <th class="px-6 py-3 text-left">Tipe</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($events as $i => $e):
                        [$badge, $label] = $typeConfig[$e['type']] ?? ['bg-gray-100 text-gray-700', $e['type']];
                        $no = ($currentPage - 1) * 10 + $i + 1;
                    ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-3 text-slate-400"><?= $no ?></td>
                        <td class="px-6 py-3 font-medium text-slate-800">
                            <span class="inline-block w-2.5 h-2.5 rounded-full mr-2 align-middle" style="background-color:<?= htmlspecialchars($e['color'] ?? '#3788d8') ?>"></span>
                            <?= htmlspecialchars($e['title']) ?>
                        </td>
                        <td class="px-6 py-3 text-slate-500"><?= date('d M Y', strtotime($e['start_date'])) ?></td>
                        <td class="px-6 py-3 text-slate-500"><?= date('d M Y', strtotime($e['end_date'])) ?></td>
                        <td class="px-6 py-3">
                            <span class="<?= $badge ?> text-xs font-bold px-2.5 py-1 rounded-full"><?= $label ?></span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick='openEdit(<?= json_encode($e) ?>)'
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                <form method="POST" action="/academic/calendar/delete" onsubmit="return confirm('Hapus event ini?')">
                                    <?= \App\Core\Csrf::input() ?>
                                    <input type="hidden" name="id" value="<?= $e['id'] ?>">
                                    <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-sm">
            <span class="text-slate-500">Halaman <?= $currentPage ?> dari <?= $totalPages ?></span>
            <div class="flex gap-1">
                <?php for ($p = 1; $p <= $totalPages; $p++):
                    $q = http_build_query(['search'=>$search,'type'=>$filter,'page'=>$p]);
                ?>
                <a href="/academic/calendar?<?= $q ?>"
                   class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-semibold transition
                          <?= $p == $currentPage ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                    <?= $p ?>
                </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Kalender Akademik</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Menambah Event</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Klik tombol <strong class="text-slate-700">Tambah Event</strong> di pojok kanan atas.</li>
                    <li>Isi nama kegiatan, tanggal mulai & selesai, tipe, dan warna label.</li>
                    <li>Klik <strong class="text-slate-700">Simpan</strong> untuk menyimpan event.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Tipe Event</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-calendar-check text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Kegiatan</div><div class="text-[11px] text-slate-400">Agenda sekolah seperti upacara, rapat, atau acara khusus.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-umbrella-beach text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Hari Libur</div><div class="text-[11px] text-slate-400">Libur nasional, libur semester, atau libur pesantren.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-pen-to-square text-red-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Ujian</div><div class="text-[11px] text-slate-400">Jadwal UTS, UAS, atau ujian lainnya.</div></div>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Edit & Hapus</h4>
                <p class="text-slate-500">Gunakan tombol <span class="bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded text-xs font-bold">✏ Edit</span> untuk mengubah data event, atau <span class="bg-red-100 text-red-600 px-1.5 py-0.5 rounded text-xs font-bold">🗑 Hapus</span> untuk menghapus event dari kalender.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800"><i class="fa-solid fa-calendar-plus text-blue-500 mr-2"></i>Tambah Event</h3>
            <button onclick="document.getElementById('modalTambah').classList.add('hidden')"
                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="/academic/calendar/store" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Kegiatan</label>
                <input type="text" name="title" placeholder="Contoh: Ujian Tengah Semester"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/50 outline-none" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/50 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/50 outline-none" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tipe</label>
                <select name="type" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/50 outline-none">
                    <option value="KEGIATAN">Kegiatan Sekolah</option>
                    <option value="LIBUR">Hari Libur</option>
                    <option value="UJIAN">Ujian</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Warna Label</label>
                <input type="color" name="color" value="#3788d8" class="w-full h-10 border border-slate-200 rounded-xl cursor-pointer bg-slate-50">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 transition text-sm">
                    <i class="fa-solid fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800"><i class="fa-solid fa-pen text-yellow-500 mr-2"></i>Edit Event</h3>
            <button onclick="document.getElementById('modalEdit').classList.add('hidden')"
                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="/academic/calendar/update" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="editId">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Kegiatan</label>
                <input type="text" name="title" id="editTitle"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/50 outline-none" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="editStart" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/50 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="editEnd" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/50 outline-none" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tipe</label>
                <select name="type" id="editType" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/50 outline-none">
                    <option value="KEGIATAN">Kegiatan Sekolah</option>
                    <option value="LIBUR">Hari Libur</option>
                    <option value="UJIAN">Ujian</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Warna Label</label>
                <input type="color" name="color" id="editColor" class="w-full h-10 border border-slate-200 rounded-xl cursor-pointer bg-slate-50">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-yellow-500 text-white py-2.5 rounded-xl font-bold hover:bg-yellow-600 transition text-sm">
                    <i class="fa-solid fa-save mr-1"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(e) {
    document.getElementById('editId').value    = e.id;
    document.getElementById('editTitle').value = e.title;
    document.getElementById('editStart').value = e.start_date;
    document.getElementById('editEnd').value   = e.end_date;
    document.getElementById('editType').value  = e.type;
    document.getElementById('editColor').value = e.color || '#3788d8';
    document.getElementById('modalEdit').classList.remove('hidden');
}
</script>

<!-- Modal Generate Kalender PDF -->
<div id="modalGenerate" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-file-pdf text-emerald-500"></i> Generate Kalender PDF
            </h3>
            <button onclick="document.getElementById('modalGenerate').classList.add('hidden')"
                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-sm text-slate-500">Pilih bulan dan tahun untuk mencetak kalender akademik beserta event yang sudah dibuat.</p>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Bulan</label>
                    <select id="pdfMonth" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <?php foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $mi => $mn): ?>
                        <option value="<?= $mi+1 ?>" <?= ($mi+1)==date('n')?'selected':'' ?>><?= $mn ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tahun</label>
                    <input type="number" id="pdfYear" value="<?= date('Y') ?>" min="2020" max="2035"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-500/30">
                </div>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('modalGenerate').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-sm">Batal</button>
                <button type="button" onclick="openCalendarPdf()"
                    class="flex-1 bg-emerald-600 text-white py-2.5 rounded-xl font-bold hover:bg-emerald-700 transition text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-print"></i> Cetak / PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openCalendarPdf() {
    const m = document.getElementById('pdfMonth').value;
    const y = document.getElementById('pdfYear').value;
    window.open('/academic/calendar/print?month=' + m + '&year=' + y, '_blank');
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
