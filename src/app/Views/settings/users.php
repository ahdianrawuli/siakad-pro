<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <h3 class="text-3xl font-medium text-gray-700 mb-6">Manajemen Pengguna</h3>
    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow h-fit">
            <h4 class="font-bold mb-4 text-lg border-b pb-2">Tambah User Baru</h4>
            <form action="/settings/users/store" method="POST">
                <?= \App\Core\Csrf::input() ?>
                <div class="mb-3">
                    <label class="label">Nama Lengkap</label>
                    <input type="text" name="name" class="input" required>
                </div>
                <div class="mb-3">
                    <label class="label">Username</label>
                    <input type="text" name="username" class="input" required>
                </div>
                <div class="mb-3">
                    <label class="label">Email</label>
                    <input type="email" name="email" class="input" required>
                </div>
                <div class="mb-3">
                    <label class="label">Role (Jabatan)</label>
                    <select name="role_id" class="input">
                        <?php foreach($roles as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="label">Password</label>
                    <input type="password" name="password" class="input" required>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded font-bold hover:bg-green-700">Buat User</button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded shadow overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-100 text-left text-xs font-bold text-gray-600 uppercase">
                        <th class="px-5 py-3">Nama / Role</th>
                        <th class="px-5 py-3">Username / Email</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <p class="font-bold text-gray-800"><?= $u['name'] ?></p>
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded"><?= $u['role_name'] ?></span>
                        </td>
                        <td class="px-5 py-4 text-sm">
                            <p><i class="fa-solid fa-user mr-1 text-gray-400"></i> <?= $u['username'] ?></p>
                            <p><i class="fa-solid fa-envelope mr-1 text-gray-400"></i> <?= $u['email'] ?></p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex justify-center space-x-2">
                                <form action="/settings/users/reset" method="POST" onsubmit="return confirm('Reset password user ini jadi 123456?')">
                                    <?= \App\Core\Csrf::input() ?>
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="bg-yellow-500 text-white p-2 rounded hover:bg-yellow-600" title="Reset Password">
                                        <i class="fa-solid fa-key"></i>
                                    </button>
                                </form>
                                <?php if($u['id'] != $_SESSION['user_id']): ?>
                                <form action="/settings/users/delete" method="POST" onsubmit="return confirm('Hapus user ini permanen?')">
                                    <?= \App\Core\Csrf::input() ?>
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="bg-red-600 text-white p-2 rounded hover:bg-red-700" title="Hapus User">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <style>
        .label { display: block; font-size: 0.75rem; font-weight: bold; color: #4b5563; margin-bottom: 0.25rem; text-transform: uppercase; }
        .input { width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; }
    </style>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
