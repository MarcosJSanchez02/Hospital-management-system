<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    header("Location: list.php");
    exit();
}

if (isset($_POST['edit'])) {
    $nombre = trim($_POST['first_name']);
    $apellido = trim($_POST['last_name']);
    $telefono = trim($_POST['phone']);
    $fecha_nacimiento = $_POST['birth_date'];

    if ($nombre && $apellido) {
        $stmt = $conn->prepare("UPDATE patients SET nombre=?, apellido=?, telefono=?, fecha_nacimiento=? WHERE id=?");
        $stmt->bind_param("ssssi", $nombre, $apellido, $telefono, $fecha_nacimiento, $id);
        if ($stmt->execute()) {
            $success = "Paciente actualizado correctamente.";
        } else {
            $error = "Error al actualizar paciente.";
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
    <title>Editar Paciente - Salita</title>
    <link rel="stylesheet" href="/salita/assets/css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <h2>Editar Paciente</h2>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>
        <form method="POST">
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($patient['nombre']); ?>" placeholder="Nombre" required>
            <input type="text" name="last_name" value="<?php echo htmlspecialchars($patient['apellido']); ?>" placeholder="Apellido" required>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($patient['telefono']); ?>" placeholder="Teléfono">
            <input type="date" name="birth_date" value="<?php echo $patient['fecha_nacimiento']; ?>" placeholder="Fecha de nacimiento">
            <button type="submit" name="edit">Actualizar Paciente</button>
        </form>
        <br>
        <a href="list.php" class="dashboard-menu">Volver a la lista</a>
    </div>
</body>

</html>