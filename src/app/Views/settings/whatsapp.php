<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">WhatsApp Gateway</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola koneksi WhatsApp dan kirim pesan ke orang tua/wali santri.</p>
            <div class="mt-3 flex items-center gap-2">
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Status & QR -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Status Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-green-600 to-emerald-600 flex items-center gap-3">
                    <i class="fa-brands fa-whatsapp text-white text-2xl"></i>
                    <div>
                        <h4 class="font-extrabold text-white text-base">Status Koneksi</h4>
                        <p class="text-green-100 text-xs">Real-time monitoring</p>
                    </div>
                </div>

                <div class="p-5 space-y-4">
                    <div id="statusBadge" class="text-center">
                        <?php
                        $st = $waStatus['status'] ?? 'disconnected';
                        $badgeClass = match($st) {
                            'connected'  => 'bg-green-100 text-green-700 border-green-200',
                            'qr_ready'   => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                            default      => 'bg-red-100 text-red-700 border-red-200',
                        };
                        $badgeText = match($st) {
                            'connected'  => '● Terhubung',
                            'qr_ready'   => '◌ Menunggu Scan QR',
                            default      => '○ Tidak Terhubung',
                        };
                        ?>
                        <span id="statusText" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold border <?= $badgeClass ?>"><?= $badgeText ?></span>
                    </div>

                    <!-- QR Code Area -->
                    <div id="qrArea" class="<?= $st !== 'qr_ready' ? 'hidden' : '' ?>">
                        <p class="text-xs text-slate-500 mb-3 text-center">Buka WhatsApp → Perangkat Tertaut → Scan QR</p>
                        <?php if (!empty($waStatus['qr'])): ?>
                            <img id="qrImg" src="<?= $waStatus['qr'] ?>" class="mx-auto w-48 h-48 border-2 border-slate-200 rounded-xl" alt="QR Code">
                        <?php else: ?>
                            <img id="qrImg" src="" class="mx-auto w-48 h-48 border-2 border-slate-200 rounded-xl hidden" alt="QR Code">
                        <?php endif; ?>
                        <p class="text-xs text-slate-400 mt-2 text-center">QR otomatis diperbarui setiap 30 detik</p>
                    </div>

                    <!-- Connected Info -->
                    <div id="connectedArea" class="<?= $st !== 'connected' ? 'hidden' : '' ?>">
                        <p class="text-center text-sm text-slate-600 mb-4">WhatsApp siap digunakan untuk mengirim pesan.</p>
                        <form action="/settings/whatsapp/logout" method="POST">
                            <?= \App\Core\Csrf::input() ?>
                            <button type="submit" class="w-full bg-red-600 text-white py-2.5 rounded-xl font-bold hover:bg-red-700 text-sm transition shadow-md shadow-red-500/20"
                                onclick="return confirm('Yakin logout WhatsApp?')">
                                <i class="fa-solid fa-right-from-bracket mr-1"></i> Logout WhatsApp
                            </button>
                        </form>
                    </div>

                    <!-- Disconnected Info -->
                    <div id="disconnectedArea" class="<?= $st !== 'disconnected' ? 'hidden' : '' ?>">
                        <p class="text-center text-sm text-slate-500">Menghubungkan ke service...</p>
                    </div>
                </div>
            </div>

            <!-- Kirim Manual -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-5 py-3 bg-slate-50 border-b border-slate-200">
                    <h4 class="font-bold text-slate-700 text-sm">Kirim Pesan Manual</h4>
                </div>
                <form action="/settings/whatsapp/send" method="POST" class="p-5 space-y-4">
                    <?= \App\Core\Csrf::input() ?>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nomor HP</label>
                        <input type="text" name="number" placeholder="08xx atau 628xx"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500/50 outline-none transition-all" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Pesan</label>
                        <textarea name="message" rows="4" placeholder="Tulis pesan..."
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500/50 outline-none transition-all resize-none" required></textarea>
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-2.5 rounded-xl font-bold hover:bg-green-700 text-sm transition shadow-md shadow-green-500/20">
                        <i class="fa-brands fa-whatsapp mr-1"></i> Kirim Sekarang
                    </button>
                </form>
            </div>
        </div>

        <!-- Blasting -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-5 py-3 bg-slate-50 border-b border-slate-200">
                    <h4 class="font-bold text-slate-700 text-sm">Blasting Pesan</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Kirim pesan serentak ke nomor orang tua berdasarkan kelas.</p>
                </div>

                <form action="/settings/whatsapp/blast" method="POST" class="p-5 space-y-4">
                    <?= \App\Core\Csrf::input() ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Target Penerima</label>
                            <select name="blast_target" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                                <option value="parent">Orang Tua / Wali Santri</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Filter Kelas</label>
                            <select name="blast_class" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                                <option value="">Semua Kelas</option>
                                <?php foreach($classrooms as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Isi Pesan</label>
                        <textarea name="blast_message" rows="6" placeholder="Contoh: Assalamu'alaikum, kami informasikan bahwa..."
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none" required></textarea>
                        <p class="text-xs text-slate-400 mt-1">Pesan yang sama akan dikirim ke semua nomor yang dipilih.</p>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-blue-700 text-sm transition shadow-md shadow-blue-500/20"
                        onclick="return confirm('Yakin kirim blasting? Pesan akan dikirim ke semua nomor yang dipilih.')">
                        <i class="fa-solid fa-paper-plane mr-1"></i> Kirim Blasting
                    </button>
                </form>

                <!-- Preview Daftar Nomor -->
                <div class="border-t border-slate-200">
                    <div class="px-5 py-3 bg-slate-50 border-b border-slate-200">
                        <h5 class="font-bold text-sm text-slate-700">Daftar Nomor Tersedia</h5>
                    </div>
                    <div class="overflow-x-auto max-h-80 overflow-y-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 sticky top-0 border-b border-slate-200">
                                <tr>
                                    <th class="text-left px-4 py-3 font-bold text-slate-500 uppercase tracking-wider">Nama Santri</th>
                                    <th class="text-left px-4 py-3 font-bold text-slate-500 uppercase tracking-wider">Kelas</th>
                                    <th class="text-left px-4 py-3 font-bold text-slate-500 uppercase tracking-wider">No. Orang Tua</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if(empty($students)): ?>
                                    <tr><td colspan="3" class="text-center p-8 text-slate-400">Tidak ada data santri aktif.</td></tr>
                                <?php endif; ?>
                                <?php foreach($students as $s): ?>
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-4 py-3 font-semibold text-slate-700"><?= htmlspecialchars($s['full_name']) ?></td>
                                    <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars($s['class_name'] ?? '-') ?></td>
                                    <td class="px-4 py-3 <?= empty($s['parent_phone']) ? 'text-red-500 italic' : 'text-green-700 font-mono' ?>">
                                        <?= htmlspecialchars($s['parent_phone'] ?: 'Tidak ada nomor') ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan WhatsApp Gateway</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Scan <strong class="text-slate-700">QR Code</strong> dengan WhatsApp untuk menghubungkan perangkat.</li>
                    <li>Gunakan <strong class="text-slate-700">Kirim Manual</strong> untuk mengirim pesan ke satu nomor.</li>
                    <li>Gunakan <strong class="text-slate-700">Blasting</strong> untuk mengirim pesan ke banyak nomor sekaligus.</li>
                    <li>Pastikan nomor orang tua sudah terisi di <strong class="text-slate-700">Data Santri</strong>.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-graduation-cap text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Santri</div><div class="text-[11px] text-slate-400">Nomor orang tua diambil dari <strong>Kesiswaan → Data Santri</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-door-open text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Kelas</div><div class="text-[11px] text-slate-400">Filter kelas diambil dari <strong>Akademik → Data Kelas</strong>.</div></div>
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
// Polling status & QR setiap 5 detik
function pollStatus() {
    fetch('/settings/whatsapp/status')
        .then(r => r.json())
        .then(data => {
            const st = data.status || 'disconnected';

            // Update badge
            const badge = document.getElementById('statusText');
            const map = {
                connected:     ['● Terhubung',           'bg-green-100 text-green-700 border-green-200'],
                qr_ready:      ['◌ Menunggu Scan QR',    'bg-yellow-100 text-yellow-700 border-yellow-200'],
                disconnected:  ['○ Tidak Terhubung',     'bg-red-100 text-red-700 border-red-200'],
            };
            const [text, cls] = map[st] || map.disconnected;
            badge.textContent = text;
            badge.className = 'inline-flex items-center px-4 py-2 rounded-full text-sm font-bold border ' + cls;

            // Show/hide panels
            document.getElementById('qrArea').classList.toggle('hidden', st !== 'qr_ready');
            document.getElementById('connectedArea').classList.toggle('hidden', st !== 'connected');
            document.getElementById('disconnectedArea').classList.toggle('hidden', st !== 'disconnected');

            // Update QR image
            if (st === 'qr_ready' && data.qr) {
                const img = document.getElementById('qrImg');
                img.src = data.qr;
                img.classList.remove('hidden');
            }
        })
        .catch(() => {});
}

setInterval(pollStatus, 5000);

window.onclick = function(e) {
    if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden');
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
