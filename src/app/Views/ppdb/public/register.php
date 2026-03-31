
<?php require __DIR__ . '/../../layouts/public_header.php'; ?>

<div class="min-h-screen bg-gray-50 flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8">
        
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Formulir Pendaftaran</h2>
            <p class="mt-2 text-sm text-gray-600">Isi data diri Anda dengan lengkap dan benar.</p>
        </div>

        <?php \App\Core\Session::flash(); ?>

        <!-- Tab Navigation -->
        <div class="flex border-b border-gray-200 mt-6" id="tab-nav">
            <button type="button" class="tab-btn w-1/4 py-3 text-center font-bold text-sm bg-white border-b-2 border-green-600 text-green-600" data-target="tab-admin">A. Administrasi</button>
            <button type="button" class="tab-btn w-1/4 py-3 text-center font-bold text-sm bg-gray-50 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-target="tab-calon">B. Data Calon</button>
            <button type="button" class="tab-btn w-1/4 py-3 text-center font-bold text-sm bg-gray-50 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-target="tab-riwayat">C. Pendidikan</button>
            <button type="button" class="tab-btn w-1/4 py-3 text-center font-bold text-sm bg-gray-50 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-target="tab-ortu">D. Orang Tua</button>
        </div>

        <form class="mt-6 space-y-6" action="/register/process" method="POST" id="register-form">
            <?= \App\Core\Csrf::input() ?>

            <!-- Tab A: Administrasi -->
            <div id="tab-admin" class="tab-content block">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">A. Administrasi</h3>

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
                            <input type="password" name="password" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ulangi Password</label>
                            <input type="password" name="password_confirm" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="button" class="next-btn px-6 py-2 bg-green-600 text-white rounded-lg font-bold" data-target="tab-calon">Selanjutnya</button>
                </div>
            </div>

            <!-- Tab B: Data Calon Santri -->
            <div id="tab-calon" class="tab-content hidden">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">B. Data Calon Santri</h3>

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
                            <input type="text" name="province" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kabupaten/Kota</label>
                            <input type="text" name="city" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kecamatan</label>
                            <input type="text" name="district" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kelurahan</label>
                            <input type="text" name="village" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kode Pos</label>
                            <input type="text" name="postal_code" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg sm:text-sm focus:ring-green-500 focus:border-green-500">
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
                <div class="flex justify-between mt-4">
                    <button type="button" class="prev-btn px-6 py-2 bg-gray-500 text-white rounded-lg font-bold" data-target="tab-admin">Kembali</button>
                    <button type="button" class="next-btn px-6 py-2 bg-green-600 text-white rounded-lg font-bold" data-target="tab-riwayat">Selanjutnya</button>
                </div>
            </div>

            <!-- Tab C: Riwayat Pendidikan -->
            <div id="tab-riwayat" class="tab-content hidden">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">C. Riwayat Pendidikan</h3>
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
                <div class="flex justify-between mt-4">
                    <button type="button" class="prev-btn px-6 py-2 bg-gray-500 text-white rounded-lg font-bold" data-target="tab-calon">Kembali</button>
                    <button type="button" class="next-btn px-6 py-2 bg-green-600 text-white rounded-lg font-bold" data-target="tab-ortu">Selanjutnya</button>
                </div>
            </div>

            <!-- Tab D: Orang Tua -->
            <div id="tab-ortu" class="tab-content hidden">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">D. Data Pribadi OrangTua / Wali</h3>

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
                <div class="flex justify-between mt-4">
                    <button type="button" class="prev-btn px-6 py-2 bg-gray-500 text-white rounded-lg font-bold" data-target="tab-riwayat">Kembali</button>
                    <button type="submit" class="px-8 py-2 bg-green-700 text-white font-bold rounded-lg hover:bg-green-800 shadow-lg transition">
                        <i class="fa-solid fa-paper-plane mr-2"></i> DAFTAR SEKARANG
                    </button>
                </div>
            </div>
            
            <div class="text-center mt-6">
                <a href="/login" class="text-sm font-bold text-green-600 hover:text-green-500">Sudah punya akun? Login disini</a>
            </div>
        </form>

        <script>
            // Tab Logic
            const tabs = ['tab-admin', 'tab-calon', 'tab-riwayat', 'tab-ortu'];
            const buttons = document.querySelectorAll('.tab-btn');
            const contents = document.querySelectorAll('.tab-content');

            function switchTab(targetId) {
                contents.forEach(c => {
                    if(c.id === targetId) {
                        c.classList.remove('hidden');
                        c.classList.add('block');
                    } else {
                        c.classList.add('hidden');
                        c.classList.remove('block');
                    }
                });

                buttons.forEach(b => {
                    if(b.dataset.target === targetId) {
                        b.classList.add('border-green-600', 'text-green-600', 'bg-white');
                        b.classList.remove('border-transparent', 'text-gray-500', 'bg-gray-50');
                    } else {
                        b.classList.remove('border-green-600', 'text-green-600', 'bg-white');
                        b.classList.add('border-transparent', 'text-gray-500', 'bg-gray-50');
                    }
                });
            }

            buttons.forEach(btn => {
                btn.addEventListener('click', () => switchTab(btn.dataset.target));
            });

            document.querySelectorAll('.next-btn, .prev-btn').forEach(btn => {
                btn.addEventListener('click', () => switchTab(btn.dataset.target));
            });

            // Jalur Logic
            function updateJalur() {
                const unit = document.getElementById('education_unit').value;
                const jalurSelect = document.getElementById('ppdb_track');
                const trackNameInput = document.getElementById('track_name');

                jalurSelect.innerHTML = '';

                let options = [];
                if (unit === 'MTs') {
                    options = [
                        {id: '1', name: 'Reguler (Umum)'},
                        {id: '2', name: 'Prestasi Akademik'},
                        {id: '3', name: 'Tahfiz (Minimal 2 Juz)'},
                        {id: '4', name: 'Banuhampu'},
                        {id: '5', name: 'Parabek'}
                    ];
                } else if (unit === 'MA') {
                    options = [
                        {id: '6', name: 'Aliyah Reguler'},
                        {id: '7', name: 'Tahfiz (minimal 10 Juz)'}
                    ];
                } else if (unit === 'PDF') {
                    options = [
                        {id: '8', name: 'Pdf Reguler'},
                        {id: '9', name: 'Kitab'}
                    ];
                } else {
                    options = [{id: '', name: '-- Pilih Unit Pendidikan Dulu --'}];
                }

                options.forEach(opt => {
                    const el = document.createElement('option');
                    el.value = opt.id;
                    el.textContent = opt.name;
                    jalurSelect.appendChild(el);
                });

                jalurSelect.classList.remove('bg-gray-50');
                if(unit === '') jalurSelect.classList.add('bg-gray-50');

                // Set initial hidden track_name
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
        </script>
    </div>
</div>
