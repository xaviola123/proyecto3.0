<?php
session_start();

// Verificar si no hay sesión activa de trabajador ni empresa
if (!isset($_SESSION["worker_id"]) && !isset($_SESSION["empresa_id"])) {
    header("Location: login_trabajador.php"); // Redirigir al login si no hay sesión activa
    exit();
}

include 'config.php';

// Determinar el ID del trabajador a mostrar (ya sea por GET o por sesión)
if (isset($_GET['id'])) {
    $trabajador_id = $_GET['id'];
} elseif (isset($_SESSION["worker_id"])) {
    $trabajador_id = $_SESSION["worker_id"];
} else {
    header("Location: gestion_trabajadores.php"); 
    exit();
}

try {
    // Obtener nombre y DNI del trabajador
    $stmt = $conn->prepare("SELECT nombre, dni FROM Trabajadores WHERE id = ?");
    $stmt->execute([$trabajador_id]);
    $trabajador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trabajador) {
        echo "Error: Trabajador no encontrado.";
        exit();
    }

    // Obtener los fichajes del trabajador
    $stmt = $conn->prepare("SELECT fecha, hora_entrada, hora_salida FROM Fichajes WHERE trabajador_id = ?");
    $stmt->execute([$trabajador_id]);
    $fichajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ver Fichajes</title>
    <link rel="stylesheet" href="styles/ver_fichajes.css">
</head>
<body>
    <div class="container">
        <h2>Fichajes de <?php echo htmlspecialchars($trabajador['nombre']) . " (DNI: " . htmlspecialchars($trabajador['dni']) . ")"; ?></h2>
        <table>
            <tr>
                <th>Fecha</th>
                <th>Hora de Entrada</th>
                <th>Hora de Salida</th>
            </tr>
            <?php foreach ($fichajes as $fichaje): ?>
            <tr>
                <td><?php echo htmlspecialchars($fichaje['fecha']); ?></td>
                <td><?php echo htmlspecialchars($fichaje['hora_entrada']); ?></td>
                <td><?php echo htmlspecialchars($fichaje['hora_salida']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <button onclick="goBack()">Volver</button>
        <br><br>
        <a href="logout.php">Cerrar Sesión</a>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
