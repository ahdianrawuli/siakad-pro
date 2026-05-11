<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?? 'SIAKAD' ?> - Pesantren Thawalib Parabek</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
    /* ── Select2 theme override ── */
    .select2-container--default .select2-selection--single {
        height: 42px; border-radius: 0.75rem; border-color: #e2e8f0;
        background: #f8fafc; display: flex; align-items: center; padding: 0 0.75rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered { color: #334155; line-height: normal; padding: 0; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px; right: 8px; }
    .select2-container--default .select2-results__option--highlighted { background-color: #16a34a !important; }
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--focus .select2-selection--single { border-color: #16a34a; outline: none; box-shadow: 0 0 0 2px rgba(22,163,74,0.2); }
    .select2-dropdown { border-radius: 0.75rem; border-color: #e2e8f0; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; }
    .select2-search--dropdown .select2-search__field { border-radius: 0.5rem; border-color: #e2e8f0; padding: 6px 10px; }
    /* ── Portal page header card ── */
    .portal-page-header {
        background: linear-gradient(135deg, #16a34a 0%, #059669 60%, #0d9488 100%);
        border-radius: 1.25rem; padding: 1.5rem; margin-bottom: 1.5rem;
        position: relative; overflow: hidden; box-shadow: 0 8px 24px rgba(22,163,74,0.2);
    }
    .portal-page-header::before {
        content: ''; position: absolute; top: -40%; right: -5%;
        width: 220px; height: 220px; background: rgba(255,255,255,0.07);
        border-radius: 50%; pointer-events: none;
    }
    .portal-page-header h3 { color: #fff; font-size: 1.4rem; font-weight: 800; margin: 0; }
    .portal-page-header p  { color: rgba(255,255,255,0.8); font-size: 0.85rem; margin: 4px 0 0; }
    .portal-page-header .header-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25);
        color: #fff; font-size: 0.7rem; font-weight: 700; padding: 4px 10px;
        border-radius: 999px; margin-top: 10px;
    }
    .portal-page-header .btn-info {
        width: 30px; height: 30px; border-radius: 50%;
        background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25);
        color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: background .2s; flex-shrink: 0;
    }
    .portal-page-header .btn-info:hover { background: rgba(255,255,255,0.3); }
    /* ── Filter bar ── */
    .portal-filter-bar {
        background: #fff; border-radius: 1rem; padding: 1rem 1.25rem;
        border: 1px solid #e2e8f0; margin-bottom: 1.25rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    /* ── Info modal ── */
    .info-modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.45);
        z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1rem;
    }
    .info-modal-box {
        background: #fff; border-radius: 1.25rem; padding: 1.75rem;
        max-width: 480px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        position: relative;
    }
    .info-modal-box h4 { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 0.75rem; }
    .info-modal-box ul { list-style: disc; padding-left: 1.25rem; color: #475569; font-size: 0.875rem; line-height: 1.7; }
    .info-modal-close {
        position: absolute; top: 1rem; right: 1rem;
        width: 28px; height: 28px; border-radius: 50%; background: #f1f5f9;
        border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
        color: #64748b; font-size: 0.85rem;
    }
    .info-modal-close:hover { background: #e2e8f0; }
    /* ── Pagination ── */
    .portal-pagination { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }
    .portal-pagination a, .portal-pagination span {
        min-width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;
        border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; border: 1px solid #e2e8f0;
        color: #475569; background: #fff; text-decoration: none; transition: all .15s;
    }
    .portal-pagination a:hover { background: #f0fdf4; border-color: #16a34a; color: #16a34a; }
    .portal-pagination span.active { background: #16a34a; border-color: #16a34a; color: #fff; }
    .portal-pagination span.disabled { opacity: 0.4; pointer-events: none; }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#15803d',   // green-700 — sidebar bg
                        secondary: '#16a34a', // green-600 — sidebar hover/active
                        accent: '#16a34a',
                        'santri': '#16a34a',
                        'santri-dark': '#15803d',
                        // Scope colors
                        'scope-mts':      '#1d4ed8', // blue-700
                        'scope-mts-dark': '#1e40af', // blue-800
                        'scope-ma':       '#9d174d', // pink-800
                        'scope-ma-dark':  '#831843', // pink-900
                        'scope-pdf':      '#c2410c', // orange-700
                        'scope-pdf-dark': '#9a3412', // orange-800
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
        /* Cegah layout shift saat Select2 dropdown terbuka */
        body { overflow-x: hidden; }
        .select2-container--open { z-index: 9999 !important; }
        
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

    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased selection:bg-santri selection:text-white" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden bg-slate-50/50 backdrop-blur-3xl">
