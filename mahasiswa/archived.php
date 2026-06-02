<?php

session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {

    header("Location: ../auth/login.php");
    exit();

}

$stmt = $pdo->prepare("
    SELECT classes.*
    FROM enrollments

    JOIN classes
        ON enrollments.class_id = classes.id

    WHERE enrollments.student_id = ?
    AND classes.archived = 1

    ORDER BY classes.created_at DESC
");

$stmt->execute([$_SESSION['user_id']]);

$classes = $stmt->fetchAll();

$sidebarStmt = $pdo->prepare("
    SELECT classes.*
    FROM enrollments

    JOIN classes
        ON enrollments.class_id = classes.id

    WHERE enrollments.student_id = ?
    AND classes.archived = 0

    ORDER BY classes.created_at DESC
");

$sidebarStmt->execute([$_SESSION['user_id']]);

$sidebarClasses = $sidebarStmt->fetchAll();

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
   content="width=device-width, initial-scale=1.0">

<title>Archived Classes</title>

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
}

.nav-right{
    display:flex;
    align-items:center;
    gap:18px;
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
    transition:transform .3s ease;
}

.sidebar.closed{
    transform:translateX(-240px);
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

.main.full{
    margin-left:0;
}

.banner{
    background:#d3e3fd;
    border-radius:24px;
    padding:26px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:24px;
}

.banner-left{
    display:flex;
    gap:20px;
    align-items:flex-start;
}

.banner-icon{
    font-size:60px;
}

.banner-title{
    font-size:18px;
    color:#202124;
    margin-bottom:8px;
}

.banner-desc{
    color:#3c4043;
    font-size:14px;
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
}

.card-header{
    height:110px;
    padding:16px;
    position:relative;
    background-image:url('https://www.gstatic.com/classroom/themes/img_graduation.jpg');
    background-size:cover;
    background-position:center;
    color:white;
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
}

.card-subtitle{
    font-size:13px;
    margin-top:4px;
}

.teacher{
    font-size:12px;
    margin-top:6px;
}

.card-body{
    height:70px;
}

.empty-card{
    background:white;
    padding:30px;
    border-radius:14px;
    text-align:center;
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

        <div class="logo-icon">📚</div>

        <div class="logo-text">
            Sistem Pengumpulan Tugas
        </div>

    </div>

</div>

<div class="nav-right">

    <a href="../logout.php"
       class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">

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

    <?php foreach($sidebarClasses as $class): ?>

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
       class="sidebar-item sidebar-active mt-4">

        📦
        <span>Archived classes</span>

    </a>

</div>

</div>

<div class="main" id="mainContent">

<div class="banner">

    <div class="banner-left">

        <div class="banner-icon">
            📦
        </div>

        <div>

            <div class="banner-title">
                Archived Classes
            </div>

            <div class="banner-desc">
                Daftar kelas yang telah diarsipkan oleh dosen.
            </div>

        </div>

    </div>

</div>

<?php if(count($classes) > 0): ?>

    <div class="class-grid">

        <?php foreach($classes as $class): ?>

            <div class="class-card">

                <div class="card-header">

                    <div class="card-header-content">

                        <div class="card-title">
                            <?= htmlspecialchars($class['class_name']) ?>
                        </div>

                        <div class="card-subtitle">
                            <?= htmlspecialchars($class['class_code']) ?>
                        </div>

                        <div class="teacher">
                            Kelas Diarsipkan
                        </div>

                    </div>

                </div>

                <div class="card-body"></div>

            </div>

        <?php endforeach; ?>

    </div>

<?php else: ?>

    <div class="empty-card">

        Belum ada kelas yang diarsipkan.

    </div>

<?php endif; ?>

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
