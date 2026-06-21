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

</head>

<body class="bg-gray-100 overflow-x-hidden">

<!-- Mobile Overlay -->
<div id="sidebarOverlay"
     class="fixed inset-0 bg-black/40 z-40 hidden"
     onclick="closeSidebar()"></div>

<div class="fixed top-0 left-0 right-0 h-16 bg-white border-b border-gray-300 flex items-center justify-between px-4 z-50">

    <div class="flex items-center gap-3">

        <div id="menuToggle"
             class="w-10 h-10 rounded-full flex items-center justify-center cursor-pointer text-xl hover:bg-gray-100 flex-shrink-0">
            ☰
        </div>

        <div class="flex items-center gap-2">

            <div class="text-2xl sm:text-3xl">
                📚
            </div>

            <div class="text-base sm:text-2xl text-gray-600 truncate">
                Sistem Pengumpulan Tugas
            </div>

        </div>

    </div>

    <div class="flex items-center gap-4 relative">


    <button onclick="profileMenu()" class="focus:outline-none">

    <?php if(!empty($_SESSION['photo'])): ?>

    <img 
    src="../<?= htmlspecialchars($_SESSION['photo']) ?>"
    class="w-10 h-10 rounded-full object-cover border">


    <?php else: ?>

    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center">

    <?= strtoupper(substr($_SESSION['full_name'],0,1)) ?>

    </div>

    <?php endif; ?>


    </button>


    <div id="profileBox"
    class="hidden absolute right-0 top-14 w-72 bg-white rounded-2xl shadow-xl border p-5 z-50">


    <div class="flex justify-center">


    <?php if(!empty($_SESSION['photo'])): ?>

    <img 
    src="../<?= htmlspecialchars($_SESSION['photo']) ?>"
    class="w-24 h-24 rounded-full object-cover">


    <?php else: ?>

    <div class="w-24 h-24 rounded-full bg-blue-600 text-white flex items-center justify-center text-4xl">

    <?= strtoupper(substr($_SESSION['full_name'],0,1)) ?>

    </div>


    <?php endif; ?>


    </div>


    <h2 class="text-center font-semibold text-lg mt-3">

    <?= htmlspecialchars($_SESSION['full_name']) ?>

    </h2>


    <p class="text-center text-gray-500 text-sm">

    <?= htmlspecialchars($_SESSION['username']) ?>

    </p>


    <a href="profile.php"
    class="block text-center mt-4 border rounded-full py-2 text-blue-600">

    Manage Profile

    </a>


    <a href="../logout.php"
    class="block text-center mt-3 bg-red-500 text-white rounded-full py-2">

    Sign out

    </a>


    </div>


    </div>

    </div>

<div id="sidebar"
     class="fixed top-16 left-0 bottom-0 w-[240px] bg-gray-50 border-r border-gray-300 overflow-y-auto transition-transform duration-300 z-50 -translate-x-full">

    <div class="py-3">

        <a href="dashboard.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full hover:bg-blue-100">
            🏠
            <span>Home</span>
        </a>

        <a href="create_class.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full bg-blue-200 font-semibold">
            ➕
            <span>Buat Kelas</span>
        </a>

        <a href="create_task.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full hover:bg-blue-100">
            📝
            <span>Buat Tugas</span>
        </a>

        <a href="laporan_tugas.php"
            class="flex items-center gap-4 h-12 px-5 hover:bg-blue-100">

            📊
            <span>
            Rekapan Tugas
            </span>

        </a>

        <a href="tasks.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full hover:bg-blue-100">
            📋
            <span>Semua Tugas</span>
        </a>

        <div class="px-6 pt-5 pb-2 text-xs text-gray-500 font-bold uppercase">
            Kelas Anda
        </div>

        <?php foreach($classes as $class): ?>

        <a href="manage_class.php?id=<?= $class['id'] ?>"
           class="flex items-start gap-3 px-6 py-3 hover:bg-gray-200">

            <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm">

                <?= strtoupper(substr($class['class_name'],0,1)) ?>

            </div>

            <div class="flex-1">

                <div class="text-sm font-medium">
                    <?= htmlspecialchars($class['class_name']) ?>
                </div>

                <div class="text-xs text-gray-500 mt-1">
                    <?= htmlspecialchars($class['DESCRIPTION'] ?? '') ?>
                </div>

            </div>

        </a>

        <?php endforeach; ?>

        <a href="archived.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full hover:bg-blue-100 mt-4">
            📦
            <span>Archived classes</span>
        </a>

    </div>

</div>

<div id="mainContent"
     class="ml-0 pt-20 px-4 sm:px-7 pb-10 transition-all duration-300">

    <div class="relative h-36 sm:h-56 rounded-2xl sm:rounded-3xl overflow-hidden mb-5 bg-cover bg-center"
         style="background-image:url('https://www.gstatic.com/classroom/themes/img_graduation.jpg')">

        <div class="absolute inset-0 bg-black/40"></div>

        <div class="relative z-10 text-white p-6 sm:p-10">

            <h1 class="text-3xl sm:text-5xl font-light">
                Buat Kelas Baru
            </h1>

            <p class="mt-2 sm:mt-3 text-sm sm:text-lg">
                Tambahkan kelas baru dan bagikan kode kelas kepada mahasiswa.
            </p>

        </div>

    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-8 shadow-sm">

        <form method="POST">

            <div class="mb-5">

                <label class="block font-semibold mb-2">
                    Nama Kelas
                </label>

                <input
                    type="text"
                    name="class_name"
                    required
                    class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

            </div>

            <div class="mb-6">

                <label class="block font-semibold mb-2">
                    Deskripsi
                </label>

                <textarea
                    name="DESCRIPTION"
                    rows="5"
                    class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>

            </div>

            <div class="flex flex-col sm:flex-row gap-3">

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-6 py-3 rounded-xl font-semibold transition-colors">
                    Buat Kelas
                </button>

                <a href="dashboard.php"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl text-center transition-colors">
                    Batal
                </a>

            </div>

        </form>

    </div>

</div>

<script>

const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const overlay = document.getElementById('sidebarOverlay');

const isMobile = () => window.innerWidth < 768;

// Menu selalu terbuka di awal, baik di HP maupun laptop
let sidebarOpen = true;

function openSidebar() {
    sidebarOpen = true;
    sidebar.classList.remove('-translate-x-full');

    if (isMobile()) {
        overlay.classList.remove('hidden');
        mainContent.classList.remove('ml-[240px]');
        mainContent.classList.add('ml-0');
    } else {
        overlay.classList.add('hidden');
        mainContent.classList.remove('ml-0');
        mainContent.classList.add('ml-[240px]');
    }
}

function closeSidebar() {
    sidebarOpen = false;
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
    mainContent.classList.remove('ml-[240px]');
    mainContent.classList.add('ml-0');
}

menuToggle.addEventListener('click', () => {
    sidebarOpen ? closeSidebar() : openSidebar();
});

window.addEventListener('resize', () => {
    // Pertahankan status terbuka/tertutup saat ukuran layar berubah
    sidebarOpen ? openSidebar() : closeSidebar();
});

// Set tampilan awal sesuai status sidebarOpen
sidebarOpen ? openSidebar() : closeSidebar();

function profileMenu() {
    document.getElementById('profileBox').classList.toggle('hidden');
}

document.addEventListener('click', (e) => {
    const box = document.getElementById('profileBox');
    if (!e.target.closest("button[onclick='profileMenu()']") && !box.contains(e.target)) {
        box.classList.add('hidden');
    }
});

</script>

</body>
</html>