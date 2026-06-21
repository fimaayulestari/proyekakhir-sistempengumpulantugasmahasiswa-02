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

<div id="overlay"
     class="hidden fixed inset-0 bg-black/40 z-40 md:hidden"></div>

<div id="sidebar"
     class="fixed top-16 left-0 bottom-0 w-60 bg-gray-50 border-r border-gray-300 overflow-y-auto transition-transform duration-300 z-50 -translate-x-full">

    <div class="py-3">

        <a href="dashboard.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full hover:bg-blue-100">
            🏠 <span>Home</span>
        </a>

        <a href="calendar.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full hover:bg-blue-100">
            📅 <span>Calendar</span>
        </a>

        <div class="px-6 pt-5 pb-2 text-xs text-gray-500 font-bold uppercase">
            ENROLLED
        </div>

        <a href="todo.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full hover:bg-blue-100">
            📝 <span>To-do</span>
        </a>

        <?php foreach($classes as $class): ?>

        <a href="class_detail.php?id=<?= $class['id'] ?>"
           class="flex items-start gap-3 px-6 py-3 hover:bg-gray-200 <?= $class['id'] == $task['class_id'] ? 'bg-blue-100' : '' ?>">

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
            📦 <span>Archived Classes</span>
        </a>

    </div>

</div>

<div id="mainContent"
     class="ml-0 pt-20 md:pt-24 px-3 sm:px-5 md:px-7 pb-10 transition-all duration-300">

    <div class="relative h-32 sm:h-40 md:h-48 rounded-2xl sm:rounded-3xl overflow-hidden mb-6 text-white"
         style="background-image:url('https://www.gstatic.com/classroom/themes/img_graduation.jpg');
                background-size:cover;
                background-position:center;">

        <div class="absolute inset-0 bg-black/30"></div>

        <div class="relative z-10 h-full flex flex-col justify-center px-4 sm:px-6 md:px-8">

            <h1 class="text-xl sm:text-2xl md:text-4xl font-semibold">

                <?= htmlspecialchars($task['title']) ?>

            </h1>

            <p class="text-sm sm:text-base md:text-lg mt-1 sm:mt-2">

                <?= htmlspecialchars($task['class_name']) ?>

            </p>

        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">

        <div class="lg:col-span-8">

            <div class="bg-white rounded-2xl shadow p-4 sm:p-6">

                <div class="flex flex-col sm:flex-row justify-between items-start gap-3 sm:gap-4 mb-5">

                    <div class="flex gap-3 sm:gap-4 min-w-0">

                        <div class="w-12 h-12 sm:w-14 sm:h-14 flex-shrink-0 rounded-full bg-cyan-100 flex items-center justify-center text-xl sm:text-2xl">
                            📋
                        </div>

                        <div class="min-w-0">

                            <h2 class="text-lg sm:text-2xl font-semibold text-gray-800">

                                <?= htmlspecialchars($task['title']) ?>

                            </h2>

                            <p class="text-gray-500 mt-1 text-sm sm:text-base">
                                Diposting dosen
                            </p>

                        </div>

                    </div>

                    <div class="text-red-500 font-medium text-sm sm:text-base whitespace-nowrap">

                        Due <?= date('d M Y H:i', strtotime($task['deadline'])) ?>

                    </div>

                </div>

                <div class="border-t pt-5">

                    <p class="text-gray-700 whitespace-pre-line leading-7 text-sm sm:text-base">

                        <?= htmlspecialchars($task['DESCRIPTION']) ?>

                    </p>

                    <?php if(!empty($task['material_file'])): ?>

                    <a href="../uploads/materials/<?= htmlspecialchars($task['material_file']) ?>"
                       target="_blank"
                       class="mt-6 flex items-center justify-between border rounded-xl p-3 sm:p-4 hover:bg-gray-50 gap-3">

                        <div class="min-w-0">

                            <div class="font-semibold text-gray-800 text-sm sm:text-base">
                                Materi Tugas
                            </div>

                            <div class="text-xs sm:text-sm text-gray-500 truncate">

                                <?= htmlspecialchars(basename($task['material_file'])) ?>

                            </div>

                        </div>

                        <div class="text-2xl sm:text-3xl flex-shrink-0">
                            📄
                        </div>

                    </a>

                    <?php endif; ?>

                </div>

            </div>

        </div>

        <div class="lg:col-span-4">

            <div class="bg-white rounded-2xl shadow p-4 sm:p-6">

                <div class="flex justify-between items-center mb-5 sm:mb-6">

                    <h2 class="text-lg sm:text-2xl font-semibold">
                        Your Work
                    </h2>

                    <span class="text-xs sm:text-sm text-gray-500">

                        <?= $submission ? 'Turned in' : 'Missing' ?>

                    </span>

                </div>

                <?php if($submission): ?>

                    <?php if(!empty($submission['file_path'])): ?>

                    <a href="../uploads/<?= htmlspecialchars($submission['file_path']) ?>"
                       target="_blank"
                       class="flex justify-between items-center border rounded-xl p-3 sm:p-4 mb-3 hover:bg-gray-50 gap-3">

                        <div class="min-w-0">

                            <div class="text-blue-600 break-all text-sm sm:text-base">

                                <?= htmlspecialchars($submission['file_path']) ?>

                            </div>

                            <div class="text-xs sm:text-sm text-gray-500">
                                FILE
                            </div>

                        </div>

                        <div class="text-xl sm:text-2xl flex-shrink-0">
                            📄
                        </div>

                    </a>

                    <?php endif; ?>

                    <?php if(!empty($submission['submission_link'])): ?>

                    <a href="<?= htmlspecialchars($submission['submission_link']) ?>"
                       target="_blank"
                       class="flex justify-between items-center border rounded-xl p-3 sm:p-4 mb-3 hover:bg-gray-50 gap-3">

                        <div class="min-w-0">

                            <div class="text-blue-600 break-all text-sm sm:text-base">

                                <?= htmlspecialchars($submission['submission_link']) ?>

                            </div>

                            <div class="text-xs sm:text-sm text-gray-500">
                                LINK
                            </div>

                        </div>

                        <div class="text-xl sm:text-2xl flex-shrink-0">
                            🔗
                        </div>

                    </a>

                    <?php endif; ?>

                    <form method="POST">

                        <button type="submit"
                                name="unsubmit"
                                class="w-full bg-red-500 hover:bg-red-600 text-white py-2.5 sm:py-3 rounded-xl text-sm sm:text-base">

                            Unsubmit

                        </button>

                    </form>

                <?php else: ?>

                    <form method="POST" enctype="multipart/form-data">

                        <div class="mb-4">

                            <label class="block mb-2 font-medium text-gray-700 text-sm sm:text-base">

                                Upload File

                            </label>

                            <input type="file"
                                   name="task_file"
                                   class="w-full border rounded-lg p-2 text-sm">

                        </div>

                        <div class="mb-5">

                            <label class="block mb-2 font-medium text-gray-700 text-sm sm:text-base">

                                Atau Link

                            </label>

                            <input type="url"
                                   name="submission_link"
                                   placeholder="https://drive.google.com/..."
                                   class="w-full border rounded-lg p-2 text-sm">

                        </div>

                        <button type="submit"
                                name="submit_task"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 sm:py-3 rounded-xl text-sm sm:text-base">

                            Turn In

                        </button>

                    </form>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

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

menuToggle.addEventListener('click', () => {

    sidebarOpen ? closeSidebar() : openSidebar();

});

overlay.addEventListener('click', () => {

    closeSidebar();

});

window.addEventListener('resize', () => {

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