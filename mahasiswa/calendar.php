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

<style>

body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#f1f3f4;
    overflow-x:hidden;
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
    align-items:center;
    justify-content:space-between;
    padding:0 18px;
    z-index:999;
}

.nav-left{
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
    font-weight:400;
}

.nav-right{
    display:flex;
    align-items:center;
    gap:18px;
}

.icon-btn{
    width:38px;
    height:38px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    font-size:18px;
}

.icon-btn:hover{
    background:#f1f3f4;
}

.profile{
    width:38px;
    height:38px;
    border-radius:50%;
    background:#d93025;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
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

    transition:.3s;
}

.sidebar.closed{
    transform:translateX(-240px);
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
    font-size:13px;
    flex-shrink:0;
}

.class-info{
    flex:1;
}

.class-name{
    font-size:14px;
    line-height:1.4;
}

.class-sub{
    font-size:12px;
    color:#5f6368;
    margin-top:2px;
}

.main{
    margin-left:240px;
    padding-top:88px;
    padding-left:28px;
    padding-right:28px;
    padding-bottom:40px;
}.main{
    margin-left:240px;
    padding-top:88px;
    padding-left:28px;
    padding-right:28px;
    padding-bottom:40px;

    transition:.3s;
}

.main.full{
    margin-left:0;
}

.banner{
    background:#d3e3fd;
    border-radius:24px;
    padding:26px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:24px;
}

.banner-left{
    display:flex;
    gap:20px;
    align-items:flex-start;
}

.banner-icon{
    font-size:60px;
}

.banner-title{
    font-size:18px;
    color:#202124;
    margin-bottom:8px;
}

.banner-desc{
    color:#3c4043;
    font-size:14px;
    line-height:1.5;
    max-width:780px;
}

.banner-link{
    color:#1967d2;
    text-decoration:none;
    font-size:14px;
}

.class-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
    gap:20px;
}

.class-card{
    background:white;
    border:1px solid #dadce0;
    border-radius:14px;
    overflow:hidden;
    text-decoration:none;
    color:#202124;
    transition:0.2s;
}

.class-card:hover{
    box-shadow:0 4px 12px rgba(0,0,0,0.12);
}

.card-header{
    height:110px;
    padding:16px;
    position:relative;

    background-image:url('https://www.gstatic.com/classroom/themes/img_graduation.jpg');

    background-size:cover;
    background-position:center;

    color:white;
    overflow:hidden;
}

.card-header::before{
    content:'';
    position:absolute;
    inset:0;
    background:rgba(0,0,0,0.35);
}

.card-header-content{
    position:relative;
    z-index:2;
}

.card-title{
    font-size:16px;
    font-weight:600;
    line-height:1.3;
    margin-bottom:4px;

    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
}

.card-subtitle{
    font-size:13px;
    opacity:0.95;
    margin-bottom:4px;
}

.teacher{
    font-size:12px;
    opacity:0.95;
}

.card-body{
    height:70px;
    background:white;
}

.card-footer{
    height:48px;
    border-top:1px solid #dadce0;

    display:flex;
    justify-content:flex-end;
    align-items:center;

    gap:20px;
    padding:0 18px;

    color:#5f6368;
}

.footer-icon{
    cursor:pointer;
    font-size:20px;
}

.footer-icon:hover{
    color:#202124;
}

.calendar-card{
    background:white;
    border:1px solid #dadce0;
    border-radius:16px;
    overflow:hidden;
}

.today{
    background:#1a73e8;
    color:white;
    width:32px;
    height:32px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
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

        <a href="calendar.php"
            class="sidebar-item sidebar-active">

            📅
            <span>Calendar</span>

        </a>

        <div class="sidebar-title">

            Enrolled

        </div>

        <a href="todo.php"
           class="sidebar-item">

            📝
            <span>To-do</span>

        </a>

        <?php foreach($classes as $class): ?>

            <a href="class_detail.php?id=<?= $class['id'] ?>"
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

<div class="main" id="mainContent">

    <div class="flex justify-end mb-6">

        <h1 class="text-4xl font-normal text-gray-800">
            Calendar
        </h1>

    </div>

    <div class="flex justify-between items-center mb-6">

        <select
        class="border rounded-lg px-4 py-2 bg-white">

            <option value="">
                All Classes
            </option>

        <?php foreach($classes as $c): ?>

            <option value="<?= $c['id'] ?>">

                <?= htmlspecialchars($c['class_name']) ?>

            </option>

        <?php endforeach; ?>

        </select>

        <div class="flex items-center gap-8">

            <button
            id="prev"
            class="text-2xl font-bold">

                ❮

            </button>

            <h2
            id="monthYear"
            class="text-lg font-semibold">
            </h2>

            <button
            id="next"
            class="text-2xl font-bold">

                ❯

            </button>

        </div>

    </div>

    <div class="bg-white border border-gray-300 rounded-xl overflow-hidden">

        <div
        class="grid grid-cols-7 border-b bg-gray-50 text-center">

            <div class="p-3 font-medium">Sun</div>
            <div class="p-3 font-medium">Mon</div>
            <div class="p-3 font-medium">Tue</div>
            <div class="p-3 font-medium">Wed</div>
            <div class="p-3 font-medium">Thu</div>
            <div class="p-3 font-medium">Fri</div>
            <div class="p-3 font-medium">Sat</div>

        </div>

        <div
        id="calendar"
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
            <div class="h-40 border"></div>
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
                class="bg-blue-100 text-blue-700 rounded-md px-2 py-1 text-xs mt-1">

                    <div class="font-semibold">
                        ${task.title}
                    </div>

                    <div>
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
        <div class="h-48 border p-2 overflow-y-auto">

            <div class="${
                isToday
                ? 'w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold'
                : 'font-medium text-lg'
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

const menuToggle =
document.getElementById('menuToggle');

const sidebar =
document.getElementById('sidebar');

const mainContent =
document.getElementById('mainContent');

menuToggle.addEventListener('click', function(){

    sidebar.classList.toggle('closed');

    mainContent.classList.toggle('full');

});

</script>

</body>
</html>