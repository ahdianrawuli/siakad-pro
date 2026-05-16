<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Data & Jadwal Ekstrakurikuler</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola data, pembina, dan jadwal kegiatan ekstrakurikuler.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-star"></i> Total Ekskul: <?= count($ekskuls) ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <button onclick="document.getElementById('modalEkskul').classList.remove('hidden')"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2 w-fit">
            <i class="fa-solid fa-plus"></i> Tambah Ekskul
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php if (empty($ekskuls)): ?>
            <div class="col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm py-16 text-center text-slate-400 text-sm font-medium">
                Belum ada data ekstrakurikuler.
            </div>
        <?php endif; ?>
        <?php foreach ($ekskuls as $e): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-4 flex justify-between items-start">
                <div class="flex-1 min-w-0 pr-3">
                    <h4 class="font-extrabold text-white text-base leading-tight truncate"><?= htmlspecialchars($e['name']) ?></h4>
                    <p class="text-blue-100 text-xs mt-0.5 line-clamp-1"><?= htmlspecialchars($e['description'] ?? '-') ?></p>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button onclick='openEditEkskul(<?= json_encode(["id"=>$e["id"],"name"=>$e["name"],"description"=>$e["description"]]) ?>)'
                        class="w-7 h-7 rounded-lg bg-white/20 text-white hover:bg-white/40 flex items-center justify-center transition" title="Edit">
                        <i class="fa-solid fa-pen text-[10px]"></i>
                    </button>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border <?= $e['status'] === 'ACTIVE' ? 'bg-green-400/20 text-green-100 border-green-400/40' : 'bg-red-400/20 text-red-100 border-red-400/40' ?>">
                        <?= $e['status'] ?>
                    </span>
                </div>
            </div>

            <div class="p-5 flex flex-col gap-4 flex-1">
                <!-- Pembina -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[10px] font-bold uppercase text-slate-400 tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-chalkboard-user text-purple-400"></i> Pembina
                        </p>
                        <button onclick="openCoachModal(<?= $e['id'] ?>, '<?= htmlspecialchars($e['name'], ENT_QUOTES) ?>')"
                            class="text-[10px] text-blue-600 font-bold flex items-center gap-1 px-2 py-1 bg-blue-50 rounded-lg border border-blue-100 hover:bg-blue-100 transition">
                            <i class="fa-solid fa-plus"></i> Tambah
                        </button>
                    </div>
                    <?php if (empty($e['coaches'])): ?>
                        <p class="text-xs text-slate-400 italic">Belum ada pembina</p>
                    <?php else: ?>
                        <div class="space-y-1.5">
                            <?php foreach ($e['coaches'] as $c): ?>
                            <div class="flex items-center justify-between bg-purple-50 border border-purple-100 rounded-xl px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-purple-200 text-purple-700 flex items-center justify-center text-[10px] font-bold shrink-0">
                                        <?= strtoupper(substr($c['name'], 0, 1)) ?>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-700 truncate"><?= htmlspecialchars($c['name']) ?></span>
                                </div>
                                <a href="/extracurricular/coach/delete?id=<?= $c['id'] ?>"
                                    onclick="return confirm('Hapus pembina ini?')"
                                    class="w-6 h-6 rounded-lg bg-red-50 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition shrink-0" title="Hapus">
                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="border-t border-slate-100"></div>

                <!-- Jadwal -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[10px] font-bold uppercase text-slate-400 tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-days text-green-400"></i> Jadwal
                        </p>
                        <button onclick="openScheduleModal(<?= $e['id'] ?>, '<?= htmlspecialchars($e['name'], ENT_QUOTES) ?>')"
                            class="text-[10px] text-blue-600 font-bold flex items-center gap-1 px-2 py-1 bg-blue-50 rounded-lg border border-blue-100 hover:bg-blue-100 transition">
                            <i class="fa-solid fa-plus"></i> Tambah
                        </button>
                    </div>
                    <?php if (empty($e['schedules'])): ?>
                        <p class="text-xs text-slate-400 italic">Belum ada jadwal</p>
                    <?php else: ?>
                        <div class="space-y-1.5">
                            <?php foreach ($e['schedules'] as $s): ?>
                            <div class="bg-green-50 border border-green-100 rounded-xl px-3 py-2 flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold text-green-700"><?= $s['day_name'] ?></span>
                                        <span class="text-[10px] text-slate-500 font-mono bg-white border border-slate-200 px-1.5 py-0.5 rounded-md">
                                            <?= substr($s['start_time'],0,5) ?>–<?= substr($s['end_time'],0,5) ?>
                                        </span>
                                    </div>
                                    <?php if ($s['location']): ?>
                                    <div class="flex items-center gap-1 mt-1">
                                        <i class="fa-solid fa-location-dot text-[9px] text-slate-400"></i>
                                        <span class="text-[10px] text-slate-500 truncate"><?= htmlspecialchars($s['location']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button onclick="openEditScheduleModal(<?= htmlspecialchars(json_encode($s), ENT_QUOTES) ?>)"
                                        class="w-6 h-6 rounded-lg bg-blue-50 text-blue-400 hover:bg-blue-500 hover:text-white flex items-center justify-center transition" title="Edit">
                                        <i class="fa-solid fa-pen text-[9px]"></i>
                                    </button>
                                    <a href="/extracurricular/schedule/delete?id=<?= $s['id'] ?>"
                                        onclick="return confirm('Hapus jadwal ini?')"
                                        class="w-6 h-6 rounded-lg bg-red-50 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition" title="Hapus">
                                        <i class="fa-solid fa-xmark text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Ekstrakurikuler</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Klik <strong class="text-slate-700">Tambah Ekskul</strong> untuk mendaftarkan kegiatan baru.</li>
                    <li>Klik <strong class="text-slate-700">Tambah</strong> pada bagian Pembina untuk menambahkan guru, klik <strong class="text-slate-700">×</strong> untuk menghapus.</li>
                    <li>Klik <strong class="text-slate-700">Tambah</strong> pada bagian Jadwal untuk menambah jadwal, ikon <strong class="text-slate-700">pensil</strong> untuk edit, <strong class="text-slate-700">×</strong> untuk hapus.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-users text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Anggota Ekskul</div><div class="text-[11px] text-slate-400">Daftarkan santri ke ekskul di menu <strong>Ekstrakurikuler → Anggota</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-calendar-check text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Absensi Ekskul</div><div class="text-[11px] text-slate-400">Catat kehadiran kegiatan di menu <strong>Ekstrakurikuler → Absensi</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-chalkboard-user text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Guru</div><div class="text-[11px] text-slate-400">Pembina diambil dari daftar guru di <strong>Kepegawaian → Data Pegawai</strong>.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<!-- Modal Tambah Ekskul -->
<div id="modalEkskul" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-star text-slate-400"></i> Tambah Ekstrakurikuler</h3>
            <button onclick="document.getElementById('modalEkskul').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/extracurricular/store" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Ekskul</label>
                <input type="text" name="name" placeholder="cth: Pramuka, Futsal, Tahfidz"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Deskripsi singkat kegiatan..."
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalEkskul').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Pembina -->
<div id="modalCoach" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-chalkboard-user text-slate-400"></i> Tambah Pembina</h3>
            <button onclick="document.getElementById('modalCoach').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/extracurricular/coach/store" method="POST" class="p-6 space-y-4">
            <p id="coachEkskulName" class="text-sm font-semibold text-blue-700 bg-blue-50 px-3 py-2 rounded-xl border border-blue-100"></p>
            <input type="hidden" name="extracurricular_id" id="coachEkskulId">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Pilih Guru Pembina</label>
                <select name="user_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalCoach').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Jadwal -->
<div id="modalSchedule" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-calendar-days text-slate-400"></i> Tambah Jadwal</h3>
            <button onclick="document.getElementById('modalSchedule').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/extracurricular/schedule/store" method="POST" class="p-6 space-y-4">
            <p id="scheduleEkskulName" class="text-sm font-semibold text-blue-700 bg-blue-50 px-3 py-2 rounded-xl border border-blue-100"></p>
            <input type="hidden" name="extracurricular_id" id="scheduleEkskulId">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Hari</label>
                <select name="day_name" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                    <?php foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Ahad'] as $day): ?>
                        <option value="<?= $day ?>"><?= $day ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jam Mulai</label>
                    <input type="time" name="start_time" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jam Selesai</label>
                    <input type="time" name="end_time" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Lokasi</label>
                <input type="text" name="location" placeholder="cth: Lapangan, Aula, Masjid"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalSchedule').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Jadwal -->
<div id="modalEditSchedule" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-pen text-slate-400"></i> Edit Jadwal</h3>
            <button onclick="document.getElementById('modalEditSchedule').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/extracurricular/schedule/update" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="schedule_id" id="editScheduleId">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Hari</label>
                <select name="day_name" id="editScheduleDay" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                    <?php foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Ahad'] as $day): ?>
                        <option value="<?= $day ?>"><?= $day ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jam Mulai</label>
                    <input type="time" name="start_time" id="editScheduleStart" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jam Selesai</label>
                    <input type="time" name="end_time" id="editScheduleEnd" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Lokasi</label>
                <input type="text" name="location" id="editScheduleLocation" placeholder="cth: Lapangan, Aula, Masjid"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalEditSchedule').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCoachModal(id, name) {
        document.getElementById('coachEkskulId').value = id;
        document.getElementById('coachEkskulName').innerText = name;
        document.getElementById('modalCoach').classList.remove('hidden');
    }
    function openScheduleModal(id, name) {
        document.getElementById('scheduleEkskulId').value = id;
        document.getElementById('scheduleEkskulName').innerText = name;
        document.getElementById('modalSchedule').classList.remove('hidden');
    }
    function openEditScheduleModal(s) {
        document.getElementById('editScheduleId').value       = s.id;
        document.getElementById('editScheduleDay').value      = s.day_name;
        document.getElementById('editScheduleStart').value    = s.start_time;
        document.getElementById('editScheduleEnd').value      = s.end_time;
        document.getElementById('editScheduleLocation').value = s.location;
        document.getElementById('modalEditSchedule').classList.remove('hidden');
    }
    window.onclick = function(e) {
        ['infoModal','modalEkskul','modalCoach','modalSchedule','modalEditSchedule'].forEach(function(id) {
            if (e.target == document.getElementById(id)) document.getElementById(id).classList.add('hidden');
        });
    }

    function openEditEkskul(e) {
        document.getElementById('editEkskulId').value = e.id;
        document.getElementById('editEkskulName').value = e.name;
        document.getElementById('editEkskulDesc').value = e.description || '';
        document.getElementById('modalEditEkskul').classList.remove('hidden');
    }
</script>

<!-- Modal Edit Ekskul -->
<div id="modalEditEkskul" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700"><i class="fa-solid fa-pen text-slate-400 mr-2"></i>Edit Ekstrakurikuler</h3>
            <button onclick="document.getElementById('modalEditEkskul').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/extracurricular/update" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="editEkskulId">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Ekskul</label>
                <input type="text" name="name" id="editEkskulName" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Deskripsi</label>
                <textarea name="description" id="editEkskulDesc" rows="3" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalEditEkskul').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
