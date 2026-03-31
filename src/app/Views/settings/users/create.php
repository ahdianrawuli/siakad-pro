<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <h3 class="text-2xl font-semibold text-gray-700 mb-6">Tambah User Baru</h3>

    <div class="bg-white p-6 rounded-lg shadow-md max-w-2xl">
        <form action="/settings/users/store" method="POST" class="grid grid-cols-1 gap-6">
            <?= \App\Core\Csrf::input() ?>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                <input type="text" name="name" class="w-full p-2 border rounded" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input type="text" name="username" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email" class="w-full p-2 border rounded" required>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" class="w-full p-2 border rounded" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Role Access</label>
                <select name="role_id" class="w-full p-2 border rounded bg-white">
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex justify-end pt-4">
                <a href="/settings/users" class="px-4 py-2 text-gray-500 hover:text-gray-700 mr-2">Batal</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan User</button>
            </div>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
