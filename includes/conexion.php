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

// Función para traducir fechas al español
function fechaEspanol($fecha, $formato = 'larga') {
    $timestamp = strtotime($fecha);
    
    // Diccionarios de traducción
    $dias = ['Sunday' => 'Domingo', 'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles', 'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado'];
    $meses = ['Jan' => 'Ene', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Abr', 'May' => 'May', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Aug' => 'Ago', 'Sep' => 'Sep', 'Oct' => 'Oct', 'Nov' => 'Nov', 'Dec' => 'Dic'];
    
    $dia_ingles = date('l', $timestamp);
    $mes_ingles = date('M', $timestamp);
    
    if ($formato == 'corta') {
        // Formato: 10 Jun, 6:00 PM (Para la Próxima Clase)
        return date('d', $timestamp) . " " . $meses[$mes_ingles] . ", " . date('g:i A', $timestamp);
    } else {
        // Formato: Lunes 01 Jun, 2026 (Para el Historial)
        return $dias[$dia_ingles] . " " . date('d', $timestamp) . " " . $meses[$mes_ingles] . ", " . date('Y', $timestamp);
    }
}
?>