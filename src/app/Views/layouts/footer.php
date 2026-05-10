<?php
use App\Core\Session;
$scope     = Session::get('active_scope', 'GLOBAL');
$prevScope = Session::get('prev_scope', null);
// Hapus prev_scope setelah dibaca agar animasi hanya sekali
if ($prevScope) Session::set('prev_scope', null);

$palette = [
    'GLOBAL' => ['#16a34a','#059669','#0d9488'],
    'MTS'    => ['#1d4ed8','#2563eb','#3b82f6'],
    'MA'     => ['#9d174d','#be185d','#db2777'],
    'PDF'    => ['#c2410c','#ea580c','#f97316'],
];
$c = match($scope) {
    'MTS'  => ['main'=>'#1d4ed8','dark'=>'#1e40af','darker'=>'#1e3a8a','light'=>'#eff6ff','light2'=>'#dbeafe','text'=>'#1d4ed8','textL'=>'#3b82f6','textD'=>'#1e40af','border'=>'#93c5fd','shadow'=>'rgba(29,78,216,0.2)','g1'=>'#1d4ed8','g2'=>'#2563eb','g3'=>'#3b82f6','gText'=>'#bfdbfe'],
    'MA'   => ['main'=>'#9d174d','dark'=>'#831843','darker'=>'#500724','light'=>'#fdf2f8','light2'=>'#fce7f3','text'=>'#9d174d','textL'=>'#ec4899','textD'=>'#831843','border'=>'#f9a8d4','shadow'=>'rgba(157,23,77,0.2)','g1'=>'#9d174d','g2'=>'#be185d','g3'=>'#db2777','gText'=>'#fbcfe8'],
    'PDF'  => ['main'=>'#c2410c','dark'=>'#9a3412','darker'=>'#7c2d12','light'=>'#fff7ed','light2'=>'#ffedd5','text'=>'#c2410c','textL'=>'#f97316','textD'=>'#9a3412','border'=>'#fdba74','shadow'=>'rgba(194,65,12,0.2)','g1'=>'#c2410c','g2'=>'#ea580c','g3'=>'#f97316','gText'=>'#fed7aa'],
    default=> ['main'=>'#16a34a','dark'=>'#15803d','darker'=>'#14532d','light'=>'#f0fdf4','light2'=>'#dcfce7','text'=>'#16a34a','textL'=>'#22c55e','textD'=>'#15803d','border'=>'#bbf7d0','shadow'=>'rgba(22,163,74,0.2)','g1'=>'#16a34a','g2'=>'#059669','g3'=>'#0d9488','gText'=>'#a7f3d0'],
};
$fromColors = $palette[$prevScope] ?? $palette['GLOBAL'];
$toColors   = $palette[$scope]    ?? $palette['GLOBAL'];
$doAnim     = $prevScope && $prevScope !== $scope;
?>
<script>
(function() {
    var c = {
        main:   '<?=$c['main']?>',
        dark:   '<?=$c['dark']?>',
        darker: '<?=$c['darker']?>',
        light:  '<?=$c['light']?>',
        light2: '<?=$c['light2']?>',
        text:   '<?=$c['text']?>',
        textL:  '<?=$c['textL']?>',
        textD:  '<?=$c['textD']?>',
        border: '<?=$c['border']?>',
        shadow: '<?=$c['shadow']?>',
        g1:     '<?=$c['g1']?>',
        g2:     '<?=$c['g2']?>',
        g3:     '<?=$c['g3']?>',
        gText:  '<?=$c['gText']?>'
    };
    var css = `
        :root{--sc-main:${c.main};--sc-dark:${c.dark};--sc-darker:${c.darker};--sc-light:${c.light};--sc-light2:${c.light2};--sc-text:${c.text};--sc-textL:${c.textL};--sc-textD:${c.textD};--sc-border:${c.border};--sc-shadow:${c.shadow};--sc-g1:${c.g1};--sc-g2:${c.g2};--sc-g3:${c.g3};--sc-gText:${c.gText}}
        .bg-blue-600,.bg-blue-500{background-color:${c.main}!important}
        .hover\\:bg-blue-700:hover,.hover\\:bg-blue-600:hover{background-color:${c.dark}!important}
        .bg-blue-50{background-color:${c.light}!important}
        .bg-blue-100{background-color:${c.light2}!important}
        .text-blue-600{color:${c.text}!important}
        .text-blue-500{color:${c.textL}!important}
        .text-blue-700{color:${c.textD}!important}
        .hover\\:text-blue-700:hover{color:${c.textD}!important}
        .border-blue-500{border-color:${c.main}!important}
        .border-blue-200,.border-blue-100{border-color:${c.border}!important}
        .focus\\:ring-blue-500:focus{--tw-ring-color:${c.main}!important}
        .focus\\:border-blue-500:focus{border-color:${c.main}!important}
        .shadow-blue-500\\/20{box-shadow:0 4px 14px ${c.shadow}!important}
        .bg-indigo-600,.bg-indigo-500{background-color:${c.dark}!important}
        .hover\\:bg-indigo-700:hover{background-color:${c.dark}!important}
        .text-indigo-600,.text-indigo-500{color:${c.dark}!important}
        .bg-indigo-50{background-color:${c.light}!important}
        .bg-indigo-100{background-color:${c.light2}!important}
        .border-indigo-500{border-color:${c.main}!important}
        .bg-green-600,.bg-green-500{background-color:${c.main}!important}
        .bg-green-700{background-color:${c.dark}!important}
        .bg-green-800{background-color:${c.darker}!important}
        .hover\\:bg-green-700:hover,.hover\\:bg-green-800:hover{background-color:${c.dark}!important}
        .bg-green-50{background-color:${c.light}!important}
        .bg-green-100{background-color:${c.light2}!important}
        .text-green-600,.text-green-500{color:${c.text}!important}
        .text-green-700{color:${c.textD}!important}
        .text-green-100{color:${c.gText}!important}
        .border-green-200{border-color:${c.border}!important}
        .border-green-700{border-color:${c.dark}!important}
        .bg-emerald-600,.bg-emerald-500{background-color:${c.g2}!important}
        .bg-teal-600,.bg-teal-500{background-color:${c.g3}!important}
        .shadow-green-500\\/20{box-shadow:0 4px 14px ${c.shadow}!important}
        [class*="from-green-"][class*="via-emerald-"]{background-image:linear-gradient(to right,${c.g1},${c.g2},${c.g3})!important}
    `;
    function applyHeaderCards() {
        // Cari card header: div di dalam main yang punya class bg-white, rounded-2xl, dan p-6
        var cards = document.querySelectorAll('main > div.bg-white.rounded-2xl');
        cards.forEach(function(el) {
            // Bedakan header card (p-6) vs filter card (p-5)
            if (el.classList.contains('p-6') || el.classList.contains('md:p-8')) {
                el.style.setProperty('background', 'linear-gradient(135deg,'+c.g1+','+c.g2+','+c.g3+')', 'important');
                el.style.setProperty('border-color', 'transparent', 'important');
                el.querySelectorAll('h2,h3').forEach(function(t){ t.style.setProperty('color','#fff','important'); });
                el.querySelectorAll('p').forEach(function(t){ t.style.setProperty('color','rgba(255,255,255,0.8)','important'); });
                el.querySelectorAll('strong').forEach(function(t){ t.style.setProperty('color','#fff','important'); });
            }
        });
    }
    function inject() {
        var old = document.getElementById('sc-override');
        if (old) old.remove();
        var s = document.createElement('style');
        s.id = 'sc-override';
        s.textContent = css;
        document.head.appendChild(s);
    }
    // Inject sekarang dan setelah Tailwind selesai
    inject();
    document.addEventListener('DOMContentLoaded', function(){ inject(); applyHeaderCards(); });
    // Tailwind CDN kadang inject ulang — watch dan re-override
    if (window.MutationObserver) {
        new MutationObserver(function(mutations) {
            for (var m of mutations) {
                for (var n of m.addedNodes) {
                    if (n.nodeName === 'STYLE' && n.id !== 'sc-override') { inject(); return; }
                }
            }
        }).observe(document.head, { childList: true });
    }
})();
</script>
<!-- Page Loading Overlay -->
<div id="page-loader" style="display:none;position:fixed;inset:0;z-index:9999;backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);background:rgba(255,255,255,0.4);align-items:center;justify-content:center;">
    <div style="display:flex;flex-direction:column;align-items:center;gap:16px;">
        <div style="width:48px;height:48px;border:4px solid rgba(0,0,0,0.1);border-top-color:<?= $c['main'] ?>;border-radius:50%;animation:spin .7s linear infinite;"></div>
        <span style="font-size:13px;font-weight:600;color:<?= $c['main'] ?>;letter-spacing:.5px;">Memuat...</span>
    </div>
</div>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
<script>
(function(){
    var loader = document.getElementById('page-loader');
    function show(){ loader.style.display = 'flex'; }

    // Intercept semua klik <a> kecuali: target blank, anchor (#), javascript:, download
    document.addEventListener('click', function(e){
        var a = e.target.closest('a[href]');
        if (!a) return;
        var href = a.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript') || a.target === '_blank' || a.hasAttribute('download') || a.getAttribute('onclick')) return;
        show();
    }, true);

    // Intercept form submit
    document.addEventListener('submit', function(e){
        if (e.target.method && e.target.method.toLowerCase() === 'get') return; // filter GET (search/filter jangan blur)
        show();
    }, true);

    // Sembunyikan saat back/forward
    window.addEventListener('pageshow', function(){ loader.style.display = 'none'; });
})();
</script>
<?php if ($doAnim): ?><!-- doAnim placeholder - animasi hanya di sidebar -->
<?php endif; ?>
</div> </body>
</html>
