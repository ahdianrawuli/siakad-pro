<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Custom Absen</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Input absensi untuk kegiatan custom.</p>
        </div>
        <div class="flex gap-2">
            <?php if ($typeId && !empty($persons)): ?>
            <a href="/attendance/custom/print?type_id=<?= $typeId ?>&class_id=<?= $classId ?>&date_from=<?= date('Y-m-01') ?>&date_to=<?= $date ?>" target="_blank"
                class="px-4 py-2.5 bg-slate-600 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak
            </a>
            <?php endif; ?>
            <a href="/attendance/custom/types" class="px-4 py-2.5 bg-purple-600 text-white rounded-xl text-sm font-semibold hover:bg-purple-700 transition flex items-center gap-2">
                <i class="fa-solid fa-gear"></i> Kelola Jenis
            </a>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Filter -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <select name="type_id" onchange="this.form.submit()" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
                <option value="">-- Pilih Jenis Absen --</option>
                <?php foreach ($types as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= $typeId==$t['id']?'selected':'' ?>><?= htmlspecialchars($t['name']) ?> (<?= $t['target']==='GURU'?'Guru/Staff':'Siswa' ?>)</option>
                <?php endforeach; ?>
            </select>
            <?php if ($selectedType && ($selectedType['target'] === 'SISWA' || $selectedType['target'] === 'SEMUA')): ?>
            <select name="class_id" onchange="this.form.submit()" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
                <option value="">Semua Kelas</option>
                <?php foreach ($classrooms as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $classId==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
            <input type="date" name="date" value="<?= $date ?>" onchange="this.form.submit()"
                class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            <div class="relative">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama..."
                    class="py-2.5 pl-9 pr-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none w-44">
                <i class="fa-solid fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition">Filter</button>
        </form>
    </div>

    <?php if ($selectedType && !empty($persons)):
        $statusList = explode(',', $selectedType['statuses']);
        $sessions = max(1, (int)$selectedType['sessions']);
        $sessionLabels = $selectedType['session_labels'] ? explode(',', $selectedType['session_labels']) : [];
        $hasTime = (int)$selectedType['has_time'];
        $sessionTimes = $selectedType['session_times'] ? explode(',', $selectedType['session_times']) : [];
    ?>
    <form action="/attendance/custom/store" method="POST">
        <?= \App\Core\Csrf::input() ?>
        <input type="hidden" name="type_id" value="<?= $typeId ?>">
        <input type="hidden" name="class_id" value="<?= $classId ?>">
        <input type="hidden" name="date" value="<?= $date ?>">
        <input type="hidden" name="page" value="<?= $page ?>">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h4 class="font-bold text-slate-700 text-sm">
                    <i class="fa-solid fa-clipboard-list mr-2 text-slate-400"></i><?= htmlspecialchars($selectedType['name']) ?>
                    — <?= date('d/m/Y', strtotime($date)) ?> (<?= $totalPersons ?> orang, <?= $sessions ?> sesi)
                </h4>
                <?php if ($hasTime && !empty($sessionTimes)): ?>
                <div class="text-[10px] text-slate-400">Jam: <?= htmlspecialchars(implode(' | ', $sessionTimes)) ?></div>
                <?php endif; ?>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-3 py-3 text-xs font-bold text-slate-500 uppercase w-10 text-center">#</th>
                            <th class="px-3 py-3 text-xs font-bold text-slate-500 uppercase">Nama</th>
                            <?php if ($selectedType['target'] === 'GURU'): ?><th class="px-3 py-3 text-xs font-bold text-slate-500 uppercase">Jabatan</th><?php endif; ?>
                            <?php for ($sn = 1; $sn <= $sessions; $sn++): ?>
                            <th class="px-2 py-3 text-xs font-bold text-slate-500 uppercase text-center">
                                <?= $sessionLabels[$sn-1] ?? "Sesi $sn" ?>
                                <?php if ($hasTime && isset($sessionTimes[$sn-1])): ?><br><span class="text-[9px] text-slate-400 font-normal"><?= $sessionTimes[$sn-1] ?></span><?php endif; ?>
                            </th>
                            <?php if ($hasTime): ?><th class="px-2 py-3 text-[10px] font-bold text-slate-500 uppercase text-center">Jam <?= $sessionLabels[$sn-1] ?? $sn ?></th><?php endif; ?>
                            <?php endfor; ?>
                            <th class="px-3 py-3 text-xs font-bold text-slate-500 uppercase">Ket.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($persons as $i => $p):
                            $rowNum = ($page - 1) * 20 + $i + 1;
                        ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-3 py-2 text-center text-slate-400 text-xs"><?= $rowNum ?></td>
                            <td class="px-3 py-2 font-semibold text-slate-800 text-xs whitespace-nowrap"><?= htmlspecialchars($p['full_name']) ?></td>
                            <?php if ($selectedType['target'] === 'GURU'): ?><td class="px-3 py-2 text-xs text-slate-500"><?= htmlspecialchars($p['position_name'] ?? '') ?></td><?php endif; ?>
                            <?php for ($sn = 1; $sn <= $sessions; $sn++):
                                $curStatus = $existing[$p['id']][$sn]['status'] ?? $statusList[0];
                                $curTime = $existing[$p['id']][$sn]['time_in'] ?? '';
                            ?>
                            <td class="px-1 py-2 text-center">
                                <select name="attendance[<?= $p['id'] ?>][<?= $sn ?>]" class="py-1 px-1 bg-slate-50 border border-slate-200 rounded-lg text-[10px] outline-none">
                                    <?php foreach ($statusList as $st): ?>
                                    <option value="<?= trim($st) ?>" <?= $curStatus===trim($st)?'selected':'' ?>><?= trim($st) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <?php if ($hasTime): ?>
                            <td class="px-1 py-2 text-center">
                                <input type="time" name="time_in[<?= $p['id'] ?>][<?= $sn ?>]" value="<?= $curTime ? substr($curTime,0,5) : '' ?>"
                                    class="py-1 px-1 bg-slate-50 border border-slate-200 rounded-lg text-[10px] outline-none w-20">
                            </td>
                            <?php endif; ?>
                            <?php endfor; ?>
                            <td class="px-3 py-2">
                                <input type="text" name="notes[<?= $p['id'] ?>]" value="<?= htmlspecialchars($existing[$p['id']][1]['notes'] ?? '') ?>"
                                    class="w-full px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none" placeholder="Opsional">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <?php if ($totalPages > 1): ?>
                <div class="flex items-center gap-1.5">
                    <?php if ($page > 1): ?>
                        <a href="?type_id=<?= $typeId ?>&class_id=<?= $classId ?>&date=<?= $date ?>&search=<?= urlencode($search) ?>&page=<?= $page-1 ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold hover:text-blue-600 transition shadow-sm"><i class="fa-solid fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <span class="text-xs font-bold text-slate-600 px-2">Hal <?= $page ?>/<?= $totalPages ?></span>
                    <?php if ($page < $totalPages): ?>
                        <a href="?type_id=<?= $typeId ?>&class_id=<?= $classId ?>&date=<?= $date ?>&search=<?= urlencode($search) ?>&page=<?= $page+1 ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold hover:text-blue-600 transition shadow-sm"><i class="fa-solid fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
                <?php else: ?><div></div><?php endif; ?>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 shadow-md transition">
                    <i class="fa-solid fa-save mr-2"></i> Simpan
                </button>
            </div>
        </div>
    </form>
    <?php elseif ($typeId): ?>
    <div class="text-center py-16 text-slate-400">Pilih kelas untuk mulai input absensi.</div>
    <?php else: ?>
    <div class="text-center py-16 text-slate-400">Pilih jenis absen terlebih dahulu.</div>
    <?php endif; ?>

</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
