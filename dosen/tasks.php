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
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full bg-blue-200 font-semibold">

            📋
            <span>Semua Tugas</span>

        </a>

        <div class="px-6 pt-5 pb-2 text-xs text-gray-500 font-bold uppercase">

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
     class="ml-0 pt-20 px-4 sm:px-7 pb-10 transition-all duration-300">

    <div class="relative h-40 sm:h-56 rounded-2xl sm:rounded-3xl overflow-hidden mb-5 bg-cover bg-center"
         style="background-image:url('https://www.gstatic.com/classroom/themes/img_graduation.jpg')">

        <div class="absolute inset-0 bg-black/40"></div>

        <div class="relative z-10 p-5 sm:p-10 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center h-full gap-3">

            <div>

                <h1 class="text-3xl sm:text-5xl font-light">

                    Semua Tugas

                </h1>

                <p class="mt-1 sm:mt-3 text-sm sm:text-lg">

                    Kelola semua tugas kelas

                </p>

            </div>

            <a href="create_task.php"
               class="bg-white text-blue-600 px-4 py-2 sm:px-6 sm:py-3 rounded-xl font-semibold hover:bg-gray-100 text-sm sm:text-base flex-shrink-0">

                + Buat Tugas

            </a>

        </div>

    </div>

    <?php if ($tasks->rowCount() == 0): ?>

        <div class="bg-white rounded-2xl p-10 text-center text-gray-500 shadow-sm">

            Belum ada tugas

        </div>

    <?php else: ?>

        <?php while($task = $tasks->fetch()): ?>

            <a href="view_submissions.php?task_id=<?= $task['id'] ?>"
               class="block">

                <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-6 mb-4 hover:shadow-lg transition">

                    <div class="text-lg sm:text-xl font-semibold text-gray-800 mb-2">

                        <?= htmlspecialchars($task['title']) ?>

                    </div>

                    <div class="text-blue-600 font-medium mb-3">

                        <?= htmlspecialchars($task['class_name']) ?>

                    </div>

                    <div class="text-gray-700 leading-7">

                        <?= nl2br(htmlspecialchars($task['DESCRIPTION'])) ?>

                    </div>

                    <div class="mt-4 text-sm text-red-600 font-medium">

                        Deadline:
                        <?= date('d M Y H:i', strtotime($task['deadline'])) ?>

                    </div>

                </div>

            </a>

        <?php endwhile; ?>

    <?php endif; ?>

</div>

<script>

const menuToggle  = document.getElementById('menuToggle');
const sidebar     = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const overlay     = document.getElementById('sidebarOverlay');

const isMobile = () => window.innerWidth < 768;

// Sidebar selalu terbuka di awal
let sidebarOpen = true;

function openSidebar() {
    sidebarOpen = true;
    sidebar.classList.remove('-translate-x-full');
    if (isMobile()) {
        // Di HP: tampilkan overlay gelap, konten tidak bergeser
        overlay.classList.remove('hidden');
    } else {
        // Di laptop: geser konten ke kanan, tanpa overlay
        overlay.classList.add('hidden');
        mainContent.classList.remove('ml-0');
        mainContent.classList.add('ml-[240px]');
    }
}

function closeSidebar() {
    sidebarOpen = false;
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
    if (!isMobile()) {
        // Di laptop: konten melebar penuh
        mainContent.classList.remove('ml-[240px]');
        mainContent.classList.add('ml-0');
    }
}

// Inisialisasi tampilan awal
openSidebar();

menuToggle.addEventListener('click', () => {
    sidebarOpen ? closeSidebar() : openSidebar();
});

// Saat resize, sesuaikan ulang
window.addEventListener('resize', () => {
    if (sidebarOpen) {
        openSidebar(); // re-apply mode yang benar (mobile vs desktop)
    }
});

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