<?php
session_start();

// Verificar si el usuario tiene una sesión activa como empresa
if (!isset($_SESSION["empresa_id"])) {
    header("Location: login_empresa.php");
    exit();
}

include 'config.php'; // Incluir archivo de configuración de la base de datos

// Verificar si se ha pasado el ID del trabajador por GET
if (!isset($_GET["id"])) {
    echo "Error: ID de trabajador no especificado.";
    exit();
}

$trabajador_id = $_GET["id"];

// Eliminar el trabajador de la base de datos
try {
    $stmt = $conn->prepare("DELETE FROM Trabajadores WHERE id = ?");
    $stmt->execute([$trabajador_id]);

    // Redirigir de vuelta a la página de gestión de trabajadores
    header("Location: gestion_trabajadores.php");
    exit();
} catch (PDOException $e) {
    echo "Error al eliminar trabajador: " . $e->getMessage();
    exit();
}
?>
