<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

if (!isset($_GET['patient_id'])) {
    header("Location: ../../modules/patients/list.php");
    exit();
}

$patient_id = intval($_GET['patient_id']);

// Obtener paciente
$stmt = $conn->prepare("SELECT nombre, apellido FROM patients WHERE id=?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

// Obtener registros médicos
$stmt = $conn->prepare("
    SELECT mr.*, u.username AS doctor_name
    FROM medical_records mr
    LEFT JOIN users u ON mr.doctor_id = u.id
    WHERE mr.patient_id=?
    ORDER BY mr.fecha_consulta DESC
");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$records = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial Médico - <?php echo htmlspecialchars($patient['nombre'] . ' ' . $patient['apellido']); ?></title>
    <link rel="stylesheet" href="/salita/assets/css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <h2>Historial Médico de <?php echo htmlspecialchars($patient['nombre'] . ' ' . $patient['apellido']); ?></h2>

        <a href="add.php?patient_id=<?php echo $patient_id; ?>" class="dashboard-menu">Agregar Registro</a>

        <table class="table-list">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Doctor</th>
                    <th>Fecha</th>
                    <th>Diagnóstico</th>
                    <th>Tratamiento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($records->num_rows > 0): ?>
                    <?php while ($row = $records->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['doctor_name'] ?: 'Sin asignar'); ?></td>
                            <td><?php echo $row['fecha_consulta']; ?></td>
                            <td><?php echo htmlspecialchars($row['diagnostico']); ?></td>
                            <td><?php echo htmlspecialchars($row['tratamiento']); ?></td>
                            <td>
                                <a href="view.php?id=<?php echo $row['id']; ?>" class="dashboard-menu">Ver</a>
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="dashboard-menu">Editar</a>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" class="dashboard-menu" onclick="return confirm('¿Eliminar registro?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;">No hay registros médicos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <br>
        <a href="/salita/modules/patients/list.php" class="dashboard-menu">Volver a Pacientes</a>
    </div>
</body>

</html>