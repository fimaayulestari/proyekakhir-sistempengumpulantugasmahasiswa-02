<?php

session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {

    header("Location: ../auth/login.php");
    exit();

}

if (!isset($_GET['id'])) {

    die("Tugas tidak ditemukan");

}

$task_id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT
        tasks.*,
        classes.class_name,
        classes.id as class_id
    FROM tasks

    JOIN classes
    ON tasks.class_id = classes.id

    WHERE tasks.id = ?
");

$stmt->execute([$task_id]);

$task = $stmt->fetch();

if (!$task) {

    die("Tugas tidak ditemukan");

}

$check = $pdo->prepare("
    SELECT *
    FROM submissions
    WHERE task_id = ?
    AND student_id = ?
");

$check->execute([
    $task_id,
    $_SESSION['user_id']
]);

$submission = $check->fetch();

if (isset($_POST['submit_task'])) {

    $file_path = null;

    $submission_link = trim($_POST['submission_link'] ?? '');

    if (
        empty($_FILES['task_file']['name']) &&
        empty($submission_link)
    ) {
    die("Silakan upload file atau masukkan link.");
    }

    if (!empty($_FILES['task_file']['name'])) {

        $file_path = time() . '_' . $_FILES['task_file']['name'];

        move_uploaded_file(
            $_FILES['task_file']['tmp_name'],
            '../uploads/' . $file_path
        );

    }

    if ($submission) {

        $update = $pdo->prepare("
            UPDATE submissions
            SET
                file_path = ?,
                submission_link = ?,
                submitted_at = NOW()
            WHERE id = ?
        ");

        $update->execute([
            $file_path,
            $submission_link,
            $submission['id']
        ]);

    } else {

        $insert = $pdo->prepare("
            INSERT INTO submissions (
                task_id,
                student_id,
                file_path,
                submission_link,
                submitted_at
            )
            VALUES (?, ?, ?, ?, NOW())
        ");

        $insert->execute([
            $task_id,
            $_SESSION['user_id'],
            $file_path,
            $submission_link
        ]);

    }

    header("Location: task_detail.php?id=" . $task_id);
    exit();

}

if (isset($_POST['unsubmit'])) {

    $delete = $pdo->prepare("
        DELETE FROM submissions
        WHERE task_id = ?
        AND student_id = ?
    ");

    $delete->execute([
        $task_id,
        $_SESSION['user_id']
    ]);

    header("Location: task_detail.php?id=" . $task_id);
    exit();

}

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

<title><?= htmlspecialchars($task['title']) ?></title>

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
    align-items:center;
    justify-content:space-between;
    padding:0 24px;
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
    transition:.3s;
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
    font-weight:600;
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
    transition:.3s;
}

.main.full{
    margin-left:0 !important;
}

.content{
    display:flex;
    gap:32px;
    align-items:flex-start;
}

.left{
    flex:1;
    min-width:0;
}

.right{
    width:360px;
    flex-shrink:0;
}

.card{
    background:white;
    border-radius:16px;
    border:1px solid #dadce0;
    padding:32px;
}

.file-box{
    border:1px solid #dadce0;
    border-radius:12px;
    overflow:hidden;
    display:flex;
    margin-top:20px;
}

.file-left{
    flex:1;
    padding:14px;
}

.file-right{
    width:70px;
    background:#f1f3f4;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:26px;
}

.submit-btn{
    width:100%;
    height:48px;
    border:none;
    border-radius:999px;
    background:#1a73e8;
    color:white;
    font-size:14px;
    cursor:pointer;
    margin-top:16px;
}

.submit-btn:hover{
    background:#1765cc;
}

.unsubmit-btn{
    width:100%;
    height:48px;
    border-radius:999px;
    border:1px solid #5f6368;
    background:white;
    cursor:pointer;
    margin-top:16px;
}

.upload-box{
    border:2px dashed #dadce0;
    border-radius:12px;
    padding:24px;
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

        <?php foreach($classes as $class): ?>

            <a href="class_detail.php?id=<?= $class['id'] ?>"
                class="class-link <?= $class['id'] == $task['class_id'] ? 'sidebar-active' : '' ?>">

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

    <div class="content">

        <div class="left">

            <div class="card">

                <div class="flex items-start justify-between gap-6">

                    <div class="flex gap-4 flex-1">

                        <div class="w-14 h-14 rounded-full bg-cyan-100 flex items-center justify-center text-2xl flex-shrink-0">

                            📋

                        </div>

                        <div class="flex-1">

                            <h1 class="text-[22px] font-normal text-gray-900 leading-normal">

                                <?= htmlspecialchars($task['title']) ?>

                            </h1>

                            <div class="text-gray-500 text-sm mt-2">

                                Diposting dosen

                            </div>

                            <div class="text-gray-700 text-sm mt-1">

                                <?= htmlspecialchars($task['class_name']) ?>

                            </div>

                        </div>

                    </div>

                    <div class="text-right flex-shrink-0 pt-2">

                        <div class="text-gray-700 text-sm whitespace-nowrap">

                            Due <?= date('d M Y H:i', strtotime($task['deadline'])) ?>

                        </div>

                    </div>

                </div>

                <div class="border-t border-gray-300 mt-2 pt-2">

                    <div class="text-[16px] leading-8 text-gray-800 whitespace-pre-line">

                        <?= htmlspecialchars($task['description']) ?>

                    </div>

                    <?php if(!empty($task['material_file'])): ?>

    <a href="../uploads/materials/<?= htmlspecialchars($task['material_file']) ?>"
       target="_blank"
       class="file-box max-w-[420px] hover:bg-gray-50 transition">

        <div class="file-left">

            <div class="font-semibold text-gray-800">
                Materi Tugas
            </div>

            <div class="text-sm text-gray-500 mt-1">

                <?= htmlspecialchars(
                    basename($task['material_file'])
                ) ?>

            </div>

        </div>

        <div class="file-right">
            📄
        </div>

    </a>

<?php endif; ?>

                </div>

            </div>

        </div>

        <div class="right">

            <div class="card">

                <div class="flex justify-between items-center mb-6">

                    <div class="text-[32px] text-gray-900">

                        Your work

                    </div>

                    <div class="text-sm text-gray-500">

                        <?= $submission ? 'Turned in' : 'Missing' ?>

                    </div>

                </div>

                <?php if($submission): ?>

    <?php if(!empty($submission['file_path'])): ?>

        <div class="file-box">

            <div class="file-left">

                <a href="../uploads/<?= htmlspecialchars($submission['file_path']) ?>"
                   target="_blank"
                   class="text-blue-700 hover:underline break-all">

                    <?= htmlspecialchars($submission['file_path']) ?>

                </a>

                <div class="text-sm text-gray-500 mt-1">
                    FILE
                </div>

            </div>

            <div class="file-right">
                📄
            </div>

        </div>

    <?php endif; ?>

    <?php if(!empty($submission['submission_link'])): ?>

        <div class="file-box mt-3">

            <div class="file-left">

                <a href="<?= htmlspecialchars($submission['submission_link']) ?>"
                   target="_blank"
                   class="text-blue-700 hover:underline break-all">

                    <?= htmlspecialchars($submission['submission_link']) ?>

                </a>

                <div class="text-sm text-gray-500 mt-1">
                    LINK
                </div>

            </div>

            <div class="file-right">
                🔗
            </div>

        </div>

    <?php endif; ?>

    <form method="POST">

        <button
            type="submit"
            name="unsubmit"
            class="unsubmit-btn">

            Unsubmit

        </button>

    </form>

                <?php else: ?>

                    <form method="POST" enctype="multipart/form-data">

                        <div class="upload-box">

                            <div class="text-gray-700 mb-4">
                                Upload File Tugas
                            </div>

                            <input
                                type="file"
                                name="task_file"
                                class="mb-4"
                            >

                            <div class="text-gray-700 mb-2">
                                Atau Tambahkan Link
                            </div>

                            <input
                                type="url"
                                name="submission_link"
                                placeholder="https://drive.google.com/..."
                                class="w-full border rounded-lg p-2"
                            >

                        </div>

                        <button
                            type="submit"
                            name="submit_task"
                            class="submit-btn"
                        >

                            Turn in

                        </button>

                    </form>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<script>

const menuToggle =
    document.getElementById('menuToggle');

const sidebar =
    document.querySelector('.sidebar');

const mainContent =
    document.getElementById('mainContent');

menuToggle.addEventListener('click', ()=>{

    sidebar.classList.toggle('closed');
    mainContent.classList.toggle('full');

});

</script>

</body>
</html>