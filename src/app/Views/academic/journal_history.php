<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    #journalModal.hidden { display: none; opacity: 0; }
</style>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800"><?= $schedule['subject_name'] ?> - <?= $schedule['class_name'] ?></h3>
            <p class="text-gray-500 text-sm">
                <i class="fa-regular fa-clock mr-1"></i> <?= $schedule['day'] ?>, <?= substr($schedule['start_time'],0,5) ?> - <?= substr($schedule['end_time'],0,5) ?>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="/academic/journals" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-bold hover:bg-gray-300 transition text-sm">
                <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
            </a>
            <button onclick="openModal('add')" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition text-sm">
                <i class="fa-solid fa-plus mr-2"></i> Isi Jurnal Baru
            </button>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="min-w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-4">Tanggal / Pertemuan</th>
                        <th class="px-6 py-4">Materi / Topik</th>
                        <th class="px-6 py-4">Catatan</th>
                        <th class="px-6 py-4 text-center">Kehadiran</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($journals)): ?>
                        <tr><td colspan="5" class="p-8 text-center text-gray-400 italic">Belum ada jurnal yang tercatat.</td></tr>
                    <?php endif; ?>

                    <?php foreach($journals as $row): 
                        // Hitung Ringkasan Absensi
                        $sakit = 0; $izin = 0; $alpa = 0;
                        if(isset($attendanceData[$row['id']])) {
                            foreach($attendanceData[$row['id']] as $status) {
                                if($status == 'S') $sakit++;
                                if($status == 'I') $izin++;
                                if($status == 'A') $alpa++;
                            }
                        }
                    ?>
                    <tr class="hover:bg-blue-50/30 transition text-sm">
                        <td class="px-6 py-4 font-mono text-blue-600 font-bold">
                            <?= date('d M Y', strtotime($row['date'])) ?>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-700">
                            <?= $row['topic'] ?>
                        </td>
                        <td class="px-6 py-4 text-gray-500 italic text-xs max-w-xs truncate">
                            <?= $row['notes'] ?: '-' ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if($sakit+$izin+$alpa > 0): ?>
                                <div class="flex justify-center gap-1 text-[10px] font-bold">
                                    <?php if($sakit): ?><span class="bg-yellow-100 text-yellow-700 px-1.5 rounded">S:<?= $sakit ?></span><?php endif; ?>
                                    <?php if($izin): ?><span class="bg-blue-100 text-blue-700 px-1.5 rounded">I:<?= $izin ?></span><?php endif; ?>
                                    <?php if($alpa): ?><span class="bg-red-100 text-red-700 px-1.5 rounded">A:<?= $alpa ?></span><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-green-600 text-[10px] font-bold">Nihil (Hadir Semua)</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center flex justify-center gap-2">
                            <button onclick='openModal("edit", <?= json_encode($row) ?>, <?= json_encode($attendanceData[$row['id']] ?? []) ?>)' 
                                    class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <a href="/academic/journals/delete?id=<?= $row['id'] ?>&schedule_id=<?= $schedule['id'] ?>" 
                               onclick="return confirm('Hapus jurnal ini?')"
                               class="text-red-400 hover:text-red-600 transition" title="Hapus">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="journalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-3xl shadow-2xl overflow-hidden animate__animated animate__zoomIn animate__faster max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-700" id="modalTitle">Form Jurnal</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <form id="journalForm" method="POST" class="flex-1 overflow-y-auto p-6">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">
            <input type="hidden" name="id" id="journalId">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tanggal</label>
                    <input type="date" name="date" id="inputDate" value="<?= date('Y-m-d') ?>" class="w-full p-2.5 border rounded-lg text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Materi / Topik</label>
                    <input type="text" name="topic" id="inputTopic" placeholder="Contoh: Bab 1 - Aljabar" class="w-full p-2.5 border rounded-lg text-sm" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Catatan / Evaluasi</label>
                <textarea name="notes" id="inputNotes" rows="2" class="w-full p-2.5 border rounded-lg text-sm" placeholder="Catatan khusus pertemuan ini..."></textarea>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h4 class="font-bold text-gray-700 text-sm mb-3 border-b pb-2">Absensi Siswa</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 max-h-60 overflow-y-auto custom-scrollbar">
                    <?php foreach($students as $s): ?>
                    <div class="flex items-center justify-between text-sm py-1 border-b border-gray-100 last:border-0">
                        <span class="text-gray-700 font-medium truncate w-1/2"><?= $s['full_name'] ?></span>
                        <div class="flex space-x-1">
                            <?php foreach(['H'=>'Hadir', 'S'=>'Sakit', 'I'=>'Izin', 'A'=>'Alpa'] as $val => $label): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="attendance[<?= $s['id'] ?>]" value="<?= $val ?>" class="peer sr-only att-radio-<?= $s['id'] ?>" <?= $val=='H'?'checked':'' ?>>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border border-gray-300 text-gray-400 peer-checked:text-white peer-checked:border-transparent transition-all
                                    <?= $val=='H' ? 'peer-checked:bg-green-500' : ($val=='S' ? 'peer-checked:bg-yellow-400' : ($val=='I' ? 'peer-checked:bg-blue-500' : 'peer-checked:bg-red-500')) ?>">
                                    <?= $val ?>
                                </span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </form>

        <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2">
            <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-300">Batal</button>
            <button type="submit" form="journalForm" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow">Simpan Jurnal</button>
        </div>
    </div>
</div>

<script>
    function openModal(mode, data = null, attendance = null) {
        const form = document.getElementById('journalForm');
        const title = document.getElementById('modalTitle');
        const modal = document.getElementById('journalModal');

        if (mode === 'add') {
            form.action = '/academic/journals/store';
            title.innerText = 'Tambah Jurnal Pertemuan';
            document.getElementById('journalId').value = '';
            document.getElementById('inputDate').value = '<?= date('Y-m-d') ?>';
            document.getElementById('inputTopic').value = '';
            document.getElementById('inputNotes').value = '';
            
            // Reset Radio to H (Hadir)
            document.querySelectorAll('input[type=radio][value=H]').forEach(r => r.checked = true);
        } else {
            form.action = '/academic/journals/update';
            title.innerText = 'Edit Jurnal Pertemuan';
            document.getElementById('journalId').value = data.id;
            document.getElementById('inputDate').value = data.date;
            document.getElementById('inputTopic').value = data.topic;
            document.getElementById('inputNotes').value = data.notes;

            // Set Attendance
            if (attendance) {
                for (const [studentId, status] of Object.entries(attendance)) {
                    const radio = document.querySelector(`.att-radio-${studentId}[value="${status}"]`);
                    if (radio) radio.checked = true;
                }
            }
        }
        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('journalModal').classList.add('hidden');
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
