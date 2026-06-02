<?php

session_start();
require_once '../config/database.php';

if ($_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT *
    FROM classes
    WHERE id = ?
    AND teacher_id = ?
");

$stmt->execute([
    $id,
    $_SESSION['user_id']
]);

$class = $stmt->fetch();

if(!$class){
    die("Kelas tidak ditemukan");
}

if(isset($_POST['update'])){

    $update = $pdo->prepare("
        UPDATE classes
        SET
            class_name = ?,
            description = ?
        WHERE id = ?
    ");

    $update->execute([
        $_POST['class_name'],
        $_POST['description'],
        $id
    ]);

    header("Location: manage_class.php?id=".$id);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Kelas</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-2xl mx-auto mt-10 bg-white p-8 rounded-xl shadow">

    <h1 class="text-2xl font-bold mb-6">
        Edit Kelas
    </h1>

    <form method="POST">

        <div class="mb-4">

            <label class="block mb-2">
                Nama Kelas
            </label>

            <input
                type="text"
                name="class_name"
                value="<?= htmlspecialchars($class['class_name']) ?>"
                class="w-full border p-3 rounded-lg"
                required
            >

        </div>

        <div class="mb-4">

            <label class="block mb-2">
                Deskripsi
            </label>

            <textarea
                name="description"
                rows="4"
                class="w-full border p-3 rounded-lg"
            ><?= htmlspecialchars($class['description']) ?></textarea>

        </div>

        <button
            type="submit"
            name="update"
            class="bg-blue-600 text-white px-5 py-3 rounded-lg"
        >
            Simpan Perubahan
        </button>

    </form>

</div>

</body>
</html>