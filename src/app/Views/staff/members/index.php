<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-3xl font-medium text-gray-700">Data Pegawai</h3>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <i class="fa fa-plus"></i> Tambah Pegawai
        </button>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white p-4 rounded shadow-sm mb-4 border border-gray-200">
        <form class="flex flex-wrap gap-4">
            <input type="text" name="search" value="<?= $search ?>" placeholder="Cari Nama / NIP..." class="p-2 border rounded w-64 text-sm">
            <select name="position_id" class="p-2 border rounded text-sm">
                <option value="">Semua Jabatan</option>
                <?php foreach($positions as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $selectedPos == $p['id'] ? 'selected' : '' ?>><?= $p['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <button class="bg-gray-800 text-white px-4 py-2 rounded text-sm">Filter</button>
            <a href="/staff/members" class="text-red-500 text-sm flex items-center">Reset</a>
        </form>
    </div>

    <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm whitespace-nowrap">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Pegawai</th>
                        <th class="px-4 py-3 text-left">Jabatan</th>
                        <th class="px-4 py-3 text-left">Kontak</th>
                        <th class="px-4 py-3 text-left">Akun User</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($staffs)): ?>
                        <tr><td colspan="6" class="p-4 text-center text-gray-400">Data tidak ditemukan.</td></tr>
                    <?php endif; ?>

                    <?php foreach($staffs as $s): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-bold text-gray-800"><?= $s['full_name'] ?></div>
                            <div class="text-xs text-gray-500">NIP: <?= $s['nip'] ?></div>
                        </td>
                        <td class="px-4 py-3"><?= $s['position_name'] ?></td>
                        <td class="px-4 py-3 text-xs">
                            <div><i class="fa fa-phone w-4"></i> <?= $s['phone'] ?? '-' ?></div>
                            <div><i class="fa fa-envelope w-4"></i> <?= $s['email'] ?? '-' ?></div>
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <?php if($s['username']): ?>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded">@<?= $s['username'] ?></span>
                            <?php else: ?>
                                <span class="text-gray-400">No Login</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs <?= $s['status'] == 'ACTIVE' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                                <?= $s['status'] ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="/staff/members/delete?id=<?= $s['id'] ?>" class="text-red-500" onclick="return confirm('Hapus pegawai & akun?')">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if($totalPages > 1): ?>
        <div class="p-4 bg-gray-50 border-t flex justify-between items-center text-xs">
            <span>Hal <?= $currentPage ?> dari <?= $totalPages ?></span>
            <div class="flex gap-1">
                <?php if($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>&search=<?= $search ?>&position_id=<?= $selectedPos ?>" class="px-3 py-1 bg-white border rounded">Prev</a>
                <?php endif; ?>
                <?php if($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>&search=<?= $search ?>&position_id=<?= $selectedPos ?>" class="px-3 py-1 bg-white border rounded">Next</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-lg shadow-xl overflow-y-auto max-h-[90vh]">
        <h3 class="text-xl font-bold mb-4">Tambah Pegawai Baru</h3>
        <form action="/staff/members/store" method="POST">
            <?= \App\Core\Csrf::input() ?>
            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="text-xs font-bold block mb-1">NIP / ID</label>
                    <input type="text" name="nip" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label class="text-xs font-bold block mb-1">Nama Lengkap</label>
                    <input type="text" name="full_name" class="w-full p-2 border rounded" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="text-xs font-bold block mb-1">Jabatan</label>
                <select name="position_id" class="w-full p-2 border rounded" required>
                    <option value="">-- Pilih --</option>
                    <?php foreach($positions as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="text-xs font-bold block mb-1">Email</label>
                    <input type="email" name="email" class="w-full p-2 border rounded">
                </div>
                <div>
                    <label class="text-xs font-bold block mb-1">No HP</label>
                    <input type="text" name="phone" class="w-full p-2 border rounded">
                </div>
            </div>
             <div class="mb-3">
                <label class="text-xs font-bold block mb-1">Gender</label>
                <select name="gender" class="w-full p-2 border rounded">
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="text-xs font-bold block mb-1">Alamat</label>
                <textarea name="address" class="w-full p-2 border rounded h-20" placeholder="Alamat lengkap..."></textarea>
            </div>

            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                    <input type="checkbox" name="create_user" value="1" checked class="w-4 h-4">
                    Buat akun login otomatis (Username: NIP, Pass: 123456)
                </label>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>

