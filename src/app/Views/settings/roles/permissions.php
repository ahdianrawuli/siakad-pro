<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-4">
            <a href="/settings/roles" class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition shrink-0">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Edit Permissions</h3>
                <p class="text-slate-500 text-sm mt-1 font-medium">Atur menu yang dapat diakses oleh role: <span class="font-bold text-blue-600"><?= htmlspecialchars($role['name']) ?></span></p>
            </div>
        </div>
        <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
            class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200 self-start md:self-auto" title="Panduan Penggunaan">
            <i class="fa-solid fa-circle-info text-sm"></i>
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="/settings/roles/permissions/update" method="POST">
            <input type="hidden" name="role_id" value="<?= $role['id'] ?>">

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php
                $mainMenus = array_filter($menus, fn($m) => $m['parent_id'] === null);
                foreach ($mainMenus as $main):
                    $subMenus = array_filter($menus, fn($m) => $m['parent_id'] === $main['id']);
                ?>
                <div class="border border-slate-200 rounded-2xl overflow-hidden">
                    <label class="flex items-center gap-3 cursor-pointer px-4 py-3 bg-slate-50 border-b border-slate-200 hover:bg-slate-100 transition">
                        <input type="checkbox" name="menus[]" value="<?= $main['id'] ?>"
                            class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500"
                            <?= in_array($main['id'], $assignedMenus) ? 'checked' : '' ?>>
                        <span class="font-bold text-slate-700 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-<?= htmlspecialchars($main['icon']) ?> text-blue-400 w-4 text-center"></i>
                            <?= htmlspecialchars($main['title']) ?>
                        </span>
                    </label>
                    <?php if (!empty($subMenus)): ?>
                    <div class="px-4 py-3 space-y-2">
                        <?php foreach ($subMenus as $sub): ?>
                        <label class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 px-2 py-1.5 rounded-xl transition">
                            <input type="checkbox" name="menus[]" value="<?= $sub['id'] ?>"
                                class="w-4 h-4 text-blue-500 rounded border-slate-300 focus:ring-blue-500"
                                <?= in_array($sub['id'], $assignedMenus) ? 'checked' : '' ?>>
                            <span class="text-sm text-slate-600"><?= htmlspecialchars($sub['title']) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Hak Akses
                </button>
            </div>
        </form>
    </div>
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Edit Permissions</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Centang <strong class="text-slate-700">menu induk</strong> untuk memberikan akses ke grup menu tersebut.</li>
                    <li>Centang <strong class="text-slate-700">sub-menu</strong> di bawahnya untuk akses ke halaman spesifik.</li>
                    <li>Klik <strong class="text-slate-700">Simpan Hak Akses</strong> untuk menyimpan perubahan.</li>
                    <li>Perubahan berlaku saat pengguna login ulang.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-shield-halved text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Manajemen Role</div><div class="text-[11px] text-slate-400">Kembali ke daftar role di <strong>Pengaturan → Manajemen Role</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-users text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Manajemen User</div><div class="text-[11px] text-slate-400">Role ini diterapkan ke pengguna di <strong>Pengaturan → Manajemen User</strong>.</div></div>
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

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
