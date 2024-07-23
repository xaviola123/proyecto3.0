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

// Inicializar el array de errores
$errores = array();
$mensaje_exito = "";

// Función para calcular la edad
function calcularEdad($fecha_nacimiento) {
    $fecha_actual = new DateTime();
    $fecha_nacimiento = new DateTime($fecha_nacimiento);
    $edad = $fecha_actual->diff($fecha_nacimiento)->y;
    return $edad;
}

// Función para validar nombres y apellidos
function validarNombreApellido($valor) {
    return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $valor);
}

// Procesamiento del formulario de agregar trabajador
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["agregar"])) {
    $dni = trim($_POST["dni"]);
    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $direccion = trim($_POST["direccion"]);
    $telefono = trim($_POST["telefono"]);
    $email = trim($_POST["email"]);
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $fecha_contratacion = $_POST["fecha_contratacion"];

    $fecha_actual = new DateTime();
    $fecha_2_meses_atras = $fecha_actual->sub(new DateInterval('P2M'))->format('Y-m-d');
    $fecha_actual = new DateTime();

    // Expresiones regulares para DNI/NIE
    $dniRegex = '/^\d{8}[a-zA-Z]$/'; // DNI
    $nieRegex = '/^[XYZ]\d{7}[A-Z]$/'; // NIE
    $telefonoRegex = '/^[6-7]\d{8}$/';
    $emailRegex = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';

    try {
        // Validar si el DNI ya existe en la base de datos
        $stmt = $conn->prepare("SELECT id FROM Trabajadores WHERE dni = ?");
        $stmt->execute([$dni]);
        $existeDNI = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existeDNI) {
            $errores['dni'] = "El DNI ingresado ya existe en la base de datos.";
        }

        // Validar si el email ya existe en la base de datos
        $stmt = $conn->prepare("SELECT id FROM Trabajadores WHERE email = ?");
        $stmt->execute([$email]);
        $existeEmail = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existeEmail) {
            $errores['email'] = "El email ingresado ya existe en la base de datos.";
        }

        // Validar DNI/NIE
        if (!(preg_match($dniRegex, $dni) || preg_match($nieRegex, $dni))) {
            $errores['dni'] = "Por favor, introduce un DNI o NIE válido.";
        }

        // Validar teléfono
        if (!preg_match($telefonoRegex, $telefono)) {
            $errores['telefono'] = "Por favor, introduce un número de teléfono válido (que empiece con 6 o 7 y tenga 9 dígitos).";
        }

        // Validar email
        if (!preg_match($emailRegex, $email)) {
            $errores['email'] = "Por favor, introduce un email válido.";
        }

        // Validar nombres y apellidos
        if (!validarNombreApellido($nombre)) {
            $errores['nombre'] = "El nombre solo debe contener letras y espacios.";
        }

        if (!validarNombreApellido($apellido)) {
            $errores['apellido'] = "El apellido solo debe contener letras y espacios.";
        }

        // Validar edad
        $edad = calcularEdad($fecha_nacimiento);
        if ($edad < 18) {
            $errores['fecha_nacimiento'] = "El trabajador debe tener al menos 18 años de edad.";
        } elseif ($edad > 65) {
            $errores['fecha_nacimiento'] = "El trabajador no puede tener más de 65 años de edad.";
        }

        // Validar fecha de contratación
        $fecha_contratacion_dt = new DateTime($fecha_contratacion);

        if ($fecha_contratacion_dt > $fecha_actual) {
            $errores['fecha_contratacion'] = "Imposible: la fecha de contratación no puede ser posterior al día de hoy.";
        }

        // Validar que la fecha de contratación no sea anterior en más de 10 días
        $fecha_maxima = $fecha_actual->sub(new DateInterval('P10D'));
        if ($fecha_contratacion_dt < $fecha_maxima) {
            $errores['fecha_contratacion'] = "La fecha de contratación no puede ser anterior al día de hoy en más de 10 días.";
        }

        // Si no hay errores, proceder con la inserción del trabajador
        if (empty($errores)) {
            $stmt = $conn->prepare("INSERT INTO Trabajadores (empresa_id, dni, nombre, apellido, direccion, telefono, email, fecha_nacimiento, fecha_contratacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$empresa_id, $dni, $nombre, $apellido, $direccion, $telefono, $email, $fecha_nacimiento, $fecha_contratacion]);
            $mensaje_exito = "Trabajador agregado exitosamente.";
        }
    } catch (PDOException $e) {
        $errores['general'] = "Error al agregar trabajador: " . $e->getMessage();
    }
}

// Consultar trabajadores
try {
    $stmt = $conn->prepare("SELECT * FROM Trabajadores WHERE empresa_id = ?");
    $stmt->execute([$empresa_id]);
    $trabajadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errores['general'] = "Error al obtener trabajadores: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestión de Trabajadores</title>
    <link rel="stylesheet" href="styles/gestion_trabajadores.css">
    <style>
      
    </style>
</head>
<body>
<nav>
    <ul>
        <li><a href="login_trabajador.php"><button id="b1">Trabajador</button></a></li>
        <li><a href="login_empresa.php"><button id="b2">Empresa</button></a></li>
    </ul>
</nav>
<div class="container">
    <form method="POST">
        <h2>Gestión <?php echo htmlspecialchars($_SESSION["empresa_nombre"]); ?></h2>
        
        <!-- Contenedor para mostrar errores y mensajes de éxito -->
        <div id="error_container">
            <?php if (!empty($errores['general'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($errores['general']); ?></p>
            <?php endif; ?>
           
        </div>
        
        <div class="form-group">
            <label for="dni">DNI/NIE:</label>
            <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($dni ?? ''); ?>" required>
            <div class="error-message"><?php echo $errores['dni'] ?? ''; ?></div>
        </div>
        
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre ?? ''); ?>" required>
            <div class="error-message"><?php echo $errores['nombre'] ?? ''; ?></div>
        </div>
        
        <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido ?? ''); ?>" required>
            <div class="error-message"><?php echo $errores['apellido'] ?? ''; ?></div>
        </div>
        
        <div class="form-group">
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($direccion ?? ''); ?>" required>
            <div class="error-message"><?php echo $errores['direccion'] ?? ''; ?></div>
        </div>
        
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono ?? ''); ?>" required>
            <div class="error-message"><?php echo $errores['telefono'] ?? ''; ?></div>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            <div class="error-message"><?php echo $errores['email'] ?? ''; ?></div>
        </div>
        
        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($fecha_nacimiento ?? ''); ?>" required>
            <div class="error-message"><?php echo $errores['fecha_nacimiento'] ?? ''; ?></div>
        </div>
        
        <div class="form-group">
            <label for="fecha_contratacion">Fecha de Contratación:</label>
            <input type="date" id="fecha_contratacion" name="fecha_contratacion" value="<?php echo htmlspecialchars($fecha_contratacion ?? ''); ?>" required>
            <div class="error-message"><?php echo $errores['fecha_contratacion'] ?? ''; ?></div>
        </div>

        <input type="submit" name="agregar" value="AGREGAR TRABAJADOR">
        <?php if (!empty($mensaje_exito)): ?>
                <p class="success-message"><?php echo htmlspecialchars($mensaje_exito); ?></p>
            <?php endif; ?>
    </form>
    <form id="cerrarSesion" method="POST">
        <input type="submit" name="cerrarSesion" value="CERRAR SESIÓN">
    </form>

    <h3>Lista de Trabajadores</h3>
    <table border="1">
        <tr>
            <th>DNI/NIE</th>
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
<script>
    window.onload = function() {
        <?php if ($mensaje_exito): ?>
            alert("Trabajador agregado exitosamente.");
        <?php endif; ?>
    };
    </script>
</body>



</html>
