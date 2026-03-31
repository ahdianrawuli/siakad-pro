<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'PPDB Online' ?> - SIAKAD PARABEK</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <nav class="bg-white shadow-md fixed w-full z-50 top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                
                <div class="flex items-center">
                    <a href="/" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-600 text-white flex items-center justify-center rounded-lg shadow-lg">
                            <i class="fa-solid fa-graduation-cap text-xl"></i>
                        </div>
                        <div class="hidden sm:block">
                            <h1 class="text-xl font-bold text-gray-800 leading-none">SIAKAD PARABEK</h1>
                            <p class="text-xs text-green-600 font-semibold tracking-wide">PENERIMAAN SANTRI BARU</p>
                        </div>
                        <div class="block sm:hidden">
                            <h1 class="text-lg font-bold text-gray-800 leading-none">Siakad Parabek</h1>
                            <p class="text-xs text-green-600 font-semibold tracking-wide">PPDB Online</p>
                        </div>
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-gray-600 hover:text-green-600 font-medium transition">Beranda</a>
                    <a href="/#alur" class="text-gray-600 hover:text-green-600 font-medium transition">Alur Pendaftaran</a>
                    <a href="/#biaya" class="text-gray-600 hover:text-green-600 font-medium transition">Biaya</a>
                    <a href="/cek-status" class="text-gray-600 hover:text-green-600 font-medium transition">Status Daftar</a>
                    
                    <a href="/login" class="px-6 py-2 border-2 border-green-600 text-green-600 font-bold rounded-full hover:bg-green-50 transition shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i> Login
                    </a>
                    <a href="/register" class="px-6 py-2 bg-green-600 text-white font-bold rounded-full hover:bg-green-700 transition shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-user-plus"></i> Daftar
                    </a>
                </div>

                <div class="flex items-center md:hidden">
                    <button id="mobile-menu-btn" class="text-gray-600 hover:text-green-600 focus:outline-none p-2">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 shadow-lg">
            <div class="px-4 pt-4 pb-6 space-y-3">
                <a href="/" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50">Beranda</a>
                <a href="/#alur" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50">Alur Pendaftaran</a>
                <a href="/#biaya" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50">Biaya Studi</a>
                <a href="/cek-status" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50">Status Daftar</a>
                
                <div class="border-t border-gray-100 my-2 pt-2"></div>
                
                <div class="flex flex-col gap-3">
                    <a href="/login" class="block w-full text-center px-4 py-3 border-2 border-green-600 text-green-600 font-bold rounded-lg hover:bg-green-50 transition shadow-sm">
                        <i class="fa-solid fa-right-to-bracket mr-2"></i> Masuk / Login
                    </a>
                    <a href="/register" class="block w-full text-center px-4 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition shadow-sm">
                        <i class="fa-solid fa-user-plus mr-2"></i> Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="h-20"></div>

    <script>
        // Script Sederhana untuk Toggle Menu Mobile
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>
