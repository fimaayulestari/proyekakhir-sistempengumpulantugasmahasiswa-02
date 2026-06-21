<?php

session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {

    header('Location: ../auth/login.php');
    exit();

}

$stmt = $pdo->prepare("
    SELECT 
        tasks.*,
        classes.class_name,

        submissions.id AS submitted

    FROM enrollments

    JOIN classes
        ON enrollments.class_id = classes.id

    JOIN tasks
        ON classes.id = tasks.class_id

    LEFT JOIN submissions
        ON tasks.id = submissions.task_id
        AND submissions.student_id = ?

    WHERE enrollments.student_id = ?

    ORDER BY tasks.deadline ASC
");

$stmt->execute([
    $_SESSION['user_id'],
    $_SESSION['user_id']
]);

$tasks = $stmt->fetchAll();

$classStmt = $pdo->prepare("
    SELECT 
        classes.*
    FROM enrollments

    JOIN classes
        ON enrollments.class_id = classes.id

    WHERE enrollments.student_id = ?

    ORDER BY classes.class_name ASC
");

$classStmt->execute([$_SESSION['user_id']]);

$classes = $classStmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>To Do Mahasiswa</title>

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

            <div class="text-3xl">
                📚
            </div>

            <div class="text-sm md:text-base text-gray-600 truncate max-w-[160px] md:max-w-none">
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

<div id="sidebarOverlay"
     class="fixed inset-0 bg-black/40 z-30 hidden"></div>

<div id="sidebar"
     class="fixed top-16 left-0 bottom-0 w-60 bg-gray-50 border-r border-gray-300 overflow-y-auto transition-all duration-300 transform z-40">

    <div class="py-3">

        <a href="dashboard.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full hover:bg-blue-100">

            🏠
            <span>Home</span>

        </a>

        <a href="calendar.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full hover:bg-blue-100">

            📅
            <span>Calendar</span>

        </a>

        <a href="todo.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full bg-blue-200 font-semibold">

            📝
            <span>To-do</span>

        </a>

        <div class="px-6 pt-5 pb-2 text-xs text-gray-500 font-bold uppercase">

            ENROLLED

        </div>

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
     class="pt-24 px-4 md:px-7 pb-10 transition-all duration-300" style="margin-left:0">

    <div class="relative h-44 rounded-3xl overflow-hidden mb-8 text-white"
         style="background-image:url('https://www.gstatic.com/classroom/themes/img_graduation.jpg');
                background-size:cover;
                background-position:center;">

        <div class="absolute inset-0 bg-blue-900/40"></div>

        <div class="relative z-10 h-full flex flex-col justify-center px-8">

            <h1 class="text-4xl font-semibold mb-3">

                To Do

            </h1>

            <p class="text-xl">

                Semua tugas dari kelas yang kamu ikuti

            </p>

        </div>

    </div>

    <div class="space-y-5">

        <?php foreach($tasks as $task): ?>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-lg transition">

            <div class="flex justify-between items-start">

                <div class="flex-1">

                    <h2 class="text-xl font-semibold text-gray-800 mb-2">

                        <?= htmlspecialchars($task['title']) ?>

                    </h2>

                    <div class="text-blue-600 text-sm font-medium mb-3">

                        <?= htmlspecialchars($task['class_name']) ?>

                    </div>

                    <p class="text-gray-600 mb-4">

                        <?= htmlspecialchars($task['DESCRIPTION']) ?>

                    </p>

                    <div class="text-sm text-red-500 font-medium">

                        Deadline:
                        <?= date('d M Y H:i', strtotime($task['deadline'])) ?>

                    </div>

                </div>

                <div class="ml-5">

                    <?php if($task['submitted']): ?>

                        <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-semibold">

                            Sudah Upload

                        </span>

                    <?php else: ?>

                        <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-full text-sm font-semibold">

                            Belum Upload

                        </span>

                    <?php endif; ?>

                </div>

            </div>

            <div class="mt-5">

                <a href="submit_task.php?task_id=<?= $task['id'] ?>"
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl">

                    Kerjakan Tugas

                </a>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

</div>

<script>

const menuToggle  = document.getElementById('menuToggle');
const sidebar     = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const overlay     = document.getElementById('sidebarOverlay');

function isMobile() {
    return window.innerWidth < 768;
}

function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    if (isMobile()) {
        overlay.classList.remove('hidden');
        mainContent.style.marginLeft = '0';
    } else {
        overlay.classList.add('hidden');
        mainContent.style.marginLeft = '240px';
    }
}

function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
    mainContent.style.marginLeft = '0';
}

function initSidebar() {
    if (isMobile()) {
        closeSidebar();
    } else {
        openSidebar();
    }
}

menuToggle.addEventListener('click', () => {
    if (sidebar.classList.contains('-translate-x-full')) {
        openSidebar();
    } else {
        closeSidebar();
    }
});

overlay.addEventListener('click', closeSidebar);
window.addEventListener('resize', initSidebar);
initSidebar();

function profileMenu() {
    document.getElementById('profileBox').classList.toggle('hidden');
}

document.addEventListener('click', function(e) {
    const box     = document.getElementById('profileBox');
    const trigger = box.previousElementSibling;
    if (!box.contains(e.target) && !trigger.contains(e.target)) {
        box.classList.add('hidden');
    }
});

</script>

</body>
</html>