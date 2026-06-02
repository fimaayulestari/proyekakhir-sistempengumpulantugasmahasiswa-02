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
    AND archived = 0
    ORDER BY created_at DESC
");

$stmt->execute([$_SESSION['user_id']]);

$classes = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Dashboard Dosen</title>

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
    line-height:1.5;
    max-width:780px;
}

.banner-link{
    color:#1967d2;
    text-decoration:none;
    font-size:14px;
}

.class-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(320px,1fr));
    gap:24px;
    align-items:start;
}

.class-card{
    background:white;
    border:1px solid #dadce0;
    border-radius:14px;
    overflow:hidden;
    display:flex;
    flex-direction:column;
}

.class-card a{
    text-decoration:none;
    color:inherit;
    display:block;
} 

.class-card:hover{
    box-shadow:0 4px 12px rgba(0,0,0,0.12);
}

.class-active{
    background:#c2e7ff;
}

.class-active .class-name{
    font-weight:600;
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
    height:80px;
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
           class="sidebar-item sidebar-active">

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

        <?php foreach($classes as $class): ?>

            <a href="manage_class.php?id=<?= $class['id'] ?>"
                class="class-link <?= (isset($_GET['id']) && $_GET['id'] == $class['id']) ? 'class-active' : '' ?>">

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

        <div class="banner-left">

            <div class="banner-icon">

                ✍️

            </div>

            <div>

                <div class="banner-title">

                    Dashboard Dosen

                </div>

                <div class="banner-desc">

                   Kelola kelas, buat tugas, melihat pengumpulan tugas mahasiswa dan memberikan nilai.

                </div>

            </div>

        </div>

        <a href="#"
           class="banner-link">

            Learn more

        </a>

    </div>

<div class="class-grid">

<?php foreach($classes as $class): ?>

    <div class="class-card">

        <a href="manage_class.php?id=<?= $class['id'] ?>" style="text-decoration:none;color:inherit;">

            <div class="card-header">

                <div class="card-header-content">

                    <div class="card-title">
                        <?= htmlspecialchars($class['class_name']) ?>
                    </div>

                    <div class="card-subtitle">
                        <?= htmlspecialchars($class['class_code']) ?>
                    </div>

                    <div class="teacher">
                        <?= htmlspecialchars($_SESSION['full_name']) ?>
                    </div>

                </div>

            </div>

            <div class="card-body"></div>

        </a>

        <div class="card-footer">

        <a href="manage_class.php?id=<?= $class['id'] ?>"
            class="footer-icon"
            title="Buka Kelas">
            📁
        </a>

        <button class="footer-icon">
            ⋮
        </button>

</div>

    </div>

<?php endforeach; ?>

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