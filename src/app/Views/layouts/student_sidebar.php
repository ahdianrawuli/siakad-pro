<aside class="w-64 bg-green-800 text-white flex flex-col shadow-xl z-10">
    <div class="h-16 flex items-center justify-center border-b border-green-700 font-bold text-xl tracking-wider">
        <i class="fa-solid fa-user-graduate mr-2"></i> PANEL SANTRI
    </div>

    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1 px-2">
            <li>
                <a href="/student/dashboard" class="flex items-center px-4 py-3 hover:bg-green-700 rounded-lg transition-colors <?= $_SERVER['REQUEST_URI'] == '/student/dashboard' ? 'bg-green-700' : '' ?>">
                    <i class="fa-solid fa-gauge-high w-6 text-center"></i>
                    <span class="ml-3 font-medium">Overview</span>
                </a>
            </li>
            <li>
                <a href="/student/profile" class="flex items-center px-4 py-3 hover:bg-green-700 rounded-lg transition-colors">
                    <i class="fa-solid fa-address-card w-6 text-center"></i>
                    <span class="ml-3 font-medium">Data Santri</span>
                </a>
            </li>
            <li>
                <a href="/student/payment" class="flex items-center px-4 py-3 hover:bg-green-700 rounded-lg transition-colors">
                    <i class="fa-solid fa-file-invoice-dollar w-6 text-center"></i>
                    <span class="ml-3 font-medium">Pembayaran</span>
                </a>
            </li>
            <li>
                <a href="/student/documents" class="flex items-center px-4 py-3 hover:bg-green-700 rounded-lg transition-colors">
                    <i class="fa-solid fa-folder-open w-6 text-center"></i>
                    <span class="ml-3 font-medium">Dokumen</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="p-4 border-t border-green-700 bg-green-900">
        <a href="/logout" class="flex items-center justify-center w-full px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
            <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
        </a>
    </div>
</aside>
