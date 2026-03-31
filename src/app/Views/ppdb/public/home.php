<?php require __DIR__ . '/../../layouts/public_header.php'; ?>

<section class="relative bg-green-700 overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-24 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            
            <div class="text-center md:text-left animate-fade-in-up">
                <span class="inline-block py-1 px-3 rounded-full bg-green-500/30 text-green-100 text-sm font-bold mb-4 border border-green-400/30">
                    Tahun Ajaran <?= date('Y') ?>/<?= date('Y')+1 ?>
                </span>
                <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight mb-6">
                    Wujudkan Generasi <br> <span class="text-yellow-300">Qur'ani & Berprestasi</span>
                </h1>
                <p class="text-green-100 text-lg md:text-xl mb-8 leading-relaxed max-w-lg mx-auto md:mx-0">
                    Bergabunglah bersama kami membentuk karakter islami yang unggul dalam ilmu pengetahuan dan teknologi.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="/register" class="px-8 py-4 bg-yellow-400 text-green-900 font-bold rounded-lg shadow-lg hover:bg-yellow-300 transition transform hover:-translate-y-1 text-center">
                        Daftar Sekarang <i class="fa-solid fa-arrow-right ml-2"></i>
                    </a>
                    <a href="#brosur" class="px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-lg hover:bg-white hover:text-green-700 transition text-center">
                        Download Brosur
                    </a>
                </div>
            </div>

            <div class="hidden md:block relative">
                <div class="absolute -inset-4 bg-yellow-400/20 rounded-full blur-3xl"></div>
                <img src="https://img.freepik.com/free-vector/happy-students-with-backpacks-books_74855-5824.jpg" alt="Santri Happy" class="relative rounded-2xl shadow-2xl border-4 border-white/20 w-full transform rotate-2 hover:rotate-0 transition duration-500">
            </div>
        </div>
    </div>
</section>

<section class="py-10 bg-white -mt-8 relative z-20">
    <div class="max-w-6xl mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-lg border-b-4 border-green-500 text-center">
                <i class="fa-solid fa-users text-4xl text-green-600 mb-3"></i>
                <h3 class="text-2xl font-bold text-gray-800">500+</h3>
                <p class="text-gray-500">Santri Aktif</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-lg border-b-4 border-yellow-400 text-center">
                <i class="fa-solid fa-chalkboard-user text-4xl text-yellow-500 mb-3"></i>
                <h3 class="text-2xl font-bold text-gray-800">30+</h3>
                <p class="text-gray-500">Guru Berdedikasi</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-lg border-b-4 border-blue-500 text-center">
                <i class="fa-solid fa-trophy text-4xl text-blue-500 mb-3"></i>
                <h3 class="text-2xl font-bold text-gray-800">100+</h3>
                <p class="text-gray-500">Prestasi Diraih</p>
            </div>
        </div>
    </div>
</section>

<section id="alur" class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800">Alur Pendaftaran</h2>
            <div class="w-20 h-1 bg-green-500 mx-auto mt-4 rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition relative overflow-hidden group">
                <div class="absolute top-0 right-0 bg-green-100 w-16 h-16 rounded-bl-full -mr-2 -mt-2 group-hover:scale-110 transition"></div>
                <div class="w-12 h-12 bg-green-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6 relative z-10">1</div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Isi Formulir</h3>
                <p class="text-gray-500 text-sm">Buat akun dan lengkapi data diri calon santri secara online.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition relative overflow-hidden group">
                <div class="absolute top-0 right-0 bg-yellow-100 w-16 h-16 rounded-bl-full -mr-2 -mt-2 group-hover:scale-110 transition"></div>
                <div class="w-12 h-12 bg-yellow-500 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6 relative z-10">2</div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Pembayaran</h3>
                <p class="text-gray-500 text-sm">Transfer biaya pendaftaran dan upload bukti pembayaran.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition relative overflow-hidden group">
                <div class="absolute top-0 right-0 bg-blue-100 w-16 h-16 rounded-bl-full -mr-2 -mt-2 group-hover:scale-110 transition"></div>
                <div class="w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6 relative z-10">3</div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Ujian Seleksi</h3>
                <p class="text-gray-500 text-sm">Cetak kartu ujian dan ikuti tes seleksi sesuai jadwal.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition relative overflow-hidden group">
                <div class="absolute top-0 right-0 bg-red-100 w-16 h-16 rounded-bl-full -mr-2 -mt-2 group-hover:scale-110 transition"></div>
                <div class="w-12 h-12 bg-red-500 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6 relative z-10">4</div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Pengumuman</h3>
                <p class="text-gray-500 text-sm">Cek hasil kelulusan di dashboard akun Anda.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-green-800 text-white text-center px-4">
    <h2 class="text-3xl font-bold mb-6">Siap Menjadi Bagian Dari Kami?</h2>
    <p class="mb-8 text-green-100 max-w-2xl mx-auto">Kuota terbatas. Segera daftarkan putra-putri Anda sebelum pendaftaran ditutup.</p>
    <a href="/register" class="inline-block px-10 py-4 bg-white text-green-800 font-bold rounded-full shadow-lg hover:bg-gray-100 transition transform hover:scale-105">
        Mulai Pendaftaran
    </a>
</section>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
