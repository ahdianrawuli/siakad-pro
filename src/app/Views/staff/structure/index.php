<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<style>
/* CSS ORG CHART SEDERHANA */
.tree ul {
	padding-top: 20px; position: relative;
	transition: all 0.5s;
    display: flex; justify-content: center;
}
.tree li {
	float: left; text-align: center;
	list-style-type: none;
	position: relative;
	padding: 20px 5px 0 5px;
	transition: all 0.5s;
}
/* Garis Konektor */
.tree li::before, .tree li::after{
	content: ''; position: absolute; top: 0; right: 50%;
	border-top: 2px solid #ccc; width: 50%; height: 20px;
}
.tree li::after{
	right: auto; left: 50%; border-left: 2px solid #ccc;
}
.tree li:only-child::after, .tree li:only-child::before {
	display: none;
}
.tree li:only-child{ padding-top: 0;}
.tree li:first-child::before, .tree li:last-child::after{
	border: 0 none;
}
.tree li:last-child::before{
	border-right: 2px solid #ccc; border-radius: 0 5px 0 0;
}
.tree li:first-child::after{
	border-radius: 5px 0 0 0;
}
.tree ul ul::before{
	content: ''; position: absolute; top: 0; left: 50%;
	border-left: 2px solid #ccc; width: 0; height: 20px;
}

/* Kotak Node */
.tree-node {
	border: 1px solid #ddd; padding: 10px; 
	text-decoration: none; color: #666; 
	font-family: arial, verdana, tahoma; 
	font-size: 11px; display: inline-block;
	border-radius: 5px; transition: all 0.5s;
    background: white; min-width: 150px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    position: relative;
}
.tree-node:hover {
    background: #f0f9ff; border-color: #3b82f6;
    transform: scale(1.05); z-index: 10;
}
.node-title { font-weight: bold; font-size: 13px; color: #1e293b; display: block; margin-bottom: 4px; }
.node-staff { color: #2563eb; font-weight: 600; font-size: 12px; display: block; }
.node-empty { color: #ef4444; font-style: italic; font-size: 11px; }

/* Tombol Delete Kecil */
.btn-del-node {
    position: absolute; top: -8px; right: -8px;
    background: #ef4444; color: white; border-radius: 50%;
    width: 20px; height: 20px; font-size: 10px;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity 0.2s; cursor: pointer;
}
.tree-node:hover .btn-del-node { opacity: 1; }
</style>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Struktur Organisasi</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Diagram hierarki kepengurusan Pesantren Thawalib Parabek.</p>
            <div class="mt-3 flex items-center gap-2">
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2 w-fit">
            <i class="fa-solid fa-plus"></i> Tambah Node / Jabatan
        </button>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded shadow-sm border border-gray-200 p-8 min-h-[500px]" style="overflow-x:auto; overflow-y:auto;">
        <div style="min-width: max-content; display: flex; justify-content: center; padding: 1rem;">
        <?php if(empty($tree)): ?>
            <div class="text-center py-20">
                <div class="text-gray-300 text-6xl mb-4"><i class="fa fa-sitemap"></i></div>
                <p class="text-gray-500">Belum ada struktur organisasi.</p>
                <p class="text-sm text-gray-400">Klik tombol "Tambah Node" untuk memulai dari Kepala Sekolah.</p>
            </div>
        <?php else: ?>
            <div class="tree">
                <ul>
                    <?php 
                    // FUNGSI REKURSIF UNTUK RENDER TREE
                    function renderTree($nodes) {
                        foreach ($nodes as $node) {
                            echo "<li>";
                            echo "<div class='tree-node'>";
                            
                            // Tombol Hapus (Muncul saat hover)
                            echo "<a href='/school/structure/delete?id={$node['id']}' class='btn-del-node' onclick=\"return confirm('Hapus jabatan ini beserta bawahannya?')\"><i class='fa fa-times'></i></a>";

                            // Isi Node
                            echo "<span class='node-title'>{$node['title']}</span>";
                            if (!empty($node['full_name'])) {
                                echo "<span class='node-staff'><i class='fa fa-user-circle mr-1'></i> {$node['full_name']}</span>";
                            } else {
                                echo "<span class='node-empty'>-- Kosong --</span>";
                            }
                            
                            echo "</div>";

                            // Render Anak (Jika ada)
                            if (!empty($node['children'])) {
                                echo "<ul>";
                                renderTree($node['children']);
                                echo "</ul>";
                            }
                            echo "</li>";
                        }
                    }

                    // PANGGIL FUNGSI PERTAMA KALI
                    renderTree($tree); 
                    ?>
                </ul>
            </div>
        <?php endif; ?>
        </div>
    </div>
</main>

<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-lg shadow-xl">
        <h3 class="text-xl font-bold mb-4">Tambah Jabatan / Node</h3>
        <form action="/school/structure/store" method="POST">
            <?= \App\Core\Csrf::input() ?>
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Jabatan (Di Struktur)</label>
                <input type="text" name="title" class="w-full p-2 border rounded" placeholder="Contoh: Kepala Sekolah / Wakasek" required>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Induk (Atasan Langsung)</label>
                    <select name="parent_id" class="w-full p-2 border rounded text-sm bg-gray-50">
                        <option value="">-- Tidak Ada (Paling Atas) --</option>
                        <?php foreach($flat as $f): ?>
                            <option value="<?= $f['id'] ?>">
                                <?= str_repeat("— ", ($f['level'] - 1)) . $f['title'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">* Kosongkan jika ini adalah posisi tertinggi.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Pejabat (Staff / Guru)</label>
                    <select name="staff_id" class="w-full p-2 border rounded text-sm">
                        <option value="">-- Pilih --</option>
                        <optgroup label="── Staff Kepegawaian ──">
                        <?php foreach($staffs as $st): if($st['sumber'] !== 'staff') continue; ?>
                            <option value="<?= $st['id'] ?>"><?= htmlspecialchars($st['full_name']) ?></option>
                        <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="── Guru / Pengajar ──">
                        <?php foreach($staffs as $st): if($st['sumber'] !== 'guru') continue; ?>
                            <option value="<?= $st['id'] ?>"><?= htmlspecialchars($st['full_name']) ?></option>
                        <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Urutan Tampil (Kiri ke Kanan)</label>
                <input type="number" name="order_num" value="1" class="w-20 p-2 border rounded text-center">
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold">Simpan Struktur</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Struktur Organisasi</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Klik <strong class="text-slate-700">Tambah Node</strong> untuk menambahkan jabatan baru ke bagan.</li>
                    <li>Pilih <strong class="text-slate-700">Induk (Atasan)</strong> untuk menentukan posisi hierarki.</li>
                    <li>Pilih <strong class="text-slate-700">Pejabat</strong> dari daftar staff atau guru yang tersedia.</li>
                    <li>Arahkan kursor ke node untuk menampilkan tombol <strong class="text-slate-700">hapus</strong>.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-users text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Staff</div><div class="text-[11px] text-slate-400">Pejabat diambil dari <strong>Kepegawaian → Data Staff</strong> dan daftar guru.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-id-card text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Master Jabatan</div><div class="text-[11px] text-slate-400">Jabatan dikelola di <strong>Kepegawaian → Master Jabatan</strong>.</div></div>
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
    window.onclick = function(e) {
        ['infoModal','addModal'].forEach(function(id) {
            if (e.target == document.getElementById(id)) document.getElementById(id).classList.add('hidden');
        });
    }
</script>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>

