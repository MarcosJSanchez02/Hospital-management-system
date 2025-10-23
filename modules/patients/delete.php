<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("DELETE FROM patients WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: list.php");
exit();
