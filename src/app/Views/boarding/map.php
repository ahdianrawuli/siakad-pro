<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Denah Kamar Asrama</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Visualisasi penempatan santri per kamar.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-building"></i> <?= count($dorms) ?> Kamar
                </div>
                <?php if (($scope ?? 'GLOBAL') !== 'GLOBAL'): ?>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold border border-amber-200">
                    <i class="fa-solid fa-filter"></i> Unit: <?= $scope ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <a href="/asrama/map/print" target="_blank"
            class="px-4 py-2.5 bg-slate-600 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 transition flex items-center gap-2 w-fit">
            <i class="fa-solid fa-print"></i> Cetak Denah
        </a>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <?php
    // Kelompokkan per unit dan gender
    $grouped = [];
    foreach ($dorms as $d) {
        $grouped[$d['unit']][$d['gender'] === 'L' ? 'Putra' : 'Putri'][] = $d;
    }
    $unitColors = ['MTS'=>'blue','MA'=>'purple','PDF'=>'amber'];
    ?>

    <?php foreach ($grouped as $unit => $genders): ?>
    <?php $color = $unitColors[$unit] ?? 'slate'; ?>
    <div class="mb-8">
        <h4 class="text-lg font-extrabold text-slate-700 mb-4 flex items-center gap-2">
            <span class="px-3 py-1 bg-<?=$color?>-100 text-<?=$color?>-700 rounded-lg text-sm border border-<?=$color?>-200"><?= $unit ?></span>
        </h4>
        <?php foreach ($genders as $gender => $rooms): ?>
        <div class="mb-6">
            <h5 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                <i class="fa-solid fa-<?= $gender==='Putra' ? 'person' : 'person-dress' ?>"></i> <?= $gender ?>
            </h5>
            <div style="column-count:2;column-gap:1rem" class="lg:![column-count:3]">
                <?php foreach ($rooms as $dorm):
                    $students = $dormMap[$dorm['id']] ?? [];
                    $occupied = count($students);
                    $pct = $dorm['capacity'] > 0 ? round($occupied / $dorm['capacity'] * 100) : 0;
                    $barColor = $pct >= 100 ? 'bg-red-500' : ($pct > 80 ? 'bg-yellow-500' : 'bg-green-500');
                ?>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-4" style="break-inside:avoid">
                    <!-- Card Header -->
                    <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                        <span class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($dorm['name']) ?></span>
                        <span class="text-xs font-bold <?= $pct>=100?'text-red-600':'text-slate-500' ?>">
                            <?= $occupied ?>/<?= $dorm['capacity'] ?>
                        </span>
                    </div>
                    <!-- Progress -->
                    <div class="px-4 pt-2">
                        <div class="w-full bg-slate-100 rounded-full h-1.5">
                            <div class="<?= $barColor ?> h-1.5 rounded-full" style="width:<?= min($pct,100) ?>%"></div>
                        </div>
                    </div>
                    <!-- Santri list -->
                    <div class="p-3 space-y-1 max-h-48 overflow-y-auto">
                        <?php if (empty($students)): ?>
                            <p class="text-xs text-slate-400 text-center py-2">Kosong</p>
                        <?php endif; ?>
                        <?php foreach ($students as $i => $s): ?>
                        <div class="flex items-center gap-2 py-1 <?= $i < count($students)-1 ? 'border-b border-slate-50' : '' ?>">
                            <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0">
                                <?= $i+1 ?>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-semibold text-slate-800 truncate"><?= htmlspecialchars($s['full_name']) ?></div>
                                <div class="text-[10px] text-slate-400"><?= $s['nis'] ?> · <?= htmlspecialchars($s['class_name'] ?? '-') ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

    <?php if (empty($dorms)): ?>
    <div class="text-center py-16 text-slate-400">Belum ada data kamar asrama.</div>
    <?php endif; ?>

</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
