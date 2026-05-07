<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6" x-data="{ showModal: false }">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Template Surat</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola template surat keterangan dan dokumen resmi pesantren.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-file-lines"></i> Total Template: <?= count($templates) ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <button @click="showModal = true"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2 w-fit">
            <i class="fa-solid fa-plus"></i> Tambah Template
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full whitespace-nowrap text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Template</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kode</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($templates)): ?>
                        <tr><td colspan="3" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Belum ada template surat.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($templates as $t): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                        <td class="px-5 py-4 font-extrabold text-slate-800"><?= htmlspecialchars($t['name']) ?></td>
                        <td class="px-5 py-4">
                            <span class="font-mono text-xs bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg border border-slate-200"><?= htmlspecialchars($t['code']) ?></span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="/settings/letters/edit?id=<?= $t['id'] ?>"
                                    class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                                <a href="/settings/letters/delete?id=<?= $t['id'] ?>"
                                    onclick="return confirm('Hapus template \'<?= htmlspecialchars($t['name']) ?>\'?')"
                                    class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Hapus">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Template -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div @click.outside="showModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h4 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-file-lines text-slate-400"></i> Tambah Template Surat</h4>
                <button @click="showModal = false" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="/settings/letters/store" method="POST" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Template</label>
                    <input type="text" name="name" required placeholder="cth: Surat Keterangan Aktif"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Kode Unik</label>
                    <input type="text" name="code" required placeholder="cth: keterangan_aktif"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    <p class="text-xs text-slate-400 mt-1">Huruf kecil, angka, dan underscore saja.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Konten Template</label>
                    <textarea name="content" rows="6" required placeholder="Gunakan placeholder: {nama}, {nis}, {kelas}, {tempat_lahir}, {tgl_lahir}, {alamat}"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"></textarea>
                    <p class="text-xs text-slate-400 mt-1">Placeholder: <code class="bg-slate-100 px-1 rounded">{nama}</code> <code class="bg-slate-100 px-1 rounded">{nis}</code> <code class="bg-slate-100 px-1 rounded">{kelas}</code> <code class="bg-slate-100 px-1 rounded">{tempat_lahir}</code> <code class="bg-slate-100 px-1 rounded">{tgl_lahir}</code> <code class="bg-slate-100 px-1 rounded">{alamat}</code></p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Template Surat</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Klik <strong class="text-slate-700">Tambah Template</strong> untuk membuat template surat baru.</li>
                    <li>Gunakan <strong class="text-slate-700">placeholder</strong> seperti <code class="bg-slate-100 px-1 rounded text-xs">{nama}</code>, <code class="bg-slate-100 px-1 rounded text-xs">{nis}</code>, dll. agar data santri otomatis terisi saat cetak.</li>
                    <li>Klik ikon <strong class="text-slate-700">pensil</strong> untuk mengedit konten template dengan preview langsung.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-graduation-cap text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Santri</div><div class="text-[11px] text-slate-400">Template dicetak dengan data santri dari <strong>Kesiswaan → Data Santri</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-user-graduate text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Portal Santri</div><div class="text-[11px] text-slate-400">Santri dapat mencetak surat dari menu <strong>Portal Santri → Surat Keterangan</strong>.</div></div>
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
        if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden');
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
