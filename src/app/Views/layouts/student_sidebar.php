<?php $currentUri = strtok($_SERVER['REQUEST_URI'], '?'); ?>
<aside class="w-64 bg-green-800 text-white flex flex-col shadow-xl z-10">
    <div class="h-16 flex items-center justify-center border-b border-green-700 font-bold text-xl tracking-wider">
        <i class="fa-solid fa-user-graduate mr-2"></i> PANEL SANTRI
    </div>

    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1 px-2">
            <?php
            $menus = [
                ['href' => '/student/dashboard',      'icon' => 'fa-gauge-high',           'label' => 'Overview'],
                ['href' => '/student/profile',        'icon' => 'fa-address-card',         'label' => 'Data Santri'],
                ['href' => '/student/biodata',        'icon' => 'fa-id-card',              'label' => 'Biodata Lengkap'],
                ['href' => '/student/schedule',       'icon' => 'fa-calendar-days',        'label' => 'Jadwal Pelajaran'],
                ['href' => '/student/attendance',     'icon' => 'fa-clipboard-check',      'label' => 'Absensi'],
                ['href' => '/student/grades',         'icon' => 'fa-chart-bar',            'label' => 'Nilai'],
                ['href' => '/student/exam-card',      'icon' => 'fa-credit-card',          'label' => 'Kartu Ujian', 'ppdb_only' => true],
                ['href' => '/student/payment',        'icon' => 'fa-file-invoice-dollar',  'label' => 'Pembayaran'],
                ['href' => '/student/boarding',       'icon' => 'fa-house',                'label' => 'Asrama'],
                ['href' => '/student/health',         'icon' => 'fa-heart-pulse',          'label' => 'Kesehatan'],
                ['href' => '/student/discipline',     'icon' => 'fa-triangle-exclamation', 'label' => 'Kedisiplinan'],
                ['href' => '/student/extracurricular','icon' => 'fa-person-running',       'label' => 'Ekstrakurikuler'],
                ['href' => '/student/announcements',  'icon' => 'fa-bullhorn',             'label' => 'Pengumuman'],
                ['href' => '/student/letter',         'icon' => 'fa-envelope',             'label' => 'Surat Keterangan'],
                ['href' => '/student/documents',      'icon' => 'fa-folder-open',          'label' => 'Dokumen'],
                ['href' => '/student/resume',         'icon' => 'fa-file-lines',           'label' => 'Resume'],
            ];
            $isActive = \App\Core\Session::get('is_active_student');
            foreach ($menus as $m):
                if (!empty($m['ppdb_only']) && $isActive) continue;
                $active = $currentUri === $m['href'] ? 'bg-green-700' : '';
            ?>
            <li>
                <a href="<?= $m['href'] ?>" class="flex items-center px-4 py-3 hover:bg-green-700 rounded-lg transition-colors <?= $active ?>">
                    <i class="fa-solid <?= $m['icon'] ?> w-6 text-center"></i>
                    <span class="ml-3 font-medium"><?= $m['label'] ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="p-4 border-t border-green-700 bg-green-900">
        <a href="/logout" class="flex items-center justify-center w-full px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
            <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
        </a>
    </div>
</aside>
