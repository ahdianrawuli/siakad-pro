<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Jenis Absen Custom</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola jenis absensi yang bisa dibuat sendiri.</p>
        </div>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Jenis
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm text-left">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase w-10 text-center">#</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase">Nama</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">Target</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase">Sesi</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase">Status Options</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($types)): ?>
                    <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">Belum ada jenis absen.</td></tr>
                <?php endif; ?>
                <?php foreach ($types as $i => $t): ?>
                <tr class="hover:bg-slate-50/80">
                    <td class="px-5 py-3 text-center text-slate-400"><?= $i+1 ?></td>
                    <td class="px-5 py-3 font-bold text-slate-800"><?= htmlspecialchars($t['name']) ?></td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $t['target']==='SISWA'?'bg-green-100 text-green-700':($t['target']==='GURU'?'bg-blue-100 text-blue-700':'bg-purple-100 text-purple-700') ?>"><?= $t['target'] ?></span>
                    </td>
                    <td class="px-5 py-3 text-xs text-slate-500"><?= $t['sessions'] ?> sesi<?= $t['session_labels'] ? ' ('.htmlspecialchars($t['session_labels']).')' : '' ?></td>
                    <td class="px-5 py-3 text-xs text-slate-500"><?= htmlspecialchars($t['statuses']) ?></td>
                    <td class="px-5 py-3 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick='openEditType(<?= json_encode($t) ?>)'
                                class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white inline-flex items-center justify-center transition">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </button>
                            <a href="/attendance/custom/types/delete?id=<?= $t['id'] ?>" onclick="return confirm('Hapus jenis absen ini beserta datanya?')"
                                class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white inline-flex items-center justify-center transition">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Modal Tambah -->
<div id="addModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700"><i class="fa-solid fa-plus text-slate-400 mr-2"></i>Tambah Jenis Absen</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/attendance/custom/types/store" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Kegiatan</label>
                <input type="text" name="name" required placeholder="cth: Apel Pagi" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none"></div>
            <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Target</label>
                <select name="target" id="addTarget" onchange="togglePositions('add')" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
                    <option value="SISWA">Siswa</option>
                    <option value="GURU">Guru/Staff</option>
                    <option value="SEMUA">Semua</option>
                </select></div>
            <div id="addPositionWrap" class="hidden">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jabatan (opsional, kosong = semua)</label>
                <div class="max-h-32 overflow-y-auto bg-slate-50 border border-slate-200 rounded-xl p-2 space-y-1">
                    <?php foreach ($positions as $pos): ?>
                    <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="position_ids[]" value="<?= $pos['id'] ?>" class="rounded"> <?= htmlspecialchars($pos['name']) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Status (pisah koma)</label>
                <input type="text" name="statuses" value="HADIR,TIDAK_HADIR,IZIN,SAKIT,TERLAMBAT" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
                <p class="text-[10px] text-slate-400 mt-1">Bisa custom, cth: HADIR,TIDAK_HADIR,DISPENSASI</p></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Jumlah Sesi/Hari</label>
                    <input type="number" name="sessions" id="addSessions" value="1" min="1" max="10" onchange="renderSessionFields('add')" oninput="renderSessionFields('add')" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none"></div>
                <div></div>
            </div>
            <div id="addSessionFields" class="space-y-2"></div>
            <input type="hidden" name="session_labels" id="addSessionLabels">
            <input type="hidden" name="session_times" id="addSessionTimes">
            <input type="hidden" name="has_time" id="addHasTimeVal" value="0">
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePositions(prefix) {
    var target = document.getElementById(prefix+'Target').value;
    var wrap = document.getElementById(prefix+'PositionWrap');
    wrap.classList.toggle('hidden', target === 'SISWA');
}

function renderSessionFields(prefix) {
    var count = parseInt(document.getElementById(prefix+'Sessions').value) || 1;
    var container = document.getElementById(prefix+'SessionFields');
    // Preserve existing values
    var oldLabels = [], oldTimes = [], oldChecks = [];
    container.querySelectorAll('.sess-label').forEach(function(el){ oldLabels.push(el.value); });
    container.querySelectorAll('.sess-time').forEach(function(el){ oldTimes.push(el.value); });
    container.querySelectorAll('.sess-check').forEach(function(el){ oldChecks.push(el.checked); });

    var html = '<label class="block text-xs font-semibold text-slate-500 mb-1">Detail Sesi:</label>';
    for (var i = 0; i < count; i++) {
        var lbl = oldLabels[i] || '';
        var tm = oldTimes[i] || '';
        var chk = oldChecks[i] || false;
        html += '<div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">';
        html += '<span class="text-xs text-slate-400 font-bold w-6">'+(i+1)+'.</span>';
        html += '<input type="text" class="sess-label flex-1 px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs outline-none" placeholder="Label sesi '+(i+1)+'" value="'+lbl+'" onchange="syncSessionHidden(\''+prefix+'\')">';
        html += '<label class="flex items-center gap-1 text-[10px] text-slate-500 whitespace-nowrap cursor-pointer"><input type="checkbox" class="sess-check rounded" '+(chk?'checked':'')+' onchange="toggleSessTime(this);syncSessionHidden(\''+prefix+'\')"> Jam</label>';
        html += '<input type="time" class="sess-time px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs outline-none w-24 '+(chk?'':'hidden')+'" value="'+tm+'" onchange="syncSessionHidden(\''+prefix+'\')">';
        html += '</div>';
    }
    container.innerHTML = html;
    syncSessionHidden(prefix);
}

function toggleSessTime(cb) {
    var timeInput = cb.parentElement.nextElementSibling;
    timeInput.classList.toggle('hidden', !cb.checked);
}

function syncSessionHidden(prefix) {
    var container = document.getElementById(prefix+'SessionFields');
    var labels = [], times = [], hasAnyTime = false;
    container.querySelectorAll('.sess-label').forEach(function(el){ labels.push(el.value); });
    container.querySelectorAll('.sess-check').forEach(function(el){ if(el.checked) hasAnyTime = true; });
    container.querySelectorAll('.sess-time').forEach(function(el, i){
        var cb = container.querySelectorAll('.sess-check')[i];
        times.push(cb && cb.checked ? el.value : '');
    });
    var labelsEl = document.getElementById(prefix === 'add' ? 'addSessionLabels' : 'etLabels');
    var timesEl = document.getElementById(prefix === 'add' ? 'addSessionTimes' : 'etSessionTimes');
    var hasTimeEl = document.getElementById(prefix === 'add' ? 'addHasTimeVal' : 'etHasTimeVal');
    if (labelsEl) labelsEl.value = labels.join(',');
    if (timesEl) timesEl.value = times.join(',');
    if (hasTimeEl) hasTimeEl.value = hasAnyTime ? '1' : '0';
}

function openEditType(t) {
    document.getElementById('etId').value = t.id;
    document.getElementById('etName').value = t.name;
    document.getElementById('etTarget').value = t.target;
    document.getElementById('etStatuses').value = t.statuses;
    document.getElementById('etSessions').value = t.sessions;
    togglePositions('et');
    // Check positions
    var posIds = t.position_ids ? t.position_ids.split(',') : [];
    document.querySelectorAll('#editTypeModal input[name="position_ids[]"]').forEach(function(cb){ cb.checked = posIds.includes(cb.value); });
    // Render session fields then fill
    renderSessionFields('et');
    var labels = t.session_labels ? t.session_labels.split(',') : [];
    var times = t.session_times ? t.session_times.split(',') : [];
    var container = document.getElementById('etSessionFields');
    container.querySelectorAll('.sess-label').forEach(function(el, i){ el.value = labels[i] || ''; });
    container.querySelectorAll('.sess-time').forEach(function(el, i){
        if (times[i]) { el.value = times[i]; el.classList.remove('hidden'); }
    });
    container.querySelectorAll('.sess-check').forEach(function(el, i){
        if (times[i]) { el.checked = true; }
    });
    syncSessionHidden('et');
    document.getElementById('editTypeModal').classList.remove('hidden');
}

window.onclick=function(e){['addModal','editTypeModal'].forEach(function(id){if(e.target==document.getElementById(id))document.getElementById(id).classList.add('hidden');});};

// Init add modal
document.addEventListener('DOMContentLoaded', function(){ renderSessionFields('add'); });
</script>

<!-- Modal Edit -->
<div id="editTypeModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700"><i class="fa-solid fa-pen text-slate-400 mr-2"></i>Edit Jenis Absen</h3>
            <button onclick="document.getElementById('editTypeModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/attendance/custom/types/update" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="etId">
            <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama</label>
                <input type="text" name="name" id="etName" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none"></div>
            <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Target</label>
                <select name="target" id="etTarget" onchange="togglePositions('et')" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
                    <option value="SISWA">Siswa</option><option value="GURU">Guru/Staff</option><option value="SEMUA">Semua</option>
                </select></div>
            <div id="etPositionWrap" class="hidden">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jabatan</label>
                <div class="max-h-32 overflow-y-auto bg-slate-50 border border-slate-200 rounded-xl p-2 space-y-1">
                    <?php foreach ($positions as $pos): ?>
                    <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="position_ids[]" value="<?= $pos['id'] ?>" class="rounded"> <?= htmlspecialchars($pos['name']) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Status (koma)</label>
                <input type="text" name="statuses" id="etStatuses" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Jumlah Sesi</label>
                    <input type="number" name="sessions" id="etSessions" min="1" max="10" onchange="renderSessionFields('et')" oninput="renderSessionFields('et')" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none"></div>
                <div></div>
            </div>
            <div id="etSessionFields" class="space-y-2"></div>
            <input type="hidden" name="session_labels" id="etLabels">
            <input type="hidden" name="session_times" id="etSessionTimes">
            <input type="hidden" name="has_time" id="etHasTimeVal" value="0">
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('editTypeModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
