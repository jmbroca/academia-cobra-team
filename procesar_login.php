<?php
// Iniciar la sesión siempre debe ser la primera línea
session_start();

// Incluir la conexión a la base de datos
require 'includes/conexion.php'; 

// Validar que los datos vengan por el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Limpiar los datos recibidos
    $email = trim($_POST['email']);
    $pass_ingresado = trim($_POST['password']);

    // Añadimos "rol" a los datos que pedimos de la base de datos
    $stmt = $conexion->prepare("SELECT id_alumno, nombre, apellidos, password, rol FROM alumnos WHERE email = ? AND estatus = 'Activo'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $stmt->close();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        
        if (password_verify($pass_ingresado, $usuario['password'])) {
            
            // Creamos las sesiones, incluyendo el rol
            $_SESSION['id_alumno'] = $usuario['id_alumno'];
            $_SESSION['nombre_completo'] = $usuario['nombre'] . " " . $usuario['apellidos'];
            $_SESSION['rol'] = $usuario['rol'];
            
            // EL CONTROL DE TRÁFICO: ¿A dónde va este usuario?
            if ($_SESSION['rol'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
            
        } else {
            header("Location: index.php?error=contrasena");
            exit();
        }
    }
    else {
        // El correo no existe o el alumno está inactivo
        header("Location: index.php?error=correo");
        exit();
    }

} else {
    // Si alguien intenta entrar a este archivo directamente desde la URL, lo regresamos
    header("Location: index.php");
    exit();
}
?>