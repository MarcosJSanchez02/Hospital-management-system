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

$stmt = $conn->prepare("
    SELECT mr.*, p.nombre AS patient_nombre, p.apellido AS patient_apellido, u.username AS doctor_name
    FROM medical_records mr
    JOIN patients p ON mr.patient_id = p.id
    LEFT JOIN users u ON mr.doctor_id = u.id
    WHERE mr.id=?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$record = $stmt->get_result()->fetch_assoc();
if (!$record) {
    die("Registro no encontrado.");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ver Registro Médico - <?php echo htmlspecialchars($record['patient_nombre'] . ' ' . $record['patient_apellido']); ?></title>
    <link rel="stylesheet" href="/salita/assets/css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <h2>Registro Médico de <?php echo htmlspecialchars($record['patient_nombre'] . ' ' . $record['patient_apellido']); ?></h2>

        <!-- Botones de navegación -->
        <a href="/salita/index.php" class="dashboard-menu">Volver al Dashboard</a>
        <a href="list.php?patient_id=<?php echo $record['patient_id']; ?>" class="dashboard-menu">Volver al Historial</a>

        <p><strong>Doctor:</strong> <?php echo htmlspecialchars($record['doctor_name'] ?: 'Sin asignar'); ?></p>
        <p><strong>Fecha de Consulta:</strong> <?php echo $record['fecha_consulta']; ?></p>
        <p><strong>Diagnóstico:</strong><br><?php echo nl2br(htmlspecialchars($record['diagnostico'])); ?></p>
        <p><strong>Tratamiento:</strong><br><?php echo nl2br(htmlspecialchars($record['tratamiento'])); ?></p>

    </div>
</body>

</html>