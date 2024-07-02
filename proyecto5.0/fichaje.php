<?php
session_start();

if (!isset($_SESSION["worker_id"])) {
    header("Location: login_trabajador.php");
    exit();
}

include 'config.php';

$trabajador_id = $_SESSION["worker_id"];
$fecha = date("Y-m-d");

$stmt = $conn->prepare("SELECT nombre, dni FROM Trabajadores WHERE id = ?");
$stmt->execute([$trabajador_id]);
$trabajador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trabajador) {
    echo "Error: Trabajador no encontrado.";
    exit();
}

$permitir_entrada = true;
$permitir_salida = false;

// Verificar el último fichaje del día actual
$stmt = $conn->prepare("SELECT hora_entrada, hora_salida FROM Fichajes WHERE trabajador_id = ? AND fecha = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$trabajador_id, $fecha]);
$fichaje = $stmt->fetch(PDO::FETCH_ASSOC);

if ($fichaje) {
    if ($fichaje['hora_entrada'] && !$fichaje['hora_salida']) {
        $permitir_entrada = false;
        $permitir_salida = true;
    } elseif ($fichaje['hora_entrada'] && $fichaje['hora_salida']) {
        $permitir_entrada = true;
        $permitir_salida = false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["entrada"]) && $permitir_entrada) {
        $hora_entrada = date("H:i:s");

        try {
            $stmt = $conn->prepare("INSERT INTO Fichajes (trabajador_id, fecha, hora_entrada) VALUES (?, ?, ?)");
            $stmt->execute([$trabajador_id, $fecha, $hora_entrada]);
            header("Location: fichaje_confirmado.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST["salida"]) && $permitir_salida) {
        $hora_salida = date("H:i:s");

        try {
            $stmt = $conn->prepare("UPDATE Fichajes SET hora_salida = ? WHERE trabajador_id = ? AND fecha = ? AND hora_salida IS NULL ORDER BY id DESC LIMIT 1");
            $stmt->execute([$hora_salida, $trabajador_id, $fecha]);
            header("Location: fichaje_confirmado.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fichaje Diario</title>
    <link rel="stylesheet" href="styles/fichajes.css">
</head>
<body>
    <div class="container">
        <h1>Fichaje Diario </h1>
        <h2> Usuario: <?php echo htmlspecialchars($trabajador['nombre']) . " con DNI: " . htmlspecialchars($trabajador['dni']); ?></h2>
        <form method="POST">
            <?php if ($permitir_entrada): ?>
                <input type="submit" name="entrada" value="Registrar Entrada" class="btn btn-entrada">
            <?php endif; ?>
            <?php if ($permitir_salida): ?>
                <input type="submit" name="salida" value="Registrar Salida" class="btn btn-salida">
            <?php endif; ?>
        </form>
        <div class="links">
            <a href="ver_fichajes.php" class="link"><button class="btn btn-verde">Ver Fichajes</button></a>
            <a href="logout.php" class="link"><button class="btn btn-rojo">Cerrar Sesión</button></a>
        </div>
    </div>
</body>
</html>
