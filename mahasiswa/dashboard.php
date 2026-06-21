<?php

session_start();

require_once '../config/database.php';

if ($_SESSION['role'] != 'mahasiswa') {

    header("Location: ../auth/login.php");
    exit();

}

$stmt = $pdo->prepare("
    SELECT classes.*
    FROM enrollments

    JOIN classes
    ON enrollments.class_id = classes.id

    WHERE enrollments.student_id = ?
    AND classes.archived = 0

    ORDER BY classes.created_at DESC
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

<title>Dashboard Mahasiswa</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 overflow-x-hidden">

<div class="fixed top-0 left-0 right-0 h-16 bg-white border-b border-gray-300 flex items-center justify-between px-3 md:px-5 z-50">

    <div class="flex items-center gap-3 md:gap-4">

        <div id="menuToggle"
             class="w-10 h-10 rounded-full flex items-center justify-center cursor-pointer text-xl hover:bg-gray-100 flex-shrink-0">
            ☰
        </div>

        <div class="flex items-center gap-2">

            <div class="text-xl md:text-3xl">
                📚
            </div>

            <div class="text-xs sm:text-sm md:text-2xl text-gray-600 truncate max-w-[120px] sm:max-w-none">
                Sistem Pengumpulan Tugas
            </div>

        </div>

    </div>

    <div class="flex items-center gap-4 relative">


    <button 
    onclick="profileMenu()"
    class="focus:outline-none">


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



    <!-- POPUP PROFILE -->

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




    <h2 class="text-center text-lg font-semibold mt-3">

    <?= htmlspecialchars($_SESSION['full_name']) ?>

    </h2>



    <p class="text-center text-gray-500 text-sm">

    <?= htmlspecialchars($_SESSION['username']) ?>

    </p>




    <a href="profile.php"

    class="block text-center mt-4 border rounded-full py-2 text-blue-600 hover:bg-gray-100">

    Manage Profile

    </a>




    <a href="../logout.php"

    class="block text-center mt-3 bg-red-500 text-white rounded-full py-2">

    Sign out

    </a>



    </div>
    
    </div>

</div>

<div id="overlay"
     class="hidden fixed inset-0 bg-black/40 z-40 md:hidden"></div>

<div id="sidebar"
     class="fixed top-16 left-0 bottom-0 w-60 bg-gray-50 border-r border-gray-300 overflow-y-auto transition-transform duration-300 z-50 -translate-x-full">

    <div class="py-3">

        <a href="dashboard.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full bg-blue-200 font-semibold">

            🏠
            <span>Home</span>

        </a>

        <a href="calendar.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full hover:bg-blue-100">

            📅
            <span>Calendar</span>

        </a>

        <div class="px-6 pt-5 pb-2 text-xs text-gray-500 font-bold uppercase">

            ENROLLED

        </div>

        <a href="todo.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full hover:bg-blue-100">

            📝
            <span>To-do</span>

        </a>

        <?php foreach($classes as $class): ?>

        <a href="class_detail.php?id=<?= $class['id'] ?>"
           class="flex items-start gap-3 px-6 py-3 hover:bg-gray-200">

            <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm">

                <?= strtoupper(substr($class['class_name'],0,1)) ?>

            </div>

            <div class="flex-1">

                <div class="text-sm font-medium">

                    <?= htmlspecialchars($class['class_name']) ?>

                </div>

                <div class="text-xs text-gray-500 mt-1">

                    <?= htmlspecialchars($class['DESCRIPTION']) ?>

                </div>

            </div>

        </a>

        <?php endforeach; ?>

        <a href="archived.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full hover:bg-blue-100 mt-4">

            📦
            <span>Archived Classes</span>

        </a>

    </div>

</div>

<div id="mainContent"
     class="ml-0 pt-20 md:pt-24 px-3 sm:px-5 md:px-7 pb-10 transition-all duration-300">

    <div class="bg-blue-100 rounded-2xl sm:rounded-3xl p-4 sm:p-6 mb-6 sm:mb-8">

        <div class="flex items-center gap-3 sm:gap-5">

            <div class="text-4xl sm:text-6xl">

                ✍️

            </div>

            <div>

                <h1 class="text-base sm:text-xl font-semibold text-gray-800">

                    Selamat Datang di Sistem Pengumpulan Tugas

                </h1>

                <p class="text-gray-600 mt-1 sm:mt-2 text-sm sm:text-base">

                    Kelola tugas dan pantau aktivitas kelas yang Anda ikuti.

                </p>

            </div>

        </div>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

    <?php foreach($classes as $class): ?>

        <a href="class_detail.php?id=<?= $class['id'] ?>"
           class="bg-white rounded-2xl overflow-hidden shadow hover:shadow-lg transition block">

            <div class="relative h-32 bg-cover bg-center text-white p-4"
                 style="background-image:url('https://www.gstatic.com/classroom/themes/img_graduation.jpg')">

                <div class="absolute inset-0 bg-black/30"></div>

                <div class="relative z-10">

                    <h2 class="text-xl font-semibold">

                        <?= htmlspecialchars($class['class_name']) ?>

                    </h2>

                    <p class="text-sm mt-1">

                        <?= htmlspecialchars($class['schedule'] ?? 'Jumat / 15:15 - 16:30') ?>

                    </p>

                    <p class="text-sm mt-2">

                        <?= htmlspecialchars($_SESSION['full_name']) ?>

                    </p>

                </div>

            </div>

            <div class="h-16 bg-white"></div>

            <div class="border-t flex justify-end items-center gap-6 px-5 h-12 text-gray-500">

                <span class="text-2xl">
                    📁
                </span>

                <span class="text-xl">
                    ⋮
                </span>

            </div>

        </a>

    <?php endforeach; ?>

    </div>

</div>

<a href="join_class.php"
   class="fixed bottom-5 right-5 sm:bottom-8 sm:right-8 w-12 h-12 sm:w-14 sm:h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg flex items-center justify-center z-50">

    <svg xmlns="http://www.w3.org/2000/svg"
         class="w-6 h-6 sm:w-8 sm:h-8"
         fill="none"
         viewBox="0 0 24 24"
         stroke="currentColor">

        <path stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 4v16m8-8H4"/>

    </svg>

</a>

<script>

const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const overlay = document.getElementById('overlay');

const isMobile = () => window.innerWidth < 768;

// Menu selalu terbuka di awal, baik di HP maupun laptop
let sidebarOpen = true;

function openSidebar() {
    sidebarOpen = true;
    sidebar.classList.remove('-translate-x-full');

    if (isMobile()) {
        overlay.classList.remove('hidden');
        mainContent.classList.remove('md:ml-60');
        mainContent.classList.add('ml-0');
    } else {
        overlay.classList.add('hidden');
        mainContent.classList.remove('ml-0');
        mainContent.classList.add('md:ml-60');
    }
}

function closeSidebar() {
    sidebarOpen = false;
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
    mainContent.classList.remove('md:ml-60');
    mainContent.classList.add('ml-0');
}

menuToggle.addEventListener('click', ()=>{

    sidebarOpen ? closeSidebar() : openSidebar();

});

overlay.addEventListener('click', ()=>{

    closeSidebar();

});

window.addEventListener('resize', ()=>{

    // Pertahankan status terbuka/tertutup saat ukuran layar berubah
    sidebarOpen ? openSidebar() : closeSidebar();

});

// Set tampilan awal sesuai status sidebarOpen
sidebarOpen ? openSidebar() : closeSidebar();

function profileMenu(){

    const box = document.getElementById('profileBox');

    box.classList.toggle('hidden');

}

</script>

</body>
</html>