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
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $first_name = trim($_POST['first_name']);

    $last_name = trim($_POST['last_name']);

    $username  = trim($_POST['username']);

    $email     = trim($_POST['email']);

    $password  = $_POST['password'];

    $role = 'mahasiswa';


    if (
        empty($first_name) ||
        empty($last_name) ||
        empty($username) ||
        empty($email) ||
        empty($password)
    ) {

        $error = "Semua field wajib diisi!";

    } else {

        $check = $pdo->prepare("
            SELECT *
            FROM users
            WHERE username = ? OR email = ?
        ");

        $check->execute([$username, $email]);

        if ($check->fetch()) {

            $error = "Username atau email sudah digunakan!";

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare("
                INSERT INTO users
                (
                    first_name,
                    last_name,
                    username,
                    email,
                    password,
                    role
                )
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $first_name,
                $last_name,
                $username,
                $email,
                $hashedPassword,
                $role
            ]);

            $success = "Registrasi berhasil!";

            header("refresh:2;url=login.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Register - Sistem Pengumpulan Tugas</title>

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

<body class="min-h-screen flex items-center justify-center px-4 py-10">

<div class="w-full max-w-md">

    <div class="bg-white rounded-2xl shadow-2xl p-8 card">

        <div class="text-center mb-8">

            <h1 class="text-3xl font-bold text-gray-800">
                Daftar Akun
            </h1>

            <p class="text-gray-500 mt-2">
                Buat akun baru untuk masuk ke sistem
            </p>

        </div>

        <?php if (!empty($error)): ?>

            <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-5">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>

        <?php if (!empty($success)): ?>

            <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-5">

                <?= htmlspecialchars($success) ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="grid grid-cols-2 gap-4 mb-4">

                <div>

                    <label class="block text-gray-700 font-semibold mb-2">

                        Nama Depan

                    </label>

                    <input
                        type="text"
                        name="first_name"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">

                </div>

                <div>

                    <label class="block text-gray-700 font-semibold mb-2">

                        Nama Belakang

                    </label>

                    <input
                        type="text"
                        name="last_name"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">

                </div>

            </div>

            <div class="mb-4">

                <label class="block text-gray-700 font-semibold mb-2">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">

            </div>

            <div class="mb-4">

                <label class="block text-gray-700 font-semibold mb-2">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
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

                Daftar

            </button>

        </form>

        <div class="text-center mt-6">

            <a href="login.php"
               class="text-blue-600 hover:underline">

                Kembali ke Login

            </a>

        </div>

    </div>

</div>

</body>
</html>