<?php

session_start();
require_once '../config/database.php';

if ($_SESSION['role'] != 'mahasiswa') {

    header("Location: ../auth/login.php");
    exit();
}

$stmt = $pdo->prepare("
SELECT
    tasks.title,
    tasks.deadline,
    classes.class_name

FROM tasks

JOIN classes
ON tasks.class_id = classes.id

JOIN enrollments
ON classes.id = enrollments.class_id

WHERE enrollments.student_id = ?

ORDER BY tasks.deadline ASC
");

$stmt->execute([$_SESSION['user_id']]);

$tasks = $stmt->fetchAll();

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Calendar</title>

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
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full hover:bg-blue-100">

            🏠
            <span>Home</span>

        </a>

        <a href="calendar.php"
           class="flex items-center gap-4 h-12 px-5 mr-3 rounded-r-full bg-blue-200 font-semibold">

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

    <div class="flex justify-end mb-4 sm:mb-6">

        <h1 class="text-2xl sm:text-3xl md:text-4xl font-normal text-gray-800">

            Calendar

        </h1>

    </div>

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4 sm:mb-6">

        <select
            class="border rounded-lg px-3 sm:px-4 py-2 bg-white text-sm sm:text-base w-full sm:w-auto">

            <option value="">
                All Classes
            </option>

            <?php foreach($classes as $c): ?>

            <option value="<?= $c['id'] ?>">

                <?= htmlspecialchars($c['class_name']) ?>

            </option>

            <?php endforeach; ?>

        </select>

        <div class="flex items-center justify-center gap-5 sm:gap-8">

            <button id="prev"
                    class="text-xl sm:text-2xl font-bold">

                ❮

            </button>

            <h2 id="monthYear"
                class="text-base sm:text-lg font-semibold">
            </h2>

            <button id="next"
                    class="text-xl sm:text-2xl font-bold">

                ❯

            </button>

        </div>

    </div>

    <div class="bg-white border border-gray-300 rounded-xl overflow-hidden">

        <div class="grid grid-cols-7 border-b bg-gray-50 text-center text-xs sm:text-sm">

            <div class="p-1.5 sm:p-3 font-medium"><span class="sm:hidden">S</span><span class="hidden sm:inline">Sun</span></div>
            <div class="p-1.5 sm:p-3 font-medium"><span class="sm:hidden">M</span><span class="hidden sm:inline">Mon</span></div>
            <div class="p-1.5 sm:p-3 font-medium"><span class="sm:hidden">T</span><span class="hidden sm:inline">Tue</span></div>
            <div class="p-1.5 sm:p-3 font-medium"><span class="sm:hidden">W</span><span class="hidden sm:inline">Wed</span></div>
            <div class="p-1.5 sm:p-3 font-medium"><span class="sm:hidden">T</span><span class="hidden sm:inline">Thu</span></div>
            <div class="p-1.5 sm:p-3 font-medium"><span class="sm:hidden">F</span><span class="hidden sm:inline">Fri</span></div>
            <div class="p-1.5 sm:p-3 font-medium"><span class="sm:hidden">S</span><span class="hidden sm:inline">Sat</span></div>

        </div>

        <div id="calendar"
             class="grid grid-cols-7">
        </div>

    </div>

</div>

<script>

const tasks = <?= json_encode($tasks) ?>;

let currentDate = new Date();

function renderCalendar(){

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    const firstDay =
        new Date(year, month, 1).getDay();

    const daysInMonth =
        new Date(year, month + 1, 0).getDate();

    const calendar =
        document.getElementById('calendar');

    calendar.innerHTML = '';

    document.getElementById('monthYear').innerText =
        currentDate.toLocaleDateString(
            'en-US',
            {
                month:'long',
                year:'numeric'
            }
        );

    for(let i=0;i<firstDay;i++){

        calendar.innerHTML += `
            <div class="h-24 sm:h-32 md:h-40 border"></div>
        `;
    }

    for(let day=1; day<=daysInMonth; day++){

        let taskHtml = '';

        const dateStr =
            year + '-' +
            String(month+1).padStart(2,'0') +
            '-' +
            String(day).padStart(2,'0');

        tasks.forEach(task=>{

            const deadline =
                task.deadline.substring(0,10);

            if(deadline === dateStr){

                taskHtml += `
                <div
                class="bg-blue-100 text-blue-700 rounded-md px-1.5 sm:px-2 py-0.5 sm:py-1 text-[10px] sm:text-xs mt-1 truncate">

                    <div class="font-semibold truncate">
                        ${task.title}
                    </div>

                    <div class="truncate hidden sm:block">
                        ${task.class_name}
                    </div>

                </div>
                `;
            }

        });

        const today = new Date();

        const isToday =
            day === today.getDate() &&
            month === today.getMonth() &&
            year === today.getFullYear();

        calendar.innerHTML += `
        <div class="h-24 sm:h-32 md:h-48 border p-1 sm:p-2 overflow-y-auto">

            <div class="${
                isToday
                ? 'w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm sm:text-base font-semibold'
                : 'font-medium text-sm sm:text-lg'
            }">

                ${day}

            </div>

            ${taskHtml}

        </div>
        `;
    }
}

document.getElementById('prev')
.addEventListener('click', ()=>{

    currentDate.setMonth(
        currentDate.getMonth() - 1
    );

    renderCalendar();
});

document.getElementById('next')
.addEventListener('click', ()=>{

    currentDate.setMonth(
        currentDate.getMonth() + 1
    );

    renderCalendar();
});

renderCalendar();

</script>

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