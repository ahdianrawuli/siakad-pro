<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Detail Profil Guru</h1>
        <div class="space-x-2">
            <a href="/student-affairs/teachers" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-bold hover:bg-gray-300 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
            </a>
            <a href="/student-affairs/teachers/edit?id=<?= $teacher['id'] ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                <i class="fa-solid fa-pen-to-square mr-2"></i> Edit Data
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                <div class="w-32 h-32 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-md">
                    <i class="fa-solid fa-user-tie text-5xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($teacher['full_name']) ?></h3>
                <p class="text-gray-500 mb-4">NIP: <?= $teacher['nip'] ?></p>
                
                <span class="px-4 py-1 rounded-full text-sm font-bold <?= $teacher['status'] == 'ACTIVE' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                    <?= $teacher['status'] ?>
                </span>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b bg-gray-50 font-bold text-gray-700">
                    Biodata Lengkap
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold"><?= $teacher['gender'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 uppercase tracking-wider">Pendidikan Terakhir</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold"><?= $teacher['education'] ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 uppercase tracking-wider">Nomor HP / WA</dt>
                            <dd class="mt-1 text-sm text-blue-600 font-bold"><?= $teacher['phone'] ?: '-' ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 uppercase tracking-wider">Alamat Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold"><?= $teacher['email'] ?: '-' ?></dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 uppercase tracking-wider">Alamat Domisili</dt>
                            <dd class="mt-1 text-sm text-gray-900 leading-relaxed"><?= $teacher['address'] ?: 'Alamat belum diisi.' ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 uppercase tracking-wider">Tanggal Bergabung</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= date('d F Y', strtotime($teacher['created_at'])) ?></dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
