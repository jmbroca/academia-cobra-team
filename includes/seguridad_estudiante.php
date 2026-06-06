<?php
session_start();

// 1. Si NO hay una sesión activa, lo pateamos de vuelta al login
if (!isset($_SESSION['id_alumno'])) {
    header("Location: index.php");
    exit();
}

// 2. Si el usuario SÍ está logueado, pero su rol es de "admin", 
// no tiene nada que hacer en la vista de estudiante, lo mandamos a su panel.
if ($_SESSION['rol'] === 'admin') {
    header("Location: admin/dashboard.php");
    exit();
}
?>