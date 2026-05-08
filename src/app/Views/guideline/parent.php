<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <div class="mb-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center shrink-0"><i class="fa-solid fa-book-open text-2xl"></i></div>
        <div>
            <h3 class="text-2xl font-extrabold text-gray-800">Panduan Portal Orang Tua</h3>
            <p class="text-gray-500 text-sm mt-0.5">Panduan lengkap memantau perkembangan putra/putri Anda melalui SIAKAD PRO</p>
        </div>
    </div>
    <div class="max-w-3xl space-y-5">

    <!-- Login -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-teal-700 flex items-center gap-3"><i class="fa-solid fa-right-to-bracket text-white text-lg"></i><h4 class="font-bold text-white text-lg">Login ke Portal Orang Tua</h4></div>
        <div class="p-6 space-y-3 text-sm text-gray-600">
            <p>Akses portal orang tua melalui <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs font-mono">/login</code> menggunakan username dan password yang diberikan oleh pihak pesantren.</p>
            <div class="bg-teal-50 rounded-xl p-4 border border-teal-100 space-y-2 text-xs text-teal-800">
                <p><strong>Belum punya akun?</strong> Hubungi admin pesantren dengan menyebutkan nama lengkap dan nama anak yang bersekolah di sini.</p>
                <p><strong>Lupa password?</strong> Gunakan fitur "Lupa Password" di halaman login, atau hubungi admin untuk reset.</p>
            </div>
            <p class="text-gray-500 text-xs">Satu akun orang tua dapat terhubung ke lebih dari satu anak jika memiliki lebih dari satu santri di pesantren ini. Gunakan tombol pilih anak di bagian atas setiap halaman untuk berpindah antar anak.</p>
        </div>
    </div>

    <!-- Dashboard -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-teal-700 flex items-center gap-3"><i class="fa-solid fa-gauge-high text-white text-lg"></i><h4 class="font-bold text-white text-lg">Dashboard</h4></div>
        <div class="p-6 space-y-3 text-sm text-gray-600">
            <p>Halaman utama menampilkan kartu untuk setiap anak yang terdaftar. Setiap kartu berisi nama, NIS, kelas, dan shortcut cepat ke menu-menu penting.</p>
            <div class="grid grid-cols-3 gap-2 text-xs text-center">
                <div class="bg-blue-50 rounded-lg p-2 text-blue-700"><i class="fa-solid fa-clipboard-check block text-lg mb-1"></i>Absensi</div>
                <div class="bg-yellow-50 rounded-lg p-2 text-yellow-700"><i class="fa-solid fa-chart-bar block text-lg mb-1"></i>Nilai</div>
                <div class="bg-orange-50 rounded-lg p-2 text-orange-700"><i class="fa-solid fa-file-invoice-dollar block text-lg mb-1"></i>Tagihan</div>
                <div class="bg-red-50 rounded-lg p-2 text-red-700"><i class="fa-solid fa-triangle-exclamation block text-lg mb-1"></i>Disiplin</div>
                <div class="bg-green-50 rounded-lg p-2 text-green-700"><i class="fa-solid fa-house block text-lg mb-1"></i>Asrama</div>
                <div class="bg-pink-50 rounded-lg p-2 text-pink-700"><i class="fa-solid fa-heart-pulse block text-lg mb-1"></i>Kesehatan</div>
            </div>
            <p class="text-gray-500 text-xs">Klik salah satu shortcut untuk langsung menuju informasi anak tersebut.</p>
        </div>
    </div>

    <!-- Absensi -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-teal-700 flex items-center gap-3"><i class="fa-solid fa-clipboard-check text-white text-lg"></i><h4 class="font-bold text-white text-lg">Absensi</h4></div>
        <div class="p-6 space-y-3 text-sm text-gray-600">
            <p>Pantau kehadiran anak di sekolah setiap hari. Gunakan filter bulan di bagian atas untuk melihat rekap bulan tertentu.</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs text-center">
                <div class="bg-green-50 rounded-lg p-3 border border-green-100"><p class="font-bold text-green-700 text-lg">H</p><p class="text-green-600">Hadir</p></div>
                <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-100"><p class="font-bold text-yellow-700 text-lg">S</p><p class="text-yellow-600">Sakit</p></div>
                <div class="bg-blue-50 rounded-lg p-3 border border-blue-100"><p class="font-bold text-blue-700 text-lg">I</p><p class="text-blue-600">Izin</p></div>
                <div class="bg-red-50 rounded-lg p-3 border border-red-100"><p class="font-bold text-red-700 text-lg">A</p><p class="text-red-600">Alpha</p></div>
            </div>
            <div class="bg-orange-50 rounded-xl p-3 border border-orange-100 text-xs text-orange-800">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>Jika anak sering Alpha (tidak hadir tanpa keterangan), segera hubungi wali kelas untuk klarifikasi. Alpha berlebihan dapat mempengaruhi kenaikan kelas.
            </div>
            <p class="text-gray-500 text-xs">Jika ada ketidaksesuaian data absensi (anak hadir tapi tercatat Alpha), segera laporkan ke wali kelas dengan menyebutkan tanggal yang bersangkutan.</p>
        </div>
    </div>

    <!-- Nilai -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-teal-700 flex items-center gap-3"><i class="fa-solid fa-chart-bar text-white text-lg"></i><h4 class="font-bold text-white text-lg">Nilai Akademik</h4></div>
        <div class="p-6 space-y-3 text-sm text-gray-600">
            <p>Lihat nilai anak per mata pelajaran untuk tahun ajaran aktif. Tabel nilai menampilkan:</p>
            <ul class="list-disc list-inside space-y-1.5 text-gray-500">
                <li><strong>Tugas</strong> — nilai rata-rata tugas harian dan ulangan harian.</li>
                <li><strong>UTS</strong> — nilai Ujian Tengah Semester.</li>
                <li><strong>UAS</strong> — nilai Ujian Akhir Semester.</li>
                <li><strong>Nilai Akhir</strong> — hasil perhitungan berdasarkan bobot yang ditetapkan sekolah.</li>
                <li><strong>KKM</strong> — Kriteria Ketuntasan Minimal per mata pelajaran.</li>
                <li><strong>Status</strong> — Tuntas (nilai ≥ KKM) atau Remedial (nilai &lt; KKM).</li>
            </ul>
            <div class="bg-blue-50 rounded-xl p-3 border border-blue-100 text-xs text-blue-800">
                <i class="fa-solid fa-info-circle mr-1"></i>Nilai belum muncul? Berarti guru belum menginput nilai untuk mata pelajaran tersebut. Tanyakan ke wali kelas atau guru yang bersangkutan.
            </div>
            <p class="text-gray-500 text-xs">Jika anak mendapat status Remedial di banyak mata pelajaran, segera diskusikan dengan wali kelas untuk mencari solusi belajar yang tepat.</p>
        </div>
    </div>

    <!-- Pembayaran -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-teal-700 flex items-center gap-3"><i class="fa-solid fa-file-invoice-dollar text-white text-lg"></i><h4 class="font-bold text-white text-lg">Pembayaran</h4></div>
        <div class="p-6 space-y-3 text-sm text-gray-600">
            <p>Pantau status tagihan SPP dan biaya lainnya. Halaman ini menampilkan dua bagian:</p>
            <div class="space-y-3">
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                    <p class="font-semibold text-gray-800 mb-1">Daftar Tagihan</p>
                    <p class="text-gray-500 text-xs">Semua tagihan yang dibebankan kepada anak beserta status pembayarannya. Tagihan berstatus <span class="bg-red-100 text-red-700 px-1.5 py-0.5 rounded font-bold">UNPAID</span> perlu segera dilunasi.</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                    <p class="font-semibold text-gray-800 mb-1">Riwayat Pembayaran</p>
                    <p class="text-gray-500 text-xs">Daftar pembayaran yang sudah dilakukan beserta tanggal dan jumlah yang dibayarkan.</p>
                </div>
            </div>
            <div class="bg-orange-50 rounded-xl p-3 border border-orange-100 text-xs text-orange-800">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i><strong>Penting:</strong> Pembayaran dilakukan langsung ke bagian keuangan pesantren (bukan melalui portal ini). Jika sudah membayar tapi status masih UNPAID, tunjukkan kwitansi ke bagian keuangan untuk konfirmasi.
            </div>
        </div>
    </div>

    <!-- Kedisiplinan -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-teal-700 flex items-center gap-3"><i class="fa-solid fa-triangle-exclamation text-white text-lg"></i><h4 class="font-bold text-white text-lg">Kedisiplinan</h4></div>
        <div class="p-6 space-y-3 text-sm text-gray-600">
            <p>Lihat catatan pelanggaran anak beserta detail dan total poin pelanggaran yang terakumulasi.</p>
            <div class="grid grid-cols-3 gap-2 text-xs text-center">
                <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-100"><p class="font-bold text-yellow-700">RINGAN</p><p class="text-yellow-600 mt-1">Poin kecil, peringatan lisan</p></div>
                <div class="bg-orange-50 rounded-lg p-3 border border-orange-100"><p class="font-bold text-orange-700">SEDANG</p><p class="text-orange-600 mt-1">Poin sedang, surat peringatan</p></div>
                <div class="bg-red-50 rounded-lg p-3 border border-red-100"><p class="font-bold text-red-700">BERAT</p><p class="text-red-600 mt-1">Poin besar, panggilan orang tua</p></div>
            </div>
            <div class="bg-red-50 rounded-xl p-3 border border-red-100 text-xs text-red-800">
                <i class="fa-solid fa-info-circle mr-1"></i>Jika total poin pelanggaran tinggi, pihak pesantren akan menghubungi orang tua. Segera koordinasikan dengan wali kelas atau BK untuk pembinaan lebih lanjut.
            </div>
            <p class="text-gray-500 text-xs">Jika ada catatan pelanggaran yang tidak sesuai atau perlu klarifikasi, hubungi wali kelas atau bagian kesiswaan.</p>
        </div>
    </div>

    <!-- Asrama -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-teal-700 flex items-center gap-3"><i class="fa-solid fa-house text-white text-lg"></i><h4 class="font-bold text-white text-lg">Asrama</h4></div>
        <div class="p-6 space-y-3 text-sm text-gray-600">
            <div>
                <p class="font-semibold text-gray-800 mb-1">Informasi Kamar</p>
                <p class="text-gray-500">Nama asrama, kapasitas kamar, dan jenis (putra/putri). Jika anak belum ditempatkan di asrama, hubungi bagian kepesantrenan.</p>
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1">Riwayat Perizinan</p>
                <p class="text-gray-500">Daftar pengajuan izin anak: keluar lingkungan, pulang ke rumah, atau izin sakit. Status izin:</p>
                <div class="grid grid-cols-2 gap-2 mt-2 text-xs">
                    <div class="bg-yellow-50 rounded-lg p-2 border border-yellow-100 text-yellow-700"><strong>Menunggu</strong> — belum diproses wali asrama</div>
                    <div class="bg-green-50 rounded-lg p-2 border border-green-100 text-green-700"><strong>Disetujui</strong> — izin diterima</div>
                    <div class="bg-red-50 rounded-lg p-2 border border-red-100 text-red-700"><strong>Ditolak</strong> — izin tidak disetujui</div>
                    <div class="bg-blue-50 rounded-lg p-2 border border-blue-100 text-blue-700"><strong>Kembali</strong> — anak sudah kembali ke asrama</div>
                </div>
            </div>
            <p class="text-gray-500 text-xs"><i class="fa-solid fa-info-circle text-teal-400 mr-1"></i>Pengajuan izin dilakukan oleh anak langsung ke wali asrama, bukan melalui portal ini.</p>
        </div>
    </div>

    <!-- Kesehatan -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-teal-700 flex items-center gap-3"><i class="fa-solid fa-heart-pulse text-white text-lg"></i><h4 class="font-bold text-white text-lg">Kesehatan</h4></div>
        <div class="p-6 space-y-3 text-sm text-gray-600">
            <p>Riwayat kunjungan anak ke Poskestren (Pos Kesehatan Pesantren). Setiap rekaman berisi:</p>
            <ul class="list-disc list-inside space-y-1 text-gray-500">
                <li><strong>Tanggal</strong> kunjungan</li>
                <li><strong>Keluhan</strong> yang disampaikan anak</li>
                <li><strong>Diagnosis</strong> dari petugas kesehatan</li>
                <li><strong>Tindakan</strong> yang diberikan (obat, istirahat, dll)</li>
                <li><strong>Status</strong>: Rawat Jalan / Rawat Inap / Rujuk RS</li>
                <li><strong>Nama petugas</strong> yang menangani</li>
            </ul>
            <div class="bg-red-50 rounded-xl p-3 border border-red-100 text-xs text-red-800">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>Jika status anak <strong>Rujuk RS</strong>, pihak pesantren akan segera menghubungi orang tua. Pastikan nomor HP Anda selalu aktif dan terdaftar di data siswa.
            </div>
        </div>
    </div>

    <!-- Jadwal & Pengumuman -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-teal-700 flex items-center gap-3"><i class="fa-solid fa-calendar-days text-white text-lg"></i><h4 class="font-bold text-white text-lg">Jadwal & Pengumuman</h4></div>
        <div class="p-6 space-y-4 text-sm text-gray-600">
            <div>
                <p class="font-semibold text-gray-800 mb-1">Jadwal Pelajaran</p>
                <p class="text-gray-500">Jadwal pelajaran anak per hari dalam seminggu sesuai kelasnya. Menampilkan nama mata pelajaran, jam mulai-selesai, dan nama guru. Berguna untuk memantau apakah anak belajar sesuai jadwal.</p>
            </div>
            <div>
                <p class="font-semibold text-gray-800 mb-1">Pengumuman</p>
                <p class="text-gray-500">Pengumuman resmi dari pihak pesantren yang ditujukan untuk orang tua dan wali santri. Periksa halaman ini secara rutin untuk informasi terbaru tentang kegiatan sekolah, libur, dan acara penting.</p>
            </div>
        </div>
    </div>

    <!-- Tips -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-teal-700 flex items-center gap-3"><i class="fa-solid fa-lightbulb text-white text-lg"></i><h4 class="font-bold text-white text-lg">Tips untuk Orang Tua</h4></div>
        <div class="p-6 space-y-3 text-sm text-gray-600">
            <ul class="list-disc list-inside space-y-2 text-gray-500">
                <li>Periksa portal minimal <strong>seminggu sekali</strong> untuk memantau perkembangan anak secara menyeluruh.</li>
                <li>Pastikan <strong>nomor HP</strong> Anda yang terdaftar di data siswa selalu aktif untuk menerima notifikasi WhatsApp.</li>
                <li>Jika ada informasi yang tidak sesuai, segera hubungi <strong>wali kelas</strong> atau <strong>admin pesantren</strong>.</li>
                <li>Gunakan data di portal sebagai bahan diskusi dengan anak saat pulang ke rumah.</li>
                <li>Jangan bagikan username dan password portal ke pihak lain.</li>
            </ul>
            <div class="bg-teal-50 rounded-xl p-4 border border-teal-100">
                <p class="font-semibold text-teal-800 mb-2">Kontak Penting:</p>
                <p class="text-teal-700 text-xs">Untuk pertanyaan teknis terkait portal, hubungi admin pesantren. Untuk pertanyaan akademik, hubungi wali kelas. Untuk pertanyaan kepesantrenan, hubungi wali asrama.</p>
            </div>
        </div>
    </div>

    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
