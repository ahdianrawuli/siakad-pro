<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?? 'SIAKAD' ?> - Pesantren Thawalib Parabek</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#15803d',   // green-700 — sidebar bg
                        secondary: '#16a34a', // green-600 — sidebar hover/active
                        accent: '#16a34a',    // green-600 — accent
                        'santri': '#16a34a',
                        'santri-dark': '#15803d',
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom Scrollbar untuk Sidebar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.4); }
        
        /* Fix untuk Main Content di Mobile agar tidak tertutup Header */
        @media (max-width: 768px) {
            main { padding-top: 5rem !important; }
        }

        /* ── Sidebar font & menu override ───────────────────────── */
        aside a, aside button, aside span, aside p, aside li {
            color: white !important;
        }
        aside .text-gray-400 { color: rgba(255,255,255,0.6) !important; }
        aside nav a:hover, aside nav button:hover { background: rgba(255,255,255,0.12) !important; }
        aside nav a.bg-secondary, aside nav button.bg-secondary { background: rgba(255,255,255,0.18) !important; }
        aside select { color: white !important; background-color: rgba(255,255,255,0.1) !important; border-color: rgba(255,255,255,0.2) !important; }
        aside select option { background-color: #15803d; color: white; }

        /* ── Global Admin Theme Override ─────────────────────────── */

        /* Background main content */
        main.flex-1 {
            background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 100%) !important;
        }

        /* ── Page Header: transform header card putih → hero hijau ── */
        /* Hanya div yang mengandung h3/h2 dengan class font-extrabold (pola header admin) */
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h3[class*="font-extrabold"]),
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h2[class*="font-extrabold"]) {
            background: linear-gradient(to right, #16a34a, #059669, #0d9488) !important;
            border: none !important;
            box-shadow: 0 10px 30px rgba(22,163,74,0.25) !important;
            border-radius: 1.5rem !important;
            position: relative;
            overflow: hidden;
        }
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h3[class*="font-extrabold"])::before,
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h2[class*="font-extrabold"])::before {
            content: '';
            position: absolute;
            top: -60%; right: -3%;
            width: 280px; height: 280px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            pointer-events: none;
        }
        /* Teks di dalam header → putih */
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h3[class*="font-extrabold"]) h3,
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h3[class*="font-extrabold"]) h2,
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h3[class*="font-extrabold"]) p,
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h3[class*="font-extrabold"]) strong,
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h2[class*="font-extrabold"]) h3,
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h2[class*="font-extrabold"]) h2,
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h2[class*="font-extrabold"]) p,
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h2[class*="font-extrabold"]) strong {
            color: white !important;
        }
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h3[class*="font-extrabold"]) p,
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h2[class*="font-extrabold"]) p {
            opacity: 0.85;
        }
        /* Badge di dalam header → putih transparan */
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h3[class*="font-extrabold"]) .inline-flex[class*="bg-"],
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h2[class*="font-extrabold"]) .inline-flex[class*="bg-"] {
            background: rgba(255,255,255,0.15) !important;
            border-color: rgba(255,255,255,0.25) !important;
            color: white !important;
        }
        /* Tombol info di header */
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h3[class*="font-extrabold"]) button[class*="rounded-full"],
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h2[class*="font-extrabold"]) button[class*="rounded-full"] {
            background: rgba(255,255,255,0.15) !important;
            border-color: rgba(255,255,255,0.2) !important;
            color: white !important;
        }
        /* Tombol aksi (submit/add) di dalam header → putih solid */
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h3[class*="font-extrabold"]) a[class*="bg-"],
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h3[class*="font-extrabold"]) button[type="submit"],
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h3[class*="font-extrabold"]) button[class*="bg-"],
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h2[class*="font-extrabold"]) a[class*="bg-"],
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h2[class*="font-extrabold"]) button[type="submit"],
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h2[class*="font-extrabold"]) button[class*="bg-"] {
            background: white !important;
            color: #15803d !important;
            border: none !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12) !important;
        }
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h3[class*="font-extrabold"]) a[class*="bg-"]:hover,
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h3[class*="font-extrabold"]) button:hover,
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h2[class*="font-extrabold"]) a[class*="bg-"]:hover,
        main.flex-1 > div[class*="mb-"][class*="bg-white"]:has(h2[class*="font-extrabold"]) button:hover {
            background: #f0fdf4 !important;
        }

        /* Semua tombol biru → hijau */
        .bg-blue-600, .bg-blue-500 { background-color: #16a34a !important; }
        .hover\:bg-blue-700:hover, .hover\:bg-blue-600:hover { background-color: #15803d !important; }
        .bg-blue-50 { background-color: #f0fdf4 !important; }
        .bg-blue-100 { background-color: #dcfce7 !important; }
        .text-blue-600 { color: #16a34a !important; }
        .text-blue-500 { color: #22c55e !important; }
        .text-blue-700 { color: #15803d !important; }
        .hover\:text-blue-700:hover { color: #15803d !important; }
        .border-blue-500 { border-color: #16a34a !important; }
        .border-blue-200 { border-color: #bbf7d0 !important; }
        .ring-blue-500, .focus\:ring-blue-500:focus { --tw-ring-color: #16a34a !important; }
        .focus\:border-blue-500:focus { border-color: #16a34a !important; }
        .shadow-blue-500\/20 { box-shadow: 0 4px 14px rgba(22,163,74,0.2) !important; }

        /* Indigo & violet → emerald */
        .bg-indigo-600, .bg-indigo-500 { background-color: #059669 !important; }
        .hover\:bg-indigo-700:hover { background-color: #047857 !important; }
        .text-indigo-600, .text-indigo-500 { color: #059669 !important; }
        .bg-indigo-50 { background-color: #ecfdf5 !important; }
        .bg-indigo-100 { background-color: #d1fae5 !important; }
        .border-indigo-500 { border-color: #059669 !important; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased selection:bg-santri selection:text-white" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden bg-slate-50/50 backdrop-blur-3xl">
