<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $direccion = $_POST["direccion"];
    $telefono = $_POST["telefono"];
    $email = $_POST["email"];
    $password = $_POST["password"]; 

    include 'config.php';
    
    try {
        $conn->beginTransaction();

        // Generar el hash de la contraseña
        $hashed_password = password_hash($password,PASSWORD_DEFAULT); 
        $stmt = $conn->prepare("INSERT INTO Empresas (nombre, direccion, telefono, email, pass) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $direccion, $telefono, $email, $hashed_password]);

        $empresa_id = $conn->lastInsertId();

        

        $conn->commit();
        $_SESSION["empresa_id"] = $empresa_id;
        $_SESSION['empresa_nombre'] = $nombre; 
        header("Location: login_empresa.php");
        exit(); 
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro de Empresa</title>
    <link rel="stylesheet" href="styles/registroEmpresa.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
<nav>
        <ul>
            <li><a href="login_trabajador.php"><button id="b1">Trabajador</button></a></li>
            <li><a href="login_empresa.php"> <button id="b2">Empresa</button></a></li>
        </ul>
    </nav>
    <h2>Registro de Nueva Empresa</h2>
    <form method="POST">
    <label for="nombre"><i class="fas fa-user"></i> Nombre de la Empresa:</label>
    <input type="text" id="nombre" name="nombre" required><br><br>
    
    <label for="direccion"><i class="fas fa-map-marker-alt"></i> Dirección:</label>
    <input type="text" id="direccion" name="direccion" required><br><br>
    
    <label for="telefono"><i class="fas fa-phone"></i> Teléfono:</label>
    <input type="text" id="telefono" name="telefono" required><br><br>
    
    <label for="email"><i class="fas fa-envelope"></i> Email:</label>
    <input type="email" id="email" name="email" required><br><br>
    
    <label for="password"><i class="fas fa-lock"></i> Contraseña:</label>
    <input type="password" id="password" name="password" required><br><br>
    
    <input type="submit" value="Registrar Empresa">
</form>


    
</body>
<script src="js/validar_registroEmpresa.js"></script>
</html>
