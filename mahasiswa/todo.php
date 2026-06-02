<?php

session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {

    header('Location: ../auth/login.php');
    exit();

}

$stmt = $pdo->prepare("
    SELECT 
        tasks.*,
        classes.class_name,

        submissions.id AS submitted

    FROM enrollments

    JOIN classes
        ON enrollments.class_id = classes.id

    JOIN tasks
        ON classes.id = tasks.class_id

    LEFT JOIN submissions
        ON tasks.id = submissions.task_id
        AND submissions.student_id = ?

    WHERE enrollments.student_id = ?

    ORDER BY tasks.deadline ASC
");

$stmt->execute([
    $_SESSION['user_id'],
    $_SESSION['user_id']
]);

$tasks = $stmt->fetchAll();

$classStmt = $pdo->prepare("
    SELECT 
        classes.*
    FROM enrollments

    JOIN classes
        ON enrollments.class_id = classes.id

    WHERE enrollments.student_id = ?

    ORDER BY classes.created_at DESC
");

$classStmt->execute([$_SESSION['user_id']]);

$classes = $classStmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>To Do Mahasiswa</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>

        body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#f1f3f4;
    overflow-x:hidden;
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
    align-items:center;
    justify-content:space-between;
    padding:0 18px;
    z-index:999;
}

.nav-left{
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

.nav-right{
    display:flex;
    align-items:center;
    gap:18px;
}

.icon-btn{
    width:38px;
    height:38px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    font-size:18px;
}

.icon-btn:hover{
    background:#f1f3f4;
}

.profile{
    width:38px;
    height:38px;
    border-radius:50%;
    background:#d93025;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
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

.main{
    transition:.3s;
}

.main.full{
    margin-left:0;
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

.class-info{
    flex:1;
}

.class-name{
    font-size:14px;
    line-height:1.4;
}

.class-sub{
    font-size:12px;
    color:#5f6368;
    margin-top:2px;
}

.main{
    margin-left:240px;
    padding-top:88px;
    padding-left:28px;
    padding-right:28px;
    padding-bottom:40px;
}.main{
    margin-left:240px;
    padding-top:88px;
    padding-left:28px;
    padding-right:28px;
    padding-bottom:40px;

    transition:.3s;
}

.main.full{
    margin-left:0;
}

.banner{
    height:180px;

    background-image:url('https://www.gstatic.com/classroom/themes/img_graduation.jpg');

    background-size:cover;
    background-position:center;

    border-radius:24px;

    padding:32px;

    display:flex;
    align-items:flex-end;

    margin-bottom:24px;

    position:relative;
    overflow:hidden;
}

.banner::before{
    content:'';
    position:absolute;
    inset:0;
    background:rgba(0,0,0,0.30);
}

.banner-content{
    position:relative;
    z-index:2;
    color:white;
}

.banner-title{
    font-size:18px;
    color:#202124;
    margin-bottom:8px;
}

.banner-desc{
    color:#3c4043;
    font-size:14px;
    line-height:1.5;
}

.banner img{
    position:absolute;
    right:0;
    top:0;
    height:100%;
}

.class-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
    gap:20px;
}

.class-card{
    background:white;
    border:1px solid #dadce0;
    border-radius:14px;
    overflow:hidden;
    text-decoration:none;
    color:#202124;
    transition:0.2s;
}

.class-card:hover{
    box-shadow:0 4px 12px rgba(0,0,0,0.12);
}

.card-header{
    height:110px;
    padding:16px;
    position:relative;

    background-image:url('https://www.gstatic.com/classroom/themes/img_graduation.jpg');

    background-size:cover;
    background-position:center;

    color:white;
    overflow:hidden;
}

.card-header::before{
    content:'';
    position:absolute;
    inset:0;
    background:rgba(0,0,0,0.35);
}

.card-header-content{
    position:relative;
    z-index:2;
}

.card-title{
    font-size:16px;
    font-weight:600;
    line-height:1.3;
    margin-bottom:4px;

    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
}

.card-subtitle{
    font-size:13px;
    opacity:0.95;
    margin-bottom:4px;
}

.teacher{
    font-size:12px;
    opacity:0.95;
}

.card-body{
    height:70px;
    background:white;
}

.card-footer{
    height:48px;
    border-top:1px solid #dadce0;

    display:flex;
    justify-content:flex-end;
    align-items:center;

    gap:20px;
    padding:0 18px;

    color:#5f6368;
}

.footer-icon{
    cursor:pointer;
    font-size:20px;
}

.footer-icon:hover{
    color:#202124;
}

.task-card{
    transition:.2s;
}

.task-card:hover{
    transform:translateY(-3px);
    box-shadow:0 8px 20px rgba(0,0,0,.12);
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
            class="sidebar-item sidebar-active">

            📝
            <span>To-do</span>

        </a>

        <?php foreach($classes as $class): ?>

            <a href="class_detail.php?id=<?= $class['id'] ?>"
               class="class-link">

                <div class="class-avatar">

                    <?= strtoupper(substr($class['class_name'],0,1)) ?>

                </div>

                <div class="class-info">

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
           class="sidebar-item mt-4">

            📦
            <span>Archived classes</span>

        </a>

    </div>

</div>

<div class="main" id="mainContent">

    <div class="banner">

        <div class="banner-content">

            <div class="text-4xl font-light mb-2">
                To Do
            </div>

            <div class="text-lg">
                Semua tugas dari kelas yang kamu ikuti
            </div>

        </div>

    </div>

    <div class="space-y-6">

        <?php foreach($tasks as $task): ?>

            <div class="task-card bg-white rounded-2xl shadow-md p-6">

                <div class="flex justify-between items-start">

                    <div>

                        <h2 class="text-2xl font-bold text-gray-800 mb-2">

                            <?= htmlspecialchars($task['title']) ?>

                        </h2>

                        <p class="text-blue-600 font-medium mb-3">

                            <?= htmlspecialchars($task['class_name']) ?>

                        </p>

                        <p class="text-gray-600 mb-4">

                            <?= htmlspecialchars($task['description']) ?>

                        </p>

                        <div class="text-sm text-red-500 font-medium">

                            Deadline:
                            <?= date('d M Y H:i', strtotime($task['deadline'])) ?>

                        </div>

                    </div>

                    <div>

                        <?php if($task['submitted']): ?>

                            <span class="bg-green-100 text-green-700 px-4 py-2 rounded-xl font-semibold">

                                Sudah Upload

                            </span>

                        <?php else: ?>

                            <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-xl font-semibold">

                                Belum Upload

                            </span>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="mt-6">

                    <a href="submit_task.php?task_id=<?= $task['id'] ?>"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl">

                        Kerjakan Tugas

                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

<script>

const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');

menuToggle.addEventListener('click', function(){

    sidebar.classList.toggle('closed');
    mainContent.classList.toggle('full');

});

</script>

</body>
</html>