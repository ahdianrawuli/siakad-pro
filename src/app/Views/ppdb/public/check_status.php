<?php require __DIR__ . '/../../layouts/public_header.php'; ?>

<main class="flex-grow pt-24 pb-12 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-10">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Cek Status Pendaftaran</h2>
            <p class="mt-4 text-lg text-gray-600">Masukkan Nomor Pendaftaran atau NISN untuk melihat status terkini.</p>
        </div>

        <div class="bg-white shadow-xl rounded-2xl p-8 mb-8">
            <form action="/cek-status" method="POST" class="flex flex-col sm:flex-row gap-4 justify-center">
                <input type="text" name="search_query" value="<?= $searchQuery ?? '' ?>" placeholder="Contoh: REG-2026-1234 atau 0012345678" required
                    class="w-full sm:w-2/3 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition shadow-sm text-lg">
                <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition shadow-md flex items-center justify-center gap-2 text-lg">
                    <i class="fa-solid fa-search"></i> Cek Status
                </button>
            </form>

            <?php if (isset($error)): ?>
                <div class="mt-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
                    <p class="font-medium"><i class="fa-solid fa-circle-exclamation mr-2"></i> <?= $error ?></p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($result) && $result): ?>
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
                <div class="bg-green-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-user-graduate"></i> Hasil Pencarian
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 font-semibold mb-1">Nomor Pendaftaran</p>
                            <p class="text-lg font-bold text-gray-900"><?= htmlspecialchars($result['registration_no']) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold mb-1">NISN</p>
                            <p class="text-lg font-bold text-gray-900"><?= htmlspecialchars($result['nisn']) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold mb-1">Nama Lengkap</p>
                            <p class="text-lg font-bold text-gray-900"><?= htmlspecialchars($result['full_name']) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold mb-1">Jalur Pendaftaran</p>
                            <p class="text-lg font-bold text-gray-900"><?= htmlspecialchars($result['track_name'] ?? '-') ?></p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="text-lg font-bold text-gray-900 mb-4">Status Pendaftaran</h4>

                        <?php
                            $status = $result['registration_status'];
                            $statusConfig = [
                                'PENDING' => ['color' => 'yellow', 'icon' => 'clock', 'text' => 'Menunggu Verifikasi'],
                                'APPROVED' => ['color' => 'blue', 'icon' => 'check-circle', 'text' => 'Berkas Diterima'],
                                'REJECTED' => ['color' => 'red', 'icon' => 'times-circle', 'text' => 'Ditolak'],
                                'LULUS' => ['color' => 'green', 'icon' => 'trophy', 'text' => 'Lulus Seleksi'],
                                'TIDAK LULUS' => ['color' => 'gray', 'icon' => 'ban', 'text' => 'Tidak Lulus'],
                                'MENGUNDURKAN DIRI' => ['color' => 'orange', 'icon' => 'user-minus', 'text' => 'Mengundurkan Diri']
                            ];

                            $config = $statusConfig[$status] ?? ['color' => 'gray', 'icon' => 'info-circle', 'text' => $status];
                        ?>

                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-12 h-12 bg-<?= $config['color'] ?>-100 text-<?= $config['color'] ?>-600 rounded-full flex items-center justify-center text-xl shadow-sm">
                                <i class="fa-solid fa-<?= $config['icon'] ?>"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-<?= $config['color'] ?>-700"><?= $config['text'] ?></p>
                            </div>
                        </div>

                        <?php if ($status === 'PENDING'): ?>
                            <p class="mt-4 text-sm text-gray-600 bg-yellow-50 p-3 rounded border border-yellow-100">
                                <strong>Informasi:</strong> Pendaftaran Anda sedang dalam antrean verifikasi oleh panitia. Harap cek kembali secara berkala.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>