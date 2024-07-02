<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    include 'config.php';

    $email = $_POST["email"];
    $pass = $_POST["password"];

    try {
        $stmt = $conn->prepare("SELECT id, nombre,pass FROM Empresas WHERE email = ?");
        $stmt->execute([$email]);
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($empresa && password_verify($password, $empresa["pass"])) {
            $_SESSION["empresa_id"] = $empresa["id"];
            $_SESSION["empresa_nombre"] = $empresa["nombre"];
            header("Location: gestion_trabajadores.php");
            exit();
        } else {
            $error_message = "Credenciales incorrectas. Por favor, intenta de nuevo.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Empresa</title>
    <link rel="stylesheet" href="styles/loginEmpresa.css">
    <!-- Font Awesome CDN para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<nav>
    <ul>
        <li><a href="login_trabajador.php"><button id="b1"><i class="fas fa-user"></i> Trabajador</button></a></li>
        <li><a href="login_empresa.php"><button id="b2"><i class="fas fa-building"></i> Empresa</button></a></li>
    </ul>
</nav>
<div id="logo">
    <a href="index.php"><img src="styles/img/logo.png" alt="Logo de la empresa"></a>
</div>
<div class="container">
    <h2>Iniciar Sesión - Empresa</h2>
    <form method="POST">
        <label for="email"><i class="fas fa-envelope"></i> Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password"><i class="fas fa-lock"></i> Contraseña:</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit" name="submit" class="button-link-green"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</button>
        <a href="registro_empresa.php" class="button-link-blue"><i class="fas fa-plus-circle"></i> Registrar Empresa</a>
    </form>
    <?php if (isset($error_message)): ?>
        <p><?php echo $error_message; ?></p>
    <?php endif; ?>
</div>
</body>
</html>



