<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

$error = '';
$success = '';

// Obtener lista de pacientes
$patients_result = $conn->query("SELECT id, nombre, apellido FROM patients ORDER BY nombre ASC");
if (!$patients_result) {
    die("Error al obtener pacientes: " . $conn->error);
}

if (isset($_POST['add'])) {
    $patient_id = $_POST['patient_id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $motivo = trim($_POST['motivo']);
    $estado = $_POST['estado'];

    if ($patient_id && $fecha && $hora) {
        $stmt = $conn->prepare("INSERT INTO appointments (patient_id, fecha, hora, motivo, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $patient_id, $fecha, $hora, $motivo, $estado);
        if ($stmt->execute()) {
            $success = "Turno agregado correctamente.";
        } else {
            $error = "Error al agregar turno: " . $stmt->error;
        }
    } else {
        $error = "Paciente, fecha y hora son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Turno - Salita</title>
    <link rel="stylesheet" href="/salita/assets/css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <h2>Agregar Turno</h2>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>

        <form method="POST">
            <label>Paciente:</label>
            <select name="patient_id" required>
                <option value="">Seleccionar paciente</option>
                <?php while ($p = $patients_result->fetch_assoc()): ?>
                    <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nombre'] . ' ' . $p['apellido']); ?></option>
                <?php endwhile; ?>
            </select>

            <label>Fecha:</label>
            <input type="date" name="fecha" required>

            <label>Hora:</label>
            <input type="time" name="hora" required>

            <label>Motivo:</label>
            <textarea name="motivo" placeholder="Descripción del turno"></textarea>

            <label>Estado:</label>
            <select name="estado">
                <option value="pendiente">Pendiente</option>
                <option value="atendido">Atendido</option>
                <option value="cancelado">Cancelado</option>
            </select>

            <button type="submit" name="add">Agregar Turno</button>
        </form>
        <br>
        <a href="list.php" class="dashboard-menu">Volver a la lista</a>
    </div>
</body>

</html>