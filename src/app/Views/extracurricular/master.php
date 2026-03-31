<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between">
        <h3 class="text-3xl font-medium text-gray-700">Data & Jadwal Ekstrakurikuler</h3>
        <button onclick="document.getElementById('modalEkskul').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
            + Tambah Ekskul
        </button>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($ekskuls as $e): ?>
        <div class="bg-white rounded shadow p-4 border-t-4 border-blue-500">
            <div class="flex justify-between items-start">
                <h4 class="font-bold text-lg"><?= $e['name'] ?></h4>
                <span class="text-xs bg-gray-200 px-2 py-1 rounded"><?= $e['status'] ?></span>
            </div>
            <p class="text-sm text-gray-500 mb-4"><?= $e['description'] ?></p>
            
            <div class="mb-3">
                <p class="text-xs font-bold uppercase text-gray-400">Pembina (Guru)</p>
                <p class="text-sm text-gray-800"><?= $e['coaches'] ?: '-' ?></p>
                <button onclick="openCoachModal(<?= $e['id'] ?>, '<?= $e['name'] ?>')" class="text-xs text-blue-600 hover:underline">+ Atur Pembina</button>
            </div>

            <div class="mb-3">
                <p class="text-xs font-bold uppercase text-gray-400">Jadwal</p>
                <p class="text-sm text-gray-800"><?= $e['schedules'] ?: '-' ?></p>
                <button onclick="openScheduleModal(<?= $e['id'] ?>, '<?= $e['name'] ?>')" class="text-xs text-blue-600 hover:underline">+ Atur Jadwal</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div id="modalEkskul" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <form action="/extracurricular/store" method="POST">
                <h3 class="text-lg font-bold mb-4">Tambah Ekstrakurikuler</h3>
                <input type="text" name="name" placeholder="Nama Ekskul" class="w-full border p-2 mb-2 rounded" required>
                <textarea name="description" placeholder="Deskripsi Singkat" class="w-full border p-2 mb-4 rounded"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalEkskul').classList.add('hidden')" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalCoach" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <form action="/extracurricular/coach/store" method="POST">
                <h3 class="text-lg font-bold mb-2">Atur Pembina</h3>
                <p id="coachEkskulName" class="mb-4 text-sm text-gray-500"></p>
                <input type="hidden" name="extracurricular_id" id="coachEkskulId">
                
                <select name="user_id" class="w-full border p-2 mb-4 rounded" required>
                    <?php foreach($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                    <?php endforeach; ?>
                </select>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalCoach').classList.add('hidden')" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalSchedule" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <form action="/extracurricular/schedule/store" method="POST">
                <h3 class="text-lg font-bold mb-2">Tambah Jadwal</h3>
                <p id="scheduleEkskulName" class="mb-4 text-sm text-gray-500"></p>
                <input type="hidden" name="extracurricular_id" id="scheduleEkskulId">
                
                <select name="day_name" class="w-full border p-2 mb-2 rounded" required>
                    <option value="Senin">Senin</option><option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option><option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option><option value="Sabtu">Sabtu</option><option value="Ahad">Ahad</option>
                </select>
                <div class="flex gap-2 mb-2">
                    <input type="time" name="start_time" class="w-1/2 border p-2 rounded" required>
                    <input type="time" name="end_time" class="w-1/2 border p-2 rounded" required>
                </div>
                <input type="text" name="location" placeholder="Lokasi (mis: Lapangan)" class="w-full border p-2 mb-4 rounded">

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalSchedule').classList.add('hidden')" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</main>

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
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

