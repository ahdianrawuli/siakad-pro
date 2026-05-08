<?php
/**
 * Portal Page Header Card
 * Variables expected:
 *   $pageTitle   - string
 *   $pageSubtitle - string (optional)
 *   $pageBadge   - string (optional, e.g. "Total: 10")
 *   $pageBadgeIcon - FA icon class (optional)
 *   $infoModalId - string (optional, e.g. "infoModal")
 *   $infoTitle   - string (optional)
 *   $infoItems   - array of strings (optional)
 *   $headerRight - string of raw HTML (optional, e.g. action buttons)
 */
$infoModalId  = $infoModalId  ?? 'infoModal_' . uniqid();
$infoTitle    = $infoTitle    ?? ($pageTitle ?? 'Panduan');
$infoItems    = $infoItems    ?? [];
?>
<div class="portal-page-header mb-6">
    <div class="flex items-start justify-between gap-3 relative z-10">
        <div>
            <h3><?= htmlspecialchars($pageTitle ?? '') ?></h3>
            <?php if (!empty($pageSubtitle)): ?>
            <p><?= htmlspecialchars($pageSubtitle) ?></p>
            <?php endif; ?>
            <?php if (!empty($pageBadge)): ?>
            <div class="header-badge">
                <i class="fa-solid <?= $pageBadgeIcon ?? 'fa-circle-info' ?>"></i>
                <?= htmlspecialchars($pageBadge) ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <?php if (!empty($headerRight)) echo $headerRight; ?>
            <?php if (!empty($infoItems)): ?>
            <button class="btn-info" onclick="document.getElementById('<?= $infoModalId ?>').classList.remove('hidden')" title="Panduan">
                <i class="fa-solid fa-circle-info"></i>
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($infoItems)): ?>
<div id="<?= $infoModalId ?>" class="info-modal-overlay hidden" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="info-modal-box">
        <button class="info-modal-close" onclick="document.getElementById('<?= $infoModalId ?>').classList.add('hidden')">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <h4><i class="fa-solid fa-circle-info text-green-600 mr-2"></i><?= htmlspecialchars($infoTitle) ?></h4>
        <ul>
            <?php foreach ($infoItems as $item): ?>
            <li><?= htmlspecialchars($item) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>
