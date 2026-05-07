<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <a href="/settings/letters" class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition shrink-0">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Edit Template Surat</h3>
            <p class="text-slate-500 text-sm mt-0.5 font-medium"><?= htmlspecialchars($template['name']) ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Kolom Kiri: Form Editor -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-200">
                <h4 class="font-bold text-slate-700 text-sm">Editor Template</h4>
            </div>
            <div class="p-5">
                <div class="mb-4 bg-blue-50 border border-blue-200 p-3 rounded-xl text-xs text-blue-800">
                    <b>Placeholder:</b> <code class="bg-blue-100 px-1 rounded">{nama}</code> <code class="bg-blue-100 px-1 rounded">{nis}</code> <code class="bg-blue-100 px-1 rounded">{kelas}</code> <code class="bg-blue-100 px-1 rounded">{tempat_lahir}</code> <code class="bg-blue-100 px-1 rounded">{tgl_lahir}</code> <code class="bg-blue-100 px-1 rounded">{alamat}</code>
                </div>
                <form action="/settings/letters/update" method="POST" id="letterForm" class="space-y-4">
                    <input type="hidden" name="id" value="<?= $template['id'] ?>">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Surat</label>
                        <input type="text" name="name" id="letterName" value="<?= htmlspecialchars($template['name']) ?>"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Isi Surat (HTML)</label>
                        <textarea name="content" id="letterContent" rows="16"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"><?= htmlspecialchars($template['content']) ?></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Template
                    </button>
                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Preview -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <h4 class="font-bold text-slate-700 text-sm">Preview Surat</h4>
                <button onclick="printPreview()" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                    <i class="fa-solid fa-print"></i> Print
                </button>
            </div>
            <!-- Preview Frame -->
            <div id="previewBox" class="flex-1 border-0 overflow-auto bg-slate-50 p-6 text-sm font-serif leading-relaxed min-h-[500px]">
                <div class="text-center border-b-2 border-black pb-3 mb-4">
                    <div class="font-bold text-base uppercase">Pondok Pesantren Sumatera Thawalib Parabek</div>
                    <div class="text-xs">Jl. Parabek, Bukittinggi, Sumatera Barat</div>
                </div>
                <h3 id="previewTitle" class="text-center font-bold underline mb-4 uppercase text-sm"></h3>
                <div id="previewContent" class="text-justify"></div>
                <div class="mt-10 float-right text-center w-48">
                    <p class="text-sm">Bukittinggi, <?= date('d F Y') ?></p>
                    <p class="text-sm">Kepala Sekolah,</p>
                    <br><br><br>
                    <p class="text-sm font-bold underline">H. M. Zaki Munawwar</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
const contentEl = document.getElementById('letterContent');
const nameEl    = document.getElementById('letterName');
const previewContent = document.getElementById('previewContent');
const previewTitle   = document.getElementById('previewTitle');

// Ganti placeholder dengan contoh data untuk preview
function renderPreview() {
    let html = contentEl.value
        .replace(/{nama}/g, '<b>Ahmad Fauzi</b>')
        .replace(/{nis}/g, '<b>2024001</b>')
        .replace(/{kelas}/g, '<b>X MTS A</b>')
        .replace(/{tempat_lahir}/g, '<b>Bukittinggi</b>')
        .replace(/{tgl_lahir}/g, '<b>01 Januari 2010</b>')
        .replace(/{alamat}/g, '<b>Jl. Parabek No. 1, Bukittinggi</b>');
    previewContent.innerHTML = html;
    previewTitle.textContent = nameEl.value;
}

contentEl.addEventListener('input', renderPreview);
nameEl.addEventListener('input', renderPreview);
renderPreview(); // initial render

function printPreview() {
    const win = window.open('', '_blank');
    win.document.write(`<!DOCTYPE html><html><head>
        <style>
            body { font-family: 'Times New Roman', serif; line-height: 1.6; padding: 20mm; }
            .header { text-align: center; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 20px; }
            .content { text-align: justify; }
            .sign { margin-top: 50px; float: right; text-align: center; width: 200px; }
            @media print { body { padding: 20mm; } }
        </style>
    </head><body onload="window.print(); window.close();">
        <div class="header">
            <b style="font-size:16pt;text-transform:uppercase">Pondok Pesantren Sumatera Thawalib Parabek</b><br>
            <span style="font-size:10pt">Jl. Parabek, Bukittinggi, Sumatera Barat</span>
        </div>
        <h3 style="text-align:center;text-decoration:underline;text-transform:uppercase">${nameEl.value}</h3>
        <div class="content">${contentEl.value}</div>
        <div class="sign">
            <p>Bukittinggi, <?= date('d F Y') ?></p>
            <p>Kepala Sekolah,</p><br><br><br>
            <p><b><u>H. M. Zaki Munawwar</u></b></p>
        </div>
    </body></html>`);
    win.document.close();
}
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
