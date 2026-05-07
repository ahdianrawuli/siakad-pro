<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pondok Pesantren Sumatera Thawalib Parabek</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        santri: '#16a34a', // green-600
                        'santri-dark': '#15803d', // green-700
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased text-gray-800 bg-white">

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
                        <a href="#" class="text-santri hover:text-santri-dark transition">HOME</a>
                        <a href="/prosedur" class="hover:text-santri transition">PROSEDUR</a>
                        <a href="#" class="hover:text-santri transition">STATUS DAFTAR</a>
                    </nav>
                    <div class="flex items-center space-x-3">
                        <a href="/login" class="px-5 py-2 border-2 border-gray-200 text-gray-600 font-bold rounded-lg hover:bg-gray-50 transition">MASUK</a>
                        <a href="/register" class="px-5 py-2 bg-santri text-white font-bold rounded-lg hover:bg-santri-dark transition shadow-lg shadow-green-200">DAFTAR</a>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Overlay (Tailwind Dropdown) -->
    <div id="mobile-menu" class="hidden fixed inset-0 z-40 bg-gray-800 bg-opacity-25 md:hidden">
        <div class="absolute top-20 left-0 w-full bg-white shadow-xl border-t border-gray-100">
            <div class="px-6 py-8 space-y-6">
                <a href="/" class="block font-bold text-gray-800 hover:text-santri transition text-lg border-b border-gray-50 pb-2">HOME</a>
                <a href="/prosedur" class="block font-bold text-gray-800 hover:text-santri transition text-lg border-b border-gray-50 pb-2">PROSEDUR</a>
                <a href="/cek-status" class="block font-bold text-gray-800 hover:text-santri transition text-lg border-b border-gray-50 pb-2">STATUS DAFTAR</a>
                <div class="pt-4 flex flex-col gap-4">
                    <a href="/login" class="text-center px-5 py-3 border-2 border-gray-200 text-gray-600 font-extrabold rounded-lg hover:bg-gray-50 transition">MASUK</a>
                    <a href="/register" class="text-center px-5 py-3 bg-santri text-white font-extrabold rounded-lg hover:bg-santri-dark transition shadow-lg">DAFTAR</a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. HERO SECTION -->
    <section class="bg-gray-50 py-16 md:py-24 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-4">
                        Selamat datang <br>
                        <span class="text-santri">calon santri!</span>
                    </h1>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-700 mb-6">
                        Pondok Pesantren Sumatera Thawalib Parabek
                    </h2>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        Kini hadir sistem pendaftaran online yang mudah dan cepat, dapat diakses dari mana saja. Inilah langkah awal menuju Generasi Unggul! Klik 'Daftar Sekarang' untuk bergabung!
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="/register" class="px-8 py-4 bg-santri text-white font-bold rounded-xl hover:bg-santri-dark transition shadow-xl shadow-green-200 text-center text-lg">
                            Daftar Sekarang <i class="fa-solid fa-arrow-right ml-2"></i>
                        </a>
                        <a href="#program" class="px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition text-center text-lg">
                            Lihat Program
                        </a>
                    </div>
                </div>
                <div class="relative hidden md:block">
                    <!-- Placeholder untuk Ilustrasi -->
                    <div class="w-full h-[500px] bg-green-100 rounded-3xl relative overflow-hidden border-8 border-white shadow-2xl flex items-center justify-center">
                        <i class="fa-solid fa-users text-9xl text-green-200"></i>
                        <!-- Mockup HP -->
                        <div class="absolute -bottom-10 -left-10 w-48 h-80 bg-white rounded-3xl border-8 border-gray-800 shadow-2xl flex items-center justify-center">
                            <i class="fa-solid fa-mobile-screen text-5xl text-gray-300"></i>
                        </div>
                    </div>
                    <!-- Dekorasi -->
                    <div class="absolute top-10 -right-10 w-24 h-24 bg-yellow-400 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                    <div class="absolute -bottom-10 right-20 w-32 h-32 bg-green-400 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. SECTION HIJAU (QUOTE) -->
    <section class="bg-santri py-20 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/arabesque.png')]"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6 leading-tight">
                "Terpuji dalam tradisi, <br>Terdepan dalam prestasi"
            </h2>
            <p class="text-green-50 text-lg md:text-xl font-medium leading-relaxed">
                114 Tahun sudah Pondok Pesantren Sumatera Thawalib Parabek Bukittinggi Agam menjadi salah satu lembaga pendidikan Islam unggulan yang ada di Indonesia.
            </p>
        </div>
    </section>

    <!-- 4. CORE VALUES SECTION -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 items-center">
                <div class="md:col-span-7">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-8 flex items-center">
                        <span class="w-12 h-1 bg-santri mr-4 rounded-full"></span> Core Values
                    </h2>
                    <blockquote class="text-xl text-gray-600 italic leading-relaxed mb-6">
                        "Dengan menjunjung tinggi nilai-nilai Taqwa, Ahli Ilmu wal Ibadah, Wara’, Amanah, Lain, istiqamah, dan Birrun, kami berkomitmen untuk mewujudkan visi sebagai Pusat Pendidikan Islam Unggulan yang Membangun Generasi Khairu Ummah. Kami percaya bahwa dengan menanamkan nilai-nilai luhur Islam sejak dini, santri akan tumbuh menjadi pribadi yang unggul, berakhlak mulia, dan mampu memberikan kontribusi positif bagi masyarakat dan bangsa."
                    </blockquote>
                    <div class="inline-block bg-green-50 border-l-4 border-santri px-6 py-3 rounded-r-lg">
                        <p class="font-bold text-santri-dark text-lg">"Cerdas, Beriman, dan Berkontribusi"</p>
                    </div>
                </div>
                <div class="md:col-span-5 relative hidden md:block">
                    <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden shadow-lg flex items-center justify-center">
                        <i class="fa-solid fa-book-open text-9xl text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. PROGRAM PENDIDIKAN -->
    <section id="program" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-red-500 font-extrabold text-xs tracking-widest uppercase bg-red-100 px-3 py-1 rounded-full mb-4 inline-block">UNIT</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-6">Program Pendidikan</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto mb-16 leading-relaxed">
                Kami menawarkan jenjang pendidikan yang lengkap mulai dari Madrasah Tsanawiyah (MTs), Madrasah Aliyah (MA), Pendidikan Diniyah Formal Ulya (PDF), hingga Ma'had Aly. Dengan kurikulum yang terintegrasi, santri akan memperoleh pengetahuan agama yang mendalam dan keterampilan hidup yang relevan untuk masa depan.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-left">
                <!-- Card 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition group">
                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-2xl mb-6 group-hover:bg-blue-600 group-hover:text-white transition">
                        <i class="fa-solid fa-school"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Madrasah Tsanawiyah</h3>
                    <p class="text-santri font-medium text-sm">Terakreditasi A (Unggul)</p>
                </div>
                <!-- Card 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition group">
                    <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-2xl mb-6 group-hover:bg-indigo-600 group-hover:text-white transition">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Madrasah Aliyah</h3>
                    <p class="text-santri font-medium text-sm">Terakreditasi A (Unggul)</p>
                </div>
                <!-- Card 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition group">
                    <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-2xl mb-6 group-hover:bg-purple-600 group-hover:text-white transition">
                        <i class="fa-solid fa-book-quran"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">PDF</h3>
                    <p class="text-gray-500 font-medium text-sm">Pendidikan Diniyah Formal (PDF) Ulya</p>
                </div>
                <!-- Card 4 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition group">
                    <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center text-2xl mb-6 group-hover:bg-orange-600 group-hover:text-white transition">
                        <i class="fa-solid fa-landmark-dome"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Ma'had Aly</h3>
                    <p class="text-gray-500 font-medium text-sm">Program Studi Fiqh & Ushul Fiqh</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. JALUR MASUK -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-red-500 font-extrabold text-xs tracking-widest uppercase bg-red-100 px-3 py-1 rounded-full mb-4 inline-block">PONDOK PESANTREN SUMATERA THAWALIB PARABEK</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-6">Jalur Masuk</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto mb-16 leading-relaxed">
                Dengan berbagai jalur masuk yang ditawarkan, Pondok Pesantren Sumatera Thawalib Parabek memberikan peluang kepada calon santri dari berbagai latar belakang untuk mendapatkan pendidikan Islam yang berkualitas dan komprehensif.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
                <!-- Jalur 1 -->
                <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm hover:shadow-md transition flex items-start gap-4">
                    <div class="w-12 h-12 bg-green-100 text-santri rounded-full flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-users-line"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-lg">Jalur Reguler / Umum</h4>
                        <p class="text-gray-500 text-sm mt-1">(Tsanawiyah, Aliyah dan PDF)</p>
                    </div>
                </div>
                <!-- Jalur 2 -->
                <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm hover:shadow-md transition flex items-start gap-4">
                    <div class="w-12 h-12 bg-green-100 text-santri rounded-full flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-book-open-reader"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-lg">Jalur Tahfizh</h4>
                        <p class="text-gray-500 text-sm mt-1">(Tsanawiyah & Aliyah)</p>
                    </div>
                </div>
                <!-- Jalur 3 -->
                <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm hover:shadow-md transition flex items-start gap-4">
                    <div class="w-12 h-12 bg-green-100 text-santri rounded-full flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-medal"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-lg">Prestasi Akademik</h4>
                        <p class="text-gray-500 text-sm mt-1">(Tsanawiyah)</p>
                    </div>
                </div>
                <!-- Jalur 4 -->
                <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm hover:shadow-md transition flex items-start gap-4">
                    <div class="w-12 h-12 bg-green-100 text-santri rounded-full flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-lg">Jalur Banuhampu</h4>
                        <p class="text-gray-500 text-sm mt-1">(Tsanawiyah)</p>
                    </div>
                </div>
                <!-- Jalur 5 -->
                <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm hover:shadow-md transition flex items-start gap-4">
                    <div class="w-12 h-12 bg-green-100 text-santri rounded-full flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-mosque"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-lg">Jalur Parabek</h4>
                        <p class="text-gray-500 text-sm mt-1">(Tsanawiyah)</p>
                    </div>
                </div>
                <!-- Jalur 6 -->
                <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm hover:shadow-md transition flex items-start gap-4">
                    <div class="w-12 h-12 bg-green-100 text-santri rounded-full flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-book"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-lg">Jalur Kitab</h4>
                        <p class="text-gray-500 text-sm mt-1">(PDF)</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        // Close menu when clicking outside
        menu.addEventListener('click', (e) => {
            if (e.target === menu) {
                menu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
