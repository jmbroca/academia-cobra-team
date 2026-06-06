<?php
// Datos de conexión por defecto en XAMPP
$servidor = "localhost";
$usuario = "root";
$password = ""; 
$base_datos = "academia_cobra_team";

// Crear la conexión usando MySQLi
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Comprobar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Para que los acentos y las ñ se guarden y lean correctamente
$conexion->set_charset("utf8mb4");
?>