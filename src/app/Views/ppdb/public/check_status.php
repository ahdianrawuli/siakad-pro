<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Daftar - SIAKAD PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        santri: {
                            light: '#4ade80', // green-400
                            DEFAULT: '#16a34a', // green-600
                            dark: '#15803d', // green-700
                        },
                        primary: '#16a34a'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen">

    <!-- 1. HEADER / NAVBAR -->
    <header class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center space-x-3 cursor-pointer">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-santri text-2xl">
                        <i class="fa-solid fa-mosque"></i>
                    </div>
                    <div class="hidden sm:block leading-tight">
                        <div class="font-bold text-sm tracking-wider text-gray-900">PONDOK PESANTREN</div>
                        <div class="font-extrabold text-lg text-santri">SUMATERA THAWALIB PARABEK</div>
                        <div class="text-xs text-gray-500">Bukittinggi Agam</div>
                    </div>
                </div>

                <!-- Nav Desktop -->
                <div class="hidden md:flex items-center space-x-8">
                    <nav class="flex space-x-6 text-sm font-bold text-gray-600">
                        <a href="/" class="hover:text-santri transition">HOME</a>
                        <a href="/prosedur" class="hover:text-santri transition">PROSEDUR</a>
                        <a href="/cek-status" class="text-primary border-b-2 border-primary pb-1 transition">STATUS DAFTAR</a>
                    </nav>
                    <div class="flex items-center space-x-3">
                        <a href="/login" class="px-5 py-2 border-2 border-gray-200 text-gray-600 font-bold rounded-lg hover:border-santri hover:text-santri transition">MASUK</a>
                        <a href="/register" class="px-5 py-2 bg-santri text-white font-bold rounded-lg shadow hover:bg-santri-dark transition">DAFTAR</a>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button class="text-gray-600 hover:text-santri focus:outline-none">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content wrapper to push footer down -->
    <main class="flex-grow pt-20">

        <!-- 2. PAGE TITLE -->
        <div class="relative bg-gray-900 pt-20 pb-16 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=1600&q=80&auto=format&fit=crop" alt="bg" class="absolute inset-0 w-full h-full object-cover opacity-25">
            <div class="absolute inset-0 bg-gradient-to-b from-gray-900/50 to-gray-900/80"></div>
            <div class="relative z-10 text-center px-4">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/10 border border-white/20 rounded-full mb-5">
                    <i class="fa-solid fa-user-magnifying-glass text-white text-2xl"></i>
                </div>
                <h2 class="text-4xl font-extrabold text-white mb-3">Cek Status Daftar</h2>
                <p class="text-gray-300 mb-4">Pantau perkembangan pendaftaran Anda secara real-time</p>
                <div class="flex items-center justify-center space-x-2 text-sm text-gray-400 font-medium">
                    <a href="/" class="hover:text-white transition">Home</a>
                    <span>/</span>
                    <span class="text-white">Status</span>
                </div>
            </div>
        </div>

        <!-- 3. CARD FORM STATUS -->
        <div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
            <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-8 sm:p-12 text-center">
                    <h3 class="text-2xl font-extrabold text-gray-900 mb-3">Periksa Status Anda</h3>
                    <p class="text-gray-500 mb-8 text-base">Masukkan nomor registrasi atau NISN Anda di bawah ini.</p>

                    <form action="/cek-status" method="POST" class="flex flex-col sm:flex-row gap-4 justify-center relative">
                        <div class="relative flex-grow">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-regular fa-id-card text-gray-400"></i>
                            </div>
                            <input type="text" name="search_query" value="<?= htmlspecialchars($searchQuery ?? '') ?>" placeholder="Masukkan No. Daftar / NISN" required
                                class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-santri focus:border-santri transition shadow-sm text-lg text-gray-700 placeholder-gray-400 outline-none">
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-santri text-white font-bold rounded-xl hover:bg-santri-dark transition shadow-md flex items-center justify-center gap-2 text-lg shrink-0">
                            Cek <i class="fa-solid fa-arrow-right ml-1"></i>
                        </button>
                    </form>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-red-50 border-t border-red-100 p-6 text-center">
                        <p class="text-red-600 font-medium"><i class="fa-solid fa-circle-exclamation mr-2"></i> <?= $error ?></p>
                    </div>
                <?php endif; ?>

                <?php if (isset($result) && $result): ?>
                    <div class="bg-green-50 border-t border-green-100 p-8 sm:p-12">
                        <h4 class="text-xl font-bold text-gray-900 mb-6 text-center border-b border-green-200 pb-4">Hasil Pencarian</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8 mb-8">
                            <div>
                                <p class="text-sm text-gray-500 font-semibold mb-1 uppercase tracking-wider">No. Pendaftaran</p>
                                <p class="text-lg font-bold text-gray-900"><?= htmlspecialchars($result['registration_no']) ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-semibold mb-1 uppercase tracking-wider">NISN</p>
                                <p class="text-lg font-bold text-gray-900"><?= htmlspecialchars($result['nisn']) ?></p>
                            </div>
                            <div class="sm:col-span-2">
                                <p class="text-sm text-gray-500 font-semibold mb-1 uppercase tracking-wider">Nama Lengkap</p>
                                <p class="text-xl font-extrabold text-gray-900"><?= htmlspecialchars($result['full_name']) ?></p>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-green-100">
                            <p class="text-sm text-gray-500 font-semibold mb-3 text-center uppercase tracking-wider">Status Saat Ini</p>

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

                            <div class="flex flex-col items-center justify-center text-center">
                                <div class="w-16 h-16 bg-<?= $config['color'] ?>-100 text-<?= $config['color'] ?>-600 rounded-full flex items-center justify-center text-3xl mb-3 shadow-sm">
                                    <i class="fa-solid fa-<?= $config['icon'] ?>"></i>
                                </div>
                                <h5 class="text-2xl font-extrabold text-<?= $config['color'] ?>-700"><?= $config['text'] ?></h5>
                            </div>
                        </div>

                    </div>
                <?php endif; ?>

            </div>
        </div>

    </main>

    <!-- 5. FOOTER -->
    <footer class="bg-santri-dark text-white pt-16 pb-8 border-t-[10px] border-santri mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                <!-- Col 1 -->
                <div>
                    <h3 class="font-extrabold text-xl mb-6">Perlu bantuan?</h3>
                    <p class="text-green-100 mb-6">Jangan ragu untuk menghubungi atau kunjungi website kami</p>
                    <a href="https://wa.me/6281260959820" target="_blank" class="inline-flex items-center px-6 py-3 bg-green-500 hover:bg-green-400 text-white font-bold rounded-lg transition shadow-lg">
                        <i class="fa-brands fa-whatsapp text-2xl mr-2"></i> Hubungi via WhatsApp
                    </a>
                </div>
                <!-- Col 2 -->
                <div>
                    <h3 class="font-extrabold text-xl mb-6">Alamat Kampus</h3>
                    <ul class="space-y-4 text-green-100">
                        <li class="flex items-start">
                            <i class="fa-solid fa-location-dot mt-1 mr-3 text-green-400"></i>
                            <span>Jorong Parabek, Kecamatan Banuhampu, Kabupaten Agam, Provinsi Sumatera Barat</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fa-solid fa-globe mr-3 text-green-400"></i>
                            <a href="https://www.thawalib-parabek.sch.id/" class="hover:text-white underline">www.thawalib-parabek.sch.id</a>
                        </li>
                    </ul>
                </div>
                <!-- Col 3 -->
                <div>
                    <h3 class="font-extrabold text-xl mb-6">Sosial Media</h3>
                    <div class="flex space-x-4">
                        <a href="https://www.instagram.com/thawalibparabek" target="_blank" class="w-12 h-12 bg-green-800 rounded-full flex items-center justify-center hover:bg-white hover:text-santri-dark transition text-xl">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="https://www.facebook.com/sumaterathawalib.parabek" target="_blank" class="w-12 h-12 bg-green-800 rounded-full flex items-center justify-center hover:bg-white hover:text-santri-dark transition text-xl">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://www.youtube.com/@ThawalibParabek" target="_blank" class="w-12 h-12 bg-green-800 rounded-full flex items-center justify-center hover:bg-white hover:text-santri-dark transition text-xl">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                        <a href="https://www.tiktok.com/@thawalibparabek" target="_blank" class="w-12 h-12 bg-green-800 rounded-full flex items-center justify-center hover:bg-white hover:text-santri-dark transition text-xl">
                            <i class="fa-brands fa-tiktok"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-green-800 pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-green-200">
                <p>&copy; <?= date('Y') ?> Pondok Pesantren Sumatera Thawalib Parabek. Hak cipta dilindungi.</p>
                <p class="mt-2 md:mt-0">Ditenagai oleh SIAKAD PRO</p>
            </div>
        </div>
    </footer>

</body>
</html>