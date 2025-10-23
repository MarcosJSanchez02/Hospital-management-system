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
$error = '';
$success = '';

$stmt = $conn->prepare("SELECT nombre, apellido FROM patients WHERE id=?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
if (!$patient) {
    die("Paciente no encontrado.");
}

$doctors_result = $conn->query("SELECT id, username FROM users ORDER BY username ASC");

if (isset($_POST['add'])) {
    $doctor_id = $_POST['doctor_id'] ?: null;
    $diagnostico = trim($_POST['diagnostico']);
    $tratamiento = trim($_POST['tratamiento']);
    $fecha_consulta = $_POST['fecha_consulta'] ?: date('Y-m-d');

    if ($diagnostico || $tratamiento) {
        $stmt = $conn->prepare("INSERT INTO medical_records (patient_id, doctor_id, diagnostico, tratamiento, fecha_consulta) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $patient_id, $doctor_id, $diagnostico, $tratamiento, $fecha_consulta);
        if ($stmt->execute()) {
            $success = "Registro médico agregado correctamente.";
        } else {
            $error = "Error al agregar registro: " . $stmt->error;
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
    <title>Agregar Registro Médico - <?php echo htmlspecialchars($patient['nombre'] . ' ' . $patient['apellido']); ?></title>
    <link rel="stylesheet" href="/salita/assets/css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <h2>Agregar Registro Médico de <?php echo htmlspecialchars($patient['nombre'] . ' ' . $patient['apellido']); ?></h2>

        <!-- Botones de navegación -->
        <a href="/salita/index.php" class="dashboard-menu">Volver al Dashboard</a>
        <a href="list.php?patient_id=<?php echo $patient_id; ?>" class="dashboard-menu">Volver a Historial</a>

        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>

        <form method="POST">
            <label>Doctor:</label>
            <select name="doctor_id">
                <option value="">Sin asignar</option>
                <?php while ($d = $doctors_result->fetch_assoc()): ?>
                    <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['username']); ?></option>
                <?php endwhile; ?>
            </select>

            <label>Fecha de Consulta:</label>
            <input type="date" name="fecha_consulta" value="<?php echo date('Y-m-d'); ?>">

            <label>Diagnóstico:</label>
            <textarea name="diagnostico" placeholder="Describir diagnóstico"></textarea>

            <label>Tratamiento:</label>
            <textarea name="tratamiento" placeholder="Describir tratamiento"></textarea>

            <button type="submit" name="add">Agregar Registro</button>
        </form>
    </div>
</body>

</html>