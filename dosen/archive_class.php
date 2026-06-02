<?php

session_start();
require_once '../config/database.php';

if ($_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'];

$stmt = $pdo->prepare("
    UPDATE classes
    SET archived = 1
    WHERE id = ?
    AND teacher_id = ?
");

$stmt->execute([
    $id,
    $_SESSION['user_id']
]);

header("Location: archived.php");
exit();