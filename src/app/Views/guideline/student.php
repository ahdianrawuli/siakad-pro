<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <div class="mb-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-700 flex items-center justify-center shrink-0"><i class="fa-solid fa-book-open text-2xl"></i></div>
        <div>
            <h3 class="text-2xl font-extrabold text-gray-800">Panduan Portal Santri</h3>
            <p class="text-gray-500 text-sm mt-0.5">Panduan lengkap penggunaan portal santri SIAKAD PRO Pesantren Thawalib Parabek</p>
        </div>
    </div>
    <div class="max-w-3xl space-y-5">

    <!-- Login -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-green-700 flex items-center gap-3"><i class="fa-solid fa-right-to-bracket text-white text-lg"></i><h4 class="font-bold text-white text-lg">Login ke Portal Santri</h4></div>
        <div class="p-6 space-y-3 text-sm text-gray-600">
            <p>Akses portal santri melalui <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs font-mono">/login</code> menggunakan:</p>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-green-50 rounded-xl p-4 border border-green-100 text-center">
                    <i class="fa-solid fa-id-card text-green-600 text-2xl mb-2 block"></i>
                    <p class="font-bold text-green-800 text-sm">Username</p>
                    <p class="text-green-700 text-xs mt-1">NIS (Nomor Induk Siswa)<br>Contoh: <code class="bg-white px-1 rounded">25260001</code></p>
                </div>
                <div class="bg-green-50 rounded-xl p-4 border border-green-100 text-center">
                    <i class="fa-solid fa-lock text-green-600 text-2xl mb-2 block"></i>
                    <p class="font-bold text-green-800 text-sm">Password</p>
                    <p class="text-green-700 text-xs mt-1">Tanggal lahir format DDMMYYYY<br>Contoh lahir 27 Sep 2013 → <code class="bg-white px-1 rounded">27092013</code></p>
                </div>
            </div>
            <div class="bg-orange-50 rounded-xl p-3 border border-orange-100 text-xs text-orange-800">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i><strong>Tidak bisa login?</strong> Hubungi admin atau wali kelas untuk reset password. Jangan berikan password ke orang lain.
            </div>
        </div>
    </div>

    <!-- Dashboard -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-green-700 flex items-center gap-3"><i class="fa-solid fa-gauge-high text-white text-lg"></i><h4 class="font-bold text-white text-lg">Dashboard (Overview)</h4></div>
        <div class="p-6 space-y-3 text-sm text-gray-600">
            <p>Halaman pertama setelah login. Menampilkan ringkasan informasi penting:</p>
            <ul class="list-disc list-inside space-y-1.5 text-gray-500">
                <li><strong>Rekap Absensi</strong> — jumlah hadir, sakit, izin, dan alpha bulan ini.</li>
                <li><strong>Tagihan Aktif</strong> — tagihan yang belum dibayar.</li>
                <li><strong>Pengumuman Terbaru</strong> — pengumuman dari pihak pesantren.</li>
                <li><strong>Info Kelas & Asrama</strong> — nama kelas dan kamar asrama saat ini.</li>
            </ul>
            <p class="text-gray-500">Gunakan menu di sidebar kiri untuk navigasi ke halaman lain.</p>
        </div>
    </div>

    <!-- Profil -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-green-700 flex items-center gap-3"><i class="fa-solid fa-address-card text-white text-lg"></i><h4 class="font-bold text-white text-lg">Data Santri & Profil</h4></div>
        <div class="p-6 space-y-4 text-sm text-gray-600">
            <div>
                <p class="font-semibold text-gray-800 mb-1">Data Santri</p>
                <p class="text-gray-500">Menampilkan informasi dasar: nama lengkap, NIS, NISN, kelas, jenis kelamin, dan foto profil. Jika ada data yang salah, laporkan ke admin atau wali kelas.</p>
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1">Biodata Lengkap</p>
                <p class="text-gray-500">Informasi lengkap meliputi: tempat/tanggal lahir, alamat, data ayah (nama, pekerjaan, nomor HP), data ibu, dan data wali. Pastikan nomor HP orang tua sudah benar karena digunakan untuk notifikasi WhatsApp.</p>
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1">Resume Santri</p>
                <p class="text-gray-500">Ringkasan pencapaian selama di pesantren: prestasi akademik, kegiatan ekstrakurikuler, dan catatan penting. Dapat dicetak sebagai dokumen portofolio.</p>
            </div>
        </div>
    </div>

    <!-- Akademik -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-green-700 flex items-center gap-3"><i class="fa-solid fa-book text-white text-lg"></i><h4 class="font-bold text-white text-lg">Akademik</h4></div>
        <div class="p-6 space-y-4 text-sm text-gray-600">
            <div>
                <p class="font-semibold text-gray-800 mb-1"><i class="fa-solid fa-calendar-days text-green-500 mr-1"></i>Jadwal Pelajaran</p>
                <p class="text-gray-500">Lihat jadwal pelajaran harian sesuai kelas. Menampilkan nama mata pelajaran, jam mulai-selesai, dan nama guru per hari dalam seminggu (Senin–Sabtu/Ahad).</p>
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1"><i class="fa-solid fa-clipboard-check text-green-500 mr-1"></i>Absensi</p>
                <p class="text-gray-500">Rekap kehadiran bulanan. Gunakan filter bulan untuk melihat data bulan tertentu. Jika ada kesalahan pencatatan absensi, segera laporkan ke wali kelas.</p>
                <div class="flex flex-wrap gap-2 mt-2 text-xs">
                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold">H = Hadir</span>
                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full font-bold">S = Sakit</span>
                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-bold">I = Izin</span>
                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full font-bold">A = Alpha</span>
                </div>
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1"><i class="fa-solid fa-chart-bar text-green-500 mr-1"></i>Nilai</p>
                <p class="text-gray-500">Nilai per mata pelajaran untuk tahun ajaran aktif. Menampilkan nilai Tugas, UTS, UAS, Nilai Akhir, KKM, dan status ketuntasan. Nilai muncul setelah guru menginput di sistem.</p>
                <div class="flex gap-3 mt-2 text-xs">
                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold">✓ Tuntas = Nilai ≥ KKM</span>
                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full font-bold">✗ Remedial = Nilai &lt; KKM</span>
                </div>
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1"><i class="fa-solid fa-credit-card text-green-500 mr-1"></i>Kartu Ujian</p>
                <p class="text-gray-500">Unduh dan cetak kartu ujian untuk keperluan ujian semester. Kartu ujian berisi foto, NIS, nama, kelas, dan daftar mata pelajaran yang diujikan. <strong>Wajib dibawa saat ujian.</strong></p>
            </div>
        </div>
    </div>

    <!-- Pembayaran -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-green-700 flex items-center gap-3"><i class="fa-solid fa-file-invoice-dollar text-white text-lg"></i><h4 class="font-bold text-white text-lg">Pembayaran</h4></div>
        <div class="p-6 space-y-3 text-sm text-gray-600">
            <p>Pantau status tagihan dan riwayat pembayaran. Halaman ini bersifat <strong>read-only</strong> — pembayaran dilakukan langsung ke bagian keuangan pesantren.</p>
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="bg-red-50 rounded-xl p-3 border border-red-100 text-center">
                    <p class="font-bold text-red-700 text-sm">UNPAID</p>
                    <p class="text-red-600 mt-1">Tagihan belum dibayar. Segera lunasi sebelum jatuh tempo.</p>
                </div>
                <div class="bg-green-50 rounded-xl p-3 border border-green-100 text-center">
                    <p class="font-bold text-green-700 text-sm">PAID / LUNAS</p>
                    <p class="text-green-600 mt-1">Tagihan sudah dibayar. Simpan kwitansi sebagai bukti.</p>
                </div>
            </div>
            <p class="text-gray-500 text-xs"><i class="fa-solid fa-info-circle text-blue-400 mr-1"></i>Jika sudah membayar tapi status masih UNPAID, tunjukkan kwitansi ke bagian keuangan untuk konfirmasi.</p>
        </div>
    </div>

    <!-- Kepesantrenan -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-green-700 flex items-center gap-3"><i class="fa-solid fa-house text-white text-lg"></i><h4 class="font-bold text-white text-lg">Kepesantrenan</h4></div>
        <div class="p-6 space-y-4 text-sm text-gray-600">
            <div>
                <p class="font-semibold text-gray-800 mb-1"><i class="fa-solid fa-house text-green-500 mr-1"></i>Asrama</p>
                <p class="text-gray-500">Informasi kamar asrama (nama asrama, kapasitas, jenis) dan riwayat perizinan yang pernah diajukan. Pengajuan izin dilakukan langsung ke wali asrama, bukan melalui portal ini.</p>
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1"><i class="fa-solid fa-heart-pulse text-green-500 mr-1"></i>Kesehatan</p>
                <p class="text-gray-500">Riwayat kunjungan ke Poskestren: tanggal, keluhan, diagnosis, tindakan, dan status (Rawat Jalan/Rawat Inap/Rujuk RS). Data diinput oleh petugas kesehatan pesantren.</p>
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1"><i class="fa-solid fa-triangle-exclamation text-green-500 mr-1"></i>Kedisiplinan</p>
                <p class="text-gray-500">Catatan pelanggaran beserta kategori (Ringan/Sedang/Berat) dan poin. Total poin pelanggaran mempengaruhi penilaian kepribadian di rapor. Jika ada catatan yang tidak sesuai, laporkan ke BK atau wali kelas.</p>
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1"><i class="fa-solid fa-person-running text-green-500 mr-1"></i>Ekstrakurikuler</p>
                <p class="text-gray-500">Daftar ekstrakurikuler yang diikuti beserta rekap kehadiran dan nilai/perkembangan dari pembina. Pendaftaran ekskul dilakukan melalui admin atau pembina ekskul.</p>
            </div>
        </div>
    </div>

    <!-- Dokumen & Surat -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-green-700 flex items-center gap-3"><i class="fa-solid fa-folder-open text-white text-lg"></i><h4 class="font-bold text-white text-lg">Dokumen & Surat</h4></div>
        <div class="p-6 space-y-4 text-sm text-gray-600">
            <div>
                <p class="font-semibold text-gray-800 mb-1">Surat Keterangan</p>
                <p class="text-gray-500">Ajukan dan unduh surat keterangan aktif, surat pindah, surat keterangan lulus, dan surat lainnya. Surat yang sudah disetujui admin dapat langsung dicetak dari portal.</p>
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1">Dokumen</p>
                <p class="text-gray-500">Akses dokumen penting yang diunggah oleh admin: SK, sertifikat, pengumuman resmi, dan dokumen lainnya.</p>
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1">Pengumuman</p>
                <p class="text-gray-500">Baca pengumuman terbaru dari pihak pesantren. Pengumuman penting biasanya juga dikirim via WhatsApp ke orang tua.</p>
            </div>
        </div>
    </div>

    <!-- Tips -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-green-700 flex items-center gap-3"><i class="fa-solid fa-lightbulb text-white text-lg"></i><h4 class="font-bold text-white text-lg">Tips Penggunaan</h4></div>
        <div class="p-6 space-y-3 text-sm text-gray-600">
            <ul class="list-disc list-inside space-y-2 text-gray-500">
                <li>Periksa portal secara rutin, minimal seminggu sekali, untuk memantau nilai dan tagihan.</li>
                <li>Segera laporkan ke admin jika ada data yang tidak sesuai (nama salah, nilai tidak muncul, dll).</li>
                <li>Jangan bagikan username dan password ke teman. Setiap akun bertanggung jawab atas aktivitasnya.</li>
                <li>Gunakan browser terbaru (Chrome/Firefox) untuk tampilan terbaik.</li>
                <li>Jika portal lambat, coba refresh halaman atau bersihkan cache browser.</li>
            </ul>
        </div>
    </div>

    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
