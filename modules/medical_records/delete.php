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

// Obtener patient_id antes de eliminar
$stmt = $conn->prepare("SELECT patient_id FROM medical_records WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$record = $res->fetch_assoc();
$patient_id = $record['patient_id'] ?? 0;

// Eliminar registro
$stmt = $conn->prepare("DELETE FROM medical_records WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

// Redirigir al historial del paciente
header("Location: list.php?patient_id=$patient_id");
exit();
