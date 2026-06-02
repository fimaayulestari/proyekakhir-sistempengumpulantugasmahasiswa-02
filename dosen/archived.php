<?php

session_start();
require_once '../config/database.php';

if ($_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit();
}

$stmt = $pdo->prepare("
    SELECT *
    FROM classes
    WHERE teacher_id = ?
    AND archived = 1
    ORDER BY created_at DESC
");

$stmt->execute([$_SESSION['user_id']]);

$classes = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelas Diarsipkan</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

body{
    margin:0;
    background:#f1f3f4;
    font-family:Arial,sans-serif;
}

.navbar{
    position:fixed;
    top:0;
    left:0;
    right:0;
    height:64px;
    background:white;
    border-bottom:1px solid #dadce0;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 24px;
    z-index:999;
}

.logo{
    display:flex;
    align-items:center;
    gap:10px;
}

.logo-icon{
    font-size:28px;
}

.logo-text{
    font-size:30px;
    color:#5f6368;
    font-weight:400;
}

.nav-left{
    display:flex;
    align-items:center;
    gap:18px;
}

.nav-right{
    display:flex;
    align-items:center;
    gap:18px;
}

.menu-btn{
    width:40px;
    height:40px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    font-size:20px;
}

.menu-btn:hover{
    background:#f1f3f4;
}

.sidebar{
    position:fixed;
    top:64px;
    left:0;
    bottom:0;
    width:240px;
    background:#f8f9fa;
    border-right:1px solid #dadce0;
    overflow-y:auto;
}

.sidebar-menu{
    padding-top:12px;
}

.sidebar-item{
    display:flex;
    align-items:center;
    gap:18px;
    height:48px;
    padding:0 20px;
    color:#202124;
    text-decoration:none;
    border-top-right-radius:24px;
    border-bottom-right-radius:24px;
    margin-right:12px;
    font-size:14px;
}

.sidebar-item:hover{
    background:#e8f0fe;
}

.sidebar-active{
    background:#c2e7ff;
    font-weight:600;
}

.sidebar-title{
    padding:18px 24px 10px;
    font-size:12px;
    color:#5f6368;
    font-weight:bold;
    text-transform:uppercase;
}

.class-link{
    display:flex;
    align-items:flex-start;
    gap:12px;
    padding:10px 24px;
    text-decoration:none;
    color:#202124;
}

.class-link:hover{
    background:#e8eaed;
}

.class-avatar{
    width:28px;
    height:28px;
    border-radius:50%;
    background:#d2e3fc;
    color:#1967d2;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:13px;
    flex-shrink:0;
}

.class-name{
    font-size:14px;
}

.class-sub{
    font-size:12px;
    color:#5f6368;
}

.main{
    margin-left:240px;
    padding-top:88px;
    padding-left:28px;
    padding-right:28px;
    padding-bottom:40px;
    transition:.3s ease;
}

.banner{
    background:#d3e3fd;
    border-radius:24px;
    padding:26px;
    margin-bottom:24px;
}

.banner h1{
    margin:0 0 8px;
    font-size:18px;
    color:#202124;
}

.banner p{
    color:#3c4043;
    font-size:14px;
}

</style>

</head>
<body>

<div class="navbar">

    <div class="nav-left">

        <div class="menu-btn" id="menuToggle">

            ☰

        </div>

        <div class="logo">

            <div class="logo-icon">

                📚

            </div>

            <div class="logo-text">

                Sistem Pengumpulan Tugas

            </div>

        </div>

    </div>

    <div class="nav-right">

    <a href="create_class.php"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">

        + Buat Kelas

    </a>

    <a href="../logout.php"
       class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium">

        Logout

    </a>

    </div>

</div>

<div class="sidebar" id="sidebar">

    <div class="sidebar-menu">

        <a href="dashboard.php"
           class="sidebar-item">

            🏠
            <span>Home</span>

        </a>

        <a href="create_class.php"
            class="sidebar-item">

             ➕
             <span>Buat Kelas</span>

        </a>

        <a href="create_task.php"
            class="sidebar-item">
    
             📝
             <span>Buat Tugas</span>

        </a>

        <a href="tasks.php"
            class="sidebar-item">

            📋
            <span>Semua Tugas</span>

        </a>

        <div class="sidebar-title">

            KELAS ANDA

        </div>

    <?php

    $sidebarClass = $pdo->prepare("
        SELECT *
        FROM classes
        WHERE teacher_id = ?
        AND archived = 0
        ORDER BY created_at DESC
    ");

    $sidebarClass->execute([
        $_SESSION['user_id']
    ]);

    foreach($sidebarClass as $class):
    ?>

        <a href="manage_class.php?id=<?= $class['id'] ?>"
           class="class-link">

            <div class="class-avatar">

                <?= strtoupper(substr($class['class_name'],0,1)) ?>

            </div>

            <div>

                <div class="class-name">

                    <?= htmlspecialchars($class['class_name']) ?>

                </div>

                <div class="class-sub">

                    <?= htmlspecialchars($class['description']) ?>

                </div>

            </div>

        </a>

    <?php endforeach; ?>

    <a href="archived.php"
       class="sidebar-item sidebar-active">

        📦 Archived Classes

    </a>

</div>

</div>
</div>

<div class="main">

    <div class="banner">

        <h1 style="font-size:18px; margin-bottom:8px;">
            📦 Kelas Diarsipkan
        </h1>

        <p>
            Daftar kelas yang telah diarsipkan
        </p>

    </div>

    <?php if(count($classes) > 0): ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <?php foreach($classes as $class): ?>

                <div class="bg-white rounded-3xl p-6 shadow">

                    <h2 class="text-xl font-bold mb-2">

                        <?= htmlspecialchars($class['class_name']) ?>

                    </h2>

                    <p class="text-gray-600 mb-5">

                        <?= htmlspecialchars($class['description']) ?>

                    </p>

                    <a href="restore_class.php?id=<?= $class['id'] ?>"
                       class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-xl">

                        Pulihkan

                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="bg-white rounded-3xl p-10 text-center">

            Belum ada kelas yang diarsipkan.

        </div>

    <?php endif; ?>

</div>
</body>
</html>