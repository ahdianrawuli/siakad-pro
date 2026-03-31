<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-semibold text-gray-700">Edit User</h3>
        <a href="/settings/users" class="px-4 py-2 text-gray-500 hover:text-gray-700 mr-2">Kembali</a>
    </div>

    <div class="bg-white shadow rounded-lg p-6 max-w-lg">
        <form action="/settings/users/update" method="POST" class="grid grid-cols-1 gap-6">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" value="<?= $user['id'] ?>">

            <label class="block">
                <span class="text-gray-700">Nama Lengkap</span>
                <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required value="<?= $user['name'] ?>">
            </label>

            <label class="block">
                <span class="text-gray-700">Username</span>
                <input type="text" name="username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required value="<?= $user['username'] ?>">
            </label>

            <label class="block">
                <span class="text-gray-700">Email</span>
                <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required value="<?= $user['email'] ?>">
            </label>

            <label class="block">
                <span class="text-gray-700">Password (Kosongkan jika tidak ingin mengubah)</span>
                <input type="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </label>

            <label class="block">
                <span class="text-gray-700">Role / Hak Akses</span>
                <select name="role_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>" <?= $user['role_id'] == $role['id'] ? 'selected' : '' ?>><?= $role['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <div class="flex justify-end">
                <a href="/settings/users" class="px-4 py-2 text-gray-500 hover:text-gray-700 mr-2">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
