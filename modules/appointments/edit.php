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

// Obtener turno
$stmt = $conn->prepare("SELECT * FROM appointments WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();

if (!$appointment) {
    header("Location: list.php");
    exit();
}

// Obtener lista de pacientes
$patients_result = $conn->query("SELECT id, nombre, apellido FROM patients ORDER BY nombre ASC");
if (!$patients_result) {
    die("Error al obtener pacientes: " . $conn->error);
}

if (isset($_POST['edit'])) {
    $patient_id = $_POST['patient_id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $motivo = trim($_POST['motivo']);
    $estado = $_POST['estado'];

    if ($patient_id && $fecha && $hora) {
        $stmt = $conn->prepare("UPDATE appointments SET patient_id=?, fecha=?, hora=?, motivo=?, estado=? WHERE id=?");
        $stmt->bind_param("issssi", $patient_id, $fecha, $hora, $motivo, $estado, $id);
        if ($stmt->execute()) {
            $success = "Turno actualizado correctamente.";
        } else {
            $error = "Error al actualizar turno: " . $stmt->error;
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
    <title>Editar Turno - Salita</title>
    <link rel="stylesheet" href="/salita/assets/css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <h2>Editar Turno</h2>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>

        <form method="POST">
            <label>Paciente:</label>
            <select name="patient_id" required>
                <option value="">Seleccionar paciente</option>
                <?php while ($p = $patients_result->fetch_assoc()): ?>
                    <option value="<?php echo $p['id']; ?>" <?php if ($p['id'] == $appointment['patient_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($p['nombre'] . ' ' . $p['apellido']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Fecha:</label>
            <input type="date" name="fecha" value="<?php echo $appointment['fecha']; ?>" required>

            <label>Hora:</label>
            <input type="time" name="hora" value="<?php echo $appointment['hora']; ?>" required>

            <label>Motivo:</label>
            <textarea name="motivo"><?php echo htmlspecialchars($appointment['motivo']); ?></textarea>

            <label>Estado:</label>
            <select name="estado">
                <option value="pendiente" <?php if ($appointment['estado'] == 'pendiente') echo 'selected'; ?>>Pendiente</option>
                <option value="atendido" <?php if ($appointment['estado'] == 'atendido') echo 'selected'; ?>>Atendido</option>
                <option value="cancelado" <?php if ($appointment['estado'] == 'cancelado') echo 'selected'; ?>>Cancelado</option>
            </select>

            <button type="submit" name="edit">Actualizar Turno</button>
        </form>
        <br>
        <a href="list.php" class="dashboard-menu">Volver a la lista</a>
    </div>
</body>

</html>