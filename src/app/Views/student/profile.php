<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">

    <?php if (isset($is_candidate) && $is_candidate === true): ?>
        
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-800">Data Diri Santri</h1>
                <p class="text-sm text-gray-600">Biodata lengkap pendaftaran.</p>
            </div>
            <div class="self-start md:self-auto">
                <span class="px-3 py-1.5 rounded-full text-xs md:text-sm font-bold border 
                    <?= ($candidate['registration_status'] ?? '') == 'APPROVED' 
                        ? 'bg-green-100 text-green-700 border-green-200' 
                        : 'bg-yellow-100 text-yellow-700 border-yellow-200' ?>">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    Status: <?= $candidate['registration_status'] ?? 'PENDING' ?>
                </span>
            </div>
        </div>

        <?php \App\Core\Session::flash(); ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="md:col-span-1 space-y-6">
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-green-700 to-green-600"></div>
                    
                    <div class="relative z-10 text-center mt-8">
                        <div class="w-28 h-28 mx-auto bg-white rounded-full flex items-center justify-center p-1 shadow-lg mb-3">
                            <div class="w-full h-full bg-gray-100 rounded-full flex items-center justify-center overflow-hidden">
                                <i class="fa-solid fa-user text-5xl text-gray-300"></i>
                            </div>
                        </div>
                        
                        <h2 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($candidate['full_name'] ?? 'Tanpa Nama') ?></h2>
                        <p class="text-xs text-gray-500 font-mono mt-1">
                            NISN: <?= htmlspecialchars($candidate['nisn'] ?? '-') ?>
                        </p>

                        <div class="mt-4 flex justify-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-green-50 text-green-700 text-xs font-bold border border-green-100">
                                <i class="fa-solid fa-road mr-2"></i>
                                <?= htmlspecialchars($candidate['track_name'] ?? '-') ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Akun Login</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="text-xs text-gray-400 block mb-1">Username</span>
                            <div class="flex items-center justify-between bg-gray-50 p-2 rounded border border-gray-200">
                                <span class="font-mono text-sm font-bold text-gray-700">
                                    <?= htmlspecialchars($candidate['account_username'] ?? '-') ?>
                                </span>
                                <i class="fa-solid fa-key text-gray-400 text-xs"></i>
                            </div>
                        </div>
                        
                        <div>
                            <span class="text-xs text-gray-400 block mb-1">Email Terdaftar</span>
                            <div class="flex items-center text-gray-700">
                                <i class="fa-solid fa-envelope text-blue-500 mr-2"></i>
                                <span class="text-sm truncate w-full">
                                    <?= htmlspecialchars($candidate['account_email'] ?? '-') ?>
                                </span>
                            </div>
                        </div>

                        <div>
                            <span class="text-xs text-gray-400 block mb-1">Tanggal Daftar</span>
                            <div class="flex items-center text-gray-700">
                                <i class="fa-solid fa-calendar-check text-green-600 mr-2"></i>
                                <span class="text-sm">
                                    <?= !empty($candidate['created_at']) ? date('d F Y', strtotime($candidate['created_at'])) : '-' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 md:p-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-6 flex items-center">
                        <div class="w-8 h-8 rounded bg-green-100 text-green-600 flex items-center justify-center mr-3 text-sm">
                            <i class="fa-solid fa-address-card"></i>
                        </div>
                        Informasi Pribadi
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div class="group">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">NIK</label>
                            <input type="text" value="<?= $candidate['nik'] ?? '-' ?>" readonly 
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-gray-800 text-sm focus:outline-none cursor-default group-hover:bg-gray-100 transition">
                        </div>
                        <div class="group">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tempat Lahir</label>
                            <input type="text" value="<?= $candidate['birth_place'] ?? '-' ?>" readonly 
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-gray-800 text-sm focus:outline-none cursor-default group-hover:bg-gray-100 transition">
                        </div>
                        <div class="group">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Lahir</label>
                            <input type="text" 
                                value="<?= !empty($candidate['birth_date']) ? date('d-m-Y', strtotime($candidate['birth_date'])) : '-' ?>" 
                                readonly 
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-gray-800 text-sm focus:outline-none cursor-default group-hover:bg-gray-100 transition">
                        </div>
                        <div class="group">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jenis Kelamin</label>
                            <input type="text" value="<?= ($candidate['gender'] ?? '') == 'L' ? 'Laki-laki' : 'Perempuan' ?>" readonly 
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-gray-800 text-sm focus:outline-none cursor-default group-hover:bg-gray-100 transition">
                        </div>
                        <div class="group md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No. WhatsApp</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-brands fa-whatsapp text-green-500"></i>
                                </div>
                                <input type="text" value="<?= $candidate['phone'] ?? '-' ?>" readonly 
                                    class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 text-gray-800 text-sm focus:outline-none cursor-default">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 md:p-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-6 flex items-center">
                        <div class="w-8 h-8 rounded bg-blue-100 text-blue-600 flex items-center justify-center mr-3 text-sm">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        Data Orang Tua & Sekolah
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Ayah</label>
                            <input type="text" value="<?= $candidate['father_name'] ?? '-' ?>" readonly 
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-gray-800 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Ibu</label>
                            <input type="text" value="<?= $candidate['mother_name'] ?? '-' ?>" readonly 
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-gray-800 text-sm">
                        </div>
                    </div>

                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                        <label class="block text-xs font-bold text-blue-800 uppercase mb-1">Asal Sekolah</label>
                        <p class="font-bold text-gray-800 text-lg"><?= $candidate['previous_school'] ?? '-' ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?= $candidate['school_address'] ?? '-' ?></p>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg text-sm flex items-start">
                    <i class="fa-solid fa-circle-info mt-0.5 mr-3 text-lg"></i>
                    <div>
                        <p class="font-bold">Info Perubahan Data</p>
                        <p class="text-xs mt-1">Jika terdapat kesalahan data pada biodata di atas, silakan hubungi panitia melalui WhatsApp atau gunakan fitur Pusat Bantuan.</p>
                    </div>
                </div>
            </div>
        </div>


    <?php elseif (isset($student)): ?>
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-800">Profil Saya</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 relative overflow-hidden">
            <div class="flex flex-col md:flex-row items-center gap-6 relative z-10">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center text-3xl text-gray-400 shadow-inner">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="text-center md:text-left">
                    <h2 class="text-2xl font-bold text-gray-800"><?= $student['full_name'] ?? 'Siswa' ?></h2>
                    <p class="text-gray-500 font-mono">NIS: <?= $student['nis'] ?? '-' ?></p>
                    <div class="mt-3 flex flex-wrap justify-center md:justify-start gap-2">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-lg text-xs font-bold"><?= $student['class_name'] ?? 'Tanpa Kelas' ?></span>
                        <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-lg text-xs font-bold"><?= $student['dorm_name'] ?? 'Non-Asrama' ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
