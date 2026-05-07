<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-8 pb-24">
    <!-- Header Area / Profile Banner -->
    <div class="relative bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-md overflow-hidden mb-8">
        <div class="absolute inset-0 opacity-20">
            <!-- Decorative pattern -->
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
                <defs>
                    <pattern id="pattern" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M0 40L40 0H20L0 20M40 40V20L20 40" fill="white" fill-opacity="0.2"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#pattern)"/>
            </svg>
        </div>

        <div class="relative p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <!-- Profile Picture -->
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-white/30 bg-white overflow-hidden shadow-xl flex-shrink-0">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($student['name'] ?? $student['full_name'] ?? 'User') ?>&background=random&size=128" alt="Profile" class="w-full h-full object-cover">
                </div>
                <!-- User Info -->
                <div class="text-center md:text-left text-white">
                    <h1 class="text-2xl md:text-3xl font-bold"><?= htmlspecialchars($student['name'] ?? $student['full_name'] ?? 'Nama Tidak Diketahui') ?></h1>
                    <p class="text-blue-100 mt-1 opacity-90"><i class="fa-solid fa-graduation-cap mr-2"></i><?= isset($student['nisn']) && $student['nisn'] ? 'NISN: '.$student['nisn'] : 'Calon Santri' ?></p>
                </div>
            </div>

            <div>
                <button class="bg-white/20 hover:bg-white/30 backdrop-blur border border-white/30 text-white px-5 py-2.5 rounded-lg font-medium transition flex items-center gap-2">
                    <i class="fa-solid fa-camera"></i> Change Profile Picture
                </button>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- CARD 1: Riwayat Pendidikan -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-lg">
                    <i class="fa-solid fa-school"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-800">Riwayat Pendidikan</h2>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Sekolah Asal</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($student['school_origin'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">NPSN Sekolah Asal</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($student['school_origin_npsn'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">NISN</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($student['nisn'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">No. KK</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($student['no_kk'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-gray-500 text-sm">NIK</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($student['nik'] ?? '-') ?></span>
                </div>
            </div>
        </div>

        <!-- CARD 2: Data Calon Santri -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                <div class="w-10 h-10 bg-green-50 text-green-600 rounded-lg flex items-center justify-center text-lg">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-800">Data <?= isset($student['nisn']) ? 'Santri' : 'Calon Santri' ?></h2>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Nama Lengkap</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($student['name'] ?? $student['full_name'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Tempat Lahir</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($student['place_of_birth'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Tanggal Lahir</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($student['date_of_birth'] ? date('d-m-Y', strtotime($student['date_of_birth'])) : '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Jenis Kelamin</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= ($student['gender'] ?? '-') == 'L' ? 'Laki-laki' : 'Perempuan' ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Alamat</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($student['address'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Anak Ke</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($student['birth_order'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-gray-500 text-sm">Jml Bersaudara</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($student['number_of_siblings'] ?? '-') ?></span>
                </div>
            </div>
        </div>

        <!-- CARD 3: Data Orang Tua (Ayah) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center text-lg">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Data Orang Tua</h2>
                    <p class="text-xs text-gray-500 font-bold uppercase">Ayah</p>
                </div>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Nama</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($father['name'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">NIK</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($father['nik'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Nomor HP</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($father['phone_number'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Tempat Lahir</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($father['place_of_birth'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Tanggal Lahir</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($father['date_of_birth'] ? date('d-m-Y', strtotime($father['date_of_birth'])) : '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Alamat</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($father['address'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Pendidikan</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($father['education'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Pekerjaan</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($father['job'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-gray-500 text-sm">Pendapatan</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($father['income_per_month'] ?? '-') ?></span>
                </div>
            </div>
        </div>

        <!-- CARD 4: Data Orang Tua (Ibu) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                <div class="w-10 h-10 bg-pink-50 text-pink-600 rounded-lg flex items-center justify-center text-lg">
                    <i class="fa-solid fa-person-dress"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Data Orang Tua</h2>
                    <p class="text-xs text-gray-500 font-bold uppercase">Ibu</p>
                </div>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Nama</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($mother['name'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">NIK</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($mother['nik'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Nomor HP</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($mother['phone_number'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Tempat Lahir</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($mother['place_of_birth'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Tanggal Lahir</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($mother['date_of_birth'] ? date('d-m-Y', strtotime($mother['date_of_birth'])) : '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Alamat</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($mother['address'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Pendidikan</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($mother['education'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                    <span class="text-gray-500 text-sm">Pekerjaan</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($mother['job'] ?? '-') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-gray-500 text-sm">Pendapatan</span>
                    <span class="col-span-2 font-medium text-gray-800"><?= htmlspecialchars($mother['income_per_month'] ?? '-') ?></span>
                </div>
            </div>
        </div>

    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
