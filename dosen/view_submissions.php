<?php

session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'dosen') {

    header('Location: ../auth/login.php');
    exit();
}

$task_id = $_GET['task_id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT 
        t.*, 
        c.class_name,
        c.teacher_id
    FROM tasks t

    JOIN classes c
        ON t.class_id = c.id

    WHERE t.id = ?
");

$stmt->execute([$task_id]);

$task = $stmt->fetch();

if (!$task || $task['teacher_id'] != $_SESSION['user_id']) {

    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $submission_id = $_POST['submission_id'] ?? null;

    $grade = trim($_POST['grade'] ?? '');

    $feedback = trim($_POST['feedback'] ?? '');

    if ($grade === '') {

        $grade = null;
    }

    $stmt = $pdo->prepare("
        UPDATE submissions
        SET 
            grade = ?,
            feedback = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $grade,
        $feedback,
        $submission_id
    ]);

    header("Location: view_submissions.php?task_id=$task_id");
    exit();
}

$submissions = $pdo->prepare("
    SELECT 
        s.*,
        u.full_name,
        u.email

    FROM submissions s

    JOIN users u
        ON s.student_id = u.id

    WHERE s.task_id = ?

    ORDER BY s.submitted_at DESC
");

$submissions->execute([$task_id]);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Pengumpulan Tugas</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100">

<div class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-8">

    <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6">

        <h1 class="text-lg sm:text-2xl font-bold text-gray-800">

            <?= htmlspecialchars($task['title']) ?>

        </h1>

        <p class="text-gray-600 mt-1 text-sm sm:text-base">

            Kelas:
            <?= htmlspecialchars($task['class_name']) ?>

        </p>

        <p class="text-red-500 font-semibold mt-2 text-sm sm:text-base">

            Deadline:
            <?= date('d/m/Y H:i', strtotime($task['deadline'])) ?>

        </p>

        <a href="tasks.php"
           class="inline-block mt-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm sm:text-base">

            ← Kembali

        </a>

    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">

        <div class="overflow-x-auto">

        <table class="min-w-full divide-y divide-gray-200">

            <thead class="bg-gray-50">

                <tr>

                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                        Mahasiswa
                    </th>

                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                        Waktu
                    </th>

                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                        Status
                    </th>

                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                        File
                    </th>

                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                        Link
                    </th>

                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                        Nilai
                    </th>

                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody class="bg-white divide-y divide-gray-200">

            <?php if ($submissions->rowCount() == 0): ?>

                <tr>

                    <td colspan="7"
                        class="text-center py-6 text-gray-500">

                        Belum ada pengumpulan tugas

                    </td>

                </tr>

            <?php else: ?>

                <?php while ($sub = $submissions->fetch()): ?>

                    <tr>

                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">

                            <div class="font-semibold">

                                <?= htmlspecialchars($sub['full_name']) ?>

                            </div>

                            <div class="text-sm text-gray-500">

                                <?= htmlspecialchars($sub['email']) ?>

                            </div>

                        </td>

                        <td class="px-3 sm:px-6 py-4 text-sm whitespace-nowrap">

                            <?= $sub['submitted_at']
                                ? date('d/m/Y H:i', strtotime($sub['submitted_at']))
                                : '-' ?>

                        </td>

                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">

                            <?php if ($sub['submitted_at']): ?>

                                <?php 
                                $deadline = strtotime($task['deadline']);
                                $submitted = strtotime($sub['submitted_at']);

                                if ($submitted > $deadline):
                                ?>

                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-sm">

                                        Terlambat

                                    </span>

                                <?php else: ?>

                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-sm">

                                        Tepat Waktu

                                    </span>

                                <?php endif; ?>

                            <?php else: ?>

                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-sm">

                                    Belum Kumpul

                                </span>

                            <?php endif; ?>

                        </td>

                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">

                            <?php if (!empty($sub['file_path'])): ?>

                                <a
                                    href="../uploads/<?= htmlspecialchars($sub['file_path']) ?>"
                                    target="_blank"
                                    class="text-blue-600 font-semibold hover:underline"
                                >

                                    Lihat File

                                </a>

                            <?php else: ?>

                                <span class="text-gray-400">

                                    Tidak ada file

                                </span>

                            <?php endif; ?>

                        </td>

                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">

                            <?php if (!empty($sub['submission_link'])): ?>

                                <a 
                                    href="<?= htmlspecialchars($sub['submission_link']) ?>"
                                    target="_blank"
                                    class="text-blue-600 font-semibold hover:underline">

                                    Buka Link

                                </a>

                            <?php else: ?>

                                <span class="text-gray-400">

                                    Tidak ada link

                                </span>

                            <?php endif; ?>

                        </td>

                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">

                            <?php if ($sub['grade'] !== null): ?>

                                <span class="font-bold text-green-600">

                                    <?= $sub['grade'] ?>

                                </span>

                            <?php else: ?>

                                <span class="text-gray-400">

                                    -

                                </span>

                            <?php endif; ?>

                        </td>

                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">

                            <button
                                onclick='showGradeModal(
                                    <?= $sub["id"] ?>,
                                    <?= json_encode($sub["submission_text"] ?? "") ?>,
                                    <?= json_encode($sub["grade"] ?? "") ?>,
                                    <?= json_encode($sub["feedback"] ?? "") ?>
                                )'

                                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">

                                Beri Nilai

                            </button>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php endif; ?>

            </tbody>

        </table>

        </div>

    </div>

</div>

<div id="gradeModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center px-4 z-50">

    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">

        <h2 class="text-lg sm:text-xl font-bold mb-4">

            Beri Nilai

        </h2>

        <form method="POST">

            <input type="hidden"
                   name="submission_id"
                   id="submission_id">

            <div class="mb-4">

                <label class="block font-semibold mb-2">

                    Jawaban Mahasiswa

                </label>

                <div id="answer_text"
                     class="bg-gray-100 p-3 rounded max-h-40 overflow-y-auto text-sm">
                </div>

            </div>

            <div class="mb-4">

                <label class="block font-semibold mb-2">

                    Nilai

                </label>

                <input type="number"
                       step="0.01"
                       min="0"
                       max="100"
                       name="grade"
                       id="grade"
                       class="w-full border rounded px-3 py-2">

            </div>

            <div class="mb-4">

                <label class="block font-semibold mb-2">

                    Feedback

                </label>

                <textarea
                    name="feedback"
                    id="feedback"
                    rows="4"
                    class="w-full border rounded px-3 py-2"></textarea>

            </div>

            <div class="flex justify-end gap-3">

                <button type="button"
                        onclick="closeModal()"
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">

                    Batal

                </button>

                <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">

                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>

<script>

function showGradeModal(id, answer, grade, feedback) {

    document.getElementById('submission_id').value = id;

    document.getElementById('answer_text').innerHTML =
        answer.replace(/\n/g, '<br>');

    document.getElementById('grade').value =
        grade ?? '';

    document.getElementById('feedback').value =
        feedback ?? '';

    document.getElementById('gradeModal').classList.remove('hidden');

    document.getElementById('gradeModal').classList.add('flex');
}

function closeModal() {

    document.getElementById('gradeModal').classList.add('hidden');

    document.getElementById('gradeModal').classList.remove('flex');
}

window.onclick = function(event) {

    let modal = document.getElementById('gradeModal');

    if (event.target == modal) {

        closeModal();
    }
}

</script>

</body>
</html>