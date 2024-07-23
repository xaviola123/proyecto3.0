<?php
session_start();

if (!isset($_SESSION["empresa_id"])) {
    header("Location: login_empresa.php");
    exit();
}

include 'config.php';

// Verificar si se ha enviado el formulario de editar trabajador
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $trabajador_id = $_POST["trabajador_id"];
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $direccion = $_POST["direccion"];
    $telefono = $_POST["telefono"];
    $email = $_POST["email"];

    try {
        // Actualizar los datos del trabajador
        $stmt = $conn->prepare("UPDATE Trabajadores SET nombre = ?, apellido = ?, direccion = ?, telefono = ?, email = ? WHERE id = ?");
        $stmt->execute([$nombre, $apellido, $direccion, $telefono, $email, $trabajador_id]);

        // Verificar si se solicitó renovar la contraseña
        if (isset($_POST["renovar_contrasena"])) {
            // Establecer la contraseña del trabajador como vacía (NULL en la base de datos)
            $stmt = $conn->prepare("UPDATE Trabajadores SET contraseña = NULL WHERE id = ?");
            $stmt->execute([$trabajador_id]);
        }

        // Redireccionar a la página de gestión de trabajadores u otra página relevante
        header("Location: gestion_trabajadores.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Obtener el ID del trabajador desde la URL (o desde donde sea que lo estés obteniendo)
$trabajador_id = $_GET['id'];

// Obtener los datos actuales del trabajador para prellenar el formulario
try {
    $stmt = $conn->prepare("SELECT * FROM Trabajadores WHERE id = ?");
    $stmt->execute([$trabajador_id]);
    $trabajador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trabajador) {
        echo "Error: Trabajador no encontrado.";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Trabajador</title>
    <link rel="stylesheet" href="styles/editar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div class="container">
    <h2>Editar Trabajador</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="trabajador_id" value="<?php echo $trabajador['id']; ?>">

        <label for="nombre"><i class="fas fa-user"></i>Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($trabajador['nombre']); ?>"
               required>

        <label for="apellido"><i class="fas fa-user"></i>Apellido:</label>
        <input type="text" id="apellido" name="apellido"
               value="<?php echo htmlspecialchars($trabajador['apellido']); ?>" required>

        <label for="direccion"><i class="fas fa-map-marker-alt"></i>Dirección:</label>
        <input type="text" id="direccion" name="direccion"
               value="<?php echo htmlspecialchars($trabajador['direccion']); ?>" required>

        <label for="telefono"><i class="fas fa-phone"></i>Teléfono:</label>
        <input type="text" id="telefono" name="telefono"
               value="<?php echo htmlspecialchars($trabajador['telefono']); ?>" required>

        <label for="email"><i class="fas fa-envelope"></i>Email:</label>
        <input type="email" id="email" name="email"
               value="<?php echo htmlspecialchars($trabajador['email']); ?>" required>

        <!-- Botón para guardar cambios -->
        <button type="submit" name="guardar" class="guardar"><i class="fas fa-save"></i> Guardar Cambios</button>

        <!-- Botón para renovar contraseña -->
        <input type="submit" name="renovar_contrasena" class="renovar" value="Renovar Contraseña">

    </form>

    <!-- Enlaces -->
    <br>
    <a href="gestion_trabajadores.php"><button class="volver"><i class="fas fa-arrow-left"></i> Volver</button></a>
   
</div>
</body>
</html>
