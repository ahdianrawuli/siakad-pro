<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Detail Profil Guru</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Informasi lengkap tenaga pendidik.</p>
        </div>
        <div class="flex gap-2">
            <a href="/school/teachers" class="px-4 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-slate-400"></i> Kembali
            </a>
            <a href="/school/teachers/edit?id=<?= $teacher['id'] ?>" class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Edit Data
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kartu Profil -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center h-fit">
            <div class="w-28 h-28 bg-blue-100 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-md">
                <i class="fa-solid fa-user-tie text-4xl"></i>
            </div>
            <h3 class="text-lg font-extrabold text-slate-800"><?= htmlspecialchars($teacher['full_name']) ?></h3>
            <p class="text-slate-400 text-sm mt-1 font-mono">NIP: <?= $teacher['nip'] ?: '-' ?></p>
            <div class="mt-4">
                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold border <?= $teacher['status'] == 'ACTIVE' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' ?>">
                    <i class="fa-solid fa-circle text-[6px]"></i> <?= $teacher['status'] ?>
                </span>
            </div>
        </div>

        <!-- Biodata -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h4 class="font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-slate-400"></i> Biodata Lengkap
                </h4>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                    <div>
                        <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jenis Kelamin</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-800"><?= $teacher['gender'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pendidikan Terakhir</dt>
                        <dd class="mt-1"><span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg border border-slate-200"><?= $teacher['education'] ?></span></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nomor HP / WA</dt>
                        <dd class="mt-1 text-sm font-bold text-blue-600"><?= $teacher['phone'] ?: '-' ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Alamat Email</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-800"><?= $teacher['email'] ?: '-' ?></dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Alamat Domisili</dt>
                        <dd class="mt-1 text-sm text-slate-700 leading-relaxed"><?= $teacher['address'] ?: 'Alamat belum diisi.' ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Bergabung</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-800"><?= date('d F Y', strtotime($teacher['created_at'])) ?></dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
