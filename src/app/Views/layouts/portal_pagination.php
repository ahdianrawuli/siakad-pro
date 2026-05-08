<?php
/**
 * Portal Pagination
 * Variables expected:
 *   $currentPage  - int
 *   $totalPages   - int
 *   $baseUrl      - string (URL without page param, e.g. "/student/grades?year=1")
 */
if (($totalPages ?? 1) <= 1) return;
$sep = str_contains($baseUrl ?? '', '?') ? '&' : '?';
?>
<div class="flex items-center justify-between mt-4 px-1">
    <p class="text-xs text-slate-500">
        Halaman <?= $currentPage ?> dari <?= $totalPages ?>
    </p>
    <div class="portal-pagination">
        <?php if ($currentPage > 1): ?>
        <a href="<?= $baseUrl . $sep ?>page=<?= $currentPage - 1 ?>"><i class="fa-solid fa-chevron-left text-xs"></i></a>
        <?php else: ?>
        <span class="disabled"><i class="fa-solid fa-chevron-left text-xs"></i></span>
        <?php endif; ?>

        <?php
        $start = max(1, $currentPage - 2);
        $end   = min($totalPages, $currentPage + 2);
        if ($start > 1): ?><a href="<?= $baseUrl . $sep ?>page=1">1</a><?php if ($start > 2): ?><span class="disabled">…</span><?php endif; endif;
        for ($i = $start; $i <= $end; $i++):
            if ($i == $currentPage): ?><span class="active"><?= $i ?></span><?php
            else: ?><a href="<?= $baseUrl . $sep ?>page=<?= $i ?>"><?= $i ?></a><?php
            endif;
        endfor;
        if ($end < $totalPages): if ($end < $totalPages - 1): ?><span class="disabled">…</span><?php endif; ?><a href="<?= $baseUrl . $sep ?>page=<?= $totalPages ?>"><?= $totalPages ?></a><?php endif;
        ?>

        <?php if ($currentPage < $totalPages): ?>
        <a href="<?= $baseUrl . $sep ?>page=<?= $currentPage + 1 ?>"><i class="fa-solid fa-chevron-right text-xs"></i></a>
        <?php else: ?>
        <span class="disabled"><i class="fa-solid fa-chevron-right text-xs"></i></span>
        <?php endif; ?>
    </div>
</div>
