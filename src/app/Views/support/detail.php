<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6 flex flex-col h-screen">
    <div class="flex justify-between items-center mb-4">
        <div>
            <a href="/support" class="text-blue-600 hover:underline text-sm">< Kembali</a>
            <h3 class="text-2xl font-bold">#<?= $ticket['id'] ?>: <?= $ticket['subject'] ?></h3>
        </div>
        <span class="px-3 py-1 bg-white rounded shadow font-bold text-sm"><?= $ticket['status'] ?></span>
    </div>

    <div class="flex-1 overflow-y-auto bg-white p-6 rounded shadow mb-4 space-y-4">
        <?php foreach($replies as $r): 
            $isMe = ($r['user_id'] == $_SESSION['user_id']);
            $isAdmin = in_array($r['role_id'], [1,2,3]); // 1,2,3 anggap staff
        ?>
        <div class="flex <?= $isMe ? 'justify-end' : 'justify-start' ?>">
            <div class="max-w-2xl <?= $isMe ? 'bg-blue-100' : ($isAdmin ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-100') ?> p-4 rounded-lg">
                <div class="flex justify-between items-center mb-1 gap-4">
                    <span class="font-bold text-xs <?= $isAdmin ? 'text-red-600' : 'text-gray-700' ?>">
                        <?= $r['sender_name'] ?> <?= $isAdmin ? '(Staff)' : '' ?>
                    </span>
                    <span class="text-xs text-gray-400"><?= $r['created_at'] ?></span>
                </div>
                <p class="text-sm text-gray-800 whitespace-pre-wrap"><?= $r['message'] ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if($ticket['status'] != 'CLOSED'): ?>
    <div class="bg-white p-4 rounded shadow">
        <form action="/support/reply" method="POST" class="flex gap-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
            <input type="text" name="message" class="flex-1 p-3 border rounded focus:outline-none focus:border-blue-500" placeholder="Tulis balasan..." required autocomplete="off">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </div>
    <?php else: ?>
        <div class="bg-gray-200 p-4 text-center rounded text-gray-600">Tiket ini telah ditutup.</div>
    <?php endif; ?>

</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
