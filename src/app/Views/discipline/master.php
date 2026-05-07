<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 md:p-6" x-data="{ addModal: false }">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Master Pelanggaran</h1>
            <p class="text-sm text-gray-500">Kelola daftar aturan dan sanksi poin pelanggaran santri.</p>
        </div>
        <div class="flex gap-2">
            <button @click="addModal = true" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Master Aturan
            </button>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-gray-800">Daftar Aturan Disiplin</h2>
            <form method="GET" class="w-full md:w-auto relative">
                <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari aturan..." class="w-full md:w-64 pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400"></i>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="p-4 font-bold border-b border-gray-100">Kode</th>
                        <th class="p-4 font-bold border-b border-gray-100">Nama Pelanggaran</th>
                        <th class="p-4 font-bold border-b border-gray-100 text-center">Tingkat Sanksi</th>
                        <th class="p-4 font-bold border-b border-gray-100 text-center">Poin</th>
                        <th class="p-4 font-bold border-b border-gray-100 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                    <?php if (!empty($violations)): foreach ($violations as $v): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-bold text-gray-800"><?= htmlspecialchars($v['code']) ?></td>
                        <td class="p-4 font-medium"><?= htmlspecialchars($v['name']) ?></td>
                        <td class="p-4 text-center">
                            <?php
                                $bg = $v['severity'] == 'BERAT' ? 'bg-red-100 text-red-700' : ($v['severity'] == 'SEDANG' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700');
                            ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $bg ?>"><?= $v['severity'] ?></span>
                        </td>
                        <td class="p-4 text-center font-bold text-red-600">-<?= $v['points'] ?></td>
                        <td class="p-4 text-center">
                            <button class="text-blue-500 hover:text-blue-700 p-1"><i class="fa-solid fa-pen-to-square"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="5" class="p-6 text-center text-gray-500">Data Master Pelanggaran belum tersedia.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div x-cloak x-show="addModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div @click.away="addModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Tambah Aturan Baru</h3>
                <button @click="addModal = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="/discipline/master-violations/store" method="POST" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Aturan</label>
                        <input type="text" name="code" required placeholder="Contoh: PL-001" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggaran</label>
                        <input type="text" name="name" required placeholder="Deskripsi pelanggaran..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat</label>
                            <select name="severity" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                                <option value="RINGAN">RINGAN</option>
                                <option value="SEDANG">SEDANG</option>
                                <option value="BERAT">BERAT</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pengurangan Poin</label>
                            <input type="number" name="points" required placeholder="10" min="1" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="addModal = false" class="px-4 py-2 text-gray-600 font-medium hover:bg-gray-100 rounded-lg transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition">Simpan Aturan</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>