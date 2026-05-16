<?php
// Helper: section header
function sec($id, $color, $icon, $title) {
    echo "<div id=\"$id\" class=\"bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden\">";
    echo "<div class=\"px-6 py-4 $color flex items-center gap-3\">";
    echo "<i class=\"fa-solid $icon text-white text-lg\"></i>";
    echo "<h4 class=\"font-bold text-white text-lg\">$title</h4>";
    echo "</div><div class=\"p-6 space-y-4 text-sm text-slate-600\">";
}
function endsec() { echo "</div></div>"; }
function step($n, $text) { echo "<div class=\"flex gap-3\"><span class=\"w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5\">$n</span><p class=\"text-slate-500\">$text</p></div>"; }
function tip($text, $color='blue') { echo "<div class=\"bg-$color-50 rounded-xl p-3 border border-$color-100 text-xs text-$color-800\"><i class=\"fa-solid fa-circle-info mr-1\"></i>$text</div>"; }
function sub($title, $desc) { echo "<div><p class=\"font-semibold text-slate-800 mb-1\">$title</p><p class=\"text-slate-500\">$desc</p></div>"; }
function grid2() { echo "<div class=\"grid grid-cols-1 md:grid-cols-2 gap-3\">"; }
function endgrid() { echo "</div>"; }
function card($icon, $color, $title, $desc) {
    echo "<div class=\"bg-slate-50 rounded-xl p-4 border border-slate-200\">";
    echo "<p class=\"font-semibold text-slate-800 mb-1\"><i class=\"fa-solid $icon text-$color-500 mr-1\"></i>$title</p>";
    echo "<p class=\"text-slate-500 text-xs\">$desc</p></div>";
}
?>

<?php sec('mulai','bg-blue-600','fa-play-circle','Memulai Sistem'); ?>
<p>Sebelum menggunakan SIAKAD PRO, pastikan konfigurasi awal berikut sudah dilakukan oleh Super Admin:</p>
<div class="space-y-2">
    <?php step(1,'<strong>Set Identitas Sekolah</strong> — Pengaturan → Identitas Sekolah. Isi nama sekolah, logo, alamat, dan kontak. Data ini tampil di rapor dan surat resmi.'); ?>
    <?php step(2,'<strong>Buat Tahun Ajaran</strong> — Master Data → Tahun Ajaran. Buat periode baru (contoh: 2025/2026 Ganjil) lalu klik <em>Aktifkan</em>. Hanya satu tahun ajaran yang bisa aktif.'); ?>
    <?php step(3,'<strong>Buat Data Kelas</strong> — Master Data → Data Kelas. Tambahkan kelas beserta jenjang (MTS/MA/PDF) dan kapasitas.'); ?>
    <?php step(4,'<strong>Buat Mata Pelajaran</strong> — Master Data → Mata Pelajaran. Tambahkan semua mapel beserta KKM dan jenjang.'); ?>
    <?php step(5,'<strong>Tambah Data Guru</strong> — Kepegawaian → Data Guru. Input data guru yang akan mengajar.'); ?>
    <?php step(6,'<strong>Input Data Siswa</strong> — bisa manual via Kesiswaan → Data Siswa, atau otomatis dari PPDB.'); ?>
    <?php step(7,'<strong>Buat Jadwal Pelajaran</strong> — Akademik → Jadwal Pelajaran. Assign guru ke kelas dan mapel per hari/jam.'); ?>
    <?php step(8,'<strong>Atur Bobot Penilaian</strong> — Akademik → Bobot Penilaian. Set proporsi Harian/UTS/UAS (total harus 100%).'); ?>
</div>
<?php tip('Gunakan <strong>Mode Tampilan</strong> di sidebar untuk memfilter data per jenjang (MTS/MA/PDF/Semua).'); ?>
<?php endsec(); ?>

<?php sec('dashboard','bg-indigo-600','fa-gauge-high','Dashboard'); ?>
<p>Dashboard adalah halaman utama yang menampilkan ringkasan kondisi sekolah secara real-time.</p>
<div class="space-y-3">
    <?php sub('Widget Statistik','Menampilkan jumlah siswa aktif, guru, tagihan belum bayar, dan absensi hari ini. Klik widget untuk navigasi cepat ke halaman terkait.'); ?>
    <?php sub('Mode Tampilan (Scope)','Dropdown di sidebar untuk filter data per jenjang. Pilih MTS, MA, PDF, atau Semua Jenjang. Pengaturan ini mempengaruhi data di seluruh halaman.'); ?>
    <?php sub('Grafik & Chart','Menampilkan tren absensi bulanan, distribusi nilai, dan rekap keuangan. Data diperbarui otomatis setiap halaman dimuat.'); ?>
</div>
<?php endsec(); ?>

<?php sec('masterdata','bg-slate-600','fa-database','Master Data'); ?>
<div class="space-y-4">
    <?php sub('Tahun Ajaran','Kelola periode akademik. Klik <strong>Aktifkan</strong> untuk menjadikan satu periode sebagai tahun ajaran berjalan. Semua data (jadwal, nilai, tagihan) terikat ke tahun ajaran aktif.'); ?>
    <?php sub('Data Kelas','Tambah dan edit kelas. Setiap kelas memiliki jenjang, kapasitas, dan wali kelas. Kelas digunakan sebagai dasar pembuatan jadwal dan pengelompokan siswa.'); ?>
    <?php sub('Mata Pelajaran','Daftar semua mapel beserta KKM (Kriteria Ketuntasan Minimal). KKM digunakan untuk menentukan status Tuntas/Remedial di rapor.'); ?>
    <?php sub('Bobot Penilaian','Proporsi nilai Harian, UTS, dan UAS dalam perhitungan nilai akhir. Contoh: Harian 40% + UTS 30% + UAS 30% = 100%.'); ?>
    <?php sub('Kalender Akademik','Agenda, hari libur, dan jadwal ujian sepanjang tahun ajaran. Dapat difilter dan dicetak.'); ?>
</div>
<?php endsec(); ?>

<?php sec('siswa','bg-green-600','fa-users','Manajemen Siswa'); ?>
<div class="space-y-4">
    <?php sub('Tambah Siswa Manual','Kesiswaan → Data Siswa → Tambah. Isi data lengkap: nama, NIS, NISN, kelas, tanggal lahir, alamat, dan data orang tua. NIS digunakan sebagai username login siswa, tanggal lahir sebagai password awal.'); ?>
    <?php sub('Edit & Nonaktifkan','Klik nama siswa untuk melihat detail. Gunakan tombol Edit untuk mengubah data, atau ubah status ke GRADUATED/MOVED/DROPPED untuk menonaktifkan.'); ?>
    <?php sub('Absensi Siswa','Kesiswaan → Absensi Siswa. Pilih kelas dan tanggal, lalu input status per siswa (H/S/I/A). Absensi yang sudah diinput bisa diedit di hari yang sama.'); ?>
    <?php sub('Data Orang Tua','Kesiswaan → Data Wali Murid. Edit data ayah, ibu, dan wali. Nomor HP wali digunakan untuk notifikasi WhatsApp otomatis.'); ?>
    <?php sub('Pelacakan Santri','Kesiswaan → Pelacakan Santri. Lihat riwayat lengkap seorang siswa: absensi, nilai, pelanggaran, kesehatan, dan pembayaran dalam satu halaman.'); ?>
    <?php sub('Prestasi & Konseling','Catat prestasi akademik/non-akademik dan sesi konseling BK. Data ini masuk ke rapor holistik siswa.'); ?>
</div>
<?php tip('Untuk import siswa massal, gunakan fitur PPDB — pendaftar yang diterima dapat dikonversi langsung menjadi siswa aktif.','green'); ?>
<?php endsec(); ?>

<?php sec('akademik','bg-yellow-500','fa-book-open','Akademik & Nilai'); ?>
<div class="space-y-4">
    <?php sub('Jadwal Pelajaran','Akademik → Jadwal Pelajaran. Buat jadwal per kelas: pilih hari, jam mulai-selesai, mata pelajaran, dan guru. Jadwal ini yang tampil di portal siswa dan orang tua.'); ?>
    <?php sub('Input Nilai','Akademik → Input Nilai. Pilih kelas dan mata pelajaran, lalu input nilai per siswa (UH, Tugas, Quiz, UTS, UAS). Jumlah UH/Tugas tidak terbatas. Nilai akhir dihitung otomatis berdasarkan bobot yang sudah diset.'); ?>
    <?php sub('Jurnal Mengajar','Guru wajib mengisi jurnal setiap selesai mengajar: materi yang diajarkan, kehadiran siswa, dan catatan. Jurnal ini menjadi bukti pelaksanaan KBM.'); ?>
    <?php sub('Kenaikan Kelas','Akademik → Kenaikan Kelas. Proses kenaikan kelas massal di akhir tahun ajaran. Sistem akan otomatis memindahkan siswa ke kelas berikutnya berdasarkan nilai dan kehadiran.'); ?>
    <?php sub('Wali Kelas','Akademik → Set Wali Kelas. Assign guru sebagai wali kelas. Wali kelas dapat mengakses laporan khusus kelasnya di menu Laporan Wali Kelas.'); ?>
    <?php sub('Silabus & RPP','Akademik → Silabus & RPP. Guru dapat mengupload dokumen silabus dan RPP per mata pelajaran dan semester.'); ?>
    <?php sub('SK Mengajar','Akademik → SK Mengajar. Kelola surat keputusan penugasan mengajar guru per tahun ajaran.'); ?>
    <?php sub('Dispensasi KBM','Akademik → Dispensasi KBM. Proses izin tidak mengajar untuk guru beserta penugasan guru pengganti.'); ?>
</div>
<div class="bg-yellow-50 rounded-xl p-4 border border-yellow-100">
    <p class="font-semibold text-yellow-800 mb-2">Alur Kerja Nilai:</p>
    <div class="flex flex-wrap gap-2 text-xs text-yellow-700">
        <span class="bg-white px-2 py-1 rounded border border-yellow-200">1. Set Bobot</span>
        <span class="text-yellow-400">→</span>
        <span class="bg-white px-2 py-1 rounded border border-yellow-200">2. Buat Jadwal</span>
        <span class="text-yellow-400">→</span>
        <span class="bg-white px-2 py-1 rounded border border-yellow-200">3. Input Nilai</span>
        <span class="text-yellow-400">→</span>
        <span class="bg-white px-2 py-1 rounded border border-yellow-200">4. Cetak Rapor</span>
    </div>
</div>
<?php endsec(); ?>

<?php sec('keuangan','bg-orange-500','fa-dollar-sign','Keuangan'); ?>
<div class="space-y-4">
    <?php sub('Jenis Tagihan','Keuangan → Jenis Tagihan. Buat kategori tagihan: SPP Bulanan, Uang Gedung, Biaya Ujian, dll. Setiap jenis memiliki nominal dan periode.'); ?>
    <?php sub('Data SPP','Keuangan → Data SPP. Generate tagihan massal untuk semua siswa atau per kelas berdasarkan jenis tagihan yang sudah dibuat.'); ?>
    <?php sub('Kasir / Pembayaran','Keuangan → Kasir. Cari siswa, pilih tagihan yang akan dibayar, input nominal, dan proses pembayaran. Sistem otomatis mencetak kwitansi dan mengubah status tagihan menjadi LUNAS.'); ?>
    <?php sub('Laporan Keuangan','Rekap pemasukan per periode, per jenis tagihan, dan per kelas. Dapat difilter dan diekspor.'); ?>
    <?php sub('Laporan Bendahara','Laporan detail untuk bendahara: semua transaksi harian, mingguan, dan bulanan beserta saldo.'); ?>
    <?php sub('Biaya Lain-lain & Fasilitas','Kelola tagihan non-rutin seperti biaya kegiatan, seragam, atau fasilitas khusus.'); ?>
</div>
<?php tip('Tagihan yang sudah LUNAS tidak dapat dihapus untuk menjaga integritas data keuangan. Hubungi Super Admin jika ada koreksi.','orange'); ?>
<?php endsec(); ?>

<?php sec('kepesantren','bg-purple-600','fa-moon','Kepesantrenan'); ?>
<div class="space-y-4">
    <?php sub('Data Asrama','Kepesantrenan → Data Asrama. Kelola kamar asrama: nama, kapasitas, jenis (putra/putri), dan penempatan santri. Satu santri hanya bisa di satu kamar.'); ?>
    <?php sub('Wali Asrama','Assign guru/staff sebagai wali asrama yang bertanggung jawab atas kamar tertentu.'); ?>
    <?php sub('Perizinan','Kepesantrenan → Perizinan. Proses pengajuan izin santri: keluar, pulang, atau sakit. Admin/wali asrama dapat menyetujui atau menolak. Status: PENDING → APPROVED/REJECTED → RETURNED.'); ?>
    <?php sub('Tahfidz','Catat progress hafalan Al-Quran santri per juz, per surah. Rekap progress tersedia di laporan.'); ?>
    <?php sub('Jurnal Kitab','Catat kegiatan pengajian kitab: nama kitab, halaman yang dipelajari, dan catatan ustadz.'); ?>
    <?php sub('Jadwal Kegiatan','Kelola jadwal kegiatan harian asrama: sholat berjamaah, muhadharah, olahraga, dll.'); ?>
    <?php sub('Mutasi Kamar','Proses perpindahan santri antar kamar asrama beserta alasan dan tanggal efektif.'); ?>
    <?php sub('Rapor Asrama','Cetak rapor kepesantrenan yang berisi rekap kegiatan, pelanggaran, dan perkembangan santri selama satu semester.'); ?>
    <?php sub('Poskestren','Kepesantrenan → Poskestren. Rekam kunjungan santri ke pos kesehatan: keluhan, diagnosis, tindakan, dan status (rawat jalan/inap/rujuk RS).'); ?>
</div>
<?php endsec(); ?>

<?php sec('ppdb','bg-teal-600','fa-user-plus','PPDB Online'); ?>
<div class="space-y-4">
    <?php sub('Konfigurasi PPDB','PPDB → Konfigurasi. Aktifkan/nonaktifkan pendaftaran online, set kuota, persyaratan dokumen, dan pesan sambutan.'); ?>
    <?php sub('Jalur Pendaftaran','PPDB → Atur Jalur. Buat jalur masuk: Reguler, Prestasi, Beasiswa, dll. Setiap jalur memiliki kuota dan persyaratan berbeda.'); ?>
    <?php sub('Periode PPDB','PPDB → Atur Periode. Set tanggal buka-tutup pendaftaran. Pendaftaran otomatis tertutup setelah tanggal berakhir atau kuota penuh.'); ?>
    <?php sub('Data Pendaftar','PPDB → Data Pendaftar. Lihat semua pendaftar, verifikasi berkas, ubah status (PENDING → VERIFIED → ACCEPTED/REJECTED), dan konversi pendaftar diterima menjadi siswa aktif.'); ?>
    <?php sub('Cek Status Pendaftar','Calon siswa dapat mengecek status pendaftaran mandiri via halaman publik /cek-status menggunakan nomor pendaftaran.'); ?>
</div>
<?php tip('Pendaftar yang statusnya ACCEPTED dapat langsung dikonversi menjadi siswa aktif dengan klik tombol "Jadikan Siswa" di halaman detail pendaftar.','teal'); ?>
<?php endsec(); ?>

<?php sec('kepegawaian','bg-slate-700','fa-briefcase','Kepegawaian'); ?>
<div class="space-y-4">
    <?php sub('Data Guru','Kepegawaian → Data Guru. Kelola data lengkap guru: NIP, pendidikan, bidang studi, kontak, dan status aktif. Akun login guru dibuat terpisah di Pengaturan → Manajemen User.'); ?>
    <?php sub('Data Staff','Kepegawaian → Data Staff. Kelola data tenaga kependidikan non-guru: TU, keamanan, kebersihan, dll.'); ?>
    <?php sub('Master Jabatan','Buat struktur jabatan: Kepala Sekolah, Wakasek, Wali Kelas, Bendahara, dll.'); ?>
    <?php sub('Struktur Organisasi','Visualisasi hierarki organisasi sekolah berdasarkan jabatan yang sudah dibuat.'); ?>
    <?php sub('Absensi Pegawai','Kepegawaian → Absensi Pegawai. Input kehadiran guru dan staff harian. Rekap absensi pegawai tersedia di laporan.'); ?>
    <?php sub('Alumni','Kepegawaian → Alumni. Kelola data alumni pesantren: tahun lulus, pendidikan lanjutan, dan pekerjaan.'); ?>
</div>
<?php endsec(); ?>

<?php sec('laporan','bg-red-600','fa-print','Laporan & Rapor'); ?>
<div class="space-y-4">
    <?php sub('Rapor Akademik','Akademik → Cetak Rapor. Pilih kelas dan semester, lalu cetak rapor per siswa atau massal. Format A4, berisi nilai semua mapel, rekap absensi, dan catatan wali kelas.'); ?>
    <?php sub('Rapor Asrama','Kepesantrenan → Rapor Asrama. Rapor khusus kepesantrenan berisi rekap kegiatan, pelanggaran, tahfidz, dan perkembangan santri.'); ?>
    <?php sub('Laporan Wali Kelas','Laporan → Laporan Wali Kelas. Rekap nilai dan absensi per kelas untuk keperluan rapat wali kelas.'); ?>
    <?php sub('Laporan Siswa','Laporan → Data Siswa. Ekspor data siswa aktif, alumni, dan mutasi dalam format tabel.'); ?>
    <?php sub('Laporan Keuangan','Keuangan → Laporan Keuangan. Rekap pemasukan per periode dengan filter jenis tagihan dan kelas.'); ?>
    <?php sub('Laporan Boarding','Kepesantrenan → Rapor Asrama. Rekap kegiatan asrama, perizinan, dan kesehatan santri per periode.'); ?>
    <?php sub('Laporan Ekstrakurikuler','Ekstrakurikuler → Rapor Ekskul. Rekap kehadiran dan perkembangan anggota per kegiatan ekstrakurikuler.'); ?>
</div>
<?php tip('Pastikan semua data sudah lengkap sebelum mencetak rapor. Rapor yang sudah dicetak tidak dapat diubah secara otomatis — edit data terlebih dahulu lalu cetak ulang.','red'); ?>
<?php endsec(); ?>

<?php sec('pengaturan','bg-slate-600','fa-gear','Pengaturan Sistem'); ?>
<div class="space-y-4">
    <?php sub('Identitas Sekolah','Nama, logo, alamat, NPSN, dan kontak sekolah. Data ini tampil di header rapor, surat, dan halaman publik PPDB.'); ?>
    <?php sub('Manajemen User','Pengaturan → Manajemen User. Tambah akun baru, edit role, reset password, dan nonaktifkan akun. Setiap user memiliki satu role yang menentukan menu yang bisa diakses.'); ?>
    <?php sub('Manajemen Role & Hak Akses','Pengaturan → Manajemen Role. Atur menu mana saja yang bisa diakses oleh setiap role. Perubahan berlaku langsung tanpa perlu logout.'); ?>
    <?php sub('Manajemen Menu','Pengaturan → Manajemen Menu. Tambah, edit, urutan, dan nonaktifkan item menu sidebar. Menu yang dinonaktifkan tidak tampil untuk semua role.'); ?>
    <?php sub('WhatsApp API','Pengaturan → WhatsApp API. Konfigurasi integrasi WhatsApp untuk notifikasi otomatis: tagihan jatuh tempo, absensi, dan pengumuman ke orang tua.'); ?>
    <?php sub('Template Surat','Pengaturan → Template Surat. Buat dan edit template surat resmi yang dapat dicetak dari sistem.'); ?>
</div>
<?php tip('Hanya Super Admin yang dapat mengakses Manajemen Role dan Manajemen User. Jangan berikan akses Super Admin ke lebih dari 2 orang.','slate'); ?>
<?php endsec(); ?>

<?php sec('tips','bg-amber-500','fa-lightbulb','Tips & FAQ'); ?>
<div class="space-y-4">
    <div>
        <p class="font-semibold text-slate-800 mb-3">Pertanyaan yang Sering Ditanyakan:</p>
        <div class="space-y-3">
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                <p class="font-semibold text-slate-700 text-xs mb-1">❓ Siswa tidak bisa login?</p>
                <p class="text-slate-500 text-xs">Pastikan NIS sudah terdaftar di sistem dan password (tanggal lahir format DDMMYYYY) sudah benar. Reset password via Pengaturan → Manajemen User.</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                <p class="font-semibold text-slate-700 text-xs mb-1">❓ Nilai tidak muncul di rapor?</p>
                <p class="text-slate-500 text-xs">Pastikan: (1) Tahun ajaran sudah aktif, (2) Jadwal pelajaran sudah dibuat, (3) Guru sudah menginput nilai via menu Input Nilai.</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                <p class="font-semibold text-slate-700 text-xs mb-1">❓ Menu tidak muncul untuk role tertentu?</p>
                <p class="text-slate-500 text-xs">Cek Pengaturan → Manajemen Role → klik role yang bersangkutan → centang menu yang ingin diaktifkan.</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                <p class="font-semibold text-slate-700 text-xs mb-1">❓ Notifikasi WhatsApp tidak terkirim?</p>
                <p class="text-slate-500 text-xs">Cek status koneksi di Pengaturan → WhatsApp API. Pastikan layanan WhatsApp Gateway aktif dan nomor HP orang tua sudah diisi dengan benar di data siswa.</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                <p class="font-semibold text-slate-700 text-xs mb-1">❓ Bagaimana cara backup data?</p>
                <p class="text-slate-500 text-xs">Lakukan backup database secara berkala via panel server atau hubungi tim IT. SIAKAD PRO tidak menyediakan fitur backup otomatis bawaan.</p>
            </div>
        </div>
    </div>
    <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
        <p class="font-semibold text-amber-800 mb-2"><i class="fa-solid fa-shield-halved mr-1"></i>Keamanan Sistem</p>
        <ul class="list-disc list-inside space-y-1 text-xs text-amber-700">
            <li>Ubah password default <code class="bg-white px-1 rounded">password</code> segera setelah instalasi.</li>
            <li>Jangan bagikan kredensial Super Admin ke pihak yang tidak berwenang.</li>
            <li>Logout setelah selesai menggunakan sistem, terutama di komputer bersama.</li>
            <li>Lakukan backup database minimal seminggu sekali.</li>
        </ul>
    </div>
</div>
<?php endsec(); ?>
