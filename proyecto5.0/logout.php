<?php
session_start();
session_unset(); // Eliminar todas las variables de sesión
session_destroy(); // Destruir la sesión

// Redireccionar a la página de inicio de sesión o a la página principal
header("Location: index.php");
exit();
?>
