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
$error = '';
$success = '';

// Obtener registro
$stmt = $conn->prepare("SELECT * FROM medical_records WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();
if (!$record) {
    die("Registro no encontrado.");
}

// Obtener paciente
$stmt2 = $conn->prepare("SELECT nombre, apellido FROM patients WHERE id=?");
$stmt2->bind_param("i", $record['patient_id']);
$stmt2->execute();
$res = $stmt2->get_result();
$patient = $res->fetch_assoc();

// Obtener lista de doctores
$doctors_result = $conn->query("SELECT id, username FROM users ORDER BY username ASC");

// Actualizar registro
if (isset($_POST['edit'])) {
    $doctor_id = $_POST['doctor_id'] ?: null;
    $diagnostico = trim($_POST['diagnostico']);
    $tratamiento = trim($_POST['tratamiento']);
    $fecha_consulta = $_POST['fecha_consulta'] ?: date('Y-m-d');

    if ($diagnostico || $tratamiento) {
        $stmt = $conn->prepare("UPDATE medical_records SET doctor_id=?, diagnostico=?, tratamiento=?, fecha_consulta=? WHERE id=?");
        $stmt->bind_param("isssi", $doctor_id, $diagnostico, $tratamiento, $fecha_consulta, $id);
        if ($stmt->execute()) {
            $success = "Registro médico actualizado correctamente.";
        } else {
            $error = "Error al actualizar registro: " . $stmt->error;
        }
    } else {
        $error = "Debe ingresar al menos diagnóstico o tratamiento.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Registro Médico - <?php echo htmlspecialchars($patient['nombre'] . ' ' . $patient['apellido']); ?></title>
    <link rel="stylesheet" href="/salita/assets/css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <h2>Editar Registro Médico de <?php echo htmlspecialchars($patient['nombre'] . ' ' . $patient['apellido']); ?></h2>

        <!-- Botones de navegación -->
        <a href="/salita/index.php" class="dashboard-menu">Volver al Dashboard</a>
        <a href="list.php?patient_id=<?php echo $record['patient_id']; ?>" class="dashboard-menu">Volver a Historial</a>

        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>

        <form method="POST">
            <label>Doctor:</label>
            <select name="doctor_id">
                <option value="">Sin asignar</option>
                <?php while ($d = $doctors_result->fetch_assoc()): ?>
                    <option value="<?php echo $d['id']; ?>" <?php if ($record['doctor_id'] == $d['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($d['username']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Fecha de Consulta:</label>
            <input type="date" name="fecha_consulta" value="<?php echo $record['fecha_consulta']; ?>">

            <label>Diagnóstico:</label>
            <textarea name="diagnostico"><?php echo htmlspecialchars($record['diagnostico']); ?></textarea>

            <label>Tratamiento:</label>
            <textarea name="tratamiento"><?php echo htmlspecialchars($record['tratamiento']); ?></textarea>

            <button type="submit" name="edit">Actualizar Registro</button>
        </form>
    </div>
</body>

</html>