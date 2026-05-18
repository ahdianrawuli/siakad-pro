<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Fingerprint Device</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola perangkat fingerprint dan integrasi absensi otomatis.</p>
        </div>
        <button onclick="document.getElementById('addDeviceModal').classList.remove('hidden')"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Device
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Panduan Integrasi -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
        <h4 class="font-bold text-slate-800 mb-3 flex items-center gap-2"><i class="fa-solid fa-book text-blue-500"></i> Panduan Integrasi Fingerprint</h4>
        <div class="space-y-3 text-sm text-slate-600">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <h5 class="font-bold text-blue-800 mb-2">1. Mesin yang Didukung</h5>
                <p>ZKTeco (K40, MB20, iClock 360, ProFace X) atau mesin lain yang support Push API / ADMS.</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <h5 class="font-bold text-green-800 mb-2">2. Cara Integrasi</h5>
                <ol class="list-decimal list-inside space-y-1 text-slate-600">
                    <li>Tambah device di bawah (isi IP, port, lokasi)</li>
                    <li>Salin <strong>API Key</strong> yang di-generate</li>
                    <li>Di software mesin, set Push URL ke: <code class="bg-white px-2 py-0.5 rounded border text-xs"><?= ($_SERVER['REQUEST_SCHEME'] ?? 'https') ?>://<?= $_SERVER['HTTP_HOST'] ?>/api/fingerprint/clock</code></li>
                    <li>Set header <code class="bg-white px-2 py-0.5 rounded border text-xs">X-API-Key: [api_key]</code></li>
                    <li>Mapping Finger ID di mesin dengan user/siswa di sistem</li>
                </ol>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <h5 class="font-bold text-amber-800 mb-2">3. Format Data (JSON POST)</h5>
                <pre class="bg-white rounded-lg p-3 text-xs font-mono overflow-x-auto border">{
  "api_key": "dari_device",
  "finger_id": 1,
  "timestamp": "2026-05-18 07:05:00",
  "type": "IN"  // atau "OUT"
}</pre>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                <h5 class="font-bold text-purple-800 mb-2">4. Logika Otomatis</h5>
                <ul class="list-disc list-inside space-y-1">
                    <li><strong>Device Staff:</strong> Clock IN → set jam datang & status HADIR. Clock OUT → set jam pulang.</li>
                    <li><strong>Device Siswa (Masjid):</strong> Otomatis deteksi waktu sholat berdasarkan jam scan (Subuh 03-06, Dzuhur 11-14, Ashar 14-17, Maghrib 17-19, Isya 19-22).</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Daftar Device -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="p-4 bg-slate-50 border-b border-slate-100">
            <h4 class="font-bold text-slate-700 text-sm"><i class="fa-solid fa-server mr-2 text-slate-400"></i>Daftar Device (<?= count($devices) ?>)</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase">Nama</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase">IP:Port</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase">Lokasi</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase">Tipe</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase">API Key</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase text-center">Status</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($devices)): ?>
                        <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">Belum ada device.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($devices as $d): ?>
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-5 py-3 font-bold text-slate-800"><?= htmlspecialchars($d['name']) ?></td>
                        <td class="px-5 py-3 font-mono text-xs"><?= $d['ip_address'] ?>:<?= $d['port'] ?></td>
                        <td class="px-5 py-3 text-slate-600"><?= htmlspecialchars($d['location'] ?? '-') ?></td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $d['type']==='STAFF'?'bg-blue-100 text-blue-700':'bg-green-100 text-green-700' ?>"><?= $d['type'] ?></span></td>
                        <td class="px-5 py-3"><code class="bg-slate-100 px-2 py-0.5 rounded text-[10px] font-mono select-all"><?= $d['api_key'] ?></code></td>
                        <td class="px-5 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $d['is_active']?'bg-green-100 text-green-700':'bg-red-100 text-red-700' ?>"><?= $d['is_active']?'Aktif':'Nonaktif' ?></span></td>
                        <td class="px-5 py-3 text-center">
                            <a href="/settings/fingerprint/delete?id=<?= $d['id'] ?>" onclick="return confirm('Hapus device ini?')"
                                class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white inline-flex items-center justify-center transition">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mapping -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <h4 class="font-bold text-slate-700 text-sm"><i class="fa-solid fa-link mr-2 text-slate-400"></i>Mapping Finger ID</h4>
            <button onclick="document.getElementById('addMappingModal').classList.remove('hidden')"
                class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition">
                <i class="fa-solid fa-plus mr-1"></i> Tambah Mapping
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase">Device</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase">Finger ID</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase">Nama</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase">Tipe</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-500 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($mappings)): ?>
                        <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400">Belum ada mapping.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($mappings as $m): ?>
                    <tr>
                        <td class="px-5 py-2"><?= htmlspecialchars($m['device_name']) ?></td>
                        <td class="px-5 py-2 font-mono font-bold"><?= $m['finger_id'] ?></td>
                        <td class="px-5 py-2 font-bold"><?= htmlspecialchars($m['person_name'] ?? '-') ?></td>
                        <td class="px-5 py-2 text-xs"><?= $m['person_type'] ?></td>
                        <td class="px-5 py-2 text-center">
                            <a href="/settings/fingerprint/mapping/delete?id=<?= $m['id'] ?>" onclick="return confirm('Hapus mapping ini?')"
                                class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white inline-flex items-center justify-center transition">
                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($mapTotalPages > 1): ?>
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
            <span class="text-xs text-slate-500"><?= $mapTotal ?> mapping</span>
            <div class="flex items-center gap-1.5">
                <?php if ($mapPage > 1): ?>
                    <a href="?map_page=<?= $mapPage-1 ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold hover:text-blue-600 transition shadow-sm"><i class="fa-solid fa-chevron-left"></i></a>
                <?php endif; ?>
                <span class="text-xs font-bold text-slate-600 px-2">Hal <?= $mapPage ?>/<?= $mapTotalPages ?></span>
                <?php if ($mapPage < $mapTotalPages): ?>
                    <a href="?map_page=<?= $mapPage+1 ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold hover:text-blue-600 transition shadow-sm"><i class="fa-solid fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

</main>

<!-- Modal Tambah Device -->
<div id="addDeviceModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700"><i class="fa-solid fa-plus text-slate-400 mr-2"></i>Tambah Device</h3>
            <button onclick="document.getElementById('addDeviceModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/settings/fingerprint/store" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Device</label>
                <input type="text" name="name" required placeholder="cth: Mesin Kantor Guru" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">IP Address</label>
                    <input type="text" name="ip_address" required placeholder="192.168.1.100" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none"></div>
                <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Port</label>
                    <input type="number" name="port" value="4370" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Lokasi</label>
                    <input type="text" name="location" placeholder="Kantor Guru" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none"></div>
                <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Tipe</label>
                    <select name="type" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
                        <option value="STAFF">Staff/Guru (Clock In/Out)</option>
                        <option value="STUDENT">Siswa (Absensi Sholat)</option>
                    </select></div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addDeviceModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
window.onclick=function(e){['addDeviceModal','addMappingModal'].forEach(function(id){if(e.target==document.getElementById(id))document.getElementById(id).classList.add('hidden');});};
$(document).ready(function(){
    $('#mappingUser').select2({ placeholder:'-- Cari Staff/Guru --', allowClear:true, width:'100%', dropdownParent:$('#addMappingModal') });
    $('#mappingStudent').select2({ placeholder:'-- Cari Siswa --', allowClear:true, width:'100%', dropdownParent:$('#addMappingModal') });
});
</script>

<!-- Modal Mapping -->
<div id="addMappingModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700"><i class="fa-solid fa-link text-slate-400 mr-2"></i>Mapping Finger ID</h3>
            <button onclick="document.getElementById('addMappingModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/settings/fingerprint/mapping" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Device</label>
                    <select name="device_id" required class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
                        <?php foreach ($devices as $d): ?><option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option><?php endforeach; ?>
                    </select></div>
                <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Finger ID (di mesin)</label>
                    <input type="number" name="finger_id" required min="1" placeholder="001" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none"></div>
            </div>
            <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Staff/Guru (untuk device Staff)</label>
                <select name="user_id" id="mappingUser" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
                    <option value="">-- Tidak dipilih --</option>
                    <?php foreach ($users as $u): ?><option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?></option><?php endforeach; ?>
                </select></div>
            <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Siswa (untuk device Siswa)</label>
                <select name="student_id" id="mappingStudent" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
                    <option value="">-- Tidak dipilih --</option>
                    <?php foreach ($students as $s): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['full_name']) ?> (<?= $s['nis'] ?>)</option><?php endforeach; ?>
                </select></div>
            <p class="text-[10px] text-slate-400">Pilih salah satu: Staff/Guru ATAU Siswa, sesuai tipe device.</p>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addMappingModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
