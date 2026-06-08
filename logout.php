<?php
// 1. Iniciamos o retomamos la sesión actual
session_start();

// 2. Vaciamos todas las variables de sesión (id_alumno, rol, nombre_completo, etc.)
$_SESSION = array();

// 3. Destruimos la sesión por completo en el servidor
session_destroy();

// 4. Redirigimos al usuario a la página de inicio (Landing Page)
header("Location: index.php");
exit();
?>