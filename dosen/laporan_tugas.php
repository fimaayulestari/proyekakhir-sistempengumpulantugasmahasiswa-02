<?php

session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit();
}

$stmt = $pdo->prepare("
SELECT

u.full_name,
u.email,

c.class_name,

t.title,
t.deadline,

s.submitted_at,
s.is_late,
s.grade

FROM tasks t

JOIN classes c
ON t.class_id = c.id

JOIN enrollments e
ON c.id = e.class_id

JOIN users u
ON e.student_id = u.id

LEFT JOIN submissions s
ON t.id = s.task_id
AND u.id = s.student_id

WHERE c.teacher_id = ?

ORDER BY t.deadline DESC
");

$stmt->execute([$_SESSION['user_id']]);

$data = $stmt->fetchAll();

$total = count($data);

$sudah = 0;

foreach ($data as $d) {

    if (!empty($d['submitted_at'])) {
        $sudah++;
    }

}

$belum = $total - $sudah;

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Rekap Pengumpulan Tugas</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

</head>

<body class="bg-gray-100 py-8">

<div class="max-w-[210mm] mx-auto">

    <!-- Tombol Cetak -->

    <div class="flex justify-end mb-4">

        <button
            id="downloadPdf"
            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">

            Cetak PDF

        </button>

    </div>

    <!-- Area PDF -->

    <div
        id="pdf-content"
        class="bg-white p-10">

        <!-- Header Kampus -->

        <div class="flex items-center border-b-2 border-black pb-4">

            <div class="w-28 flex justify-center">

                <img
                    src="../assets/logo.jpeg"
                    alt="Logo UTM"
                    class="w-24 h-24">

            </div>

            <div class="flex-1 text-center">

                <h1 class="text-2xl font-bold uppercase">

                    Universitas Trunojoyo Madura

                </h1>

                <h2 class="text-lg font-semibold uppercase">

                    Fakultas Teknik

                </h2>

                <h3 class="uppercase">

                    Program Studi Sistem Informasi

                </h3>

                <p class="text-sm">

                    Bangkalan - Jawa Timur

                </p>

            </div>

            <div class="w-28"></div>

        </div>

        <!-- Judul -->

        <div class="text-center mt-6 mb-6">

            <h2 class="text-xl font-bold uppercase">

                Rekap Pengumpulan Tugas Mahasiswa

            </h2>

        </div>

        <!-- Info -->

        <div class="mb-6">

            <table>

                <tr>

                    <td class="pr-4 font-semibold">

                        Tanggal Cetak

                    </td>

                    <td>:</td>

                    <td class="pl-2">

                        <?= date('d F Y H:i') ?>

                    </td>

                </tr>

            </table>

        </div>

        <!-- Ringkasan -->

        <div class="mb-8">

            <table class="w-full border border-black">

                <tr>

                    <td class="border p-2 font-semibold w-64">

                        Total Data

                    </td>

                    <td class="border p-2">

                        <?= $total ?>

                    </td>

                </tr>

                <tr>

                    <td class="border p-2 font-semibold">

                        Sudah Mengumpulkan

                    </td>

                    <td class="border p-2">

                        <?= $sudah ?>

                    </td>

                </tr>

                <tr>

                    <td class="border p-2 font-semibold">

                        Belum Mengumpulkan

                    </td>

                    <td class="border p-2">

                        <?= $belum ?>

                    </td>

                </tr>

            </table>

        </div>

        <!-- Tabel Rekap -->

        <table class="w-full border border-collapse">

            <thead>

            <tr>

                <th class="border p-3 text-center w-16">

                    No

                </th>

                <th class="border p-3 text-left">

                    Mahasiswa

                </th>

                <th class="border p-3 text-left">

                    Kelas

                </th>

                <th class="border p-3 text-left">

                    Tugas

                </th>

                <th class="border p-3 text-left">

                    Deadline

                </th>

                <th class="border p-3 text-center">

                    Status

                </th>

                <th class="border p-3 text-center">

                    Nilai

                </th>

            </tr>

            </thead>

            <tbody>

            <?php $no = 1; ?>

            <?php foreach($data as $d): ?>

                <tr>

                    <td class="border p-3 text-center">

                        <?= $no++ ?>

                    </td>

                    <td class="border p-3">

                        <div class="font-medium">

                            <?= htmlspecialchars($d['full_name']) ?>

                        </div>

                        <div class="text-sm text-gray-600">

                            <?= htmlspecialchars($d['email']) ?>

                        </div>

                    </td>

                    <td class="border p-3">

                        <?= htmlspecialchars($d['class_name']) ?>

                    </td>

                    <td class="border p-3">

                        <?= htmlspecialchars($d['title']) ?>

                    </td>

                    <td class="border p-3">

                        <?= date('d-m-Y H:i', strtotime($d['deadline'])) ?>

                    </td>

                    <td class="border p-3 text-center">

                        <?php if (!$d['submitted_at']): ?>

                            <span class="font-semibold">

                                Belum Kumpul

                            </span>

                        <?php elseif ($d['is_late']): ?>

                            <span class="font-semibold">

                                Terlambat

                            </span>

                        <?php else: ?>

                            <span class="font-semibold">

                                Tepat Waktu

                            </span>

                        <?php endif; ?>

                    </td>

                    <td class="border p-3 text-center">

                        <?= $d['grade'] ?? '-' ?>

                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

        <!-- Footer -->

        <div class="mt-8 pt-3 border-t text-sm text-center text-gray-600">

            Dicetak dari Sistem Penugasan Mahasiswa

        </div>

    </div>

</div>

<script>

document.getElementById('downloadPdf').addEventListener('click', function () {

    const element = document.getElementById('pdf-content');

    const options = {

        margin: 10,

        filename: 'rekap_pengumpulan_tugas.pdf',

        image: {
            type: 'jpeg',
            quality: 1
        },

        html2canvas: {
            scale: 2
        },

        jsPDF: {
            unit: 'mm',
            format: 'a4',
            orientation: 'portrait'
        }

    };

    html2pdf()
        .set(options)
        .from(element)
        .save();

});

</script>

</body>

</html>