<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single { border-color: #e2e8f0; height: 42px; padding-top: 6px; border-radius: 0.75rem; background-color: #f8fafc; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
    .select2-container { width: 100% !important; }
    .select2-dropdown { border-color: #e2e8f0; border-radius: 0.75rem; overflow: hidden; }
    .select2-search--dropdown .select2-search__field { border-radius: 0.5rem; border-color: #e2e8f0; padding: 6px 10px; }
</style>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight"><?= $title ?></h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Proses kenaikan kelas atau kelulusan santri secara massal.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-bold border border-green-100">
                    <i class="fa-solid fa-arrow-up-right-dots"></i> Kenaikan Kelas / Kelulusan
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Pilih Kelas -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-6">
        <h4 class="font-bold text-slate-700 flex items-center gap-2 border-b border-slate-100 pb-4 mb-5">
            <i class="fa-solid fa-chalkboard text-slate-400"></i> Pilih Kelas Asal
        </h4>
        <form method="GET" action="/academic/promotion" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <select name="source_id" class="select2-class w-full" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($classrooms as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (isset($sourceId) && $sourceId == $c['id']) ? 'selected' : '' ?>>
                            Kelas <?= $c['name'] ?> (<?= $c['level'] ?> - <?= $c['major'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all text-sm whitespace-nowrap">
                <i class="fa-solid fa-users-viewfinder mr-2"></i> Tampilkan Siswa
            </button>
        </form>
    </div>

    <?php if (!empty($students)): ?>
    <form action="/academic/promotion/process" method="POST" id="promotionForm">
        <?= \App\Core\Csrf::input() ?>
        <input type="hidden" name="source_id" value="<?= $sourceId ?>">

        <!-- Opsi Tindakan -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-6">
            <h4 class="font-bold text-slate-700 flex items-center gap-2 border-b border-slate-100 pb-4 mb-5">
                <i class="fa-solid fa-gear text-slate-400"></i> Opsi Tindakan
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jenis Tindakan</label>
                    <select name="action" id="actionSelect" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                        <option value="promote">Naikkan ke Kelas Berikutnya</option>
                        <option value="graduate">Luluskan (Pindah ke Alumni)</option>
                    </select>
                </div>
                <div id="targetClassBox">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Kelas Tujuan</label>
                    <select name="target_class" class="select2-target w-full">
                        <option value="">-- Pilih Kelas Tujuan --</option>
                        <?php foreach ($allClassrooms as $c): ?>
                            <?php if ($c['id'] != $sourceId): ?>
                            <option value="<?= $c['id'] ?>">Kelas <?= $c['name'] ?> (<?= $c['level'] ?>)</option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabel Siswa -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h4 class="font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-users text-slate-400"></i> Daftar Siswa Kelas Asal
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg border border-blue-100"><?= count($students) ?> siswa</span>
                </h4>
                <label class="inline-flex items-center gap-2 cursor-pointer bg-white px-3 py-1.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    <input type="checkbox" id="selectAll" class="rounded text-blue-600 focus:ring-blue-500">
                    Pilih Semua
                </label>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full whitespace-nowrap text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-10 text-center">#</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">NIS</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php foreach ($students as $s): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                            <td class="px-5 py-4 text-center">
                                <input type="checkbox" name="student_ids[]" value="<?= $s['id'] ?>" class="student-checkbox rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                            </td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-500"><?= $s['nis'] ?></td>
                            <td class="px-5 py-4 font-extrabold text-slate-800"><?= $s['full_name'] ?></td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-bold border border-green-200">
                                    <i class="fa-solid fa-circle-check"></i> <?= $s['status'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin memproses data terpilih?')"
                    class="px-8 py-2.5 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 shadow-md shadow-green-500/20 transition-all text-sm flex items-center gap-2">
                    <i class="fa-solid fa-check-circle"></i> Proses Sekarang
                </button>
            </div>
        </div>
    </form>

    <?php elseif (isset($sourceId)): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-3">
        <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5"></i>
        <p class="text-sm text-amber-700 font-medium">Tidak ada siswa aktif ditemukan di kelas ini.</p>
    </div>
    <?php endif; ?>

</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Kenaikan Kelas</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 overflow-y-auto max-h-[70vh]">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Pilih <strong class="text-slate-700">Kelas Asal</strong> lalu klik <strong class="text-slate-700">Tampilkan Siswa</strong>.</li>
                    <li>Pilih <strong class="text-slate-700">Jenis Tindakan</strong>: Naik Kelas atau Luluskan.</li>
                    <li>Jika Naik Kelas, pilih <strong class="text-slate-700">Kelas Tujuan</strong>.</li>
                    <li>Centang siswa yang akan diproses (atau Pilih Semua).</li>
                    <li>Klik <strong class="text-slate-700">Proses Sekarang</strong> — data siswa akan dipindahkan.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Perbedaan Tindakan</h4>
                <div class="space-y-2">
                    <div class="flex items-start gap-3 p-2.5 bg-blue-50 rounded-xl border border-blue-200">
                        <i class="fa-solid fa-arrow-up text-blue-500 mt-0.5 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Naik Kelas</div><div class="text-[11px] text-slate-500">Siswa dipindahkan ke kelas tujuan yang dipilih. Status tetap ACTIVE.</div></div>
                    </div>
                    <div class="flex items-start gap-3 p-2.5 bg-green-50 rounded-xl border border-green-200">
                        <i class="fa-solid fa-graduation-cap text-green-500 mt-0.5 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Luluskan</div><div class="text-[11px] text-slate-500">Siswa dipindahkan ke tabel Alumni. Status berubah menjadi ALUMNI.</div></div>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-chalkboard text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Kelas</div><div class="text-[11px] text-slate-400">Kelas asal dan tujuan diambil dari <strong>Master → Data Kelas</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-graduation-cap text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Alumni</div><div class="text-[11px] text-slate-400">Siswa yang diluluskan akan muncul di <strong>Kesiswaan → Data Alumni</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-users text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Siswa</div><div class="text-[11px] text-slate-400">Perubahan kelas siswa akan langsung tercermin di <strong>Kesiswaan → Data Siswa</strong>.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-class').select2({ placeholder: '-- Pilih Kelas --', allowClear: true });
        $('.select2-target').select2({ placeholder: '-- Pilih Kelas Tujuan --', allowClear: true });
    });

    // Select All
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = this.checked);
        });
    }

    // Show/hide kelas tujuan
    const actionSelect = document.getElementById('actionSelect');
    const targetClassBox = document.getElementById('targetClassBox');
    if (actionSelect) {
        actionSelect.addEventListener('change', function() {
            const isGraduate = this.value === 'graduate';
            targetClassBox.style.display = isGraduate ? 'none' : 'block';
            const sel = targetClassBox.querySelector('select');
            if (isGraduate) { sel.value = ''; sel.removeAttribute('required'); }
            else { sel.setAttribute('required', 'required'); }
        });
    }

    window.onclick = function(e) { if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden'); }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
