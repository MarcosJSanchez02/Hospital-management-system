<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

$error = '';
$success = '';

if (isset($_POST['add'])) {
    $nombre = trim($_POST['first_name']);
    $apellido = trim($_POST['last_name']);
    $telefono = trim($_POST['phone']);
    $fecha_nacimiento = $_POST['birth_date'];

    if ($nombre && $apellido) {
        $stmt = $conn->prepare("INSERT INTO patients (nombre, apellido, telefono, fecha_nacimiento) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $apellido, $telefono, $fecha_nacimiento);
        if ($stmt->execute()) {
            $success = "Paciente agregado correctamente.";
        } else {
            $error = "Error al agregar paciente.";
        }
    } else {
        $error = "Nombre y apellido son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Paciente - Salita</title>
    <link rel="stylesheet" href="/salita/assets/css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <h2>Agregar Paciente</h2>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>
        <form method="POST">
            <input type="text" name="first_name" placeholder="Nombre" required>
            <input type="text" name="last_name" placeholder="Apellido" required>
            <input type="text" name="phone" placeholder="Teléfono">
            <input type="date" name="birth_date" placeholder="Fecha de nacimiento">
            <button type="submit" name="add">Agregar Paciente</button>
        </form>
        <br>
        <a href="list.php" class="dashboard-menu">Volver a la lista</a>
    </div>
</body>

</html>