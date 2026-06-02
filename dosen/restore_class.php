<?php

session_start();
require_once '../config/database.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("
    UPDATE classes
    SET archived = 0
    WHERE id = ?
    AND teacher_id = ?
");

$stmt->execute([
    $id,
    $_SESSION['user_id']
]);

header("Location: archived.php");
exit();