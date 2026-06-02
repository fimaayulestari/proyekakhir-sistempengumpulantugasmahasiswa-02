<?php

session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'dosen') {

    header("Location: ../auth/login.php");
    exit();
}

$tasks = $pdo->prepare("
    SELECT
        tasks.*,
        classes.class_name

    FROM tasks

    JOIN classes
        ON tasks.class_id = classes.id

    WHERE classes.teacher_id = ?

    ORDER BY tasks.created_at DESC
");

$tasks->execute([$_SESSION['user_id']]);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Tugas</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

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
    background:
    linear-gradient(
        135deg,
        #3b82f6,
        #2563eb
    );

    border-radius:24px;
    padding:40px;
    margin-bottom:30px;

    display:flex;
    justify-content:space-between;
    align-items:center;

    color:white;
}

.banner-title{
    font-size:56px;
    font-weight:700;
    margin-bottom:12px;
}

.banner-desc{
    font-size:20px;
    opacity:.95;
}

.banner-btn{
    background:white;
    color:#2563eb;
    padding:14px 28px;
    border-radius:14px;
    text-decoration:none;
    font-weight:600;
}

.banner-btn:hover{
    background:#f3f4f6;
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
    font-weight:bold;
    color:#202124;
    margin-bottom:8px;
}

.banner-desc{
    color:#3c4043;
    font-size:14px;
}

.banner-create{
    height:180px;
    border-radius:24px;
    overflow:hidden;

    background-image:url('https://www.gstatic.com/classroom/themes/img_graduation.jpg');
    background-size:cover;
    background-position:center;

    position:relative;
    margin-bottom:24px;
}

.banner-create::before{
    content:'';
    position:absolute;
    inset:0;
    background:rgba(25,103,210,.35);
}

.banner-content{
    position:relative;
    z-index:2;
    color:white;
    padding:40px;
}

.banner-content h1{
    margin:0;
    font-size:56px;
    font-weight:300;
}

.banner-content p{
    margin-top:12px;
    font-size:18px;
}

.task-card{
    background:white;
    border-radius:28px;
    padding:30px;
    margin-bottom:24px;
    box-shadow:0 2px 10px rgba(0,0,0,0.06);
    transition:0.2s;
}

.task-card:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

.task-title{
    font-size:32px;
    font-weight:700;
    color:#111827;
    margin-bottom:10px;
}

.task-class{
    color:#2563eb;
    font-weight:600;
    margin-bottom:18px;
}

.deadline{
    color:#ef4444;
    font-weight:600;
    margin-top:18px;
}

.empty{
    background:white;
    padding:50px;
    border-radius:28px;
    text-align:center;
    color:#6b7280;
    font-size:18px;
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
            class="sidebar-item sidebar-active">

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
           class="sidebar-item">

            📦
            <span>Archived Classes</span>

        </a>

    </div>

</div>

<div class="main">

    <div class="banner-create">

    <div class="banner-content">

    <div style="display:flex;justify-content:space-between;align-items:center;">

        <div>

            <h1>Semua Tugas</h1>

            <p>
                Kelola semua tugas kelas
            </p>

        </div>

        <a href="create_task.php"
           class="bg-white text-blue-600 px-6 py-3 rounded-xl font-semibold">

            + Buat Tugas

        </a>

    </div>

</div>

</div>

    <?php if ($tasks->rowCount() == 0): ?>

        <div class="empty">

            Belum ada tugas

        </div>

    <?php else: ?>

        <?php while($task = $tasks->fetch()): ?>

            <a
                href="view_submissions.php?task_id=<?= $task['id'] ?>"
                style="text-decoration:none;"
            >

                <div class="task-card">

                    <div class="task-title">

                        <?= htmlspecialchars($task['title']) ?>

                    </div>

                    <div class="task-class">

                        <?= htmlspecialchars($task['class_name']) ?>

                    </div>

                    <div class="text-gray-700 leading-7">

                        <?= nl2br(htmlspecialchars($task['description'])) ?>

                    </div>

                    <div class="deadline">

                        Deadline:
                        <?= date('d M Y H:i', strtotime($task['deadline'])) ?>

                    </div>

                </div>

            </a>

        <?php endwhile; ?>

    <?php endif; ?>

</div>

<script>

const menuToggle =
document.getElementById('menuToggle');

const sidebar =
document.getElementById('sidebar');

const main =
document.querySelector('.main');

menuToggle.addEventListener('click', ()=>{

    sidebar.classList.toggle('closed');
    main.classList.toggle('full');

});

</script>

</body>
</html>