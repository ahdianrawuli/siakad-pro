<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .select2-container--default .select2-selection--single { border-color: #e5e7eb; height: 42px; padding-top: 6px; border-radius: 0.5rem; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
    .select2-container { width: 100% !important; }
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h3 class="text-3xl font-medium text-gray-700">Data Kelas</h3>
            <p class="text-gray-500 text-sm">Ditemukan <?= $totalData ?> data sesuai kriteria.</p>
        </div>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded shadow-sm h-fit border border-gray-200">
            <h4 class="font-bold mb-4 text-gray-700 border-b pb-2 flex items-center">
                <i class="fa-solid fa-plus-circle mr-2 text-blue-600"></i> Buat Kelas Baru
            </h4>
            <form action="/master/classrooms/store" method="POST">
                <?= \App\Core\Csrf::input() ?>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Jenjang</label>
                    <select name="major" class="select2 w-full" required>
                        <option value="MTS">MTS</option>
                        <option value="MA">MA</option>
                        <option value="PDF">PDF</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tingkat</label>
                    <input type="number" name="level" placeholder="7" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Kelas</label>
                    <input type="text" name="name" placeholder="7-A" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm" required>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Wali Kelas</label>
                    <select name="homeroom_teacher_id" class="select2 w-full">
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach($teachers as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg">
                    Simpan Data Kelas
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-4">
            
            <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="limit" value="<?= $limit ?>">
                    
                    <div class="flex-1 min-w-[200px]">
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                                   placeholder="Cari nama kelas..." 
                                   class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none border-gray-300">
                        </div>
                    </div>

                    <div class="w-full md:w-40">
                        <select name="major" class="w-full p-2 border rounded-lg text-sm border-gray-300 outline-none">
                            <option value="">Semua Jenjang</option>
                            <option value="MTS" <?= $selectedMajor == 'MTS' ? 'selected' : '' ?>>MTS</option>
                            <option value="MA" <?= $selectedMajor == 'MA' ? 'selected' : '' ?>>MA</option>
                            <option value="PDF" <?= $selectedMajor == 'PDF' ? 'selected' : '' ?>>PDF</option>
                        </select>
                    </div>

                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-black transition">
                        Filter
                    </button>
                    
                    <?php if(!empty($search) || !empty($selectedMajor)): ?>
                        <a href="/master/classrooms" class="text-red-500 text-xs font-bold underline">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h4 class="font-bold text-gray-700 text-xs uppercase">Daftar Kelas</h4>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-gray-500 font-bold uppercase">Show:</span>
                        <select onchange="window.location.href=updateQueryStringParameter(window.location.href, 'limit', this.value)" class="border rounded p-1 text-xs outline-none">
                            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                        </select>
                    </div>
                </div>
                
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="min-w-full whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-100 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-5 py-4 border-b">Unit</th>
                                <th class="px-5 py-4 border-b">Tingkat</th>
                                <th class="px-5 py-4 border-b">Nama Kelas</th>
                                <th class="px-5 py-4 border-b">Wali Kelas</th>
                                <th class="px-5 py-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($classrooms)): ?>
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-gray-400 italic">Data tidak ditemukan.</td>
                            </tr>
                            <?php endif; ?>

                            <?php foreach ($classrooms as $row): ?>
                            <tr class="hover:bg-blue-50/30 transition text-sm">
                                <td class="px-5 py-4">
                                    <span class="px-2 py-0.5 text-[10px] font-extrabold rounded border <?= $row['major'] == 'MTS' ? 'bg-blue-50 text-blue-700 border-blue-200' : ($row['major'] == 'MA' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-purple-50 text-purple-700 border-purple-200') ?>">
                                        <?= $row['major'] ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-bold text-gray-700"><?= $row['level'] ?></td>
                                <td class="px-5 py-4 font-bold text-blue-800"><?= $row['name'] ?></td>
                                <td class="px-5 py-4 text-gray-600">
                                    <?= $row['teacher_name'] ?? '<span class="text-red-300 text-xs italic">Belum ditentukan</span>' ?>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <a href="/master/classrooms/delete?id=<?= $row['id'] ?>" class="text-red-400 hover:text-red-600" onclick="return confirm('Hapus kelas?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if($totalPages > 1): ?>
                <div class="p-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-xs text-gray-500 font-medium">
                        Hal. <?= $currentPage ?> / <?= $totalPages ?>
                    </div>
                    <div class="flex gap-1">
                        <?php 
                        $queryString = "&limit=$limit&search=" . urlencode($search) . "&major=" . urlencode($selectedMajor);
                        ?>
                        
                        <?php if($currentPage > 1): ?>
                            <a href="?page=<?= $currentPage - 1 . $queryString ?>" class="px-3 py-1 bg-white border rounded text-xs hover:bg-gray-100 transition">Prev</a>
                        <?php endif; ?>

                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i . $queryString ?>" class="px-3 py-1 border rounded text-xs transition <?= $i == $currentPage ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'bg-white hover:bg-gray-100' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if($currentPage < $totalPages): ?>
                            <a href="?page=<?= $currentPage + 1 . $queryString ?>" class="px-3 py-1 bg-white border rounded text-xs hover:bg-gray-100 transition">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({ placeholder: "-- Pilih --", allowClear: true });
    });

    // Helper JS untuk ganti limit tanpa hapus search query
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    }
</script>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
