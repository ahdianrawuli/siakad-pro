<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prosedur Pendaftaran - Pondok Pesantren Sumatera Thawalib Parabek</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        santri: '#16a34a', // green-600
                        'santri-dark': '#15803d', // green-700
                        primary: '#f97316', // orange-500
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased text-gray-800 bg-gray-50">

    <!-- 1. HEADER / NAVBAR -->
    <header class="bg-white sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Kiri: Logo & Text -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-santri text-2xl">
                        <i class="fa-solid fa-mosque"></i>
                    </div>
                    <div class="hidden sm:block leading-tight">
                        <div class="font-bold text-sm tracking-wider text-gray-900">PONDOK PESANTREN</div>
                        <div class="font-extrabold text-lg text-santri">SUMATERA THAWALIB PARABEK</div>
                        <div class="text-xs text-gray-500">Bukittinggi Agam</div>
                    </div>
                </div>

                <!-- Kanan: Menu & Buttons -->
                <div class="hidden md:flex items-center space-x-8">
                    <nav class="flex space-x-6 text-sm font-bold text-gray-600">
                        <a href="/" class="hover:text-santri transition">HOME</a>
                        <a href="/prosedur" class="text-primary border-b-2 border-primary pb-1 transition">PROSEDUR</a>
                        <a href="/cek-status" class="hover:text-santri transition">STATUS DAFTAR</a>
                    </nav>
                    <div class="flex items-center space-x-3">
                        <a href="/login" class="px-5 py-2 border-2 border-gray-200 text-gray-600 font-bold rounded-lg hover:bg-gray-50 transition">MASUK</a>
                        <a href="/register" class="px-5 py-2 bg-santri text-white font-bold rounded-lg hover:bg-santri-dark transition shadow-lg shadow-green-200">DAFTAR</a>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- 2. PAGE TITLE -->
    <section class="bg-white py-12 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Prosedur</h1>
            <p class="text-sm text-gray-500 font-medium">
                <a href="/" class="hover:text-santri">Home</a> <span class="mx-2">/</span> <span class="text-gray-900">Prosedur</span>
            </p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        <!-- 3. SECTION ALUR PENDAFTARAN (CARD HIJAU BESAR) -->
        <div class="bg-santri rounded-3xl p-8 md:p-12 shadow-2xl mb-16 relative overflow-hidden flex flex-col md:flex-row items-center justify-between">
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/arabesque.png')]"></div>

            <div class="relative z-10 md:w-2/3 text-white mb-8 md:mb-0">
                <span class="inline-block bg-white/20 text-white font-bold text-xs tracking-widest uppercase px-3 py-1 rounded-full mb-4">Tentang</span>
                <h2 class="text-3xl md:text-5xl font-extrabold mb-4">Alur Pendaftaran</h2>
                <p class="text-xl text-green-50 font-medium">Santri Baru Tahun Ajaran 2025 - 2026</p>
            </div>

            <div class="relative z-10 md:w-1/3 flex justify-center md:justify-end">
                <div class="w-48 h-48 bg-white rounded-full flex items-center justify-center shadow-inner">
                     <i class="fa-solid fa-user-graduate text-8xl text-santri"></i>
                </div>
            </div>
        </div>

        <!-- 4. GELOMBANG 1 -->
        <div class="mb-16">
            <h3 class="text-2xl font-extrabold text-gray-900 mb-8 border-l-4 border-santri pl-4">Gelombang 1</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                    <div class="w-12 h-12 bg-green-50 text-santri rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">BIAYA PENDAFTARAN</h4>
                    <p class="text-xl font-extrabold text-gray-900">RP 250.000</p>
                </div>
                <!-- Card 2 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">PENDAFTARAN</h4>
                    <p class="text-lg font-extrabold text-gray-900">01 Nov 2025 - 04 Jan 2026</p>
                </div>
                <!-- Card 3 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-50 text-purple-500 rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">SELEKSI</h4>
                    <p class="text-lg font-extrabold text-gray-900">11 Januari 2026</p>
                </div>
            </div>
        </div>

        <!-- 5. GELOMBANG 2 -->
        <div class="mb-20">
            <h3 class="text-2xl font-extrabold text-gray-900 mb-8 border-l-4 border-santri pl-4">Gelombang 2</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                    <div class="w-12 h-12 bg-green-50 text-santri rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">BIAYA PENDAFTARAN</h4>
                    <p class="text-xl font-extrabold text-gray-900">RP 250.000</p>
                </div>
                <!-- Card 2 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">PENDAFTARAN</h4>
                    <p class="text-lg font-extrabold text-gray-900">05 Jan 2026 - 03 Mei 2026</p>
                </div>
                <!-- Card 3 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-50 text-purple-500 rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">SELEKSI</h4>
                    <p class="text-lg font-extrabold text-gray-900">10 Mei 2026</p>
                </div>
            </div>
        </div>

        <!-- 6. SECTION LANGKAH PENDAFTARAN (TIMELINE) -->
        <div class="max-w-4xl mx-auto">
            <h3 class="text-3xl font-extrabold text-gray-900 mb-12 text-center">Langkah Pendaftaran</h3>

            <div class="relative border-l-4 border-green-200 ml-6 md:ml-12 space-y-12 pb-8">

                <!-- Step 1 -->
                <div class="relative pl-10 md:pl-16">
                    <div class="absolute -left-[26px] top-0 w-12 h-12 bg-santri text-white rounded-full flex items-center justify-center font-bold text-xl border-4 border-white shadow-md">
                        1
                    </div>
                    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-start md:items-center gap-6">
                        <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center text-3xl shrink-0">
                            <i class="fa-solid fa-laptop-file"></i>
                        </div>
                        <div>
                            <p class="text-lg text-gray-700 font-medium leading-relaxed">
                                Calon Santri mendaftar secara online atau offline dengan mengisi syarat berkas yang telah ditentukan
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="relative pl-10 md:pl-16">
                    <div class="absolute -left-[26px] top-0 w-12 h-12 bg-santri text-white rounded-full flex items-center justify-center font-bold text-xl border-4 border-white shadow-md">
                        2
                    </div>
                    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-start md:items-center gap-6">
                        <div class="w-16 h-16 bg-green-50 text-santri rounded-full flex items-center justify-center text-3xl shrink-0">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <div>
                            <p class="text-lg text-gray-700 font-medium leading-relaxed">
                                Calon Santri melakukan pembayaran biaya pendaftaran sesuai instruksi yang diberikan
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative pl-10 md:pl-16">
                    <div class="absolute -left-[26px] top-0 w-12 h-12 bg-santri text-white rounded-full flex items-center justify-center font-bold text-xl border-4 border-white shadow-md">
                        3
                    </div>
                    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-start md:items-center gap-6">
                        <div class="w-16 h-16 bg-orange-50 text-orange-500 rounded-full flex items-center justify-center text-3xl shrink-0">
                            <i class="fa-solid fa-file-arrow-up"></i>
                        </div>
                        <div>
                            <p class="text-lg text-gray-700 font-medium leading-relaxed">
                                Calon Santri menyerahkan bukti pembayaran dan kelengkapan dokumen
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="relative pl-10 md:pl-16">
                    <div class="absolute -left-[26px] top-0 w-12 h-12 bg-santri text-white rounded-full flex items-center justify-center font-bold text-xl border-4 border-white shadow-md">
                        4
                    </div>
                    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-start md:items-center gap-6">
                        <div class="w-16 h-16 bg-purple-50 text-purple-500 rounded-full flex items-center justify-center text-3xl shrink-0">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                        <div>
                            <p class="text-lg text-gray-700 font-medium leading-relaxed">
                                Panitia melakukan verifikasi data dan dokumen calon Santri
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="relative pl-10 md:pl-16">
                    <div class="absolute -left-[26px] top-0 w-12 h-12 bg-santri text-white rounded-full flex items-center justify-center font-bold text-xl border-4 border-white shadow-md">
                        5
                    </div>
                    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-start md:items-center gap-6">
                        <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center text-3xl shrink-0">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>
                        <div>
                            <p class="text-lg text-gray-700 font-medium leading-relaxed">
                                Calon Santri mengikuti seleksi pada tanggal yang telah ditentukan
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- 7. FOOTER -->
    <footer class="bg-santri-dark text-white pt-16 pb-8 border-t-[10px] border-santri">
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