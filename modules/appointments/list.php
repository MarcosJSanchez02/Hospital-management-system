<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

// Traer todos los turnos con estado
$sql = "SELECT a.id, a.fecha, a.hora, a.motivo, a.estado, p.nombre, p.apellido
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        ORDER BY a.fecha DESC, a.hora DESC";

$result = $conn->query($sql);
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Turnos - Salita</title>
    <link rel="stylesheet" href="/salita/assets/css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <h2>Turnos</h2>
        <a href="add.php" class="dashboard-menu">Agregar Turno</a>
        <table class="table-list">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Paciente</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                            <td>
                                <?php
                                $hora = new DateTime($row['hora']);
                                echo $hora->format('h:i A'); // Formato 12h AM/PM
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['motivo']); ?></td>
                            <td>
                                <?php
                                switch ($row['estado']) {
                                    case 'pendiente':
                                        echo "<span class='estado-pendiente'>Pendiente</span>";
                                        break;
                                    case 'atendido':
                                        echo "<span class='estado-atendido'>Atendido</span>";
                                        break;
                                    case 'cancelado':
                                        echo "<span class='estado-cancelado'>Cancelado</span>";
                                        break;
                                }
                                ?>
                            </td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="dashboard-menu">Editar</a>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" class="dashboard-menu" onclick="return confirm('¿Eliminar turno?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">No hay turnos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <a href="/salita/index.php" class="dashboard-menu">Volver al Dashboard</a>
    </div>
</body>

</html>