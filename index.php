<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Sistem Pengumpulan Tugas</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="text-gray-800 bg-[#f1f3f4] font-sans">

<nav class="bg-white border-b border-gray-300 sticky top-0 z-50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex justify-between items-center">

        <div class="flex items-center gap-2 sm:gap-3 min-w-0">

            <div class="text-2xl sm:text-3xl flex-shrink-0">
                📚
            </div>

            <h1 class="text-base sm:text-xl md:text-3xl text-gray-600 font-normal truncate">
                Sistem Pengumpulan Tugas
            </h1>

        </div>

        <div class="hidden sm:flex items-center space-x-3">

            <a href="auth/login.php"
               class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">

                Login

            </a>

            <a href="auth/register.php"
               class="border border-blue-600 text-blue-600 hover:bg-blue-50 px-5 py-2 rounded-lg">

                Register

            </a>

        </div>

        <div id="menuToggle"
             class="sm:hidden w-10 h-10 rounded-full flex items-center justify-center cursor-pointer text-xl hover:bg-gray-100 flex-shrink-0">
            ☰
        </div>

    </div>

    <div id="mobileMenu"
         class="sm:hidden hidden border-t border-gray-200 px-4 py-4 space-y-3">

        <a href="auth/login.php"
           class="block text-center bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg">

            Login

        </a>

        <a href="auth/register.php"
           class="block text-center border border-blue-600 text-blue-600 hover:bg-blue-50 px-5 py-2.5 rounded-lg">

            Register

        </a>

    </div>

</nav>

<section class="bg-[#d3e3fd]">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16 md:py-20 grid md:grid-cols-2 gap-8 md:gap-10 items-center">

        <div>

            <h1 class="text-2xl sm:text-3xl md:text-5xl text-gray-800 font-normal leading-tight mb-4 sm:mb-6">

                Sistem Pengumpulan Tugas
                Berbasis Classroom

            </h1>

            <p class="text-sm sm:text-base md:text-lg text-gray-600 leading-relaxed mb-6 sm:mb-8">

                Platform pembelajaran digital untuk dosen dan mahasiswa.
                Kelola kelas, bagikan tugas, upload file dan berikan
                penilaian dalam satu sistem yang sederhana seperti
                Google Classroom.

            </p>

        </div>

        <div>

            <img src="https://www.gstatic.com/classroom/themes/img_graduation.jpg"
                 class="rounded-2xl sm:rounded-3xl shadow-lg w-full">

        </div>

    </div>

</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16 md:py-20">

    <div class="text-center mb-8 sm:mb-12">

        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-2 sm:mb-3">
            Fitur Sistem
        </h2>

        <p class="text-sm sm:text-base text-gray-500">
            Semua kebutuhan pembelajaran dalam satu platform
        </p>

    </div>

    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">

        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm p-5 sm:p-8 transition duration-300 hover:-translate-y-1">
            <div class="text-4xl sm:text-5xl mb-3 sm:mb-4">📚</div>
            <h3 class="text-lg sm:text-xl font-semibold mb-2 sm:mb-3">Kelola Kelas</h3>
            <p class="text-sm sm:text-base text-gray-600">
                Dosen dapat membuat dan mengelola kelas.
            </p>
        </div>

        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm p-5 sm:p-8 transition duration-300 hover:-translate-y-1">
            <div class="text-4xl sm:text-5xl mb-3 sm:mb-4">📝</div>
            <h3 class="text-lg sm:text-xl font-semibold mb-2 sm:mb-3">Tambah Tugas</h3>
            <p class="text-sm sm:text-base text-gray-600">
                Membuat tugas dengan deadline dan deskripsi.
            </p>
        </div>

        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm p-5 sm:p-8 transition duration-300 hover:-translate-y-1">
            <div class="text-4xl sm:text-5xl mb-3 sm:mb-4">📤</div>
            <h3 class="text-lg sm:text-xl font-semibold mb-2 sm:mb-3">Upload Tugas</h3>
            <p class="text-sm sm:text-base text-gray-600">
                Mahasiswa mengumpulkan tugas secara online.
            </p>
        </div>

        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm p-5 sm:p-8 transition duration-300 hover:-translate-y-1">
            <div class="text-4xl sm:text-5xl mb-3 sm:mb-4">⭐</div>
            <h3 class="text-lg sm:text-xl font-semibold mb-2 sm:mb-3">Penilaian</h3>
            <p class="text-sm sm:text-base text-gray-600">
                Dosen memberikan nilai dan feedback.
            </p>
        </div>

        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm p-5 sm:p-8 transition duration-300 hover:-translate-y-1">
            <div class="text-4xl sm:text-5xl mb-3 sm:mb-4">👥</div>
            <h3 class="text-lg sm:text-xl font-semibold mb-2 sm:mb-3">Kelola Mahasiswa</h3>
            <p class="text-sm sm:text-base text-gray-600">
                Melihat dan mengatur anggota kelas.
            </p>
        </div>

        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm p-5 sm:p-8 transition duration-300 hover:-translate-y-1">
            <div class="text-4xl sm:text-5xl mb-3 sm:mb-4">📅</div>
            <h3 class="text-lg sm:text-xl font-semibold mb-2 sm:mb-3">Deadline Tugas</h3>
            <p class="text-sm sm:text-base text-gray-600">
                Pengingat batas waktu pengumpulan tugas.
            </p>
        </div>

    </div>

</section>

<footer class="bg-white border-t">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 flex flex-col md:flex-row justify-between items-center text-center md:text-left">

        <p class="text-sm sm:text-base text-gray-500">
            © 2026 Sistem Pengumpulan Tugas
        </p>

        <p class="text-gray-400 text-xs sm:text-sm mt-2 md:mt-0">
            Platform pembelajaran digital modern
        </p>

    </div>

</footer>

<script>

const menuToggle = document.getElementById('menuToggle');
const mobileMenu = document.getElementById('mobileMenu');

menuToggle.addEventListener('click', () => {

    mobileMenu.classList.toggle('hidden');

});

</script>

</body>
</html>