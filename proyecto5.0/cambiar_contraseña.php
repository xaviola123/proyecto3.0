<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nueva_contraseña = $_POST["nueva_contraseña"];
    $confirmar_contraseña = $_POST["confirmar_contraseña"];

    // Verificar que las contraseñas coincidan
    if ($nueva_contraseña === $confirmar_contraseña) {
        include 'config.php'; 

        try {
            $hashed_password = password_hash($nueva_contraseña, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE Trabajadores SET contraseña = ?, primer_login = 0 WHERE id = ?");
            $stmt->execute([$hashed_password, $_SESSION["temp_worker_id"]]);

            // Limpiar la variable de sesión temporal
            unset($_SESSION["temp_worker_id"]);

            // Redireccionar a la página principal o de fichaje después de cambiar la contraseña
            header("Location: fichaje.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        $error = "Las contraseñas no coinciden";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cambiar Contraseña</title>
    <link rel="stylesheet" href="styles/cambiarContrasena.css">
</head>
<body>
    <h2>Cambiar Contraseña</h2>
    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
    <form method="POST">
        <label for="nueva_contraseña">Nueva Contraseña:</label>
        <input type="password" id="nueva_contraseña" name="nueva_contraseña" required><br><br>
        <label for="confirmar_contraseña">Confirmar Contraseña:</label>
        <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" required><br><br>
        <input type="submit" value="Cambiar Contraseña">
    </form>
</body>
</html>
