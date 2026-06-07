<?php
session_start();
require 'includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Limpiar y capturar datos
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $password_plana = trim($_POST['password']);
    $fecha_nac = trim($_POST['fecha_nacimiento']);

    // Validar que no vengan vacíos
    if(empty($nombre) || empty($apellidos) || empty($email) || empty($password_plana) || empty($fecha_nac)) {
        header("Location: index.php?error_reg=vacio");
        exit();
    }

    // 2. Verificar si el correo ya existe
    $stmt_check = $conexion->prepare("SELECT id_alumno FROM alumnos WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $resultado_check = $stmt_check->get_result();
    $stmt_check->close();

    if ($resultado_check->num_rows > 0) {
        // El correo ya está registrado
        header("Location: index.php?error_reg=correo_existe");
        exit();
    }

    // 3. Encriptar la contraseña por seguridad
    $password_hash = password_hash($password_plana, PASSWORD_DEFAULT);

    // 4. Insertar en la Base de Datos
    $stmt_insert = $conexion->prepare("INSERT INTO alumnos (nombre, apellidos, email, password, fecha_nacimiento, rol) VALUES (?, ?, ?, ?, ?, 'estudiante')");
    $stmt_insert->bind_param("sssss", $nombre, $apellidos, $email, $password_hash, $fecha_nac);

    if ($stmt_insert->execute()) {
        // Registro exitoso
        $stmt_insert->close();
        header("Location: index.php?exito=1");
        exit();
    } else {
        // Ocurrió un error en la BD
        $stmt_insert->close();
        header("Location: index.php?error_reg=bd");
        exit();
    }

} else {
    header("Location: index.php");
    exit();
}
?>