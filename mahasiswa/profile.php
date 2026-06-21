<?php

session_start();

require_once '../config/database.php';


if(!isset($_SESSION['user_id'])){

    header("Location: ../auth/login.php");
    exit();

}


// ================= HAPUS FOTO =================

if(isset($_GET['hapus'])){


    $stmt = $pdo->prepare("
        UPDATE users
        SET photo = NULL
        WHERE id = ?
    ");


    $stmt->execute([
        $_SESSION['user_id']
    ]);


    $_SESSION['photo'] = null;


    header("Location: profile.php");
    exit();

}



// ================= UPLOAD FOTO =================

if(isset($_POST['upload'])){


    if(!isset($_FILES['photo']) || $_FILES['photo']['error'] != 0){

        die("Foto belum dipilih");

    }


    $file = $_FILES['photo'];


    $folder = __DIR__ . "/../uploads/profile/";



    if(!is_dir($folder)){

        mkdir($folder,0777,true);

    }



    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);


    $namaFile = time().'_profile.'.$ext;


    $path = $folder.$namaFile;



    if(move_uploaded_file($file['tmp_name'], $path)){



        $photo = "uploads/profile/".$namaFile;



        $stmt = $pdo->prepare("
            UPDATE users
            SET photo = ?
            WHERE id = ?
        ");



        $stmt->execute([

            $photo,

            $_SESSION['user_id']

        ]);



        $_SESSION['photo'] = $photo;



        header("Location: profile.php");

        exit();



    }else{


        die("Upload foto gagal");

    }

}



// ================= AMBIL DATA USER =================


$stmt = $pdo->prepare("
SELECT *
FROM users
WHERE id = ?
");


$stmt->execute([

    $_SESSION['user_id']

]);


$user = $stmt->fetch();



?>


<!DOCTYPE html>

<html lang="id">


<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>Profile</title>


<script src="https://cdn.tailwindcss.com"></script>


</head>



<body class="bg-gray-100 p-10">


<div class="bg-white rounded-xl shadow p-6 max-w-md mx-auto">



<h1 class="text-2xl font-bold mb-5">

Profile

</h1>



<!-- FOTO PROFILE -->


<?php if(!empty($user['photo'])): ?>


<img

src="../<?= htmlspecialchars($user['photo']) ?>"

class="w-32 h-32 rounded-full object-cover border">



<?php else: ?>


<div class="w-32 h-32 rounded-full bg-blue-600 text-white flex items-center justify-center text-5xl">


<?= strtoupper(substr($user['full_name'],0,1)) ?>


</div>



<?php endif; ?>




<h2 class="text-xl font-semibold mt-4">

<?= htmlspecialchars($user['full_name']) ?>

</h2>


<p class="text-gray-500">

<?= htmlspecialchars($user['username']) ?>

</p>





<!-- FORM UPLOAD -->


<form method="POST"

enctype="multipart/form-data"

class="mt-6">





<input

type="file"

name="photo"

accept="image/*"

required

class="border p-2 rounded w-full">





<button

type="submit"

name="upload"

class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">


Upload Foto

</button>



</form>





<!-- HAPUS FOTO -->


<?php if(!empty($user['photo'])): ?>


<a href="profile.php?hapus=1"

onclick="return confirm('Hapus foto profil?')"

class="inline-block mt-3 bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg">


Hapus Foto


</a>


<?php endif; ?>






<a href="dashboard.php"

class="block mt-5 text-blue-600">


← Kembali Dashboard


</a>



</div>



</body>


</html>