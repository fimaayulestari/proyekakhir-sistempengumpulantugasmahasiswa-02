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

</head>

<body class="bg-gray-100 overflow-x-hidden">

<div class="fixed top-0 left-0 right-0 h-16 bg-white border-b border-gray-300 flex items-center justify-between px-3 md:px-5 z-50">

    <div class="flex items-center gap-3 md:gap-4">

        <div id="menuToggle"
             class="w-10 h-10 rounded-full flex items-center justify-center cursor-pointer text-xl hover:bg-gray-100 flex-shrink-0">
            ☰
        </div>

        <div class="flex items-center gap-2">

            <div class="text-xl md:text-3xl">📚</div>

            <div class="text-xs sm:text-sm md:text-2xl text-gray-600 truncate max-w-[120px] sm:max-w-none">
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

<div id="overlay"
     class="hidden fixed inset-0 bg-black/40 z-40 md:hidden"></div>

<div id="sidebar"
     class="fixed top-16 left-0 bottom-0 w-60 bg-gray-50 border-r border-gray-300 overflow-y-auto transition-transform duration-300 z-50 -translate-x-full">

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

        <?php foreach($sidebarClass as $kelas): ?>

        <a href="manage_class.php?id=<?= $kelas['id'] ?>"
           class="flex items-start gap-3 px-6 py-3 hover:bg-gray-200 <?= ($kelas['id']==$class_id)?'bg-blue-100':'' ?>">

            <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm">

                <?= strtoupper(substr($kelas['class_name'],0,1)) ?>

            </div>

            <div class="flex-1">

                <div class="text-sm font-medium">

                    <?= htmlspecialchars($kelas['class_name']) ?>

                </div>

                <div class="text-xs text-gray-500 mt-1">

                    <?= htmlspecialchars($kelas['DESCRIPTION']) ?>

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

    <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden mb-6 h-44 sm:h-56 md:h-64 bg-cover bg-center"
         style="background-image:url('https://www.gstatic.com/classroom/themes/img_graduation.jpg')">

        <div class="absolute inset-0 bg-black/40"></div>

        <div class="relative z-10 h-full flex flex-col justify-between p-5 sm:p-6 md:p-8 text-white">

            <div>

                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold">

                    <?= htmlspecialchars($class['class_name']) ?>

                </h1>

                <p class="mt-2 text-sm sm:text-base">

                    <?= htmlspecialchars($class['DESCRIPTION']) ?>

                </p>

            </div>

            <div class="flex flex-wrap gap-2 sm:gap-3">

                <div class="bg-white text-blue-600 px-4 sm:px-5 py-2 rounded-xl font-semibold text-sm sm:text-base">

                    Kode:
                    <?= htmlspecialchars($class['class_code']) ?>

                </div>

                <a href="create_task.php?class_id=<?= $class['id'] ?>"
                   class="bg-blue-600 hover:bg-blue-700 px-4 sm:px-5 py-2 rounded-xl text-sm sm:text-base">

                    + Tambah Tugas

                </a>

                <a href="edit_class.php?id=<?= $class['id'] ?>"
                   class="bg-yellow-500 hover:bg-yellow-600 px-4 sm:px-5 py-2 rounded-xl text-sm sm:text-base">

                    ✏ Edit

                </a>

                <a href="archive_class.php?id=<?= $class['id'] ?>"
                   onclick="return confirm('Arsipkan kelas ini?')"
                   class="bg-orange-500 hover:bg-orange-600 px-4 sm:px-5 py-2 rounded-xl text-sm sm:text-base">

                    📦 Arsipkan

                </a>

            </div>

        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">

            <h3 class="text-2xl font-bold mb-5">

                👨‍🎓 Daftar Mahasiswa
                (<?= count($studentsList) ?>)

            </h3>

            <div class="space-y-3 max-h-96 overflow-y-auto">

                <?php if(empty($studentsList)): ?>

                    <p class="text-gray-500 text-center py-6">

                        Belum ada mahasiswa yang bergabung

                    </p>

                <?php else: ?>

                    <?php foreach($studentsList as $student): ?>

                        <div class="border-b pb-3 flex items-center gap-4">


                    <?php if(!empty($student['photo'])): ?>

                        <img 
                        src="../<?= htmlspecialchars($student['photo']) ?>"
                        class="w-12 h-12 rounded-full object-cover border">


                    <?php else: ?>

                        <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold">

                            <?= strtoupper(substr($student['full_name'],0,1)) ?>

                        </div>


                    <?php endif; ?>


                    <div>

                        <p class="font-semibold">

                            <?= htmlspecialchars($student['full_name']) ?>

                        </p>

                        <p class="text-sm text-gray-500">

                            <?= htmlspecialchars($student['email']) ?>

                        </p>

                    </div>


                </div>

                <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">

            <div class="flex justify-between items-center mb-5">

                <h3 class="text-2xl font-bold">

                    📋 Daftar Tugas
                    (<?= count($tasksList) ?>)

                </h3>

                <a href="create_task.php?class_id=<?= $class['id'] ?>"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm">

                    + Tambah

                </a>

            </div>

            <div class="space-y-4 max-h-96 overflow-y-auto">

                <?php if(empty($tasksList)): ?>

                    <p class="text-gray-500 text-center py-6">

                        Belum ada tugas

                    </p>

                <?php else: ?>

                    <?php foreach($tasksList as $task): ?>

                    <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition">

                        <div class="flex justify-between items-center">

                            <div>

                                <h4 class="font-semibold">

                                    <?= htmlspecialchars($task['title']) ?>

                                </h4>

                                <p class="text-sm text-gray-500">

                                    Deadline:
                                    <?= date('d/m/Y H:i', strtotime($task['deadline'])) ?>

                                </p>

                            </div>

                            <a href="view_submissions.php?task_id=<?= $task['id'] ?>"
                               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">

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

const mainContent =
document.getElementById('mainContent');

const overlay =
document.getElementById('overlay');

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