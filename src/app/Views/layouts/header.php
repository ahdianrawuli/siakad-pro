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
                        primary: '#1e293b', 
                        secondary: '#334155',
                        accent: '#3b82f6',
                        'santri': '#2E603E', // Warna khusus santri
                        'santri-dark': '#275235',
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
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased selection:bg-santri selection:text-white" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden bg-slate-50/50 backdrop-blur-3xl">
