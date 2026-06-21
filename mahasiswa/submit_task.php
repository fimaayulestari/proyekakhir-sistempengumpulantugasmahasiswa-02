<?php

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit();
}

$task_id = $_GET['task_id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT t.*, c.class_name
    FROM tasks t
    JOIN classes c ON t.class_id = c.id
    JOIN enrollments e ON c.id = e.class_id
    WHERE t.id = ? AND e.student_id = ?
");

$stmt->execute([$task_id, $_SESSION['user_id']]);

$task = $stmt->fetch();

if (!$task) {
    header('Location: dashboard.php');
    exit();
}

$check = $pdo->prepare("
    SELECT * FROM submissions
    WHERE task_id = ? AND student_id = ?
");

$check->execute([$task_id, $_SESSION['user_id']]);

$submission = $check->fetch();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $submission_text = trim($_POST['submission_text']);
    $drive_link = trim($_POST['drive_link']);

    $file_path = $submission['file_path'] ?? null;
    $file_name = $submission['file_name'] ?? null;
    $file_type = $submission['file_type'] ?? null;

    if (!empty($_FILES['task_file']['name'])) {

        $allowed = [
            'pdf',
            'ppt',
            'pptx',
            'doc',
            'docx',
            'jpg',
            'jpeg',
            'png',
            'zip',
            'rar'
        ];

        $uploadDir = '../uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $originalName = $_FILES['task_file']['name'];

        $tmpName = $_FILES['task_file']['tmp_name'];

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {

            $error = "Format file tidak didukung!";

        } else {

            $newName = time() . '_' . uniqid() . '.' . $ext;

            $destination = $uploadDir . $newName;

            move_uploaded_file($tmpName, $destination);

            $file_path = $destination;
            $file_name = $originalName;
            $file_type = $ext;
        }
    }

    if (!$error) {

        date_default_timezone_set('Asia/Jakarta');

        $currentTime = new DateTime("now", new DateTimeZone("Asia/Jakarta"));
        $deadlineTime = new DateTime($task['deadline'], new DateTimeZone("Asia/Jakarta"));

        $is_late = ($currentTime > $deadlineTime) ? 1 : 0;

        if ($submission) {

            $stmt = $pdo->prepare("
                UPDATE submissions
                SET
                    submission_text = ?,
                    file_path = ?,
                    file_name = ?,
                    file_type = ?,
                    feedback = ?,
                    submitted_at = NOW(),
                    is_late = ?
                WHERE task_id = ? AND student_id = ?
            ");

            $stmt->execute([
                $submission_text,
                $file_path,
                $file_name,
                $file_type,
                $drive_link,
                $is_late,
                $task_id,
                $_SESSION['user_id']
            ]);

            $message = "Tugas berhasil diperbarui!";

        } else {

            $stmt = $pdo->prepare("
                INSERT INTO submissions
                (
                    task_id,
                    student_id,
                    submission_text,
                    file_path,
                    file_name,
                    file_type,
                    feedback,
                    is_late
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $task_id,
                $_SESSION['user_id'],
                $submission_text,
                $file_path,
                $file_name,
                $file_type,
                $drive_link,
                $is_late
            ]);

            $message = "Tugas berhasil dikumpulkan!";
        }

        $check->execute([$task_id, $_SESSION['user_id']]);
        $submission = $check->fetch();
    }
}

$isDeadlinePassed = date('Y-m-d H:i:s') > $task['deadline'];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Tugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-4xl mx-auto py-8 px-4">

    <div class="bg-white rounded-lg shadow p-6">

        <div class="flex justify-between items-center mb-4">

            <h1 class="text-2xl font-bold">
                <?= htmlspecialchars($task['title']) ?>
            </h1>

            <a href="dashboard.php"
               class="bg-gray-500 text-white px-4 py-2 rounded">
                Kembali
            </a>

        </div>

        <p class="text-gray-600 mb-2">
            Kelas:
            <?= htmlspecialchars($task['class_name']) ?>
        </p>

        <p class="mb-4 text-sm <?= $isDeadlinePassed ? 'text-red-600' : 'text-gray-600' ?>">
            Deadline:
            <?= date('d/m/Y H:i', strtotime($task['deadline'])) ?>
        </p>

        <?php if ($message): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-4">

                <label class="block font-bold mb-2">
                    Jawaban
                </label>

                <textarea
                    name="submission_text"
                    rows="6"
                    class="w-full border rounded-lg px-3 py-2"
                ><?= htmlspecialchars($submission['submission_text'] ?? '') ?></textarea>

            </div>

            <div class="mb-4">

                <label class="block font-bold mb-2">
                    Upload File
                </label>

                <input
                    type="file"
                    name="task_file"
                    class="w-full border rounded-lg px-3 py-2"
                >

                <p class="text-sm text-gray-500 mt-1">
                    PDF, PPT, DOC, JPG, PNG, ZIP, RAR
                </p>

            </div>

            <div class="mb-4">

                <label class="block font-bold mb-2">
                    Link Google Drive / YouTube / Website
                </label>

                <input
                    type="url"
                    name="drive_link"
                    value="<?= htmlspecialchars($submission['feedback'] ?? '') ?>"
                    class="w-full border rounded-lg px-3 py-2"
                    placeholder="https://..."
                >

            </div>

            <?php if (!empty($submission['file_name'])): ?>

                <div class="mb-4 bg-gray-100 p-3 rounded">

                    <p class="font-semibold">
                        File saat ini:
                    </p>

                    <a
                        href="<?= $submission['file_path'] ?>"
                        target="_blank"
                        class="text-blue-600 underline"
                    >
                        <?= htmlspecialchars($submission['file_name']) ?>
                    </a>

                </div>

            <?php endif; ?>

            <button
                type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded"
            >
                <?= $submission ? 'Update Tugas' : 'Kumpulkan Tugas' ?>
            </button>

        </form>

    </div>

</div>

</body>
</html>