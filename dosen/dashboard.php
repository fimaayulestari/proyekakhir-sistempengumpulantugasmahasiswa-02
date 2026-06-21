<?php

session_start();

require_once '../config/database.php';

if ($_SESSION['role'] != 'dosen') {

    header("Location: ../auth/login.php");
    exit();

}

$stmt = $pdo->prepare("
    SELECT 
        classes.*,
        users.full_name,
        users.photo
    FROM classes
    JOIN users
    ON classes.teacher_id = users.id
    WHERE classes.teacher_id = ?
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

<title>Dashboard Dosen</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 overflow-x-hidden w-full">

<div class="fixed top-0 left-0 right-0 h-16 bg-white border-b border-gray-300 flex items-center justify-between px-3 md:px-5 z-50">
    <div class="flex items-center gap-4">

        <button id="menuToggle"
            class="w-10 h-10 rounded-full flex items-center justify-center cursor-pointer text-xl hover:bg-gray-100">

            ☰

        </button>

        <div class="flex items-center gap-2">

            <div class="text-xl md:text-3xl">
                📚
            </div>

            <div class="text-xs sm:text-sm md:text-2xl text-gray-600 leading-tight max-w-[120px] sm:max-w-none">

                Sistem Pengumpulan Tugas

            </div>

        </div>

    </div>

    <div class="flex items-center gap-3 relative">


<a href="create_class.php"
class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-medium">

+ Buat Kelas

</a>



<button 
onclick="profileMenu()"
class="w-10 h-10 rounded-full overflow-hidden border-2 border-gray-300 hover:ring-2 hover:ring-blue-300">


<?php if(!empty($_SESSION['photo'])): ?>

<img 
src="../<?= htmlspecialchars($_SESSION['photo']) ?>"
class="w-full h-full object-cover">


<?php else: ?>

<div class="w-full h-full bg-blue-600 text-white flex items-center justify-center font-bold">

<?= strtoupper(substr($_SESSION['full_name'],0,1)) ?>

</div>

<?php endif; ?>


</button>



<!-- PROFILE POPUP -->

<div id="profileBox"
class="hidden absolute right-0 top-12 w-[280px] md:w-80 bg-white rounded-2xl shadow-xl border p-5 z-50">

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



<h2 class="text-center mt-3 text-lg font-semibold">

<?= htmlspecialchars($_SESSION['full_name']) ?>

</h2>


<p class="text-center text-gray-500 text-sm">

<?= htmlspecialchars($_SESSION['username']) ?>

</p>



<a href="profile.php"

class="block text-center mt-5 border rounded-full py-2 text-blue-600 hover:bg-gray-100">

Manage Profile

</a>



<a href="../logout.php"

class="block text-center mt-3 bg-gray-800 text-white rounded-full py-2">

Sign out

</a>



</div>


</div>

</div>

<div id="overlay"
    class="hidden fixed inset-0 bg-black/40 z-40 md:hidden">
</div>

<div id="sidebar"
class="fixed top-16 left-0 bottom-0 w-[230px] bg-gray-50 border-r border-gray-300 overflow-y-auto transition-transform duration-300 z-50 -translate-x-full">
    <div class="py-3">

        <a href="dashboard.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full bg-blue-200 font-semibold">

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

        <?php foreach($classes as $class): ?>

            <a href="manage_class.php?id=<?= $class['id'] ?>"
                class="flex items-start gap-3 px-6 py-3 hover:bg-gray-200
                <?= (isset($_GET['id']) && $_GET['id'] == $class['id']) ? 'bg-blue-200' : '' ?>">

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
            <span>Archived classes</span>

        </a>

    </div>

</div>

<div id="mainContent"
    class="ml-0 pt-20 px-3 sm:px-5 md:px-7 pb-10 w-full transition-all duration-300">
    <div class="bg-blue-100 rounded-3xl p-4 mb-6">

    <div class="flex items-start gap-3">

        <div class="text-4xl">
            ✍️
        </div>

        <div>

            <div class="text-base font-semibold mb-2">
                Dashboard Dosen
            </div>

            <div class="text-gray-700 text-xs">
                Kelola kelas, buat tugas, melihat pengumpulan tugas mahasiswa dan memberikan nilai.
            </div>

        </div>

    </div>

</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">

<?php foreach($classes as $class): ?>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden flex flex-col min-h-[200px] sm:min-h-[220px] hover:shadow-xl transition duration-300">
        <a href="manage_class.php?id=<?= $class['id'] ?>" style="text-decoration:none;color:inherit;">

            <div class="relative h-28 p-4 text-white bg-cover bg-center"
                style="background-image:url('https://www.gstatic.com/classroom/themes/img_graduation.jpg')">

                <div class="absolute inset-0 bg-black/40"></div>

                <div class="relative z-10">

                    <div class="text-lg font-semibold">
                        <?= htmlspecialchars($class['class_name']) ?>
                    </div>

                    <div class="text-sm opacity-90">
                        <?= htmlspecialchars($class['class_code']) ?>
                    </div>

                   <div class="text-xs opacity-90 mt-2">

                        <?= htmlspecialchars($class['full_name']) ?>

                    </div>

                </div>

            </div>

            <div class="p-4 flex-1">
                <p class="text-sm text-gray-600">
                    <?= htmlspecialchars($class['DESCRIPTION'] ?? 'Tidak ada deskripsi') ?>
                </p>
            </div>
        </a>

        <div class="h-12 border-t border-gray-300 flex justify-end items-center gap-5 px-4 text-gray-500">

        <a href="manage_class.php?id=<?= $class['id'] ?>"
            class="text-xl hover:text-black"
            title="Buka Kelas">
            📁
        </a>

        <button class="text-xl hover:text-black">
            ⋮
        </button>

</div>

    </div>

<?php endforeach; ?>

</div>

<script>

const menuToggle = document.getElementById("menuToggle");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");
const mainContent = document.getElementById("mainContent");

const isMobile = () => window.innerWidth < 768;

// Menu selalu terbuka di awal, baik di HP maupun laptop
let sidebarOpen = true;

function openSidebar() {
    sidebarOpen = true;
    sidebar.classList.remove("-translate-x-full");

    if (isMobile()) {
        overlay.classList.remove("hidden");
        mainContent.classList.remove("md:ml-[240px]");
        mainContent.classList.add("ml-0");
    } else {
        overlay.classList.add("hidden");
        mainContent.classList.remove("ml-0");
        mainContent.classList.add("md:ml-[240px]");
    }
}

function closeSidebar() {
    sidebarOpen = false;
    sidebar.classList.add("-translate-x-full");
    overlay.classList.add("hidden");
    mainContent.classList.remove("md:ml-[240px]");
    mainContent.classList.add("ml-0");
}

menuToggle.addEventListener("click", () => {
    sidebarOpen ? closeSidebar() : openSidebar();
});

overlay.addEventListener("click", () => {
    closeSidebar();
});

window.addEventListener("resize", () => {
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