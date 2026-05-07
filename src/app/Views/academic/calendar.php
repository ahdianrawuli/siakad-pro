<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Kalender Akademik</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola agenda, libur, dan jadwal ujian tahun ajaran aktif.</p>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <?php
                $counts = ['KEGIATAN' => 0, 'LIBUR' => 0, 'UJIAN' => 0];
                foreach ($events as $e) { if (isset($counts[$e['type']])) $counts[$e['type']]++; }
                $badges = ['KEGIATAN' => ['bg-blue-50','text-blue-700','border-blue-100','fa-calendar-check'], 'LIBUR' => ['bg-green-50','text-green-700','border-green-100','fa-umbrella-beach'], 'UJIAN' => ['bg-red-50','text-red-700','border-red-100','fa-pen-to-square']];
                foreach ($badges as $type => [$bg, $text, $border, $icon]):
                ?>
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 <?= $bg ?> <?= $text ?> rounded-lg text-xs font-bold border <?= $border ?>">
                    <i class="fa-solid <?= $icon ?>"></i> <?= $type ?>: <?= $counts[$type] ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
            class="flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all text-sm whitespace-nowrap">
            <i class="fa-solid fa-plus"></i> Tambah Event
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <?php if (empty($events)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-16 text-center">
        <i class="fa-solid fa-calendar-xmark text-5xl text-slate-300 mb-4"></i>
        <p class="text-slate-500 font-medium">Belum ada agenda untuk tahun ajaran aktif.</p>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
            class="mt-4 inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-blue-700 transition">
            <i class="fa-solid fa-plus"></i> Tambah Sekarang
        </button>
    </div>
    <?php else: ?>

    <!-- Group by type -->
    <?php
    $grouped = [];
    foreach ($events as $e) { $grouped[$e['type']][] = $e; }
    $typeConfig = [
        'UJIAN'     => ['label' => 'Ujian',             'icon' => 'fa-pen-to-square',    'header' => 'bg-red-600',    'badge' => 'bg-red-100 text-red-700'],
        'LIBUR'     => ['label' => 'Hari Libur',        'icon' => 'fa-umbrella-beach',   'header' => 'bg-green-600',  'badge' => 'bg-green-100 text-green-700'],
        'KEGIATAN'  => ['label' => 'Kegiatan Sekolah',  'icon' => 'fa-calendar-check',   'header' => 'bg-blue-600',   'badge' => 'bg-blue-100 text-blue-700'],
    ];
    ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <?php foreach ($typeConfig as $type => $cfg):
            if (empty($grouped[$type])) continue; ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
            <div class="<?= $cfg['header'] ?> px-5 py-4 flex items-center gap-3">
                <i class="fa-solid <?= $cfg['icon'] ?> text-white text-lg"></i>
                <h4 class="font-bold text-white"><?= $cfg['label'] ?></h4>
                <span class="ml-auto bg-white/20 text-white text-xs font-bold px-2 py-0.5 rounded-full"><?= count($grouped[$type]) ?></span>
            </div>
            <ul class="divide-y divide-slate-100 flex-1">
                <?php foreach ($grouped[$type] as $e):
                    $start = date('d M Y', strtotime($e['start_date']));
                    $end   = date('d M Y', strtotime($e['end_date']));
                    $sameDay = $e['start_date'] === $e['end_date'];
                ?>
                <li class="px-5 py-3.5 flex items-start gap-3 hover:bg-slate-50 transition-colors group">
                    <div class="w-1 self-stretch rounded-full flex-shrink-0 mt-1" style="background-color: <?= htmlspecialchars($e['color'] ?? '#3788d8') ?>"></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 text-sm truncate"><?= htmlspecialchars($e['title']) ?></p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            <i class="fa-regular fa-calendar mr-1"></i>
                            <?= $sameDay ? $start : "$start — $end" ?>
                        </p>
                    </div>
                    <form method="POST" action="/academic/calendar/delete" class="opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                        <?= \App\Core\Csrf::input() ?>
                        <input type="hidden" name="id" value="<?= $e['id'] ?>">
                        <button type="submit" onclick="return confirm('Hapus event ini?')"
                            class="w-7 h-7 flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Timeline semua event -->
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <i class="fa-solid fa-timeline text-slate-400"></i>
            <h4 class="font-bold text-slate-700">Timeline Semua Agenda</h4>
            <span class="ml-auto text-xs text-slate-400"><?= count($events) ?> event</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Nama Kegiatan</th>
                        <th class="px-6 py-3 text-left font-semibold">Mulai</th>
                        <th class="px-6 py-3 text-left font-semibold">Selesai</th>
                        <th class="px-6 py-3 text-left font-semibold">Tipe</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($events as $e):
                        $cfg = $typeConfig[$e['type']] ?? ['badge' => 'bg-gray-100 text-gray-700', 'label' => $e['type']];
                    ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-3 font-medium text-slate-800 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: <?= htmlspecialchars($e['color'] ?? '#3788d8') ?>"></span>
                            <?= htmlspecialchars($e['title']) ?>
                        </td>
                        <td class="px-6 py-3 text-slate-500"><?= date('d M Y', strtotime($e['start_date'])) ?></td>
                        <td class="px-6 py-3 text-slate-500"><?= date('d M Y', strtotime($e['end_date'])) ?></td>
                        <td class="px-6 py-3">
                            <span class="<?= $cfg['badge'] ?> text-xs font-bold px-2.5 py-1 rounded-full"><?= $cfg['label'] ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</main>

<!-- Modal Tambah Event -->
<div id="modalTambah" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-calendar-plus text-blue-500"></i> Tambah Event
            </h3>
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
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="start_date"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal Selesai</label>
                    <input type="date" name="end_date"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tipe</label>
                <select name="type" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    <option value="KEGIATAN">Kegiatan Sekolah</option>
                    <option value="LIBUR">Hari Libur</option>
                    <option value="UJIAN">Ujian</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Warna Label</label>
                <input type="color" name="color" value="#3788d8"
                    class="w-full h-10 border border-slate-200 rounded-xl cursor-pointer bg-slate-50">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-sm">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">
                    <i class="fa-solid fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
