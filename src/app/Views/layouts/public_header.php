<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'PPDB Online' ?> - SIAKAD PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        santri: {
                            light: '#4ade80', // green-400
                            DEFAULT: '#16a34a', // green-600
                            dark: '#15803d', // green-700
                        },
                        primary: '#16a34a'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <!-- 1. HEADER / NAVBAR -->
    <header class="bg-white sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Kiri: Logo & Text -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-santri text-2xl">
                        <i class="fa-solid fa-mosque"></i>
                    </div>
                    <div class="hidden sm:block leading-tight">
                        <div class="font-bold text-sm tracking-wider text-gray-900">PONDOK PESANTREN</div>
                        <div class="font-extrabold text-lg text-santri">SUMATERA THAWALIB PARABEK</div>
                        <div class="text-xs text-gray-500">Bukittinggi Agam</div>
                    </div>
                </div>

                <!-- Kanan: Menu & Buttons -->
                <div class="hidden md:flex items-center space-x-8">
                    <nav class="flex space-x-6 text-sm font-bold text-gray-600">
                        <a href="/" class="hover:text-santri transition">HOME</a>
                        <a href="/prosedur" class="hover:text-santri transition">PROSEDUR</a>
                        <a href="/cek-status" class="hover:text-santri transition">STATUS DAFTAR</a>
                    </nav>
                    <div class="flex items-center space-x-3">
                        <a href="/login" class="px-5 py-2 border-2 border-gray-200 text-gray-600 font-bold rounded-lg hover:bg-gray-50 transition">MASUK</a>
                        <a href="/register" class="px-5 py-2 bg-santri text-white font-bold rounded-lg hover:bg-santri-dark transition shadow-lg shadow-green-200">DAFTAR</a>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

    </header>

    <!-- Mobile Menu Overlay (Tailwind Dropdown) -->
    <div id="mobile-menu" class="hidden fixed inset-0 z-40 bg-gray-800 bg-opacity-25 md:hidden">
        <div class="absolute top-20 left-0 w-full bg-white shadow-xl border-t border-gray-100">
            <div class="px-6 py-8 space-y-6">
                <a href="/" class="block font-bold text-gray-800 hover:text-santri transition text-lg border-b border-gray-50 pb-2">HOME</a>
                <a href="/prosedur" class="block font-bold text-gray-800 hover:text-santri transition text-lg border-b border-gray-50 pb-2">PROSEDUR</a>
                <a href="/cek-status" class="block font-bold text-gray-800 hover:text-santri transition text-lg border-b border-gray-50 pb-2">STATUS DAFTAR</a>
                <div class="pt-4 flex flex-col gap-4">
                    <a href="/login" class="text-center px-5 py-3 border-2 border-gray-200 text-gray-600 font-extrabold rounded-lg hover:bg-gray-50 transition">MASUK</a>
                    <a href="/register" class="text-center px-5 py-3 bg-santri text-white font-extrabold rounded-lg hover:bg-santri-dark transition shadow-lg">DAFTAR</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        // Close menu when clicking outside
        menu.addEventListener('click', (e) => {
            if (e.target === menu) {
                menu.classList.add('hidden');
            }
        });
    </script>