<?php
session_start();

if (!isset($_SESSION["worker_id"])) {
    header("Location: login_trabajador.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fichaje Confirmado</title>
    <!-- Enlace al archivo CSS externo -->
    <link rel="stylesheet" href="styles/fichaje_confirmado.css">
    <!-- Font Awesome para el icono -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
</head>
<body>
    <div class="container">
        <div class="icon">
            <i class="fas fa-thumbs-up"></i>
        </div>
        <h1>Fichaje Confirmado</h1>
        <p>El fichaje ha sido registrado correctamente.</p>
        <a class="btn btn-primary" href="fichaje.php">Volver al registro de fichaje</a>
        <a class="btn btn-secondary" href="logout.php">Cerrar sesión</a>
    </div>
</body>
</html>
