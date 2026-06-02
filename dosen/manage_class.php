<?php

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'dosen') {

    header('Location: ../auth/login.php');
    exit();

}

$class_id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT * 
    FROM classes 
    WHERE id = ? 
    AND teacher_id = ?
");

$stmt->execute([
    $class_id,
    $_SESSION['user_id']
]);

$class = $stmt->fetch();

$sidebarClass = $pdo->prepare("
    SELECT *
    FROM classes
    WHERE teacher_id = ?
    AND archived = 0
    ORDER BY created_at DESC
");

$sidebarClass->execute([$_SESSION['user_id']]);

if (!$class) {

    header('Location: dashboard.php');
    exit();

}

$students = $pdo->prepare("
    SELECT 
        u.*, 
        e.enrolled_at

    FROM enrollments e

    JOIN users u
        ON e.student_id = u.id

    WHERE e.class_id = ?
");

$students->execute([$class_id]);

$studentsList = $students->fetchAll();

$tasks = $pdo->prepare("
    SELECT * 
    FROM tasks 
    WHERE class_id = ?
    ORDER BY id DESC
");

$tasks->execute([$class_id]);

$tasksList = $tasks->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Kelola Kelas</title>

    <script src="https://cdn.tailwindcss.com"></script>

<style>

body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#f1f3f4;
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
           class="bg-red-500 text-white px-4 py-2 rounded-lg">

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

        <?php foreach($sidebarClass as $kelas): ?>

        <a href="manage_class.php?id=<?= $kelas['id'] ?>"
            class="class-link <?= ($kelas['id'] == $class_id) ? 'class-active' : '' ?>">

            <div class="class-avatar">
                <?= strtoupper(substr($kelas['class_name'],0,1)) ?>
            </div>

            <div class="class-info">

                <div class="class-name">
                    <?= htmlspecialchars($kelas['class_name']) ?>
                </div>

                <div class="class-sub">
                    <?= htmlspecialchars($kelas['description']) ?>
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

        <div class="rounded-3xl p-8 mb-6 text-white"
            style="
                background-image:url('https://www.gstatic.com/classroom/themes/img_graduation.jpg');
                background-size:cover;
                background-position:center;
            ">

            <div class="flex justify-between items-start">

                <div>

                    <h2 class="text-3xl font-bold">

                        <?= htmlspecialchars($class['class_name']) ?>

                    </h2>

                    <p class="text-gray-600 mt-1">

                        <?= htmlspecialchars($class['description']) ?>

                    </p>

                    <div class="mt-4 inline-flex items-center px-5 py-3 rounded-xl bg-white/90 backdrop-blur-sm shadow-md">

                        <span class="font-bold text-gray-700 mr-2">
                            Kode Kelas:
                        </span>

                        <span class="font-semibold text-blue-600 tracking-wider">
                            <?= htmlspecialchars($class['class_code']) ?>
                        </span>

                    </div>

                </div>

                <div class="flex gap-3">

                    <a href="create_task.php?class_id=<?= $class['id'] ?>"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl transition">

                            + Tambah Tugas

                    </a>

                    <a href="edit_class.php?id=<?= $class['id'] ?>"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-xl transition">

                            ✏ Edit Kelas

                    </a>

                    <a href="archive_class.php?id=<?= $class['id'] ?>"
                        onclick="return confirm('Arsipkan kelas ini?')"
                        class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-xl transition">

                            📦 Arsipkan

                    </a>

                    <a href="dashboard.php"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-xl transition">

                            Kembali

                    </a>

                </div>

            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="bg-white rounded-2xl shadow-md p-6">

                <h3 class="text-2xl font-bold mb-5">

                    👨‍🎓 Daftar Mahasiswa
                    (<?= count($studentsList) ?>)

                </h3>

                <div class="space-y-3 max-h-96 overflow-y-auto">

                    <?php if (empty($studentsList)): ?>

                        <p class="text-gray-500 text-center py-6">

                            Belum ada mahasiswa yang bergabung

                        </p>

                    <?php else: ?>

                        <?php foreach ($studentsList as $student): ?>

                            <div class="border-b pb-3">

                                <p class="font-semibold text-lg">

                                    <?= htmlspecialchars($student['full_name']) ?>

                                </p>

                                <p class="text-sm text-gray-600">

                                    <?= htmlspecialchars($student['email']) ?>

                                    |

                                    Bergabung:
                                    <?= date('d/m/Y H:i', strtotime($student['enrolled_at'])) ?>

                                </p>

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            </div>

            <div class="bg-white rounded-2xl shadow-md p-6">

                <div class="flex justify-between items-center mb-5">

                    <h3 class="text-2xl font-bold">

                        📋 Daftar Tugas
                        (<?= count($tasksList) ?>)

                    </h3>

                    <a href="create_task.php?class_id=<?= $class['id'] ?>"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm transition">

                        + Tambah

                    </a>

                </div>

                <div class="space-y-4 max-h-96 overflow-y-auto">

                    <?php if (empty($tasksList)): ?>

                        <p class="text-gray-500 text-center py-6">

                            Belum ada tugas. Buat tugas baru!

                        </p>

                    <?php else: ?>

                        <?php foreach ($tasksList as $task): ?>

                            <div class="border rounded-xl p-4">

                                <div class="flex justify-between items-start">

                                    <div>

                                        <h4 class="font-bold text-lg">

                                            <?= htmlspecialchars($task['title']) ?>

                                        </h4>

                                        <p class="text-sm text-gray-600 mt-1">

                                            Deadline:
                                            <?= date('d/m/Y H:i', strtotime($task['deadline'])) ?>

                                        </p>

                                    </div>

                                    <a href="view_submissions.php?task_id=<?= $task['id'] ?>"
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition">

                                        Lihat Pengumpulan

                                    </a>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            </div>

        </div>

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