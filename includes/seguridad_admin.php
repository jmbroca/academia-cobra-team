<?php
session_start();

// 1. Si NO hay sesión, lo regresamos al index principal (nota los ../ para salir de la carpeta admin)
if (!isset($_SESSION['id_alumno'])) {
    header("Location: ../index.php");
    exit();
}

// 2. Si es un estudiante intentando espiar la carpeta del administrador, lo regresamos a su dashboard
if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit();
}
?>