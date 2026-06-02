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


$teacherStmt = $pdo->prepare("
    SELECT users.*
    FROM classes

    JOIN users
    ON classes.teacher_id = users.id

    WHERE classes.id = ?
");

$teacherStmt->execute([$class_id]);

$teachers = $teacherStmt->fetchAll();

$studentStmt = $pdo->prepare("
    SELECT users.*

    FROM enrollments

    JOIN users
    ON enrollments.student_id = users.id

    WHERE enrollments.class_id = ?
");

$studentStmt->execute([$class_id]);

$students = $studentStmt->fetchAll();

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

<title>People</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

body{
    background:#f1f3f4;
    font-family:'Arial',sans-serif;
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
    font-weight:600;
}

.class-sub{
    font-size:12px;
    color:#5f6368;
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
     class="main-content ml-[240px] pt-20 px-10 pb-10">

<div class="bg-[#546e7a] rounded-2xl overflow-hidden relative h-52 mb-5">

    <div class="absolute left-8 bottom-20 text-white">

        <h1 class="text-4xl font-normal leading-tight mb-1">

            <?= htmlspecialchars($class['class_name']) ?>

        </h1>

        <p class="text-xl">

            People

        </p>

    </div>

</div>

    <div class="bg-white rounded-xl shadow-sm mb-6">

        <div class="flex gap-10 px-8 py-5 border-b">

            <a href="class_detail.php?id=<?= $class['id'] ?>"
               class="text-gray-500 hover:text-blue-600">

                Stream

            </a>

            <a href="classwork.php?id=<?= $class['id'] ?>"
               class="text-gray-500 hover:text-blue-600">

                Classwork

            </a>

            <a href="people.php?id=<?= $class['id'] ?>"
               class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-2">

                People

            </a>

        </div>

    </div>

<div class="bg-white rounded-xl p-8 shadow-sm">

    <h2 class="text-3xl font-light text-gray-800 mb-4">
        Teachers
    </h2>

    <div class="border-b mb-4"></div>

    <?php foreach($teachers as $teacher): ?>

        <div class="flex items-center gap-4 py-4 border-b">

            <div class="w-10 h-10 rounded-full bg-red-500 text-white flex items-center justify-center font-bold">

                <?= strtoupper(substr($teacher['full_name'],0,1)) ?>

            </div>

            <span class="text-lg">

                <?= htmlspecialchars($teacher['full_name']) ?>

            </span>

        </div>

    <?php endforeach; ?>

    <div class="flex justify-between items-center mt-12 mb-4">

        <h2 class="text-3xl font-light text-gray-800">
            Classmates
        </h2>

        <span class="text-gray-500">
            <?= count($students) ?> students
        </span>

    </div>

    <div class="border-b mb-4"></div>

    <?php foreach($students as $student): ?>

        <div class="flex items-center gap-4 py-4 border-b">

            <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">

                <?= strtoupper(substr($student['full_name'],0,1)) ?>

            </div>

            <span class="text-lg">

                <?= htmlspecialchars($student['full_name']) ?>

            </span>

        </div>

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