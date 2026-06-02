<?php

session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {

    header('Location: ../auth/login.php');
    exit();

}

if (!isset($_GET['id'])) {

    header('Location: dashboard.php');
    exit();

}

$class_id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT *
    FROM classes
    WHERE id = ?
");

$stmt->execute([$class_id]);

$class = $stmt->fetch();

if (!$class) {

    die("Kelas tidak ditemukan");

}

$taskStmt = $pdo->prepare("
    SELECT *
    FROM tasks
    WHERE class_id = ?
    AND (
    publish_at IS NULL
    OR publish_at <= NOW()
    )
    ORDER BY created_at DESC
");

$taskStmt->execute([$class_id]);

$tasks = $taskStmt->fetchAll();

$classSidebar = $pdo->prepare("
    SELECT classes.*
    FROM enrollments

    JOIN classes
    ON enrollments.class_id = classes.id

    WHERE enrollments.student_id = ?
");

$classSidebar->execute([$_SESSION['user_id']]);

$classes = $classSidebar->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Classwork</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

body{
    background:#f1f3f4;
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
    padding:0 18px;
    z-index:999;
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

.sidebar{
    transition:.3s;
}

.sidebar.closed{
    transform:translateX(-240px);
}

.main-content{
    transition:.3s;
}

.main-content.full{
    margin-left:0 !important;
}

.sidebar-menu{
    padding:12px 0;
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

.class-active{
    background:#c2e7ff;
}

.class-active .class-name{
    font-weight:600;
}

.class-avatar{
    width:28px;
    height:28px;
    min-width:28px;
    border-radius:50%;
    background:#d2e3fc;
    color:#1967d2;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:13px;
    flex-shrink:0;
    margin-top:2px;
}

.class-info{
    flex:1;
}

.class-name{
    font-size:14px;
    font-weight:600;
    line-height:1.4;
}

.class-sub{
    font-size:12px;
    color:#5f6368;
    line-height:1.5;
    margin-top:2px;
}

.main-content{
    margin-left:240px;
    padding:88px 28px 40px;
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

        <a href="calendar.php"
           class="sidebar-item">

            📅
            <span>Calendar</span>

        </a>

        <div class="sidebar-title">

            Enrolled

        </div>

        <a href="todo.php"
           class="sidebar-item">

            📝
            <span>To-do</span>

        </a>

        <?php foreach($classes as $c): ?>

        <a href="class_detail.php?id=<?= $c['id'] ?>"
        class="class-link <?= ($c['id'] == $class_id) ? 'class-active' : '' ?>">

            <div class="class-avatar">
                <?= strtoupper(substr($c['class_name'],0,1)) ?>
            </div>

           <div class="class-info">

            <div class="class-name">
                <?= htmlspecialchars($c['class_name']) ?>
            </div>

            <div class="class-sub">
                <?= htmlspecialchars($c['description']) ?>
            </div>

        </div>

        </a>

        <?php endforeach; ?>

        <a href="archived.php"
           class="sidebar-item mt-4">

            📦
            <span>Archived classes</span>

        </a>

    </div>

</div>

<div id="mainContent"
     class="main-content ml-[240px] pt-20 px-8 pb-10">

    <div class="bg-[#546e7a] rounded-3xl p-10 text-white mb-6">

        <h1 class="text-5xl font-bold mb-3">

            <?= htmlspecialchars($class['class_name']) ?>

        </h1>

        <p class="text-lg text-gray-200">
            <?= htmlspecialchars($class['description']) ?>
        </p>

    </div>

    <div class="bg-white rounded-2xl shadow-sm mb-8">

        <div class="flex gap-10 px-8 py-5 border-b">

            <a href="class_detail.php?id=<?= $class['id'] ?>"
               class="text-gray-500 hover:text-blue-600">

                Stream

            </a>

            <a href="classwork.php?id=<?= $class['id'] ?>"
               class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-2">

                Classwork

            </a>

            <a href="people.php?id=<?= $class['id'] ?>"
               class="text-gray-500 hover:text-blue-600">

                People

            </a>

        </div>

    </div>

<div class="space-y-4">

    <?php foreach($tasks as $task): ?>

        <a href="task_detail.php?id=<?= $task['id'] ?>"
           class="block bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition">

            <div class="flex items-center justify-between p-5">

                <div class="flex items-center gap-4">

                    <div class="w-12 h-12 rounded-full bg-cyan-100 flex items-center justify-center text-xl">

                        📋

                    </div>

                    <div>

                        <h2 class="font-semibold text-gray-800">

                            <?= htmlspecialchars($task['title']) ?>

                        </h2>

                        <p class="text-sm text-gray-500">

                            Tugas • <?= date('d M Y', strtotime($task['created_at'])) ?>

                        </p>

                    </div>

                </div>

                <div class="text-sm text-red-500">

                    <?= date('d M Y H:i', strtotime($task['deadline'])) ?>

                </div>

            </div>

        </a>

    <?php endforeach; ?>

</div>

<script>

const menuToggle =
    document.getElementById('menuToggle');

const sidebar =
    document.getElementById('sidebar');

const mainContent =
    document.getElementById('mainContent');

menuToggle.addEventListener('click', ()=>{

    sidebar.classList.toggle('closed');
    mainContent.classList.toggle('full');

});

</script>

</body>
</html>