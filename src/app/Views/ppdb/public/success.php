<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - SIAKAD PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1555008872-f03b347ffb53?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative">
    <!-- Dark Overlay Backdrop -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm z-0"></div>

    <!-- Centered Success Modal Card -->
    <div class="relative z-10 w-full max-w-lg p-6 animate-fade-in-up">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col items-center text-center p-8 sm:p-12">

            <!-- Success Icon/Illustration -->
            <div class="w-24 h-24 bg-green-50 text-green-500 rounded-full flex items-center justify-center text-5xl mb-6 shadow-inner">
                <i class="fa-solid fa-check-circle"></i>
            </div>

            <!-- Title -->
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-4 leading-tight">
                Terima kasih telah mendaftarkan putra/putri Anda!
            </h2>

            <!-- Description -->
            <p class="text-gray-600 mb-8 leading-relaxed">
                Data formulir telah berhasil kami terima dengan Nomor Registrasi <strong class="text-gray-900 bg-gray-100 px-2 py-1 rounded"><?= htmlspecialchars($reg_no) ?></strong>. Informasi selanjutnya akan kami kirimkan melalui email atau WhatsApp. Jika ada pertanyaan lebih lanjut, silakan hubungi panitia penerimaan di nomor <strong>081260959820</strong>.
            </p>

            <!-- Purple Primary Button -->
            <a href="/" class="w-full sm:w-auto px-8 py-3.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl shadow-lg shadow-purple-200 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2">
                <i class="fa-solid fa-house"></i> Kembali ke Beranda
            </a>

        </div>
    </div>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }
    </style>
</body>
</html>