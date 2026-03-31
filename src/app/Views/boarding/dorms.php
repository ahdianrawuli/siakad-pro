<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-3xl font-medium text-gray-700">Manajemen Asrama</h3>
        
        <button onclick="document.getElementById('modalAddDorm').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded font-bold shadow hover:bg-blue-700">
            <i class="fa-solid fa-plus mr-2"></i> Tambah Gedung
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div id="modalAddDorm" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-bold mb-4">Tambah Gedung / Kamar</h3>
            <form action="/boarding/dorms/store" method="POST">
                <?= \App\Core\Csrf::input() ?>
                <div class="mb-3">
                    <label class="block text-xs font-bold uppercase mb-1">Nama Gedung/Kamar</label>
                    <input type="text" name="name" class="w-full p-2 border rounded" placeholder="Contoh: Gedung A - Lt 1" required>
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-bold uppercase mb-1">Kapasitas (Orang)</label>
                    <input type="number" name="capacity" class="w-full p-2 border rounded" placeholder="10" required>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold uppercase mb-1">Peruntukan</label>
                    <select name="gender" class="w-full p-2 border rounded">
                        <option value="L">Putra (Ikhwan)</option>
                        <option value="P">Putri (Akhwat)</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalAddDorm').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded text-sm font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow h-fit">
            <h4 class="font-bold mb-4 border-b pb-2 text-purple-600">
                <i class="fa-solid fa-users-move mr-2"></i> Tempatkan Santri
            </h4>
            <form action="/boarding/assign" method="POST">
                <?= \App\Core\Csrf::input() ?>
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-600">Pilih Santri (Non-Asrama)</label>
                    <select name="student_id" class="w-full p-2 border rounded select2" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach($students as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?> (<?= $s['nis'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">*Hanya menampilkan santri yg belum dapat kamar.</p>
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-600">Pilih Kamar Tujuan</label>
                    <select name="dorm_id" class="w-full p-2 border rounded" required>
                        <?php foreach($dorms as $d): ?>
                            <option value="<?= $d['id'] ?>"><?= $d['name'] ?> (Sisa: <?= $d['capacity'] - $d['occupied'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded font-bold hover:bg-purple-700">
                    Simpan Penempatan
                </button>
            </form>
        </div>

        <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach($dorms as $d): 
                $percent = ($d['capacity'] > 0) ? ($d['occupied'] / $d['capacity']) * 100 : 0;
                $color = $percent >= 100 ? 'bg-red-500' : ($percent > 80 ? 'bg-yellow-500' : 'bg-green-500');
            ?>
            <div class="bg-white p-4 rounded shadow border-l-4 <?= $d['gender']=='L'?'border-blue-500':'border-pink-500' ?> relative group">
                
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                    <form action="/boarding/dorms/delete" method="POST" onsubmit="return confirm('Hapus gedung <?= $d['name'] ?>?')">
                        <?= \App\Core\Csrf::input() ?>
                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                        <button class="text-gray-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </div>

                <div class="flex justify-between items-center mb-2 pr-6">
                    <h4 class="font-bold text-lg text-gray-800"><?= $d['name'] ?></h4>
                    <span class="text-xs font-bold px-2 py-1 bg-gray-100 rounded text-gray-600 border">
                        <?= $d['gender']=='L'?'IKHWAN':'AKHWAT' ?>
                    </span>
                </div>
                
                <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                    <div class="<?= $color ?> h-3 rounded-full transition-all duration-500" style="width: <?= $percent ?>%"></div>
                </div>
                
                <div class="flex justify-between text-sm text-gray-600 mt-2 bg-gray-50 p-2 rounded">
                    <span>Terisi: <b><?= $d['occupied'] ?></b> Orang</span>
                    <span>Kapasitas: <b><?= $d['capacity'] ?></b></span>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if(empty($dorms)): ?>
                <div class="col-span-2 text-center p-8 bg-white rounded border border-dashed border-gray-300 text-gray-400">
                    Belum ada gedung asrama. Klik tombol "Tambah Gedung" di atas.
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
