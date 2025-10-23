
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

// Eliminar turno
$stmt = $conn->prepare("DELETE FROM appointments WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Turno eliminado correctamente.";
} else {
    $_SESSION['error'] = "Error al eliminar turno: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: list.php");
exit();
?>
