<?php

session_start();

require_once '../config/database.php';

if (isset($_SESSION['user_id'])) {

    if ($_SESSION['role'] == 'dosen') {

        header('Location: ../dosen/dashboard.php');

    } else {

        header('Location: ../mahasiswa/dashboard.php');

    }

    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("
        SELECT * FROM users
        WHERE username = ? OR email = ?
    ");

    $stmt->execute([$username, $username]);

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['role']      = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];

        if ($user['role'] == 'dosen') {

            header('Location: ../dosen/dashboard.php');

        } else {

            header('Location: ../mahasiswa/dashboard.php');

        }

        exit();

    } else {

        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - Sistem Pengumpulan Tugas</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>

        body{
            background: linear-gradient(
                135deg,
                #2563eb,
                #4f46e5
            );
        }

        .card{
            animation:fadeIn 0.5s ease;
        }

        @keyframes fadeIn{
            from{
                opacity:0;
                transform:translateY(20px);
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }

    </style>

</head>

<body class="min-h-screen flex items-center justify-center px-4">

<div class="w-full max-w-md">

    <div class="bg-white rounded-2xl shadow-2xl p-8 card">

        <div class="text-center mb-8">

            <h1 class="text-3xl font-bold text-gray-800">
                Sistem Pengumpulan Tugas
            </h1>

            <p class="text-gray-500 mt-2">
                Silahkan login untuk melanjutkan
            </p>

        </div>

        <?php if (!empty($error)): ?>

            <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-5">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="mb-4">

                <label class="block text-gray-700 font-semibold mb-2">
                    Username atau Email
                </label>

                <input
                    type="text"
                    name="username"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">

            </div>

            <div class="mb-6">

                <label class="block text-gray-700 font-semibold mb-2">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">

            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition duration-300">

                Login

            </button>

        </form>

        <div class="text-center mt-6">

            <p class="text-gray-500">

                Belum punya akun?

                <a href="register.php"
                   class="text-blue-600 hover:underline font-semibold">

                    Register

                </a>

            </p>

        </div>

    </div>

</div>

</body>
</html>