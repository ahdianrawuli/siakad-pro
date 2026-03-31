<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6">
        <h3 class="text-3xl font-medium text-gray-700">Rapor Asrama</h3>
        <p class="text-sm text-gray-500">Input nilai kepesantrenan (Tahfidz, Bahasa, Akhlaq).</p>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white p-4 rounded shadow-sm mb-6 border border-gray-200">
        <form class="flex gap-4 items-end">
            <div>
                <label class="block text-xs font-bold mb-1">Pilih Kelas</label>
                <select name="classroom_id" class="p-2 border rounded w-64" onchange="this.form.submit()">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($classrooms as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $selectedClass == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <?php if($selectedClass && !empty($students)): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($students as $s): ?>
        <div class="bg-white rounded border border-gray-200 shadow-sm p-4 relative group hover:shadow-md transition">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h4 class="font-bold text-gray-800"><?= $s['full_name'] ?></h4>
                    <span class="text-xs text-gray-500"><?= $s['nis'] ?></span>
                </div>
                <button onclick='inputScore(<?= json_encode($s) ?>)' class="text-blue-600 hover:text-blue-800 text-sm border px-2 py-1 rounded">
                    <i class="fa fa-edit"></i> Input Nilai
                </button>
            </div>
            
            <div class="text-xs space-y-1 mt-3">
                <div class="flex justify-between border-b pb-1">
                    <span class="text-gray-600">Tahfidz</span>
                    <span class="font-bold <?= $s['tahfidz_grade'] ? 'text-green-600' : 'text-gray-300' ?>"><?= $s['tahfidz_grade'] ?? '-' ?></span>
                </div>
                <div class="flex justify-between border-b pb-1">
                    <span class="text-gray-600">Bahasa</span>
                    <span class="font-bold <?= $s['language_grade'] ? 'text-green-600' : 'text-gray-300' ?>"><?= $s['language_grade'] ?? '-' ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Akhlaq</span>
                    <span class="font-bold <?= $s['character_grade'] ? 'text-green-600' : 'text-gray-300' ?>"><?= $s['character_grade'] ?? '-' ?></span>
                </div>
            </div>

            <?php if($s['tahfidz_grade']): ?>
                <a href="/report/boarding/print?student_id=<?= $s['id'] ?>&year_id=<?= $activeYear['id'] ?>" target="_blank" class="absolute bottom-2 right-2 text-gray-400 hover:text-gray-800">
                    <i class="fa fa-print"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php elseif($selectedClass): ?>
        <div class="text-center p-10 text-gray-400 bg-white rounded">Tidak ada siswa di kelas ini.</div>
    <?php endif; ?>
</main>

<div id="scoreModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-2xl shadow-xl overflow-y-auto max-h-[90vh]">
        <h3 class="text-xl font-bold mb-4">Input Rapor Asrama: <span id="modalStudentName" class="text-blue-600"></span></h3>
        <form action="/report/boarding/store" method="POST">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="student_id" id="inpStudentId">
            <input type="hidden" name="academic_year_id" value="<?= $activeYear['id'] ?? '' ?>">
            <input type="hidden" name="classroom_id" value="<?= $selectedClass ?>">

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="border p-3 rounded">
                    <label class="block text-xs font-bold mb-2 text-center bg-gray-100 p-1">TAHFIDZ</label>
                    <select name="tahfidz_grade" class="w-full p-2 border rounded mb-2 text-center font-bold">
                        <option value="">- Nilai -</option>
                        <option value="A">A (Mumtaz)</option>
                        <option value="B">B (Jayyid Jiddan)</option>
                        <option value="C">C (Jayyid)</option>
                        <option value="D">D (Maqbul)</option>
                    </select>
                    <textarea name="tahfidz_desc" class="w-full p-2 border rounded h-20 text-xs" placeholder="Deskripsi hafalan..."></textarea>
                </div>

                <div class="border p-3 rounded">
                    <label class="block text-xs font-bold mb-2 text-center bg-gray-100 p-1">BAHASA</label>
                    <select name="language_grade" class="w-full p-2 border rounded mb-2 text-center font-bold">
                        <option value="">- Nilai -</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                    <textarea name="language_desc" class="w-full p-2 border rounded h-20 text-xs" placeholder="Deskripsi bahasa..."></textarea>
                </div>

                <div class="border p-3 rounded">
                    <label class="block text-xs font-bold mb-2 text-center bg-gray-100 p-1">AKHLAQ / DISIPLIN</label>
                    <select name="character_grade" class="w-full p-2 border rounded mb-2 text-center font-bold">
                        <option value="">- Nilai -</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                    <textarea name="character_desc" class="w-full p-2 border rounded h-20 text-xs" placeholder="Catatan perilaku..."></textarea>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold mb-1">Catatan Wali Asrama (Musyrif)</label>
                <textarea name="homeroom_note" class="w-full p-2 border rounded h-16"></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('scoreModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold">Simpan Rapor</button>
            </div>
        </form>
    </div>
</div>

<script>
    function inputScore(student) {
        document.getElementById('scoreModal').classList.remove('hidden');
        document.getElementById('modalStudentName').innerText = student.full_name;
        document.getElementById('inpStudentId').value = student.id;
        // Reset form or load existing via AJAX if needed (Simple version: always blank/default)
    }
</script>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
