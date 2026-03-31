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

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h3 class="text-3xl font-medium text-gray-700">Struktur Organisasi</h3>
            <p class="text-sm text-gray-500">Diagram hierarki kepengurusan sekolah.</p>
        </div>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-sm flex items-center gap-2">
            <i class="fa fa-plus"></i> Tambah Node / Jabatan
        </button>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded shadow-sm border border-gray-200 p-8 overflow-x-auto min-h-[500px] flex justify-center items-start">
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
                            echo "<a href='/staff/structure/delete?id={$node['id']}' class='btn-del-node' onclick=\"return confirm('Hapus jabatan ini beserta bawahannya?')\"><i class='fa fa-times'></i></a>";

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
</main>

<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-lg shadow-xl">
        <h3 class="text-xl font-bold mb-4">Tambah Jabatan / Node</h3>
        <form action="/staff/structure/store" method="POST">
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
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Pejabat (Staff)</label>
                    <select name="staff_id" class="w-full p-2 border rounded text-sm">
                        <option value="">-- Pilih Staff --</option>
                        <?php foreach($staffs as $st): ?>
                            <option value="<?= $st['id'] ?>"><?= $st['full_name'] ?></option>
                        <?php endforeach; ?>
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

<?php require __DIR__ . '/../../layouts/footer.php'; ?>

