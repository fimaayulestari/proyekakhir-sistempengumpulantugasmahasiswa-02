<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $class_code = strtoupper(trim($_POST['class_code']));

    if (empty($class_code)) {
        $error = "Kode kelas wajib diisi!";
    } else {

        $stmt = $pdo->prepare("SELECT * FROM classes WHERE class_code = ?");
        $stmt->execute([$class_code]);

        $class = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($class) {

            $check = $pdo->prepare("SELECT id FROM enrollments WHERE class_id = ? AND student_id = ?");
            $check->execute([$class['id'], $_SESSION['user_id']]);

            if ($check->rowCount() > 0) {

                $error = "Anda sudah terdaftar di kelas ini!";

            } else {

                $insert = $pdo->prepare("INSERT INTO enrollments (class_id, student_id) VALUES (?, ?)");
                $insert->execute([$class['id'], $_SESSION['user_id']]);

                $success = "Berhasil bergabung ke kelas " . htmlspecialchars($class['class_name']) . "!";

                header("Refresh: 2; URL=dashboard.php");
            }

        } else {

            $error = "Kode kelas tidak valid!";

        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gabung Kelas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-md mx-auto px-4 py-8">

    <div class="bg-white rounded-lg shadow-md p-6">

        <h2 class="text-2xl font-bold mb-6">Gabung Kelas</h2>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">
                    Masukkan Kode Kelas
                </label>

                <input 
                    type="text" 
                    name="class_code" 
                    required
                    placeholder="Contoh: ABC12345"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                >

                <p class="text-xs text-gray-500 mt-1">
                    Masukkan kode kelas yang diberikan oleh dosen/dosen
                </p>
            </div>

            <div class="flex space-x-4">

                <button 
                    type="submit"
                    class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600"
                >
                    Gabung
                </button>

                <a 
                    href="dashboard.php"
                    class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600"
                >
                    Batal
                </a>

            </div>

        </form>

    </div>

    <div class="mt-4 text-center">
        <p class="text-gray-500 text-sm">
            Belum punya kode kelas? Hubungi dosen/dosen Anda.
        </p>
    </div>

</div>

</body>
</html>