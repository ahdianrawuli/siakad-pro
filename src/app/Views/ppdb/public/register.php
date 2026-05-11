
<?php require __DIR__ . '/../../layouts/public_header.php'; ?>

<!-- Hero Banner Register -->
<div class="relative bg-gray-900 py-14 overflow-hidden">
    <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?w=1600&q=80&auto=format&fit=crop"
         alt="bg" class="absolute inset-0 w-full h-full object-cover opacity-25">
    <div class="absolute inset-0 bg-gradient-to-b from-gray-900/60 to-gray-900/80"></div>
    <div class="relative z-10 max-w-2xl mx-auto px-4 text-center">
        <span class="inline-block bg-white/10 border border-white/20 text-white text-xs font-bold tracking-widest uppercase px-4 py-1.5 rounded-full mb-4">PPDB 2025/2026</span>
        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Formulir Pendaftaran</h1>
        <p class="text-gray-300 text-sm">Isi data diri Anda dengan lengkap dan benar · <a href="/prosedur" class="text-green-400 hover:underline">Lihat prosedur</a></p>
    </div>
</div>

<div class="bg-gray-50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto space-y-8">

        <?php \App\Core\Session::flash(); ?>

        <!-- Stepper Navigation -->
        <div class="flex items-center justify-between" id="tab-nav">
            <?php
            $steps = [
                ['id' => 'tab-admin',   'label' => 'Administrasi', 'icon' => 'fa-id-card'],
                ['id' => 'tab-calon',   'label' => 'Data Calon',   'icon' => 'fa-user'],
                ['id' => 'tab-riwayat', 'label' => 'Pendidikan',   'icon' => 'fa-school'],
                ['id' => 'tab-ortu',    'label' => 'Orang Tua',    'icon' => 'fa-people-roof'],
            ];
            foreach ($steps as $i => $step):
            ?>
            <button type="button" class="tab-btn flex-1 flex flex-col items-center gap-1.5 py-3 group <?= $i === 0 ? 'active-step' : '' ?>" data-target="<?= $step['id'] ?>">
                <div class="step-circle w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-all
                    <?= $i === 0 ? 'bg-green-600 border-green-600 text-white' : 'bg-white border-gray-300 text-gray-400 group-hover:border-green-400' ?>">
                    <i class="fa-solid <?= $step['icon'] ?>"></i>
                </div>
                <span class="text-xs font-bold hidden sm:block <?= $i === 0 ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-600' ?> transition-colors step-label"><?= $step['label'] ?></span>
            </button>
            <?php if ($i < count($steps) - 1): ?>
            <div class="step-line flex-1 h-0.5 bg-gray-200 mb-5 max-w-[60px]"></div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <form class="mt-6 space-y-6" action="/register/process" method="POST" id="register-form">
            <?= \App\Core\Csrf::input() ?>

            <!-- Tab A: Administrasi -->
            <div id="tab-admin" class="tab-content block">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-green-500 px-6 py-4 flex items-center gap-3">
                        <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-id-card text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-base">A. Administrasi</h3>
                            <p class="text-green-100 text-xs">Pilih unit pendidikan dan buat akun</p>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Unit Pendidikan</label>
                        <select name="education_unit" id="education_unit" class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500" required onchange="updateJalur()">
                            <option value="">-- Pilih Unit Pendidikan --</option>
                            <option value="MTs">Madrasah Tsanawiyah (MTs/SMP)</option>
                            <option value="MA">Madrasah Aliyah (MA/SMA)</option>
                            <option value="PDF">Pendidikan Diniyah Formal (PDF)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jalur Pendaftaran</label>
                        <select name="ppdb_track_id" id="ppdb_track" class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500 bg-gray-50" required>
                            <option value="">-- Pilih Unit Pendidikan Dulu --</option>
                        </select>
                        <input type="hidden" name="track_name" id="track_name">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No WhatsApp (Grup Calon Santri)</label>
                        <input type="number" name="phone" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500" placeholder="0812xxxx">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Aktif (Untuk Login)</label>
                        <input type="email" name="email" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password</label>
                            <input type="password" name="password" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ulangi Password</label>
                            <input type="password" name="password_confirm" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                        </div>
                    </div>
                </div>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="button" class="next-btn inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition shadow" data-target="tab-calon">Selanjutnya <i class="fa-solid fa-arrow-right text-sm"></i></button>
                </div>
            </div>

            <!-- Tab B: Data Calon Santri -->
            <div id="tab-calon" class="tab-content hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4 flex items-center gap-3">
                        <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-user text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-base">B. Data Calon Santri</h3>
                            <p class="text-blue-100 text-xs">Identitas dan alamat calon santri</p>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Lengkap</label>
                            <input type="text" name="full_name" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jenis Kelamin</label>
                            <select name="gender" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tempat Lahir</label>
                            <input type="text" name="birth_place" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Lahir</label>
                            <input type="date" name="birth_date" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alamat (Jalan, RT/RW)</label>
                            <textarea name="address" required rows="2" class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Provinsi</label>
                            <select id="sel_province" class="wilayah-select block w-full" required>
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                            <input type="hidden" name="province" id="inp_province">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kabupaten/Kota</label>
                            <select id="sel_city" class="wilayah-select block w-full" required disabled>
                                <option value="">-- Pilih Provinsi Dulu --</option>
                            </select>
                            <input type="hidden" name="city" id="inp_city">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kecamatan</label>
                            <select id="sel_district" class="wilayah-select block w-full" required disabled>
                                <option value="">-- Pilih Kab/Kota Dulu --</option>
                            </select>
                            <input type="hidden" name="district" id="inp_district">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kelurahan/Desa</label>
                            <select id="sel_village" class="wilayah-select block w-full" required disabled>
                                <option value="">-- Pilih Kecamatan Dulu --</option>
                            </select>
                            <input type="hidden" name="village" id="inp_village">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kode Pos</label>
                            <input type="text" name="postal_code" id="inp_postal_code" required class="block w-full px-3 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none" placeholder="Contoh: 26181">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Anak Ke-</label>
                            <input type="number" name="child_order" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jumlah Bersaudara</label>
                            <input type="number" name="siblings_count" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sumber Info Pendaftaran</label>
                            <select name="info_source" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                                <option value="">Pilih Sumber Info</option>
                                <option value="INSTAGRAM">INSTAGRAM</option>
                                <option value="FACEBOOK">FACEBOOK</option>
                                <option value="YOUTUBE">YOUTUBE</option>
                                <option value="TIKTOK">TIKTOK</option>
                                <option value="WEBSITE">WEBSITE</option>
                                <option value="WHATSAPP">WHATSAPP</option>
                                <option value="BROSUR">BROSUR</option>
                                <option value="BALIHO SPANDUK">BALIHO SPANDUK</option>
                                <option value="ALUMNI">ALUMNI</option>
                                <option value="KELUARGA SANTRI">KELUARGA SANTRI</option>
                                <option value="LAINNYA">LAINNYA</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 mt-4 p-4 bg-gray-50 rounded-lg">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Apakah Ada Saudara yang Sedang Mondok di Ponpes Parabek?</label>
                            <select name="has_sibling_in_ponpes" id="has_sibling" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" onchange="toggleSiblingFields()">
                                <option value="TIDAK">Tidak Ada</option>
                                <option value="YA">Ada</option>
                            </select>

                            <div id="sibling_fields" class="hidden mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Saudara</label>
                                    <input type="text" name="sibling_name" id="sibling_name" class="block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kelas Saudara</label>
                                    <input type="text" name="sibling_class" id="sibling_class" class="block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                </div>
                <div class="flex justify-between mt-4">
                    <button type="button" class="prev-btn inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition" data-target="tab-admin"><i class="fa-solid fa-arrow-left text-sm"></i> Kembali</button>
                    <button type="button" class="next-btn inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition shadow" data-target="tab-riwayat">Selanjutnya <i class="fa-solid fa-arrow-right text-sm"></i></button>
                </div>
            </div>

            <!-- Tab C: Riwayat Pendidikan -->
            <div id="tab-riwayat" class="tab-content hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-500 px-6 py-4 flex items-center gap-3">
                        <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-school text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-base">C. Riwayat Pendidikan</h3>
                            <p class="text-purple-100 text-xs">Data sekolah asal dan nomor identitas</p>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sekolah Asal</label>
                            <input type="text" name="previous_school" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">NPSN Sekolah Asal (8 Digit)</label>
                            <input type="number" name="npsn" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">NISN (10 Digit)</label>
                            <input type="number" name="nisn" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No KK (16 Digit)</label>
                            <input type="number" name="kk_number" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">NIK (16 Digit)</label>
                            <input type="number" name="nik" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <input type="hidden" name="school_address" value="-">
                    </div>
                </div>
                </div>
                <div class="flex justify-between mt-4">
                    <button type="button" class="prev-btn inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition" data-target="tab-calon"><i class="fa-solid fa-arrow-left text-sm"></i> Kembali</button>
                    <button type="button" class="next-btn inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition shadow" data-target="tab-ortu">Selanjutnya <i class="fa-solid fa-arrow-right text-sm"></i></button>
                </div>
            </div>

            <!-- Tab D: Orang Tua -->
            <div id="tab-ortu" class="tab-content hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-400 px-6 py-4 flex items-center gap-3">
                        <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-people-roof text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-base">D. Data Orang Tua / Wali</h3>
                            <p class="text-orange-100 text-xs">Informasi orang tua atau wali santri</p>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pilih yang diisi:</label>
                        <select name="parent_type" id="parent_type" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" onchange="toggleParentType()">
                            <option value="AYAH_IBU">Ayah & Ibu</option>
                            <option value="WALI">Wali</option>
                        </select>
                    </div>

                    <div id="ayah_ibu_fields" class="space-y-6">
                        <!-- Ayah -->
                        <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                            <h4 class="font-bold text-blue-800 mb-3 border-b border-blue-200 pb-2">Data Ayah</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Ayah</label>
                                    <input type="text" name="father_name" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pendidikan</label>
                                    <select name="father_education" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Pilih --</option>
                                        <option value="SD">SD</option><option value="SMP">SMP / Sederajat</option><option value="SMA">SMA / Sederajat</option><option value="D1">D1 (Diploma I)</option><option value="D2">D2 (Diploma II)</option><option value="D3">D3 (Diploma III)</option><option value="D4">D4 (Diploma IV)</option><option value="S1">S1 (Sarjana)</option><option value="S2">S2 (Master)</option><option value="S3">S3 (Doktor)</option><option value="TIDAK_SEKOLAH">Tidak Sekolah</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">NIK (16 Digit)</label>
                                    <input type="number" name="father_nik" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No Handphone</label>
                                    <input type="number" name="father_phone" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                                    <input type="email" name="father_email" class="block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tempat Lahir</label>
                                    <input type="text" name="father_birth_place" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Lahir</label>
                                    <input type="date" name="father_birth_date" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pekerjaan</label>
                                    <select name="father_job" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Pilih --</option>
                                        <option value="GURU">GURU</option><option value="DOKTER">DOKTER</option><option value="PETANI">PETANI</option><option value="NELAYAN">NELAYAN</option><option value="PETERNAK">PETERNAK</option><option value="PNS">PNS</option><option value="PEGAWAI_SWASTA">PEGAWAI SWASTA</option><option value="PEDAGANG">PEDAGANG</option><option value="BURUH_PABRIK">BURUH PABRIK</option><option value="TNI">TNI</option><option value="POLRI">POLRI</option><option value="PARUH_WAKTU">PARUH WAKTU</option><option value="IBU_RUMAH_TANGGA">IBU RUMAH TANGGA</option><option value="LAINNYA">LAINNYA</option><option value="ALMARHUM">ALMARHUM</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Penghasilan</label>
                                    <select name="father_income" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Pilih --</option>
                                        <option value="BELOW_500K">< Rp 500.000</option><option value="BETWEEN_500K_1M">Rp 500.000 – Rp 1.000.000</option><option value="BETWEEN_1M_2M">Rp 1.000.001 – Rp 2.000.000</option><option value="BETWEEN_2M_3M">Rp 2.000.001 – Rp 3.000.000</option><option value="BETWEEN_3M_5M">Rp 3.000.001 – Rp 5.000.000</option><option value="ABOVE_5M">> Rp 5.000.000</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Provinsi Ayah</label>
                                    <input type="text" name="father_province" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kab/Kota Ayah</label>
                                    <input type="text" name="father_city" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kecamatan Ayah</label>
                                    <input type="text" name="father_district" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kelurahan Ayah</label>
                                    <input type="text" name="father_village" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kode Pos</label>
                                    <input type="text" name="father_postal_code" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alamat Ayah (Jalan, RT/RW)</label>
                                    <textarea name="father_address" rows="2" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Ibu -->
                        <div class="p-4 border border-pink-200 rounded-lg bg-pink-50">
                            <h4 class="font-bold text-pink-800 mb-3 border-b border-pink-200 pb-2">Data Ibu</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Ibu</label>
                                    <input type="text" name="mother_name" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pendidikan</label>
                                    <select name="mother_education" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-pink-500 focus:border-pink-500">
                                        <option value="">-- Pilih --</option>
                                        <option value="SD">SD</option><option value="SMP">SMP / Sederajat</option><option value="SMA">SMA / Sederajat</option><option value="D1">D1 (Diploma I)</option><option value="D2">D2 (Diploma II)</option><option value="D3">D3 (Diploma III)</option><option value="D4">D4 (Diploma IV)</option><option value="S1">S1 (Sarjana)</option><option value="S2">S2 (Master)</option><option value="S3">S3 (Doktor)</option><option value="TIDAK_SEKOLAH">Tidak Sekolah</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">NIK (16 Digit)</label>
                                    <input type="number" name="mother_nik" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No Handphone</label>
                                    <input type="number" name="mother_phone" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                                    <input type="email" name="mother_email" class="block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tempat Lahir</label>
                                    <input type="text" name="mother_birth_place" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Lahir</label>
                                    <input type="date" name="mother_birth_date" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pekerjaan</label>
                                    <select name="mother_job" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-pink-500 focus:border-pink-500">
                                        <option value="">-- Pilih --</option>
                                        <option value="GURU">GURU</option><option value="DOKTER">DOKTER</option><option value="PETANI">PETANI</option><option value="NELAYAN">NELAYAN</option><option value="PETERNAK">PETERNAK</option><option value="PNS">PNS</option><option value="PEGAWAI_SWASTA">PEGAWAI SWASTA</option><option value="PEDAGANG">PEDAGANG</option><option value="BURUH_PABRIK">BURUH PABRIK</option><option value="TNI">TNI</option><option value="POLRI">POLRI</option><option value="PARUH_WAKTU">PARUH WAKTU</option><option value="IBU_RUMAH_TANGGA">IBU RUMAH TANGGA</option><option value="LAINNYA">LAINNYA</option><option value="ALMARHUM">ALMARHUM</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Penghasilan</label>
                                    <select name="mother_income" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-pink-500 focus:border-pink-500">
                                        <option value="">-- Pilih --</option>
                                        <option value="BELOW_500K">< Rp 500.000</option><option value="BETWEEN_500K_1M">Rp 500.000 – Rp 1.000.000</option><option value="BETWEEN_1M_2M">Rp 1.000.001 – Rp 2.000.000</option><option value="BETWEEN_2M_3M">Rp 2.000.001 – Rp 3.000.000</option><option value="BETWEEN_3M_5M">Rp 3.000.001 – Rp 5.000.000</option><option value="ABOVE_5M">> Rp 5.000.000</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Provinsi Ibu</label>
                                    <input type="text" name="mother_province" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kab/Kota Ibu</label>
                                    <input type="text" name="mother_city" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kecamatan Ibu</label>
                                    <input type="text" name="mother_district" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kelurahan Ibu</label>
                                    <input type="text" name="mother_village" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kode Pos</label>
                                    <input type="text" name="mother_postal_code" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alamat Ibu (Jalan, RT/RW)</label>
                                    <textarea name="mother_address" rows="2" class="parent-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-pink-500 focus:border-pink-500"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wali -->
                    <div id="wali_fields" class="hidden p-4 border border-purple-200 rounded-lg bg-purple-50">
                        <h4 class="font-bold text-purple-800 mb-3 border-b border-purple-200 pb-2">Data Wali</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Wali</label>
                                <input type="text" name="guardian_name" class="wali-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jenis Kelamin</label>
                                <select name="guardian_gender" class="wali-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500">
                                    <option value="L">Laki-Laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pendidikan</label>
                                <select name="guardian_education" class="wali-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">-- Pilih --</option>
                                    <option value="SD">SD</option><option value="SMP">SMP / Sederajat</option><option value="SMA">SMA / Sederajat</option><option value="D1">D1 (Diploma I)</option><option value="D2">D2 (Diploma II)</option><option value="D3">D3 (Diploma III)</option><option value="D4">D4 (Diploma IV)</option><option value="S1">S1 (Sarjana)</option><option value="S2">S2 (Master)</option><option value="S3">S3 (Doktor)</option><option value="TIDAK_SEKOLAH">Tidak Sekolah</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">NIK (16 Digit)</label>
                                <input type="number" name="guardian_nik" class="wali-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No Handphone</label>
                                <input type="number" name="guardian_phone" class="wali-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                                <input type="email" name="guardian_email" class="block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tempat Lahir</label>
                                <input type="text" name="guardian_birth_place" class="wali-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Lahir</label>
                                <input type="date" name="guardian_birth_date" class="wali-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pekerjaan</label>
                                <select name="guardian_job" class="wali-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">-- Pilih --</option>
                                    <option value="GURU">GURU</option><option value="DOKTER">DOKTER</option><option value="PETANI">PETANI</option><option value="NELAYAN">NELAYAN</option><option value="PETERNAK">PETERNAK</option><option value="PNS">PNS</option><option value="PEGAWAI_SWASTA">PEGAWAI SWASTA</option><option value="PEDAGANG">PEDAGANG</option><option value="BURUH_PABRIK">BURUH PABRIK</option><option value="TNI">TNI</option><option value="POLRI">POLRI</option><option value="PARUH_WAKTU">PARUH WAKTU</option><option value="IBU_RUMAH_TANGGA">IBU RUMAH TANGGA</option><option value="LAINNYA">LAINNYA</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Penghasilan</label>
                                <select name="guardian_income" class="wali-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">-- Pilih --</option>
                                    <option value="BELOW_500K">< Rp 500.000</option><option value="BETWEEN_500K_1M">Rp 500.000 – Rp 1.000.000</option><option value="BETWEEN_1M_2M">Rp 1.000.001 – Rp 2.000.000</option><option value="BETWEEN_2M_3M">Rp 2.000.001 – Rp 3.000.000</option><option value="BETWEEN_3M_5M">Rp 3.000.001 – Rp 5.000.000</option><option value="ABOVE_5M">> Rp 5.000.000</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Provinsi Wali</label>
                                <input type="text" name="guardian_province" class="wali-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kab/Kota Wali</label>
                                <input type="text" name="guardian_city" class="wali-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kecamatan Wali</label>
                                <input type="text" name="guardian_district" class="wali-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kelurahan Wali</label>
                                <input type="text" name="guardian_village" class="wali-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kode Pos</label>
                                <input type="text" name="guardian_postal_code" class="wali-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alamat Wali (Jalan, RT/RW)</label>
                                <textarea name="guardian_address" rows="2" class="wali-req block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-purple-500 focus:border-purple-500"></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                </div>
                <div class="flex justify-between mt-4">
                    <button type="button" class="prev-btn inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition" data-target="tab-riwayat"><i class="fa-solid fa-arrow-left text-sm"></i> Kembali</button>
                    <button type="button" id="btn-final-submit" class="inline-flex items-center gap-2 px-8 py-2.5 bg-green-700 text-white font-bold rounded-xl hover:bg-green-800 shadow-lg transition">
                        <i class="fa-solid fa-paper-plane"></i> DAFTAR SEKARANG
                    </button>
                </div>
            </div>
            
            <div class="text-center mt-6">
                <a href="/login" class="text-sm font-bold text-green-600 hover:text-green-500">Sudah punya akun? Login disini</a>
            </div>
        </form>

        <!-- Terms and Conditions Modal Overlay -->
        <div id="terms-modal-overlay" class="fixed inset-0 z-[100] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">

                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xl font-bold text-gray-900">Syarat & Ketentuan Pendaftaran</h3>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="px-6 py-6 overflow-y-auto flex-grow text-gray-700 space-y-5 text-sm md:text-base leading-relaxed">
                    <p class="font-semibold text-gray-800">
                        Dengan mengisi formulir pendaftaran ini, Anda menyatakan telah membaca, memahami, dan menyetujui syarat dan ketentuan berikut:
                    </p>

                    <ol class="list-decimal pl-5 space-y-4 marker:text-santri marker:font-bold">
                        <li>
                            <strong>Kebenaran Data</strong><br>
                            Anda menyatakan bahwa seluruh data yang diisi dalam formulir ini adalah benar dan dapat dipertanggungjawabkan. Kesalahan atau ketidaklengkapan data dapat berakibat pada pembatalan pendaftaran.
                        </li>
                        <li>
                            <strong>Proses Seleksi</strong><br>
                            Pendaftaran tidak menjamin penerimaan. Seluruh calon santri akan melalui proses seleksi sesuai dengan ketentuan yang berlaku di Pesantren.
                        </li>
                        <li>
                            <strong>Dokumen Pendukung</strong><br>
                            Anda diwajibkan untuk mengunggah seluruh dokumen pendukung yang diminta. Dokumen yang tidak lengkap atau tidak valid dapat mempengaruhi status pendaftaran.
                        </li>
                        <li>
                            <strong>Pembayaran Biaya Pendaftaran</strong><br>
                            Biaya pendaftaran yang telah dibayarkan tidak dapat dikembalikan, kecuali dalam kondisi tertentu yang ditentukan oleh pihak pesantren.
                        </li>
                        <li>
                            <strong>Privasi dan Keamanan Data</strong><br>
                            Data yang Anda berikan akan digunakan hanya untuk keperluan proses seleksi dan administrasi. Kami menjamin bahwa data Anda akan disimpan dengan aman dan tidak akan disebarluaskan kepada pihak ketiga tanpa izin.
                        </li>
                        <li>
                            <strong>Keputusan Penerimaan</strong><br>
                            Keputusan penerimaan santri baru sepenuhnya berada di tangan panitia penerimaan santri baru Pesantren dan bersifat final.
                        </li>
                    </ol>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-5 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 shrink-0">
                    <button type="button" id="btn-batal-terms" class="px-5 py-2.5 rounded-xl font-bold text-gray-600 bg-white border border-gray-300 hover:bg-gray-100 transition shadow-sm">
                        Batal
                    </button>
                    <button type="button" id="btn-setuju-terms" class="px-5 py-2.5 rounded-xl font-bold text-white bg-santri hover:bg-santri-dark transition shadow-md flex items-center justify-center gap-2">
                        <span>Setuju & Mendaftar</span>
                    </button>
                </div>
            </div>
        </div>

        <script>
            // Tab Logic
            const tabs = ['tab-admin', 'tab-calon', 'tab-riwayat', 'tab-ortu'];
            const buttons = document.querySelectorAll('.tab-btn');
            const contents = document.querySelectorAll('.tab-content');
            const stepColors = ['green', 'blue', 'purple', 'orange'];
            const unlockedTabs = new Set(['tab-admin']); // only first tab unlocked initially

            function updateStepperUI(activeId) {
                buttons.forEach((b, i) => {
                    const circle = b.querySelector('.step-circle');
                    const label = b.querySelector('.step-label');
                    const isActive = b.dataset.target === activeId;
                    const isUnlocked = unlockedTabs.has(b.dataset.target);
                    const color = stepColors[i] || 'green';

                    if (isActive) {
                        circle.className = `step-circle w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-all bg-${color}-600 border-${color}-600 text-white`;
                        circle.innerHTML = `<i class="fa-solid ${['fa-id-card','fa-user','fa-school','fa-people-roof'][i]}"></i>`;
                        if (label) label.className = `text-xs font-bold hidden sm:block text-${color}-600 transition-colors step-label`;
                        b.style.cursor = 'default';
                    } else if (isUnlocked) {
                        circle.className = `step-circle w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-all bg-${color}-100 border-${color}-400 text-${color}-600`;
                        circle.innerHTML = `<i class="fa-solid fa-check text-sm"></i>`;
                        if (label) label.className = `text-xs font-bold hidden sm:block text-${color}-500 transition-colors step-label`;
                        b.style.cursor = 'pointer';
                    } else {
                        circle.className = 'step-circle w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-all bg-white border-gray-200 text-gray-300';
                        circle.innerHTML = `<i class="fa-solid fa-lock text-xs"></i>`;
                        if (label) label.className = 'text-xs font-bold hidden sm:block text-gray-300 transition-colors step-label';
                        b.style.cursor = 'not-allowed';
                    }
                });
            }

            function switchTab(targetId) {
                contents.forEach(c => {
                    c.classList.toggle('hidden', c.id !== targetId);
                    c.classList.toggle('block', c.id === targetId);
                });
                updateStepperUI(targetId);
            }

            // Validate all required fields in a tab content div
            function validateTab(tabId) {
                const tabEl = document.getElementById(tabId);
                const fields = tabEl.querySelectorAll('input[required], select[required], textarea[required]');
                for (const f of fields) {
                    if (!f.checkValidity()) {
                        f.reportValidity();
                        return false;
                    }
                }
                return true;
            }

            // Stepper click — only if unlocked
            buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                    if (unlockedTabs.has(btn.dataset.target)) {
                        switchTab(btn.dataset.target);
                    }
                });
            });

            // Next button — validate current tab first, then unlock next
            document.querySelectorAll('.next-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const currentTab = document.querySelector('.tab-content.block, .tab-content:not(.hidden)').id;
                    if (validateTab(currentTab)) {
                        unlockedTabs.add(btn.dataset.target);
                        switchTab(btn.dataset.target);
                    }
                });
            });

            // Prev button — always allowed
            document.querySelectorAll('.prev-btn').forEach(btn => {
                btn.addEventListener('click', () => switchTab(btn.dataset.target));
            });

            // Init stepper UI
            updateStepperUI('tab-admin');

            // Jalur Logic — data dari DB
            const trackData = <?= json_encode(array_map(fn($t) => ['id' => (string)$t['id'], 'name' => $t['name'], 'level' => $t['level']], $tracks)) ?>;

            function updateJalur() {
                const unit = document.getElementById('education_unit').value;
                const jalurSelect = document.getElementById('ppdb_track');
                const trackNameInput = document.getElementById('track_name');

                jalurSelect.innerHTML = '';

                // Map unit ke level di ppdb_tracks
                const levelMap = { 'MTs': 'MTS', 'MA': 'MA', 'PDF': 'PDF' };
                const targetLevel = levelMap[unit] || '';

                let options = targetLevel
                    ? trackData.filter(t => t.level === targetLevel)
                    : [{id: '', name: '-- Pilih Unit Pendidikan Dulu --'}];

                if (options.length === 0) options = [{id: '', name: '-- Tidak ada jalur tersedia --'}];

                options.forEach(opt => {
                    const el = document.createElement('option');
                    el.value = opt.id;
                    el.textContent = opt.name;
                    jalurSelect.appendChild(el);
                });

                jalurSelect.classList.remove('bg-gray-50');
                if(unit === '') jalurSelect.classList.add('bg-gray-50');
                trackNameInput.value = jalurSelect.options[jalurSelect.selectedIndex]?.text || '';
            }

            document.getElementById('ppdb_track').addEventListener('change', function() {
                document.getElementById('track_name').value = this.options[this.selectedIndex].text;
            });

            // Saudara Logic
            function toggleSiblingFields() {
                const hasSibling = document.getElementById('has_sibling').value;
                const siblingFields = document.getElementById('sibling_fields');
                const inputs = siblingFields.querySelectorAll('input');

                if (hasSibling === 'YA') {
                    siblingFields.classList.remove('hidden');
                    inputs.forEach(i => i.required = true);
                } else {
                    siblingFields.classList.add('hidden');
                    inputs.forEach(i => {
                        i.required = false;
                        i.value = '';
                    });
                }
            }

            // Parent Type Logic
            function toggleParentType() {
                const type = document.getElementById('parent_type').value;
                const ayahIbu = document.getElementById('ayah_ibu_fields');
                const wali = document.getElementById('wali_fields');

                const ayahIbuReq = document.querySelectorAll('.parent-req');
                const waliReq = document.querySelectorAll('.wali-req');

                if (type === 'WALI') {
                    ayahIbu.classList.add('hidden');
                    wali.classList.remove('hidden');
                    ayahIbuReq.forEach(i => { i.required = false; i.value = ''; });
                    waliReq.forEach(i => i.required = true);
                } else {
                    ayahIbu.classList.remove('hidden');
                    wali.classList.add('hidden');
                    ayahIbuReq.forEach(i => i.required = true);
                    waliReq.forEach(i => { i.required = false; i.value = ''; });
                }
            }

            // Form Validation before submit
            document.getElementById('register-form').addEventListener('submit', function(e) {
                // Set default requirements for parents on init if not changed
                toggleParentType();

                // Clear local storage upon successful submission
                localStorage.removeItem('siakad_registration_form_data');
            });

            // Auto-Save Form Data to LocalStorage
            function initAutoSave() {
                const form = document.getElementById('register-form');
                const formElements = form.querySelectorAll('input:not([type="hidden"]):not([type="password"]), select, textarea');
                const storageKey = 'siakad_registration_form_data';

                // Restore data
                const savedData = localStorage.getItem(storageKey);
                if (savedData) {
                    try {
                        const parsedData = JSON.parse(savedData);
                        formElements.forEach(el => {
                            if (parsedData[el.name] !== undefined) {
                                if (el.type === 'radio') {
                                    if (el.value === parsedData[el.name]) {
                                        el.checked = true;
                                    }
                                } else {
                                    el.value = parsedData[el.name];
                                }
                            }
                        });

                        // Trigger dynamic UI updates based on restored values
                        if(parsedData['education_unit']) {
                            updateJalur();
                            // Need to re-select the track after options are rebuilt
                            if(parsedData['ppdb_track_id']) {
                                document.getElementById('ppdb_track').value = parsedData['ppdb_track_id'];
                                document.getElementById('track_name').value = document.getElementById('ppdb_track').options[document.getElementById('ppdb_track').selectedIndex]?.text || '';
                            }
                        }
                        if(parsedData['has_sibling_in_ponpes']) toggleSiblingFields();
                        if(parsedData['parent_type']) toggleParentType();

                    } catch (e) {
                        console.error('Error parsing saved form data', e);
                    }
                }

                // Save data on input change
                form.addEventListener('input', function(e) {
                    if (e.target.type === 'password' || e.target.type === 'hidden') return;

                    let currentData = localStorage.getItem(storageKey);
                    currentData = currentData ? JSON.parse(currentData) : {};

                    if (e.target.type === 'radio') {
                        if (e.target.checked) currentData[e.target.name] = e.target.value;
                    } else {
                        currentData[e.target.name] = e.target.value;
                    }

                    localStorage.setItem(storageKey, JSON.stringify(currentData));
                });
            }

            // Init
            toggleParentType();
            initAutoSave();

            // Force all text inputs to UPPERCASE automatically
            // Form Submission Intercept & Modal Logic
            const registerForm = document.getElementById('register-form');
            const termsModal = document.getElementById('terms-modal-overlay');
            const btnBatal = document.getElementById('btn-batal-terms');
            const btnSetuju = document.getElementById('btn-setuju-terms');
            const btnFinalSubmit = document.getElementById('btn-final-submit');
            let isSubmitting = false;

            btnFinalSubmit.addEventListener('click', function() {
                if (registerForm.checkValidity()) {
                    termsModal.classList.remove('hidden');
                } else {
                    const firstInvalid = registerForm.querySelector(':invalid');
                    if (firstInvalid) {
                        const tabContent = firstInvalid.closest('.tab-content');
                        if (tabContent) {
                            switchTab(tabContent.id);
                        }
                        registerForm.reportValidity();
                    }
                }
            });

            btnBatal.addEventListener('click', function() {
                termsModal.classList.add('hidden');
            });

            btnSetuju.addEventListener('click', function() {
                if(isSubmitting) return;

                isSubmitting = true;

                // Set loading state on button
                const btnText = btnSetuju.querySelector('span');
                const originalText = btnText.innerText;
                btnText.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...';
                btnSetuju.classList.add('opacity-70', 'cursor-not-allowed');

                // Clear localStorage draft upon successful submit intention
                localStorage.removeItem('ppdb_form_draft');

                // Actually submit the form to the backend
                registerForm.submit();
            });

            function initUppercaseInputs() {
                // Select all inputs and textareas
                const allInputs = registerForm.querySelectorAll('input, textarea');

                allInputs.forEach(input => {
                    // Exclude specific types and specific field names explicitly requested by user
                    const isEmail = input.type === 'email' || input.name.includes('email');
                    const isPassword = input.type === 'password' || input.name.includes('password');
                    const isPhoneOrNumber = input.type === 'number' || input.name === 'phone';
                    const isHiddenOrRadio = input.type === 'hidden' || input.type === 'radio' || input.type === 'checkbox';

                    if (!isEmail && !isPassword && !isPhoneOrNumber && !isHiddenOrRadio) {
                        // Apply CSS text-transform for visual immediate feedback
                        input.style.textTransform = 'uppercase';

                        input.addEventListener('input', function(e) {
                            // Save cursor position to prevent jumping
                            const start = this.selectionStart;
                            const end = this.selectionEnd;

                            this.value = this.value.toUpperCase();

                            // Restore cursor position
                            this.setSelectionRange(start, end);
                        });
                    }
                });
            }
            initUppercaseInputs();

        </script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            // Init Select2 on all selects (re-init after tab switch too)
            function initSelect2() {
                $('select').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            width: '100%',
                            minimumResultsForSearch: $(this).find('option').length > 6 ? 0 : Infinity
                        });
                        // Sync Select2 change back to native change event for existing JS logic
                        $(this).on('select2:select', function() {
                            this.dispatchEvent(new Event('change'));
                        });
                    }
                });
            }
            initSelect2();

            // ── Wilayah Bertingkat ──────────────────────────────────────────
            const WILAYAH_BASE = 'https://ibnux.github.io/data-indonesia';

            function initWilayahSelect2(selector, placeholder) {
                return $(selector).select2({ width: '100%', placeholder });
            }

            function populateSelect(selector, data, placeholder, enable) {
                const $sel = $(selector);
                $sel.empty().append(`<option value="">${placeholder}</option>`);
                data.forEach(d => $sel.append(new Option(d.nama, d.id)));
                $sel.prop('disabled', !enable);
                // destroy & re-init select2
                if ($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
                $sel.select2({ width: '100%', placeholder });
            }

            function resetSelect(selector, placeholder) {
                const $sel = $(selector);
                $sel.empty().append(`<option value="">${placeholder}</option>`).prop('disabled', true);
                if ($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
                $sel.select2({ width: '100%', placeholder });
            }

            // Load provinsi on page load
            $.getJSON(`${WILAYAH_BASE}/provinsi.json`, function(data) {
                populateSelect('#sel_province', data, '-- Pilih Provinsi --', true);
            });

            $('#sel_province').on('change', function() {
                const id = $(this).val();
                const nama = $(this).find('option:selected').text();
                $('#inp_province').val(id ? nama.toUpperCase() : '');
                resetSelect('#sel_city', '-- Pilih Kab/Kota --');
                resetSelect('#sel_district', '-- Pilih Kecamatan Dulu --');
                resetSelect('#sel_village', '-- Pilih Kecamatan Dulu --');
                $('#inp_city, #inp_district, #inp_village').val('');
                if (!id) return;
                $.getJSON(`${WILAYAH_BASE}/kabupaten/${id}.json`, function(data) {
                    populateSelect('#sel_city', data, '-- Pilih Kab/Kota --', true);
                });
            });

            $('#sel_city').on('change', function() {
                const id = $(this).val();
                const nama = $(this).find('option:selected').text();
                $('#inp_city').val(id ? nama.toUpperCase() : '');
                resetSelect('#sel_district', '-- Pilih Kecamatan --');
                resetSelect('#sel_village', '-- Pilih Kelurahan Dulu --');
                $('#inp_district, #inp_village').val('');
                if (!id) return;
                $.getJSON(`${WILAYAH_BASE}/kecamatan/${id}.json`, function(data) {
                    populateSelect('#sel_district', data, '-- Pilih Kecamatan --', true);
                });
            });

            $('#sel_district').on('change', function() {
                const id = $(this).val();
                const nama = $(this).find('option:selected').text();
                $('#inp_district').val(id ? nama.toUpperCase() : '');
                resetSelect('#sel_village', '-- Pilih Kelurahan/Desa --');
                $('#inp_village').val('');
                if (!id) return;
                $.getJSON(`${WILAYAH_BASE}/kelurahan/${id}.json`, function(data) {
                    populateSelect('#sel_village', data, '-- Pilih Kelurahan/Desa --', true);
                });
            });

            $('#sel_village').on('change', function() {
                const nama = $(this).find('option:selected').text();
                $('#inp_village').val($(this).val() ? nama.toUpperCase() : '');
            });
            // ────────────────────────────────────────────────────────────────
        </script>

    </div>
</div>
