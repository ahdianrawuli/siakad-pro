<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-semibold text-gray-700">Manajemen Users</h3>
        <a href="/settings/users/create" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            <i class="fa-solid fa-plus mr-2"></i> Tambah User
        </a>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white shadow rounded-lg overflow-hidden overflow-x-auto">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">Username / Email</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td class="px-5 py-5 border-b bg-white text-sm">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold">
                                <?= substr($u['name'], 0, 1) ?>
                            </div>
                            <div class="ml-3">
                                <p class="text-gray-900 whitespace-no-wrap"><?= $u['name'] ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-5 border-b bg-white text-sm">
                        <p class="text-gray-900 font-bold"><?= $u['username'] ?></p>
                        <p class="text-gray-500"><?= $u['email'] ?></p>
                    </td>
                    <td class="px-5 py-5 border-b bg-white text-sm">
                        <span class="relative inline-block px-3 py-1 font-semibold text-blue-900 leading-tight">
                            <span aria-hidden class="absolute inset-0 bg-blue-200 opacity-50 rounded-full"></span>
                            <span class="relative"><?= $u['role_name'] ?></span>
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b bg-white text-sm">
                        <span class="text-green-600 font-bold"><?= $u['status'] ?></span>
                    </td>
                    <td class="px-5 py-5 border-b bg-white text-sm">
                        <a href="/settings/users/edit?id=<?= $u['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                        <a href="/settings/users/delete?id=<?= $u['id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Hapus user ini?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
