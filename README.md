<div align="center">

# 🎓 SIAKAD PRO - Pesantren Thawalib Parabek

**Sistem Informasi Akademik Berbasis Web Terintegrasi**

![Siakad Pro](https://img.shields.io/badge/SIAKAD-PRO-blue?style=for-the-badge) ![CodeIgniter 4](https://img.shields.io/badge/CodeIgniter-4-ef4223?style=for-the-badge&logo=codeigniter) ![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-38bdf8?style=for-the-badge&logo=tailwindcss) ![Docker](https://img.shields.io/badge/Docker-Ready-2496ed?style=for-the-badge&logo=docker)

Siakad Pro adalah platform Sistem Informasi Akademik yang dirancang khusus untuk memodernisasi dan mengotomatisasi pengelolaan data akademik serta operasional di **Pondok Pesantren Sumatera Thawalib Parabek**.

</div>

---

## 🚀 Fitur Utama

Sistem ini menawarkan serangkaian fitur komprehensif yang dirancang untuk mendukung seluruh ekosistem pendidikan pesantren:

- 👥 **Manajemen Santri Terpusat** - Pengelolaan data pribadi, riwayat pendidikan, dan status aktif santri secara real-time.
- 👨‍🏫 **Manajemen Guru & Staf** - Pencatatan data tenaga pengajar beserta penugasan kelas dan mata pelajaran.
- 📝 **Penerimaan Peserta Didik Baru (PPDB)** - Sistem pendaftaran online terintegrasi dengan validasi data dan manajemen kuota.
- 📊 **Pengelolaan Nilai & Rapor** - Pencatatan nilai ujian, tugas, serta pembuatan rapor akademik cetak secara otomatis.
- 🗓️ **Penjadwalan Otomatis** - Pengaturan jadwal kelas, mata pelajaran, dan plotting ruangan secara efisien.
- 💰 **Manajemen Keuangan** - Pemantauan status tagihan SPP, pendaftaran, dan histori pembayaran santri.

---

## 🛠️ Stack Teknologi

Proyek ini dibangun menggunakan teknologi modern untuk memastikan performa yang cepat dan keamanan yang optimal:
- **Backend:** PHP 8.1, CodeIgniter 4
- **Frontend:** HTML5, Alpine.js, Tailwind CSS
- **Database:** MariaDB 10.6
- **Web Server:** Nginx 1.24
- **Infrastruktur:** Docker & Kubernetes (Helm)

---

## 📦 Panduan Instalasi (Deployment)

Proyek ini telah sepenuhnya dikonfigurasi menggunakan arsitektur container (Docker) untuk memastikan deployment yang konsisten dan bebas hambatan di berbagai lingkungan (AutoDev Ready).

### 1. Persiapan
Pastikan sistem Anda telah memiliki **Docker** dan **Docker Compose** terinstal.

### 2. Menjalankan Aplikasi
Cukup gunakan satu perintah untuk membangun dan menjalankan seluruh layanan (Web Server, Database, dan Aplikasi):

```bash
docker compose up -d --build
```

### 3. Mengakses Aplikasi
Setelah container berjalan, buka browser dan akses alamat berikut:
```text
http://localhost/
```

---

## 🔐 Kredensial Akses (Default)

Gunakan kredensial berikut untuk masuk sebagai administrator sistem (Super Admin):

| Role | Username / Email | Password |
| :--- | :--- | :--- |
| **Super Admin** | `admin` / `admin@siakad.com` | `password` |

*(Sangat disarankan untuk mengubah password default segera setelah instalasi berhasil di lingkungan produksi)*

---

## 🤝 Kontribusi & Bantuan

Sistem ini terus dikembangkan secara aktif. Jika Anda menemukan *bug* atau memiliki saran fitur baru, silakan hubungi tim pengembang internal Pesantren Thawalib Parabek.

<div align="center">
  <p>&copy; 2024 Pondok Pesantren Sumatera Thawalib Parabek. Hak cipta dilindungi undang-undang.</p>
</div>
