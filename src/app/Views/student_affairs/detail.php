<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-6 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-2xl font-extrabold shrink-0">
                    <?= mb_strtoupper(mb_substr($student['full_name'], 0, 1)) ?>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-slate-800"><?= htmlspecialchars($student['full_name']) ?></h3>
                    <div class="flex flex-wrap gap-2 mt-1">
                        <span class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200">NIS: <?= $student['nis'] ?></span>
                        <?php if ($student['nisn']): ?><span class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200">NISN: <?= $student['nisn'] ?></span><?php endif; ?>
                        <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded border border-blue-100 font-semibold"><?= $student['class_name'] ?? 'Belum ada kelas' ?></span>
                        <?php if ($student['dorm_name']): ?><span class="text-xs bg-purple-50 text-purple-700 px-2 py-0.5 rounded border border-purple-100 font-semibold"><i class="fa-solid fa-building mr-1"></i><?= $student['dorm_name'] ?></span><?php endif; ?>
                        <?php
                        $sc = ['ACTIVE'=>'bg-green-50 text-green-700','GRADUATED'=>'bg-blue-50 text-blue-700','MOVED'=>'bg-amber-50 text-amber-700','DROPPED'=>'bg-red-50 text-red-700'];
                        ?>
                        <span class="text-xs px-2 py-0.5 rounded border font-bold <?= $sc[$student['status']] ?? 'bg-slate-100 text-slate-500' ?>"><?= $student['status'] ?></span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="/student-affairs/students/print?id=<?= $student['id'] ?>" target="_blank"
                    class="px-4 py-2 bg-green-50 text-green-700 border border-green-200 rounded-xl text-sm font-semibold hover:bg-green-100 transition flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> Cetak Biodata
                </a>
                <a href="/student-affairs/students" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-200 transition flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Data Pribadi & Orang Tua -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <h4 class="font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-100 pb-3"><i class="fa-solid fa-user text-slate-400"></i> Data Pribadi</h4>
                <?php
                $fields = [
                    ['Jenis Kelamin', $student['gender'] == 'L' ? 'Laki-laki' : 'Perempuan'],
                    ['Tempat Lahir',  $student['birth_place'] ?? '-'],
                    ['Tanggal Lahir', $student['birth_date'] ? date('d M Y', strtotime($student['birth_date'])) : '-'],
                    ['Alamat',        $student['address'] ?? '-'],
                ];
                foreach ($fields as [$label, $val]):
                ?>
                <div class="flex justify-between py-2 border-b border-slate-50 text-sm">
                    <span class="text-slate-400 font-medium"><?= $label ?></span>
                    <span class="text-slate-700 font-semibold text-right max-w-[60%]"><?= htmlspecialchars($val) ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <h4 class="font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-100 pb-3"><i class="fa-solid fa-people-roof text-slate-400"></i> Data Orang Tua</h4>
                <?php
                $parentFields = [
                    ['Ayah',     $student['father_name'] ?? '-', $student['father_phone'] ?? null],
                    ['Ibu',      $student['mother_name'] ?? '-', $student['mother_phone'] ?? null],
                    ['Wali',     $student['guardian_name'] ?? '-', $student['guardian_phone'] ?? null],
                ];
                foreach ($parentFields as [$role, $name, $phone]):
                ?>
                <div class="py-2 border-b border-slate-50 text-sm">
                    <div class="text-slate-400 text-xs font-semibold uppercase tracking-wider"><?= $role ?></div>
                    <div class="text-slate-700 font-semibold"><?= htmlspecialchars($name) ?></div>
                    <?php if ($phone): ?><div class="text-xs text-green-600"><i class="fa-brands fa-whatsapp mr-1"></i><?= $phone ?></div><?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Kolom Kanan: Ringkasan Akademik -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Absensi -->
            <?php
            $attMap = ['H'=>0,'S'=>0,'I'=>0,'A'=>0];
            foreach ($attendance as $a) $attMap[$a['status']] = $a['total'];
            $total = array_sum($attMap);
            ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <h4 class="font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-100 pb-3"><i class="fa-solid fa-calendar-check text-slate-400"></i> Rekap Absensi</h4>
                <div class="grid grid-cols-4 gap-3">
                    <?php foreach (['H'=>['Hadir','green'],'S'=>['Sakit','amber'],'I'=>['Izin','blue'],'A'=>['Alfa','red']] as $k=>[$label,$color]): ?>
                    <div class="text-center p-3 bg-<?= $color ?>-50 rounded-xl border border-<?= $color ?>-100">
                        <div class="text-2xl font-extrabold text-<?= $color ?>-700"><?= $attMap[$k] ?></div>
                        <div class="text-xs text-<?= $color ?>-500 font-semibold"><?= $label ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Nilai -->
            <?php if (!empty($grades)): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-star text-slate-400"></i> Nilai Rata-rata
                </div>
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($grades as $g): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-2.5 text-slate-700"><?= htmlspecialchars($g['subject_name']) ?></td>
                            <td class="px-5 py-2.5 text-right">
                                <?php $avg = round($g['avg_score'], 1); $color = $avg >= 75 ? 'green' : ($avg >= 60 ? 'amber' : 'red'); ?>
                                <span class="px-2.5 py-1 bg-<?= $color ?>-50 text-<?= $color ?>-700 rounded-lg text-xs font-bold border border-<?= $color ?>-200"><?= $avg ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Tagihan -->
            <?php if (!empty($bills)): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 font-bold text-slate-700 flex items-center justify-between">
                    <span class="flex items-center gap-2"><i class="fa-solid fa-receipt text-slate-400"></i> Tagihan Terakhir</span>
                    <a href="/finance/billing?nis=<?= $student['nis'] ?>" class="text-xs text-blue-600 hover:underline font-semibold">Lihat Semua →</a>
                </div>
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($bills as $b): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-2.5 text-slate-700"><?= htmlspecialchars($b['title'] ?? '-') ?></td>
                            <td class="px-5 py-2.5 text-right font-mono text-xs">Rp <?= number_format($b['amount'], 0, ',', '.') ?></td>
                            <td class="px-5 py-2.5 text-right">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full <?= $b['status']==='PAID' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' ?>"><?= $b['status']==='PAID' ? 'Lunas' : 'Belum' ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Pelanggaran -->
            <?php if (!empty($violations)): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-slate-400"></i> Pelanggaran Terakhir
                </div>
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($violations as $v): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-2.5 text-slate-700"><?= htmlspecialchars($v['name']) ?></td>
                            <td class="px-5 py-2.5 text-right text-xs text-slate-400"><?= date('d M Y', strtotime($v['date'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
