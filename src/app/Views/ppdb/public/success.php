<?php require __DIR__ . '/../../layouts/public_header.php'; ?>

<div class="min-h-screen bg-green-50 flex items-center justify-center p-4">
    <div class="bg-white max-w-md w-full rounded-2xl shadow-xl overflow-hidden border border-green-100">
        
        <div class="bg-green-600 p-8 text-center">
            <div class="w-20 h-20 bg-white text-green-600 rounded-full flex items-center justify-center text-4xl mx-auto shadow-lg mb-4">
                <i class="fa-solid fa-check"></i>
            </div>
            <h2 class="text-2xl font-bold text-white">Pendaftaran Berhasil!</h2>
            <p class="text-green-100 text-sm mt-1">Data Anda telah kami terima.</p>
        </div>

        <div class="p-8">
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center mb-6 relative">
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-white px-2 text-xs font-bold text-yellow-600 uppercase tracking-widest">
                    Penting
                </div>
                <p class="text-gray-600 text-sm mb-2">Gunakan nomor ini sebagai <b>USERNAME</b> untuk login:</p>
                <div class="text-3xl font-mono font-extrabold text-gray-800 tracking-wider select-all" id="regNo">
                    <?= htmlspecialchars($reg_no) ?>
                </div>
                <button onclick="copyText()" class="mt-3 text-xs bg-white border border-gray-300 px-3 py-1 rounded text-gray-600 hover:bg-gray-50 transition">
                    <i class="fa-regular fa-copy mr-1"></i> Salin Nomor
                </button>
            </div>

            <div class="space-y-4">
                <p class="text-sm text-gray-600 text-center">
                    Password login adalah password yang Anda buat saat mengisi formulir.
                </p>

                <a href="/login" class="block w-full bg-green-600 text-white text-center font-bold py-3 rounded-xl hover:bg-green-700 shadow-lg shadow-green-200 transition transform active:scale-95">
                    LANJUT KE LOGIN <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
        
    </div>
</div>

<script>
function copyText() {
    var text = document.getElementById("regNo").innerText;
    navigator.clipboard.writeText(text).then(function() {
        alert("Nomor Registrasi disalin: " + text);
    });
}
</script>
