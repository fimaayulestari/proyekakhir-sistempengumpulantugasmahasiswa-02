<?php

session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'dosen') {

    header("Location: ../auth/login.php");
    exit();
}

$classes = $pdo->prepare("
    SELECT *
    FROM classes
    WHERE teacher_id = ?
    ORDER BY created_at DESC
");

$classes->execute([$_SESSION['user_id']]);

$allClasses = $classes->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $class_id = $_POST['class_id'];

    $title = trim($_POST['title']);

    $description = trim($_POST['description']);

    $deadline = $_POST['deadline'];

    $publish_at = $_POST['publish_at'];

    $material_file = null;

if (!empty($_FILES['material_file']['name'])) {

    $uploadDir = "../uploads/materials/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['material_file']['name']);

    move_uploaded_file(
        $_FILES['material_file']['tmp_name'],
        $uploadDir . $fileName
    );

    $material_file = $fileName;
}

    $stmt = $pdo->prepare("
        INSERT INTO tasks (
            class_id,
            title,
            description,
            deadline,
            created_by,
            material_file
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $class_id,
        $title,
        $description,
        $deadline,
        $_SESSION['user_id'],
        $material_file
    ]);

    header("Location: tasks.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Buat Tugas</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#f1f3f4;
    font-family:Arial,sans-serif;
}

.navbar{
    position:fixed;
    top:0;
    left:0;
    right:0;
    height:74px;
    background:white;
    border-bottom:1px solid #e5e7eb;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 24px;
    z-index:1000;
}

.logo{
    font-size:22px;
    font-weight:700;
    color:#2563eb;
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
    transition:left .3s ease;
}

.sidebar.hidden-sidebar{
    left:-240px;
}

.main{
    margin-left:240px;
    width:calc(100% - 240px);
    padding:88px 28px 40px;
    box-sizing:border-box;
}

.banner{
    background:#d3e3fd;
    border-radius:24px;
    padding:26px;
    margin-bottom:24px;
}

.banner-left{
    display:flex;
    gap:20px;
    align-items:center;
}

.banner-icon{
    font-size:60px;
}

.banner-title{
    font-size:32px;
    font-weight:700;
    color:#202124;
    margin-bottom:8px;
}

.banner-desc{
    color:#3c4043;
    font-size:15px;
}

.form-card{
    background:white;
    border-radius:24px;
    padding:30px;
    max-width:1000px;
    margin:auto;
    box-shadow:0 4px 10px rgba(0,0,0,.08);
}

.input{
    width:100%;
    border:1px solid #d1d5db;
    border-radius:18px;
    padding:16px 18px;
    outline:none;
    transition:0.2s;
}

.input:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 3px rgba(37,99,235,0.1);
}

.label{
    display:block;
    font-weight:600;
    margin-bottom:10px;
    color:#374151;
}

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

.sidebar{
    position:fixed;
    top:64px;
    left:0;
    bottom:0;
    width:240px;
    background:#f8f9fa;
    border-right:1px solid #dadce0;
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

.sidebar-item:hover{
    background:#e8f0fe;
}

.sidebar-active{
    background:#c2e7ff;
    font-weight:600;
}

.main{
    margin-left:240px;
    width:calc(100% - 240px);
    padding:88px 28px 40px;
    transition:.3s;
}

.main.full{
    margin-left:0;
    width:100%;
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

.form-card{
    background:white;
    border-radius:24px;
    padding:30px;
    width:100%;
    max-width:none;
    box-shadow:0 4px 10px rgba(0,0,0,.08);
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
            class="sidebar-item sidebar-active">
    
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

        <?php foreach($allClasses as $class): ?>

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

<div class="main">

    <div class="banner-create">

        <div class="banner-content">

            <h1>Buat Tugas</h1>

            <p>
                Tambahkan tugas baru untuk mahasiswa
            </p>

        </div>

    </div>

    <div class="form-card">

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-6">

                <label class="label">

                    Pilih Kelas

                </label>

                <select
                    name="class_id"
                    class="input"
                    required
                >

                    <option value="">

                        -- Pilih Kelas --

                    </option>

                    <?php foreach($allClasses as $class): ?>

                        <option value="<?= $class['id'] ?>">

                            <?= htmlspecialchars($class['class_name']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="mb-6">

                <label class="label">

                    Judul Tugas

                </label>

                <input
                    type="text"
                    name="title"
                    class="input"
                    required
                >

            </div>

            <div class="mb-6">

                <label class="label">

                    Deskripsi

                </label>

                <textarea
                    name="description"
                    rows="6"
                    class="input"
                    required
                ></textarea>

            </div>

            <div class="mb-6">

                <label class="label">

                    Deadline

                </label>

                <input
                    type="datetime-local"
                    name="deadline"
                    class="input"
                    required
                >

            </div>

            <div class="mb-8">

                <label class="label">

                    Tampilkan Tugas Pada

                </label>

                <input
                    type="datetime-local"
                    name="publish_at"
                    class="input"
                    required
                >

            </div>

            <div class="mb-4">

    <label class="block font-semibold mb-2">

        Materi Tugas (PDF, PPT, DOCX, ZIP)

    </label>

    <input
        type="file"
        name="material_file"
        class="w-full border rounded-lg p-2">

</div>

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl font-semibold transition"
            >

                Buat Tugas

            </button>

            </form>

    </div>

</div>

<script>

const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
const main = document.querySelector('.main');

menuToggle.addEventListener('click', () => {

    sidebar.classList.toggle('hidden-sidebar');
    main.classList.toggle('full');

});

</script>

</body>
</html>