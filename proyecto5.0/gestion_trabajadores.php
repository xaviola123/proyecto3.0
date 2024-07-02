
<?php
session_start();

if (!isset($_SESSION["empresa_id"])) {
    header("Location: login_empresa.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cerrarSesion'])) {
    header("Location: login_empresa.php");
    exit();
}

include 'config.php';

$empresa_id = $_SESSION["empresa_id"];
$mostrarTrabajadores = false;
$error = "";

// Procesamiento del formulario de agregar trabajador
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["agregar"])) {
    $dni = $_POST["dni"];
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $direccion = $_POST["direccion"];
    $telefono = $_POST["telefono"];
    $email = $_POST["email"];
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $fecha_contratacion = $_POST["fecha_contratacion"];

    // Validar si el DNI ya existe en la base de datos
    try {
        $stmt = $conn->prepare("SELECT id FROM Trabajadores WHERE dni = ?");
        $stmt->execute([$dni]);
        $existeDNI = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existeDNI) {
            // Si el DNI existe, mostrar mensaje de error
            $error = "El DNI ingresado ya existe en la base de datos.";
        } else {
            // Verificar que el trabajador tiene más de 18 años
            $hoy = new DateTime();
            $nacimiento = new DateTime($fecha_nacimiento);
            $edad = $hoy->diff($nacimiento)->y;

            if ($edad < 18) {
                $error = "El trabajador debe tener al menos 18 años de edad.";
            } else {
                // Si el DNI no existe y la fecha de nacimiento es válida, proceder con la inserción del trabajador
                $stmt = $conn->prepare("INSERT INTO Trabajadores (empresa_id, dni, nombre, apellido, direccion, telefono, email, fecha_nacimiento, fecha_contratacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$empresa_id, $dni, $nombre, $apellido, $direccion, $telefono, $email, $fecha_nacimiento, $fecha_contratacion]);
                header("Location: gestion_trabajadores.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        echo "Error al agregar trabajador: " . $e->getMessage();
    }
}
    try {
        $stmt = $conn->prepare("SELECT * FROM Trabajadores WHERE empresa_id = ?");
        $stmt->execute([$empresa_id]);
        $trabajadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error al obtener trabajadores: " . $e->getMessage();
    }


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestión de Trabajadores</title>
    <link rel="stylesheet" href="styles/gestion_trabajadores.css">
    <script src="js/validar_formulario.js"></script>
   
</head>
<body>
<nav>
        <ul>
            <li><a href="login_trabajador.php"><button id="b1">Trabajador</button></a></li>
            <li><a href="login_empresa.php"> <button id="b2">Empresa</button></a></li>
        </ul>
    </nav>
    <div class="container">
        <form method="POST" onsubmit="return validarFormulario();">
            <h2>Gestión <?php echo "Empresa: " . $_SESSION["empresa_nombre"] ?></h2>
            <?php if (!empty($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
            <label for="dni">DNI:</label>
            <input type="text" id="dni" name="dni" required><br><br>
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required><br><br>
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" required><br><br>
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" required><br><br>
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required><br><br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required><br><br>
            <label for="fecha_contratacion">Fecha de Contratación:</label>
            <input type="date" id="fecha_contratacion" name="fecha_contratacion" required><br><br>
            <input type="submit" name="agregar" value="AGREGAR TRABAJADOR">
        </form>
        <form id="cerrarSesion" method="POST">
            <input type="submit" name="cerrarSesion" value="CERRAR SESIÓN">
        </form>
        
      

        
        <h3>Lista de Trabajadores</h3>
        <table border="1">
            <tr>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Fecha Nacimiento</th>
                <th>Fecha Contratación</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($trabajadores as $trabajador): ?>
            <tr>
                <td><?php echo htmlspecialchars($trabajador['dni']); ?></td>
                <td><?php echo htmlspecialchars($trabajador['nombre']); ?></td>
                <td><?php echo htmlspecialchars($trabajador['apellido']); ?></td>
                <td><?php echo htmlspecialchars($trabajador['direccion']); ?></td>
                <td><?php echo htmlspecialchars($trabajador['telefono']); ?></td>
                <td><?php echo htmlspecialchars($trabajador['email']); ?></td>
                <td><?php echo htmlspecialchars($trabajador['fecha_nacimiento']); ?></td>
                <td><?php echo htmlspecialchars($trabajador['fecha_contratacion']); ?></td>
                <td>
                    <button><a href="consultar_trabajador.php?id=<?php echo $trabajador['id']; ?>">Consultar</a></button>
                    <button><a href="editar_trabajador.php?id=<?php echo $trabajador['id']; ?>">Editar</a></button>
                    <button><a href="eliminar_trabajador.php?id=<?php echo $trabajador['id']; ?>">Eliminar</a></button>
                    <button><a href="ver_fichajes.php?id=<?php echo $trabajador['id']; ?>">Consultar Fichajes</a></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
     
    </div>
</body>
</html>
