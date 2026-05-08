<?php $currentUri = strtok($_SERVER['REQUEST_URI'], '?'); ?>
<aside class="w-64 bg-teal-800 text-white flex flex-col shadow-xl z-10">
    <div class="h-16 flex items-center justify-center border-b border-teal-700 font-bold text-xl tracking-wider">
        <i class="fa-solid fa-users mr-2"></i> PORTAL WALI
    </div>

    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1 px-2">
            <?php
            $menus = [
                ['href' => '/portal/orangtua',              'icon' => 'fa-gauge-high',           'label' => 'Dashboard'],
                ['href' => '/portal/orangtua/absensi',      'icon' => 'fa-clipboard-check',      'label' => 'Absensi'],
                ['href' => '/portal/orangtua/nilai',        'icon' => 'fa-chart-bar',            'label' => 'Nilai'],
                ['href' => '/portal/orangtua/pembayaran',   'icon' => 'fa-file-invoice-dollar',  'label' => 'Pembayaran'],
                ['href' => '/portal/orangtua/kedisiplinan', 'icon' => 'fa-triangle-exclamation', 'label' => 'Kedisiplinan'],
                ['href' => '/portal/orangtua/asrama',       'icon' => 'fa-house',                'label' => 'Asrama'],
                ['href' => '/portal/orangtua/kesehatan',    'icon' => 'fa-heart-pulse',          'label' => 'Kesehatan'],
                ['href' => '/portal/orangtua/jadwal',       'icon' => 'fa-calendar-days',        'label' => 'Jadwal'],
                ['href' => '/portal/orangtua/pengumuman',   'icon' => 'fa-bullhorn',             'label' => 'Pengumuman'],
            ];
            foreach ($menus as $m):
                $active = $currentUri === $m['href'] ? 'bg-teal-700' : '';
            ?>
            <li>
                <a href="<?= $m['href'] ?>" class="flex items-center px-4 py-3 hover:bg-teal-700 rounded-lg transition-colors <?= $active ?>">
                    <i class="fa-solid <?= $m['icon'] ?> w-6 text-center"></i>
                    <span class="ml-3 font-medium"><?= $m['label'] ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>

        <!-- Panduan -->
        <div class="px-2 mt-2">
            <div class="border-t border-teal-700 my-2"></div>
            <a href="/guideline" class="flex items-center px-4 py-3 hover:bg-teal-700 rounded-lg transition-colors <?= $currentUri === '/guideline' ? 'bg-teal-700' : '' ?>">
                <i class="fa-solid fa-book-open w-6 text-center"></i>
                <span class="ml-3 font-medium">Panduan</span>
            </a>
        </div>

    </nav>

    <div class="p-4 border-t border-teal-700 bg-teal-900">
        <p class="text-xs text-teal-300 mb-2 truncate"><?= htmlspecialchars(\App\Core\Session::get('user_name') ?? '') ?></p>
        <a href="/logout" class="flex items-center justify-center w-full px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
            <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
        </a>
    </div>
</aside>
