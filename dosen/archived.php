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
    AND archived = 1
    ORDER BY created_at DESC
");

$stmt->execute([$_SESSION['user_id']]);

$classes = $stmt->fetchAll();

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

$sidebarClass = $sidebarClass->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelas Diarsipkan</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 overflow-x-hidden">

<div class="fixed top-0 left-0 right-0 h-16 bg-white border-b border-gray-300 flex items-center justify-between px-5 z-50">

    <div class="flex items-center gap-4">

        <div id="menuToggle"
             class="w-10 h-10 rounded-full flex items-center justify-center cursor-pointer text-xl hover:bg-gray-100">
            ☰
        </div>

        <div class="flex items-center gap-2">

            <div class="text-3xl">📚</div>

            <div class="text-sm sm:text-xl md:text-2xl text-gray-600 whitespace-nowrap">
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
    class="fixed top-16 left-0 bottom-0 w-[240px] bg-gray-50 border-r border-gray-300 overflow-y-auto transition-transform duration-300 z-40 shadow-lg">

    <div class="py-3">

        <a href="dashboard.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full hover:bg-blue-100">

            🏠
            <span>Home</span>

        </a>

        <a href="create_class.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full hover:bg-blue-100">

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

            KELAS ANDA

        </div>

        <?php foreach($sidebarClass as $class): ?>

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

                    <?= htmlspecialchars($class['DESCRIPTION']) ?>

                </div>

            </div>

        </a>

        <?php endforeach; ?>

        <a href="archived.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full bg-blue-200 font-semibold mt-4">

            📦
            <span>Archived Classes</span>

        </a>

    </div>

</div>

<div id="overlay"
class="fixed inset-0 bg-black/40 hidden z-30 md:hidden"></div>

<div id="mainContent"
    class="pt-20 px-4 sm:px-5 md:px-7 pb-10 transition-all duration-300 md:ml-[240px]">

    <div class="bg-blue-100 rounded-3xl p-5 flex flex-col md:flex-row gap-4 justify-between items-start md:items-center mb-6">

        <div class="flex flex-col sm:flex-row gap-4 items-start">

            <div class="text-6xl">

                📦

            </div>

            <div>

                <div class="text-lg font-semibold mb-2">

                    Kelas Diarsipkan

                </div>

                <div class="text-gray-700 text-sm">

                    Daftar kelas yang telah diarsipkan dan dapat dipulihkan kembali.

                </div>

            </div>

        </div>

    </div>

    <?php if(count($classes) > 0): ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">

        <?php foreach($classes as $class): ?>

        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-lg transition">

            <div class="relative h-28 bg-cover bg-center text-white p-4"
                 style="background-image:url('https://www.gstatic.com/classroom/themes/img_graduation.jpg')">

                <div class="absolute inset-0 bg-black/40"></div>

                <div class="relative z-10">

                    <div class="text-lg font-semibold">

                        <?= htmlspecialchars($class['class_name']) ?>

                    </div>

                    <div class="text-sm opacity-90">

                        <?= htmlspecialchars($class['class_code']) ?>

                    </div>

                </div>

            </div>

            <div class="p-4">

                <p class="text-gray-600 text-sm mb-4">

                    <?= htmlspecialchars($class['DESCRIPTION']) ?>

                </p>

                <a href="restore_class.php?id=<?= $class['id'] ?>"
                   class="block w-full text-center bg-green-500 hover:bg-green-600 text-white py-2 rounded-xl">

                    Pulihkan

                </a>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

    <?php else: ?>

    <div class="bg-white rounded-2xl border border-gray-200 p-10 text-center text-gray-500">

        Belum ada kelas yang diarsipkan.

    </div>

    <?php endif; ?>

</div>

<script>

const menuToggle = document.getElementById("menuToggle");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");
const mainContent = document.getElementById("mainContent");


let sidebarOpen = true;


// fungsi buka sidebar
function openSidebar(){

    sidebarOpen = true;

    sidebar.classList.remove("-translate-x-full");

    mainContent.classList.add("md:ml-[240px]");


    // hanya HP tampilkan hitam
    if(window.innerWidth < 768){

        overlay.classList.remove("hidden");

    }

}


// fungsi tutup sidebar
function closeSidebar(){

    sidebarOpen = false;

    sidebar.classList.add("-translate-x-full");

    mainContent.classList.remove("md:ml-[240px]");


    overlay.classList.add("hidden");

}


// tombol menu
menuToggle.addEventListener("click",()=>{


    if(sidebarOpen){

        closeSidebar();

    }else{

        openSidebar();

    }


});



// klik area hitam HP
overlay.addEventListener("click",()=>{

    closeSidebar();

});



// profile dropdown
function profileMenu(){

    document.getElementById("profileBox")
    .classList.toggle("hidden");

}



// resize layar
window.addEventListener("resize",()=>{


    if(window.innerWidth >= 768){


        // laptop selalu tanpa overlay
        overlay.classList.add("hidden");


        if(sidebarOpen){

            sidebar.classList.remove("-translate-x-full");

            mainContent.classList.add("md:ml-[240px]");

        }


    }else{


        // HP mengikuti status sidebar

        if(sidebarOpen){

            openSidebar();

        }else{

            closeSidebar();

        }

    }


});


// kondisi awal
if(window.innerWidth < 768){

    openSidebar();

}else{

    sidebar.classList.remove("-translate-x-full");

    mainContent.classList.add("md:ml-[240px]");

}

</script>

</body>
</html>