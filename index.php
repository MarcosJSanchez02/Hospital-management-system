<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: modules/users/login.php");
    exit();
}

// Obtener info del usuario
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, role FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Salita</title>
    <link rel="stylesheet" href="/salita/assets/css/style.css">
</head>

<body>

    <div class="dashboard-container">
        <h2>Bienvenido, <?php echo htmlspecialchars($user['username']); ?>!</h2>

        <div class="card">
            <h3>Pacientes</h3>
            <a href="modules/patients/list.php" class="btn dashboard-menu">Pacientes</a>
        </div>

        <div class="card">
            <h3>Turnos</h3>
            <a href="modules/appointments/list.php" class="btn dashboard-menu">Turnos</a>
        </div>

        <div class="card">
            <h3>Historial Médico</h3>
            <a href="modules/medical_records/list.php" class="btn dashboard-menu">Historial Médico</a>
        </div>

        <div class="card">
            <h3>Salir</h3>
            <a href="modules/users/logout.php" class="dashboard-menu">Cerrar Sesión</a>
        </div>
    </div>

</body>

</html>