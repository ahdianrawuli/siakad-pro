<?php
use App\Core\Database;
use App\Models\AppConfig;
use App\Core\Session;

// ==========================================
// 1. LOGIKA PHP
// ==========================================
$isCandidate = false;
if (($_SESSION['user_role'] ?? '') == 'siswa') {
    $db = Database::getInstance();
    $isActiveStudent = $db->query("SELECT id FROM students WHERE user_id = ?", [$_SESSION['user_id']])->fetch();
    if (!$isActiveStudent) $isCandidate = true;
}

// Get Active Scope
$activeScope = Session::get('active_scope', 'GLOBAL');

// Logic Menu (Sama seperti sebelumnya)
$roleId = $_SESSION['user_role_id'] ?? 0;
if ($roleId == 0 && isset($_SESSION['user_role'])) {
    $db = Database::getInstance();
    $roleSlug = $_SESSION['user_role'];
    $roleData = $db->query("SELECT id FROM roles WHERE slug = ?", [$roleSlug])->fetch();
    $roleId = $roleData['id'] ?? 0;
}
$db = Database::getInstance();
$sql = "SELECT m.* FROM menus m JOIN role_menus rm ON m.id = rm.menu_id WHERE rm.role_id = ? ORDER BY m.order_num ASC";
$rawMenus = $db->query($sql, [$roleId])->fetchAll();

function buildTree(array $elements, $parentId = null) {
    $branch = array();
    foreach ($elements as $element) {
        if ($element['parent_id'] == $parentId) {
            $children = buildTree($elements, $element['id']);
            if ($children) { $element['children'] = $children; }
            $branch[] = $element;
        }
    }
    return $branch;
}
$menuTree = buildTree($rawMenus);
function isActive($url) {
    if ($url == '#' || $url == '') return false;
    return strpos($_SERVER['REQUEST_URI'], $url) === 0;
}

// Styling
if ($isCandidate) {
    $bgSidebar = "bg-santri";
    $bgHeader = "bg-santri-dark";
    $hoverColor = "hover:bg-santri-dark";
    $activeColor = "bg-santri-dark";
    $headerTitle = "PANEL SANTRI";
    $headerIcon = "fa-user-graduate";
} else {
    $bgSidebar = "bg-primary";
    $bgHeader = "bg-gray-900";
    $hoverColor = "hover:bg-secondary";
    $activeColor = "bg-secondary";
    $headerTitle = "SIAKAD PARABEK";
    $headerIcon = "fa-graduation-cap";
}
?>

<div class="fixed top-0 left-0 w-full h-16 <?= $bgSidebar ?> text-white flex items-center justify-between px-4 z-40 shadow-md md:hidden">
    <div class="flex items-center font-bold tracking-wide">
        <i class="fa-solid <?= $headerIcon ?> mr-2"></i> <?= $headerTitle ?>
        <?php if(!$isCandidate && $activeScope != 'GLOBAL'): ?>
            <span class="ml-2 bg-yellow-400 text-black text-[10px] px-1.5 py-0.5 rounded font-bold"><?= $activeScope ?></span>
        <?php endif; ?>
    </div>
    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded focus:outline-none hover:bg-white/10 transition">
        <i class="fa-solid fa-bars text-xl"></i>
    </button>
</div>

<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden" style="display: none;"></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed inset-y-0 left-0 z-50 w-64 <?= $bgSidebar ?> text-white flex flex-col shadow-xl transition-transform duration-300 transform md:translate-x-0 md:static md:inset-0">
    
    <div class="h-16 flex items-center justify-center border-b border-white/10 font-bold text-lg tracking-wide px-2 text-center <?= $bgHeader ?> hidden md:flex">
        <i class="fa-solid <?= $headerIcon ?> mr-2"></i> <?= $headerTitle ?>
    </div>

    <div class="h-16 flex items-center justify-between px-6 border-b border-white/10 font-bold text-lg <?= $bgHeader ?> md:hidden">
        <span>Menu</span>
        <button @click="sidebarOpen = false"><i class="fa-solid fa-xmark text-xl"></i></button>
    </div>

    <?php if (!$isCandidate): ?>
        <div class="px-4 py-3 border-b border-white/10 bg-black/10">
            <form action="/change-scope" method="POST">
                <label class="text-[10px] uppercase text-gray-400 font-bold tracking-wider mb-1 block">Mode Tampilan</label>
                <div class="relative">
<select name="scope" onchange="this.form.submit()" class="w-full bg-secondary text-white text-xs font-bold py-2 pl-3 pr-8 rounded border border-gray-600 focus:outline-none focus:border-blue-500 appearance-none cursor-pointer hover:bg-gray-700 transition">
    <option value="GLOBAL" <?= $activeScope == 'GLOBAL' ? 'selected' : '' ?>>🌐 Semua Jenjang</option>
    <option value="MTS" <?= $activeScope == 'MTS' ? 'selected' : '' ?>>🏫 MTS (Tsanawiyah)</option>
    <option value="MA" <?= $activeScope == 'MA' ? 'selected' : '' ?>>🎓 MA (Aliyah)</option>
    <option value="PDF" <?= $activeScope == 'PDF' ? 'selected' : '' ?>>👳 PDF (Diniyah Formal)</option>
</select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <nav class="flex-1 overflow-y-auto py-4 custom-scrollbar">
        <ul class="space-y-1 px-2">
            
            <?php if ($isCandidate): ?>
                <li><a href="/student/dashboard" class="flex items-center px-4 py-3 rounded-lg <?= $hoverColor ?> <?= isActive('/student/dashboard') ? $activeColor : '' ?>"><i class="fa-solid fa-gauge-high w-6 text-center"></i><span class="ml-3 font-medium">Overview</span></a></li>
                <li><a href="/student/profile" class="flex items-center px-4 py-3 rounded-lg <?= $hoverColor ?> <?= isActive('/student/profile') ? $activeColor : '' ?>"><i class="fa-solid fa-id-card w-6 text-center"></i><span class="ml-3 font-medium">Biodata</span></a></li>
                <li><a href="/student/payment" class="flex items-center px-4 py-3 rounded-lg <?= $hoverColor ?> <?= isActive('/student/payment') ? $activeColor : '' ?>"><i class="fa-solid fa-file-invoice-dollar w-6 text-center"></i><span class="ml-3 font-medium">Pembayaran</span></a></li>
                <li><a href="/student/documents" class="flex items-center px-4 py-3 rounded-lg <?= $hoverColor ?> <?= isActive('/student/documents') ? $activeColor : '' ?>"><i class="fa-solid fa-folder-open w-6 text-center"></i><span class="ml-3 font-medium">Dokumen</span></a></li>
            
            <?php else: ?>
                <?php $dashboardUrl = ($_SESSION['user_role'] == 'siswa') ? '/student/dashboard' : '/dashboard'; ?>
                <li>
                    <a href="<?= $dashboardUrl ?>" class="flex items-center px-4 py-3 rounded-lg transition-colors group <?= $hoverColor ?> <?= isActive($dashboardUrl) ? $activeColor : '' ?>">
                        <i class="fa-solid fa-home w-6 text-center text-gray-400 group-hover:text-white <?= isActive($dashboardUrl) ? 'text-white' : '' ?>"></i>
                        <span class="ml-3 text-sm font-medium">Dashboard</span>
                    </a>
                </li>
                
                <?php foreach ($menuTree as $menu): ?>
                    <?php if(strtolower($menu['title']) == 'dashboard') continue; ?>
                    <?php if ($menu['title'] === 'Kartu Ujian' && isset($isActiveStudent) && $isActiveStudent) continue; ?>

                    <?php if (empty($menu['children'])): ?>
                        <li>
                            <a href="<?= $menu['url'] ?>" class="flex items-center px-4 py-3 rounded-lg transition-colors group <?= $hoverColor ?> <?= isActive($menu['url']) ? $activeColor : '' ?>">
                                <i class="fa-solid fa-<?= $menu['icon'] ?> w-6 text-center text-gray-400 group-hover:text-white <?= isActive($menu['url']) ? 'text-white' : '' ?>"></i>
                                <span class="ml-3 text-sm font-medium"><?= $menu['title'] ?></span>
                            </a>
                        </li>
                    <?php else: ?>
                        <?php 
                            $isChildActive = false;
                            foreach ($menu['children'] as $child) { if (isActive($child['url'])) { $isChildActive = true; break; } }
                        ?>
                        <li x-data="{ open: <?= $isChildActive ? 'true' : 'false' ?> }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors group focus:outline-none <?= $hoverColor ?> <?= $isChildActive ? $activeColor : '' ?>">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-<?= $menu['icon'] ?> w-6 text-center group-hover:text-white <?= $isChildActive ? 'text-white' : 'text-gray-400' ?>"></i>
                                    <span class="ml-3 text-sm font-medium"><?= $menu['title'] ?></span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="{'rotate-180': open}"></i>
                            </button>
                            <ul x-show="open" class="pl-10 pr-2 py-1 space-y-1 bg-black/20 rounded-b-lg">
                                <?php foreach ($menu['children'] as $child): ?>
                                    <li><a href="<?= $child['url'] ?>" class="block px-3 py-2 text-sm hover:text-white hover:bg-white/10 rounded-md transition-colors <?= isActive($child['url']) ? 'text-white font-bold bg-white/10' : 'text-gray-400' ?>"><?= $child['title'] ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="p-4 border-t border-white/10 <?= $bgHeader ?>">
        <div class="flex items-center mb-4">
            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-xs font-bold text-white border-2 border-gray-800">
                <?= substr($_SESSION['user_name'] ?? 'A', 0, 1) ?>
            </div>
            <div class="ml-3 overflow-hidden">
                <p class="text-sm font-medium text-white truncate"><?= $_SESSION['user_name'] ?? 'User' ?></p>
                <p class="text-xs text-gray-400 truncate"><?= ucfirst($_SESSION['user_role'] ?? 'Guest') ?></p>
            </div>
        </div>
        <a href="/logout" class="flex items-center justify-center w-full px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow">
            <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
        </a>
    </div>
</aside>
