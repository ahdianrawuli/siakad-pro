<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-50 to-slate-100 p-4 md:p-6">

    <!-- Hero Header dengan Pattern -->
    <div class="mb-6 relative overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 p-6 md:p-8 rounded-3xl shadow-2xl text-white">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
        </div>
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-blue-100 text-sm font-medium mb-1">
                    <i class="fa-solid fa-hand-wave mr-1"></i> Assalamu'alaikum
                </p>
                <h2 class="text-2xl md:text-4xl font-black tracking-tight"><?= htmlspecialchars($user ?? 'Administrator') ?></h2>
                <p class="text-blue-100 text-sm mt-2 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-day"></i>
                    <?= date('l, d F Y') ?>
                    <span class="w-1 h-1 bg-blue-200 rounded-full"></span>
                    <i class="fa-solid fa-mosque"></i>
                    Pondok Pesantren Sumatera Thawalib Parabek
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-white/15 backdrop-blur-sm border border-white/30 rounded-2xl px-5 py-3 text-center hover:bg-white/25 transition cursor-default">
                    <div class="text-[10px] text-blue-100 font-semibold uppercase tracking-wider">Tahun Ajaran</div>
                    <div class="text-base font-black">2025/2026 Ganjil</div>
                </div>
                <div class="bg-white/15 backdrop-blur-sm border border-white/30 rounded-2xl px-5 py-3 text-center hover:bg-white/25 transition cursor-default">
                    <div class="text-[10px] text-blue-100 font-semibold uppercase tracking-wider">Hari Ini</div>
                    <div class="text-base font-black"><?= date('d M') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats dengan Gradient Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <a href="/student-affairs/students" class="group relative bg-gradient-to-br from-blue-500 to-blue-600 p-5 rounded-2xl shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-blue-100 uppercase tracking-wider">Total Santri</span>
                    <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="fa-solid fa-graduation-cap text-white text-lg"></i>
                    </div>
                </div>
                <div class="text-4xl font-black text-white"><?= $totalStudents ?></div>
                <div class="mt-2 flex gap-4 text-xs text-blue-100">
                    <span><i class="fa-solid fa-person"></i> <?= $genderStats['L'] ?? 0 ?> Putra</span>
                    <span><i class="fa-solid fa-person-dress"></i> <?= $genderStats['P'] ?? 0 ?> Putri</span>
                </div>
            </div>
        </a>

        <a href="/student-affairs/teachers" class="group relative bg-gradient-to-br from-emerald-500 to-green-600 p-5 rounded-2xl shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-emerald-100 uppercase tracking-wider">Tenaga Guru</span>
                    <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="fa-solid fa-chalkboard-user text-white text-lg"></i>
                    </div>
                </div>
                <div class="text-4xl font-black text-white"><?= $totalTeachers ?></div>
                <div class="mt-2 text-xs text-emerald-100">Pengajar aktif</div>
            </div>
        </a>

        <a href="/staff/members" class="group relative bg-gradient-to-br from-violet-500 to-purple-600 p-5 rounded-2xl shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-violet-100 uppercase tracking-wider">Staff</span>
                    <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="fa-solid fa-users-gear text-white text-lg"></i>
                    </div>
                </div>
                <div class="text-4xl font-black text-white"><?= $totalStaff ?></div>
                <div class="mt-2 text-xs text-violet-100">Kependidikan aktif</div>
            </div>
        </a>

        <a href="/master/classrooms" class="group relative bg-gradient-to-br from-amber-500 to-orange-500 p-5 rounded-2xl shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-amber-100 uppercase tracking-wider">Kelas</span>
                    <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="fa-solid fa-door-open text-white text-lg"></i>
                    </div>
                </div>
                <div class="text-4xl font-black text-white"><?= $totalClasses ?></div>
                <div class="mt-2 text-xs text-amber-100">Rombongan belajar</div>
            </div>
        </a>
    </div>

    <!-- Unit Stats & PPDB Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Santri per Unit dengan Chart Visual -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-blue-500"></i> Distribusi Santri
                </h3>
                <a href="/student-affairs/students" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">Detail →</a>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <?php 
                $units = [
                    'MTS' => ['blue', 'Madrasah Tsanawiyah', $unitStats['MTS'] ?? 0],
                    'MA' => ['emerald', 'Madrasah Aliyah', $unitStats['MA'] ?? 0],
                    'PDF' => ['violet', 'Pendidikan Diniyah', $unitStats['PDF'] ?? 0]
                ];
                $maxUnit = max($unitStats['MTS'] ?? 0, $unitStats['MA'] ?? 0, $unitStats['PDF'] ?? 0, 1);
                foreach ($units as $code => [$color, $label, $count]): 
                    $pct = $totalStudents > 0 ? round($count / $totalStudents * 100) : 0;
                ?>
                <div class="group">
                    <div class="bg-<?= $color ?>-50 border-2 border-<?= $color ?>-100 rounded-2xl p-4 text-center hover:border-<?= $color ?>-300 transition">
                        <div class="text-3xl font-black text-<?= $color ?>-600"><?= $count ?></div>
                        <div class="text-xs font-bold text-<?= $color ?>-500 mt-1"><?= $code ?></div>
                        <div class="w-full bg-<?= $color ?>-100 rounded-full h-1.5 mt-3">
                            <div class="bg-<?= $color ?>-500 h-1.5 rounded-full" style="width: <?= $pct ?>%"></div>
                        </div>
                        <div class="text-[10px] text-slate-400 mt-1"><?= $pct ?>% dari total</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- PPDB Stats -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-green-500"></i> Status PPDB 2025/2026
                </h3>
                <a href="/ppdb/registrations" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">Kelola →</a>
            </div>
            <div class="grid grid-cols-4 gap-3">
                <?php 
                $ppdbItems = [
                    ['Total', $ppdbStats['total'] ?? 0, 'slate', 'users'],
                    ['Pending', $ppdbStats['pending'] ?? 0, 'amber', 'clock'],
                    ['Aktif', $ppdbStats['active'] ?? 0, 'emerald', 'check-circle'],
                    ['Ditolak', $ppdbStats['rejected'] ?? 0, 'rose', 'xmark'],
                ];
                foreach ($ppdbItems as [$label, $val, $color, $icon]):
                ?>
                <div class="bg-<?= $color ?>-50 border border-<?= $color ?>-100 rounded-2xl p-4 text-center hover:bg-<?= $color ?>-100 transition">
                    <div class="w-8 h-8 mx-auto rounded-lg bg-<?= $color ?>-200 flex items-center justify-center mb-2">
                        <i class="fa-solid fa-<?= $icon ?> text-<?= $color ?>-600 text-sm"></i>
                    </div>
                    <div class="text-2xl font-black text-<?= $color ?>-700"><?= $val ?></div>
                    <div class="text-[10px] font-semibold text-<?= $color ?>-500 mt-0.5"><?= $label ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Jadwal Hari Ini -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-slate-100 flex items-center justify-between">
                <h4 class="font-bold text-slate-700 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-day text-blue-500"></i>
                    </div>
                    Jadwal Hari Ini
                    <span class="text-xs font-normal text-slate-400">(<?= $todayName ?>)</span>
                </h4>
                <a href="/academic/schedules" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">Lihat Semua →</a>
            </div>
            <div class="divide-y divide-slate-50 max-h-80 overflow-y-auto">
                <?php if (empty($todaySchedules)): ?>
                    <div class="px-6 py-12 text-center text-slate-400">
                        <i class="fa-solid fa-calendar-xmark text-4xl mb-3 block text-slate-200"></i>
                        <p class="text-sm">Tidak ada jadwal hari ini</p>
                    </div>
                <?php endif; ?>
                <?php foreach ($todaySchedules as $sch): ?>
                <div class="px-6 py-4 hover:bg-slate-50 transition group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0 group-hover:bg-blue-100 transition">
                            <span class="text-xs font-mono font-bold text-blue-600"><?= substr($sch['start_time'],0,5) ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-slate-800 truncate"><?= htmlspecialchars($sch['subject']) ?></div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                <span class="inline-flex items-center gap-1 bg-slate-100 px-2 py-0.5 rounded-md"><?= htmlspecialchars($sch['class_name']) ?></span>
                                <span class="mx-1">•</span>
                                <?= htmlspecialchars($sch['teacher_name']) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Kapasitas Asrama -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-slate-100 flex items-center justify-between">
                <h4 class="font-bold text-slate-700 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class="fa-solid fa-building text-purple-500"></i>
                    </div>
                    Kapasitas Asrama
                </h4>
                <a href="/asrama/dorms" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">Kelola →</a>
            </div>
            <div class="p-6 space-y-5 max-h-80 overflow-y-auto">
                <?php foreach ($dormStats as $d):
                    $pct = $d['capacity'] > 0 ? min(100, round($d['occupied'] / $d['capacity'] * 100)) : 0;
                    $barColor = $pct >= 90 ? 'bg-rose-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                    $bgColor = $pct >= 90 ? 'bg-rose-50' : ($pct >= 70 ? 'bg-amber-50' : 'bg-emerald-50');
                ?>
                <div class="<?= $bgColor ?> rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-semibold text-slate-700 flex items-center gap-2">
                            <?= htmlspecialchars($d['name']) ?>
                            <span class="text-[10px] bg-white/50 px-2 py-0.5 rounded-full text-slate-500">
                                <?= $d['gender'] == 'L' ? '👬 Ikhwan' : '👭 Akhwat' ?>
                            </span>
                        </span>
                        <span class="text-sm font-bold text-slate-600"><?= $d['occupied'] ?>/<?= $d['capacity'] ?></span>
                    </div>
                    <div class="w-full bg-white/50 rounded-full h-3">
                        <div class="<?= $barColor ?> h-3 rounded-full transition-all" style="width: <?= $pct ?>%"></div>
                    </div>
                    <div class="text-[10px] text-slate-500 mt-1.5 font-medium"><?= $pct ?>% terisi</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Aktivitas PPDB Terbaru -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition">
            <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-teal-50 border-b border-slate-100 flex items-center justify-between">
                <h4 class="font-bold text-slate-700 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class="fa-solid fa-clock-rotate-left text-green-500"></i>
                    </div>
                    Aktivitas Terbaru
                </h4>
                <a href="/ppdb/registrations" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">Lihat →</a>
            </div>
            <div class="p-6 max-h-80 overflow-y-auto">
                <?php if (empty($activities)): ?>
                    <p class="text-sm text-slate-400 text-center py-8">Belum ada aktivitas pendaftaran.</p>
                <?php endif; ?>
                <div class="relative border-l-2 border-slate-100 ml-2 space-y-6">
                    <?php foreach ($activities as $act): ?>
                    <div class="pl-6 relative">
                        <div class="w-6 h-6 absolute -left-3 top-0 bg-<?= $act['color'] ?>-100 rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                            <i class="fa-solid <?= $act['icon'] ?> text-<?= $act['color'] ?>-600 text-[8px]"></i>
                        </div>
                        <div class="text-[10px] text-slate-400 font-medium mb-0.5"><?= $act['time'] ?></div>
                        <div class="text-sm text-slate-800 font-semibold"><?= htmlspecialchars($act['user']) ?></div>
                        <div class="text-xs text-slate-500 mt-0.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-<?= $act['color'] ?>-50 text-<?= $act['color'] ?>-600 font-medium"><?= $act['label'] ?></span>
                            <span class="mx-1 text-slate-300">•</span>
                            <?= htmlspecialchars($act['track']) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
