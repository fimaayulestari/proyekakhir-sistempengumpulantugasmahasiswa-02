<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Sistem Pengumpulan Tugas</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>

        body{
            background:#f1f3f4;
            font-family:Arial,sans-serif;
        }

        .hero-bg{
            background:#d3e3fd;
        }

        .card-hover{
            transition:.3s;
        }

        .card-hover:hover{
            transform:translateY(-4px);
        }

        .card-hover{
            transition:0.3s;
        }

        .card-hover:hover{
            transform:translateY(-5px);
        }

    </style>

</head>

<body class="text-gray-800">

<nav class="bg-white border-b border-gray-300 sticky top-0 z-50">

    <div class="max-w-7xl mx-auto px-6 h-16 flex justify-between items-center">

        <div class="flex items-center gap-3">

            <div class="text-3xl">
                📚
            </div>

            <h1 class="text-3xl text-gray-600 font-normal">
                Sistem Pengumpulan Tugas 
            </h1>

        </div>

        <div class="space-x-3">

            <a href="auth/login.php"
               class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">

                Login

            </a>

            <a href="auth/register.php"
               class="border border-blue-600 text-blue-600 hover:bg-blue-50 px-5 py-2 rounded-lg">

                Register

            </a>

        </div>

    </div>

</nav>

<section class="hero-bg">

    <div class="max-w-7xl mx-auto px-6 py-20 grid md:grid-cols-2 gap-10 items-center">

        <div>

            <h1 class="text-5xl text-gray-800 font-normal leading-tight mb-6">

                Sistem Pengumpulan Tugas
                Berbasis Classroom

            </h1>

            <p class="text-lg text-gray-600 leading-relaxed mb-8">

                Platform pembelajaran digital untuk dosen dan mahasiswa.
                Kelola kelas, bagikan tugas, upload file dan berikan
                penilaian dalam satu sistem yang sederhana seperti
                Google Classroom.

            </p>

        </div>

        <div>

            <img src="https://www.gstatic.com/classroom/themes/img_graduation.jpg"
                 class="rounded-3xl shadow-lg w-full">

        </div>

    </div>

</section>

<section class="max-w-7xl mx-auto px-6 py-20">

    <div class="text-center mb-12">

        <h2 class="text-4xl font-bold mb-3">
            Fitur Sistem
        </h2>

        <p class="text-gray-500">
            Semua kebutuhan pembelajaran dalam satu platform
        </p>

    </div>

    <div class="grid md:grid-cols-3 gap-6">

        <div class="bg-white rounded-3xl shadow-sm p-8 card-hover">
            <div class="text-5xl mb-4">📚</div>
            <h3 class="text-xl font-semibold mb-3">Kelola Kelas</h3>
            <p class="text-gray-600">
                Dosen dapat membuat dan mengelola kelas.
            </p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-8 card-hover">
            <div class="text-5xl mb-4">📝</div>
            <h3 class="text-xl font-semibold mb-3">Tambah Tugas</h3>
            <p class="text-gray-600">
                Membuat tugas dengan deadline dan deskripsi.
            </p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-8 card-hover">
            <div class="text-5xl mb-4">📤</div>
            <h3 class="text-xl font-semibold mb-3">Upload Tugas</h3>
            <p class="text-gray-600">
                Mahasiswa mengumpulkan tugas secara online.
            </p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-8 card-hover">
            <div class="text-5xl mb-4">⭐</div>
            <h3 class="text-xl font-semibold mb-3">Penilaian</h3>
            <p class="text-gray-600">
                Dosen memberikan nilai dan feedback.
            </p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-8 card-hover">
            <div class="text-5xl mb-4">👥</div>
            <h3 class="text-xl font-semibold mb-3">Kelola Mahasiswa</h3>
            <p class="text-gray-600">
                Melihat dan mengatur anggota kelas.
            </p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-8 card-hover">
            <div class="text-5xl mb-4">📅</div>
            <h3 class="text-xl font-semibold mb-3">Deadline Tugas</h3>
            <p class="text-gray-600">
                Pengingat batas waktu pengumpulan tugas.
            </p>
        </div>

    </div>

</section>

<footer class="bg-white border-t">

    <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col md:flex-row justify-between items-center">

        <p class="text-gray-500">
            © 2026 Sistem Pengumpulan Tugas
        </p>

        <p class="text-gray-400 text-sm mt-2 md:mt-0">
            Platform pembelajaran digital modern
        </p>

    </div>

</footer>

</body>
</html>