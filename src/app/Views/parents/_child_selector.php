<?php
// Partial: _child_selector.php
// Requires: $students, $selectedStudent, $baseUrl (current page base url)
?>
<?php if (count($students) > 1): ?>
<div class="mb-5 flex flex-wrap gap-2">
    <?php foreach ($students as $s): ?>
    <a href="<?= $baseUrl ?>?student_id=<?= $s['id'] ?>"
       class="px-4 py-2 rounded-full text-sm font-medium border transition
              <?= $selectedStudent['id'] == $s['id'] ? 'bg-teal-700 text-white border-teal-700' : 'bg-white text-gray-700 border-gray-300 hover:border-teal-500' ?>">
        <?= htmlspecialchars($s['full_name']) ?>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>
