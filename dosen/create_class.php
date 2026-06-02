<?php

session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'dosen') {
    header('Location: ../auth/login.php');
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $class_name = trim($_POST['class_name']);
    $description = trim($_POST['description']);

    $class_code = strtoupper(substr(md5(uniqid()), 0, 8));

    $stmt = $pdo->prepare("
        INSERT INTO classes
        (
            class_name,
            class_code,
            description,
            teacher_id
        )
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $class_name,
        $class_code,
        $description,
        $_SESSION['user_id']
    ]);

    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buat Kelas</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

body{
    margin:0;
    font-family:Arial,sans-serif;
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

.logo-text{
    font-size:30px;
    color:#5f6368;
    font-weight:400;
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
    transition:left .3s ease;
}

.sidebar.hidden-sidebar{
    left:-240px;
}

.main{
    margin-left:240px;
    transition:margin-left .3s ease;
}

.main.full{
    margin-left:0;
    width:100vw;
    padding:88px 40px 40px;
    box-sizing:border-box;
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
    width:calc(100vw - 240px);
    padding:88px 28px 40px;
    box-sizing:border-box;
}

.banner{
    background:#d3e3fd;
    border-radius:24px;
    padding:26px;
    margin-bottom:24px;
}

.input{
    width:100%;
    border:1px solid #d1d5db;
    border-radius:14px;
    padding:14px;
}

.label{
    display:block;
    font-weight:600;
    margin-bottom:8px;
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

.logo-icon{
    font-size:28px;
}

.nav-right{
    display:flex;
    align-items:center;
    gap:18px;
}

.sidebar-title{
    padding:18px 24px 10px;
    font-size:12px;
    color:#5f6368;
    font-weight:bold;
    text-transform:uppercase;
}

.banner-title{
    font-size:28px;
    font-weight:600;
    color:#202124;
}

.banner-desc{
    font-size:14px;
    color:#5f6368;
    margin-top:6px;
}

.form-card{
    background:white;
    border:1px solid #dadce0;
    border-radius:14px;
    padding:30px;
    width:100%;
    max-width:none;
    margin-top:24px;
    box-sizing:border-box;
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
    font-size:52px;
    font-weight:300;
    margin:0;
}

.banner-content p{
    font-size:18px;
    margin-top:10px;
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
            class="sidebar-item sidebar-active">

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
            Kelas Anda
        </div>

        <!-- LIST KELAS -->
        <?php foreach($classes as $class): ?>

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
           class="sidebar-item mt-4">

            📦
            <span>Archived classes</span>

        </a>

    </div>

</div>

<div class="main" id="mainContent">

    <div class="banner-create">

        <div class="banner-content">

            <h1>Buat Kelas Baru</h1>

            <p>
                Tambahkan kelas baru dan bagikan kode kelas kepada mahasiswa.
            </p>

        </div>

</div>

    <div class="form-card">

        <form method="POST">

            <div class="mb-5">

                <label class="label">
                    Nama Kelas
                </label>

                <input
                    type="text"
                    name="class_name"
                    class="input"
                    required>

            </div>

            <div class="mb-6">

                <label class="label">
                    Deskripsi
                </label>

                <textarea
                    name="description"
                    rows="5"
                    class="input"></textarea>

            </div>

            <div class="flex gap-3">

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl">

                    Buat Kelas

                </button>

                <a href="dashboard.php"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl">

                    Batal

                </a>

            </div>

        </form>

    </div>

</div>

<script>
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
const main = document.getElementById('mainContent');

menuToggle.addEventListener('click', () => {

    sidebar.classList.toggle('hidden-sidebar');
    main.classList.toggle('full');

});
</script>

</body>
</html>