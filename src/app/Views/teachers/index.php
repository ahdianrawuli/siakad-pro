<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h3 class="text-3xl font-medium text-gray-700">Manajemen Guru</h3>
            <p class="text-gray-500 text-sm">Ditemukan <?= $totalData ?> data guru aktif.</p>
        </div>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded shadow-sm h-fit border border-gray-200">
            <h4 class="font-bold mb-4 text-gray-700 border-b pb-2 flex items-center">
                <i class="fa-solid fa-user-plus mr-2 text-blue-600"></i> Tambah Guru Baru
            </h4>
            <form action="/student-affairs/teachers/store" method="POST">
                <?= \App\Core\Csrf::input() ?>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Informasi Personal</label>
                        <div class="mt-2 space-y-3">
                            <input type="text" name="nip" placeholder="NIP (Opsional)" class="w-full p-2 border border-gray-300 rounded text-sm">
                            <input type="text" name="full_name" placeholder="Nama Lengkap" class="w-full p-2 border border-gray-300 rounded text-sm" required>
                            <div class="flex gap-2">
                                <select name="gender" class="flex-1 p-2 border border-gray-300 rounded text-sm bg-white" required>
                                    <option value="">Gender</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                <select name="education" class="flex-1 p-2 border border-gray-300 rounded text-sm bg-white" required>
                                    <option value="">Pendidikan</option>
                                    <option value="SMA">SMA</option>
                                    <option value="D3">D3</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Kredensial Akun</label>
                        <div class="mt-2 space-y-3">
                            <input type="text" name="username" placeholder="Username Login" class="w-full p-2 border border-gray-300 rounded text-sm" required>
                            <input type="password" name="password" placeholder="Password" class="w-full p-2 border border-gray-300 rounded text-sm" required>
                            <input type="email" name="email" placeholder="Email (Opsional)" class="w-full p-2 border border-gray-300 rounded text-sm">
                        </div>
                    </div>

                    <div class="pt-2">
                        <input type="text" name="phone" placeholder="Nomor Telepon/WA" class="w-full p-2 border border-gray-300 rounded text-sm mb-3">
                        <textarea name="address" placeholder="Alamat Lengkap" class="w-full p-2 border border-gray-300 rounded text-sm h-20"></textarea>
                    </div>
                </div>

                <button type="submit" class="w-full mt-6 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition">
                    Simpan Data & Akun
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-4">
            
            <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
                <form method="GET" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" name="limit" value="<?= $limit ?>">
                    
                    <div class="flex-1 min-w-[150px]">
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                               placeholder="Cari NIP atau Nama..." 
                               class="w-full px-3 py-2 border rounded text-sm focus:ring-2 focus:ring-blue-500 outline-none border-gray-300">
                    </div>

                    <select name="gender" class="px-2 py-2 border rounded text-sm border-gray-300 outline-none">
                        <option value="">Semua Gender</option>
                        <option value="L" <?= $selectedGender == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= $selectedGender == 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>

                    <select name="status" class="px-2 py-2 border rounded text-sm border-gray-300 outline-none">
                        <option value="">Semua Status</option>
                        <option value="ACTIVE" <?= $selectedStatus == 'ACTIVE' ? 'selected' : '' ?>>Aktif</option>
                        <option value="INACTIVE" <?= $selectedStatus == 'INACTIVE' ? 'selected' : '' ?>>Non-Aktif</option>
                    </select>

                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm font-bold hover:bg-black transition">
                        Filter
                    </button>
                    
                    <?php if(!empty($search) || !empty($selectedGender) || !empty($selectedStatus)): ?>
                        <a href="/student-affairs/teachers" class="text-red-500 text-[10px] font-bold underline ml-1">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h4 class="font-bold text-gray-700 text-xs uppercase tracking-wider">Daftar Tenaga Pendidik</h4>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-gray-500 font-bold">Show:</span>
                        <select onchange="window.location.href=updateQueryStringParameter(window.location.href, 'limit', this.value)" class="border rounded p-1 text-xs outline-none">
                            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        </select>
                    </div>
                </div>
                
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="min-w-full whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-100 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-5 py-4 border-b">Guru</th>
                                <th class="px-5 py-4 border-b">Kontak</th>
                                <th class="px-5 py-4 border-b text-center">Pendidikan</th>
                                <th class="px-5 py-4 border-b text-center">Status</th>
                                <th class="px-5 py-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($teachers)): ?>
                            <tr><td colspan="5" class="px-5 py-12 text-center text-gray-400 italic text-sm">Belum ada data guru.</td></tr>
                            <?php endif; ?>

                            <?php foreach ($teachers as $row): ?>
                            <tr class="hover:bg-blue-50/30 transition text-sm">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-blue-900"><?= $row['full_name'] ?></div>
                                    <div class="text-[10px] text-gray-400 font-mono"><?= $row['nip'] ?: 'NIP: -' ?> • <?= $row['gender'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-xs font-medium text-gray-600"><?= $row['phone'] ?></div>
                                    <div class="text-[10px] text-gray-400"><?= $row['email'] ?></div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-bold rounded border border-gray-200"><?= $row['education'] ?></span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <a href="/student-affairs/teachers/toggle?id=<?= $row['id'] ?>" class="text-[10px] font-bold px-2.5 py-1 rounded-full border <?= $row['status'] == 'ACTIVE' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' ?>">
                                        <?= $row['status'] ?>
                                    </a>
                                </td>
                                <td class="px-5 py-4 text-center space-x-2">
                                    <button onclick="openEditModal(this)" 
                                            data-id="<?= $row['id'] ?>" 
                                            data-nip="<?= $row['nip'] ?>" 
                                            data-name="<?= $row['full_name'] ?>" 
                                            data-gender="<?= $row['gender'] ?>" 
                                            data-edu="<?= $row['education'] ?>" 
                                            data-phone="<?= $row['phone'] ?>" 
                                            data-email="<?= $row['email'] ?>" 
                                            data-address="<?= $row['address'] ?>" 
                                            data-status="<?= $row['status'] ?>"
                                            class="text-blue-500 hover:text-blue-700 transition">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a href="/student-affairs/teachers/detail?id=<?= $row['id'] ?>" class="text-green-500 hover:text-green-700 transition">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if($totalPages > 1): ?>
                <div class="p-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-[11px] text-gray-500">Hal. <?= $currentPage ?> / <?= $totalPages ?></div>
                    <div class="flex gap-1">
                        <?php $qs = "&limit=$limit&search=".urlencode($search)."&gender=$selectedGender&status=$selectedStatus"; ?>
                        <?php if($currentPage > 1): ?>
                            <a href="?page=<?= $currentPage - 1 . $qs ?>" class="px-2 py-1 bg-white border rounded text-[10px] hover:bg-gray-100">Prev</a>
                        <?php endif; ?>
                        <?php for($i=1; $i<=$totalPages; $i++): ?>
                            <a href="?page=<?= $i . $qs ?>" class="px-2 py-1 border rounded text-[10px] <?= $i == $currentPage ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'bg-white hover:bg-gray-100' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if($currentPage < $totalPages): ?>
                            <a href="?page=<?= $currentPage + 1 . $qs ?>" class="px-2 py-1 bg-white border rounded text-[10px] hover:bg-gray-100">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<div id="editModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate__animated animate__zoomIn animate__faster">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-700">Edit Profil Guru</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/student-affairs/teachers/update" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="edit_id">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase">NIP</label>
                    <input type="text" name="nip" id="edit_nip" class="w-full mt-1 p-2 border border-gray-300 rounded text-sm bg-gray-50">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase">Status</label>
                    <select name="status" id="edit_status" class="w-full mt-1 p-2 border border-gray-300 rounded text-sm bg-white">
                        <option value="ACTIVE">ACTIVE</option>
                        <option value="INACTIVE">INACTIVE</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase">Nama Lengkap</label>
                <input type="text" name="full_name" id="edit_name" class="w-full mt-1 p-2 border border-gray-300 rounded text-sm" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase">Gender</label>
                    <select name="gender" id="edit_gender" class="w-full mt-1 p-2 border border-gray-300 rounded text-sm bg-white">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase">Pendidikan Terakhir</label>
                    <select name="education" id="edit_edu" class="w-full mt-1 p-2 border border-gray-300 rounded text-sm bg-white">
                        <option value="SMA">SMA</option>
                        <option value="D3">D3</option>
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase">WhatsApp / Telp</label>
                    <input type="text" name="phone" id="edit_phone" class="w-full mt-1 p-2 border border-gray-300 rounded text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase">Email</label>
                    <input type="email" name="email" id="edit_email" class="w-full mt-1 p-2 border border-gray-300 rounded text-sm">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase">Alamat</label>
                <textarea name="address" id="edit_address" class="w-full mt-1 p-2 border border-gray-300 rounded text-sm h-20"></textarea>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-lg font-bold hover:bg-gray-200">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg">Perbarui Profil</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(btn) {
        document.getElementById('edit_id').value = btn.getAttribute('data-id');
        document.getElementById('edit_nip').value = btn.getAttribute('data-nip');
        document.getElementById('edit_name').value = btn.getAttribute('data-name');
        document.getElementById('edit_gender').value = btn.getAttribute('data-gender');
        document.getElementById('edit_edu').value = btn.getAttribute('data-edu');
        document.getElementById('edit_phone').value = btn.getAttribute('data-phone');
        document.getElementById('edit_email').value = btn.getAttribute('data-email');
        document.getElementById('edit_address').value = btn.getAttribute('data-address');
        document.getElementById('edit_status').value = btn.getAttribute('data-status');
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    window.onclick = function(e) { if (e.target == document.getElementById('editModal')) closeEditModal(); }

    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + separator + key + "=" + value;
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
