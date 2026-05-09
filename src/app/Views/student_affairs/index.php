<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Data Siswa</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola data induk siswa pesantren.</p>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-users"></i> <?= $totalData ?> Siswa
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition border border-slate-200" title="Panduan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="/student-affairs/students/export?class_id=<?= urlencode($classId) ?>&status=<?= urlencode($status) ?>"
                class="px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-emerald-500/20 hover:bg-emerald-700 transition flex items-center gap-2">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </a>
            <button onclick="document.getElementById('importModal').classList.remove('hidden')"
                class="px-4 py-2.5 bg-amber-500 text-white rounded-xl text-sm font-semibold shadow-md shadow-amber-500/20 hover:bg-amber-600 transition flex items-center gap-2">
                <i class="fa-solid fa-file-import"></i> Import
            </button>
            <button onclick="document.getElementById('addModal').classList.remove('hidden')"
                class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i> Tambah Siswa
            </button>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Filter -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="hidden" name="limit" value="<?= $limit ?>">
            <div class="flex-1 min-w-[200px] relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari Nama, NIS, atau Kelas..."
                    class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
            </div>
            <select name="class_id" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none select2-class">
                <option value="">Semua Kelas</option>
                <?php foreach ($classrooms as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $classId == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="status" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                <option value="ACTIVE"    <?= $status === 'ACTIVE'    ? 'selected' : '' ?>>Aktif</option>
                <option value="GRADUATED" <?= $status === 'GRADUATED' ? 'selected' : '' ?>>Lulus</option>
                <option value="MOVED"     <?= $status === 'MOVED'     ? 'selected' : '' ?>>Pindah</option>
                <option value="DROPPED"   <?= $status === 'DROPPED'   ? 'selected' : '' ?>>DO</option>
                <option value="ALL"       <?= $status === 'ALL'       ? 'selected' : '' ?>>Semua Status</option>
            </select>
            <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition">Terapkan</button>
            <?php if (!empty($search) || !empty($classId) || $status !== 'ACTIVE'): ?>
                <a href="/student-affairs/students" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
        <div class="overflow-x-auto">
            <table class="min-w-full whitespace-nowrap text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Siswa</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">TTL</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Orang Tua</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($students)): ?>
                    <tr><td colspan="6" class="px-5 py-16 text-center text-slate-400 text-sm">Data tidak ditemukan.</td></tr>
                    <?php endif; ?>
                    <?php
                    $statusBadge = [
                        'ACTIVE'    => 'bg-green-50 text-green-700 border-green-200',
                        'GRADUATED' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'MOVED'     => 'bg-amber-50 text-amber-700 border-amber-200',
                        'DROPPED'   => 'bg-red-50 text-red-700 border-red-200',
                    ];
                    ?>
                    <?php foreach ($students as $row): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                        <td class="px-5 py-4">
                            <a href="/student-affairs/students/detail?id=<?= $row['id'] ?>" class="font-extrabold text-slate-800 hover:text-blue-600 transition"><?= htmlspecialchars($row['full_name']) ?></a>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="font-mono text-[10px] text-slate-400"><?= $row['nis'] ?></span>
                                <?php if ($row['nisn']): ?><span class="font-mono text-[10px] text-slate-400">NISN: <?= $row['nisn'] ?></span><?php endif; ?>
                                <span class="text-[10px] font-semibold <?= $row['gender']==='L' ? 'text-blue-500' : 'text-pink-500' ?>"><i class="fa-solid <?= $row['gender']==='L' ? 'fa-mars' : 'fa-venus' ?>"></i></span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <?php if ($row['class_name']): ?>
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-lg border border-blue-200"><?= $row['class_name'] ?></span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 bg-red-50 text-red-600 text-[10px] font-bold rounded-lg border border-red-200">No Class</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-500">
                            <?= $row['birth_place'] ? htmlspecialchars($row['birth_place']) : '-' ?>
                            <?php if ($row['birth_date']): ?><div class="text-[10px] text-slate-400"><?= date('d M Y', strtotime($row['birth_date'])) ?></div><?php endif; ?>
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-600">
                            <?= htmlspecialchars($row['father_name'] ?? $row['parent_name'] ?? '-') ?>
                            <?php $phone = $row['father_phone'] ?? $row['parent_phone'] ?? null; ?>
                            <?php if ($phone): ?><div class="text-[10px] text-slate-400"><?= $phone ?></div><?php endif; ?>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border <?= $statusBadge[$row['status']] ?? 'bg-slate-100 text-slate-500 border-slate-200' ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="/student-affairs/students/detail?id=<?= $row['id'] ?>"
                                    class="w-8 h-8 rounded-lg bg-slate-50 text-slate-500 hover:bg-blue-600 hover:text-white inline-flex items-center justify-center transition shadow-sm border border-slate-200" title="Lihat Detail">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                                <button onclick='openEditModal(<?= json_encode($row) ?>)'
                                    class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white inline-flex items-center justify-center transition shadow-sm" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <a href="/student-affairs/students/print?id=<?= $row['id'] ?>" target="_blank"
                                    class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white inline-flex items-center justify-center transition shadow-sm" title="Cetak Biodata">
                                    <i class="fa-solid fa-print text-xs"></i>
                                </a>
                                <a href="/student-affairs/students/delete?id=<?= $row['id'] ?>"
                                    onclick="return confirm('Hapus data siswa ini? Data nilai dan tagihan juga akan terhapus!')"
                                    class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white inline-flex items-center justify-center transition shadow-sm" title="Hapus">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Show:</span>
                <select onchange="window.location.href=updateQS(window.location.href,'limit',this.value)"
                    class="border border-slate-300 rounded-lg px-2 py-1 text-sm outline-none bg-white font-medium">
                    <option value="10"  <?= $limit==10  ? 'selected':'' ?>>10</option>
                    <option value="50"  <?= $limit==50  ? 'selected':'' ?>>50</option>
                    <option value="100" <?= $limit==100 ? 'selected':'' ?>>100</option>
                </select>
            </div>
            <?php if ($totalPages > 1): ?>
            <div class="flex items-center gap-1.5">
                <?php $qs = "&limit=$limit&search=".urlencode($search)."&class_id=$classId&status=$status"; ?>
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage-1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm"><i class="fa-solid fa-chevron-left"></i></a>
                <?php endif; ?>
                <span class="text-xs font-bold text-slate-600 px-2">Hal <?= $currentPage ?> / <?= $totalPages ?></span>
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage+1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm"><i class="fa-solid fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
// Helper untuk field input
function inp($name, $label, $val='', $type='text', $required=false) {
    $r = $required ? 'required' : '';
    $v = htmlspecialchars($val ?? '');
    echo "<div><label class='block text-xs font-semibold text-slate-600 mb-1'>$label</label>
    <input type='$type' name='$name' value='$v' $r
        class='w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none'></div>";
}
function sel($name, $label, $options, $selected='') {
    echo "<div><label class='block text-xs font-semibold text-slate-600 mb-1'>$label</label><select name='$name' class='w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none'>";
    foreach ($options as $v => $l) echo "<option value='$v'" . ($selected==$v?' selected':'') . ">$l</option>";
    echo "</select></div>";
}
$classOpts = ['' => '-- Pilih Kelas --'];
foreach ($classrooms as $c) $classOpts[$c['id']] = $c['name'];
$dormOpts = ['' => '-- Pilih Asrama --'];
foreach ($dorms as $d) $dormOpts[$d['id']] = $d['name'];
$genderOpts = ['L'=>'Laki-laki','P'=>'Perempuan'];
$statusOpts = ['ACTIVE'=>'Aktif','GRADUATED'=>'Lulus','MOVED'=>'Pindah','DROPPED'=>'DO'];
?>

<!-- Modal Tambah -->
<div id="addModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center shrink-0">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-user-plus text-blue-500"></i> Tambah Siswa Baru</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/student-affairs/students/store" method="POST" class="p-6 overflow-y-auto space-y-5">
            <?= \App\Core\Csrf::input() ?>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 border-b border-slate-100 pb-2">Data Pribadi</p>
                <div class="grid grid-cols-2 gap-3">
                    <?php inp('nis','NIS','','text',true); inp('nisn','NISN'); ?>
                    <?php inp('full_name','Nama Lengkap','','text',true); ?>
                    <?php sel('gender','Jenis Kelamin',$genderOpts); ?>
                    <?php inp('birth_place','Tempat Lahir'); inp('birth_date','Tanggal Lahir','','date'); ?>
                    <?php sel('classroom_id','Kelas',$classOpts); sel('dorm_id','Asrama',$dormOpts); ?>
                </div>
                <div class="mt-3"><label class="block text-xs font-semibold text-slate-600 mb-1">Alamat</label>
                <textarea name="address" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none resize-none"></textarea></div>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 border-b border-slate-100 pb-2">Data Orang Tua</p>
                <div class="grid grid-cols-3 gap-3">
                    <?php inp('father_name','Nama Ayah'); inp('father_job','Pekerjaan Ayah'); inp('father_phone','HP Ayah'); ?>
                    <?php inp('mother_name','Nama Ibu'); inp('mother_job','Pekerjaan Ibu'); inp('mother_phone','HP Ibu'); ?>
                    <?php inp('guardian_name','Nama Wali'); inp('guardian_relation','Hub. Wali'); inp('guardian_phone','HP Wali'); ?>
                </div>
            </div>
            <div class="flex gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center shrink-0">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-pen-to-square text-blue-500"></i> Edit Data Siswa</h3>
            <button onclick="document.getElementById('editModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/student-affairs/students/update" method="POST" class="p-6 overflow-y-auto space-y-5" id="editForm">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="e_id">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 border-b border-slate-100 pb-2">Data Pribadi</p>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">NIS</label><input type="text" name="nis" id="e_nis" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">NISN</label><input type="text" name="nisn" id="e_nisn" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
                    <div class="col-span-2"><label class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap</label><input type="text" name="full_name" id="e_full_name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Jenis Kelamin</label><select name="gender" id="e_gender" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Status</label><select name="status" id="e_status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"><option value="ACTIVE">Aktif</option><option value="GRADUATED">Lulus</option><option value="MOVED">Pindah</option><option value="DROPPED">DO</option></select></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Tempat Lahir</label><input type="text" name="birth_place" id="e_birth_place" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Lahir</label><input type="date" name="birth_date" id="e_birth_date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Kelas</label><select name="classroom_id" id="e_classroom_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"><option value="">-- Pilih Kelas --</option><?php foreach ($classrooms as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?></select></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Asrama</label><select name="dorm_id" id="e_dorm_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"><option value="">-- Pilih Asrama --</option><?php foreach ($dorms as $d): ?><option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option><?php endforeach; ?></select></div>
                </div>
                <div class="mt-3"><label class="block text-xs font-semibold text-slate-600 mb-1">Alamat</label><textarea name="address" id="e_address" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none resize-none"></textarea></div>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 border-b border-slate-100 pb-2">Data Orang Tua</p>
                <div class="grid grid-cols-3 gap-3">
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Nama Ayah</label><input type="text" name="father_name" id="e_father_name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Pekerjaan Ayah</label><input type="text" name="father_job" id="e_father_job" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">HP Ayah</label><input type="text" name="father_phone" id="e_father_phone" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Nama Ibu</label><input type="text" name="mother_name" id="e_mother_name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Pekerjaan Ibu</label><input type="text" name="mother_job" id="e_mother_job" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">HP Ibu</label><input type="text" name="mother_phone" id="e_mother_phone" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Nama Wali</label><input type="text" name="guardian_name" id="e_guardian_name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Hub. Wali</label><input type="text" name="guardian_relation" id="e_guardian_relation" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">HP Wali</label><input type="text" name="guardian_phone" id="e_guardian_phone" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none"></div>
                </div>
                <div class="mt-3"><label class="block text-xs font-semibold text-slate-600 mb-1">Alamat Wali</label><textarea name="guardian_address" id="e_guardian_address" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none resize-none"></textarea></div>
            </div>
            <div class="flex gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Update Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import -->
<div id="importModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-amber-50 flex justify-between items-center">
            <h3 class="font-bold text-amber-800 flex items-center gap-2"><i class="fa-solid fa-file-import text-amber-600"></i> Import Data Siswa</h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 hover:bg-amber-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/student-affairs/students/import" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <div class="bg-blue-50 p-3 rounded-xl border border-blue-100 text-xs text-blue-700 space-y-1">
                <p class="font-bold">Format CSV (kolom berurutan):</p>
                <p class="font-mono">NIS, NISN, Nama Lengkap, L/P, Tempat Lahir, Tgl Lahir (YYYY-MM-DD), Alamat</p>
                <p class="text-blue-500">Baris pertama = header (akan dilewati otomatis)</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Pilih File CSV</label>
                <input type="file" name="import_file" accept=".csv" required
                    class="w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 transition">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-amber-500 text-white py-2.5 rounded-xl font-bold hover:bg-amber-600 shadow-md shadow-amber-500/20 transition text-sm">Import</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Data Siswa</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-4 text-sm text-slate-600 max-h-[70vh] overflow-y-auto">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Filter & Pencarian</h4>
                <ul class="list-disc list-inside space-y-1 text-slate-500 text-xs">
                    <li>Filter berdasarkan <strong class="text-slate-700">Kelas</strong>, <strong class="text-slate-700">Status</strong>, atau cari nama/NIS.</li>
                    <li>Status: <strong>Aktif</strong> (default), Lulus, Pindah, DO, atau Semua.</li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Form Edit Lengkap</h4>
                <p class="text-slate-500 text-xs">Klik ikon <i class="fa-solid fa-pen-to-square text-blue-600"></i> untuk edit semua data: NIS, NISN, TTL, alamat, kelas, asrama, status, dan data lengkap ayah/ibu/wali.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Import & Export</h4>
                <ul class="list-disc list-inside space-y-1 text-slate-500 text-xs">
                    <li><strong class="text-slate-700">Export Excel</strong> — Unduh data sesuai filter aktif.</li>
                    <li><strong class="text-slate-700">Import CSV</strong> — Upload file CSV untuk tambah data massal. NIS duplikat dilewati otomatis.</li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">4</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-calendar-check text-green-400 w-4 text-center"></i>
                        <div class="text-xs"><strong class="text-slate-700">Absensi</strong> — Kesiswaan → Absensi Santri</div>
                    </div>
                    <div class="flex items-center gap-3 p-2 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-receipt text-blue-400 w-4 text-center"></i>
                        <div class="text-xs"><strong class="text-slate-700">Tagihan</strong> — Keuangan → Kasir (klik nama siswa di halaman detail)</div>
                    </div>
                    <div class="flex items-center gap-3 p-2 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-building text-purple-400 w-4 text-center"></i>
                        <div class="text-xs"><strong class="text-slate-700">Asrama</strong> — Kepesantrenan → Data Asrama</div>
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
$(document).ready(function() {
    $('.select2-class').select2({ placeholder: 'Semua Kelas', allowClear: true, width: '200px' });
});
function openEditModal(row) {
    const fields = ['id','nis','nisn','full_name','gender','status','birth_place','birth_date','address',
        'classroom_id','dorm_id','father_name','father_job','father_phone',
        'mother_name','mother_job','mother_phone','guardian_name','guardian_relation','guardian_phone','guardian_address'];
    fields.forEach(f => {
        const el = document.getElementById('e_' + f);
        if (el) el.value = row[f] ?? '';
    });
    document.getElementById('editModal').classList.remove('hidden');
}
function updateQS(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var sep = uri.indexOf('?') !== -1 ? "&" : "?";
    return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + sep + key + "=" + value;
}
window.onclick = function(e) {
    ['addModal','editModal','importModal','infoModal'].forEach(id => {
        if (e.target == document.getElementById(id)) document.getElementById(id).classList.add('hidden');
    });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
