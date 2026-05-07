<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <a href="/settings/users" class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition shrink-0">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Tambah User Baru</h3>
            <p class="text-slate-500 text-sm mt-0.5 font-medium">Buat akun pengguna baru dan tentukan hak aksesnya.</p>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden max-w-2xl">
        <form action="/settings/users/store" method="POST" class="p-6 space-y-5">
            <?= \App\Core\Csrf::input() ?>

            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" placeholder="cth: Ahmad Fauzi"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Username</label>
                    <input type="text" name="username" placeholder="cth: ahmad.fauzi"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Email</label>
                    <input type="email" name="email" placeholder="cth: ahmad@siakad.com"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Password</label>
                <input type="password" name="password" placeholder="Minimal 6 karakter"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Role Akses</label>
                <select name="role_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                    <option value="">-- Pilih Role --</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex gap-3 pt-2 border-t border-slate-100">
                <a href="/settings/users" class="flex-1 text-center bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</a>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan User</button>
            </div>
        </form>
    </div>
</main>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
