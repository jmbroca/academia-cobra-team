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
            
            // ==========================================
            // EVALUADOR SILENCIOSO: ASISTENCIA PERFECTA DEL MES PASADO
            // ==========================================
            if ($_SESSION['rol'] !== 'admin') {
                // 1. Calculamos cuál fue el mes anterior
                $mes_anterior = date('m', strtotime('first day of last month'));
                $ano_anterior = date('Y', strtotime('first day of last month'));
                
                // Diccionario para el texto de la medalla
                $meses_texto = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
                $desc_logro = "100% en " . $meses_texto[$mes_anterior];

                // 2. Verificamos si ya le dimos este logro específico para no duplicarlo cada vez que inicie sesión
                $check_logro = $conexion->prepare("SELECT id_logro FROM logros WHERE id_alumno = ? AND descripcion = ?");
                $check_logro->bind_param("is", $usuario['id_alumno'], $desc_logro);
                $check_logro->execute();
                
                if ($check_logro->get_result()->num_rows === 0) {
                    // Si no tiene el logro, evaluamos su asistencia del mes pasado
                    $sql_asist = "
                        SELECT 
                            COUNT(a.id_asistencia) as total_clases,
                            SUM(CASE WHEN a.estatus = 'Presente' THEN 1 ELSE 0 END) as total_presente
                        FROM asistencias a
                        JOIN clases c ON a.id_clase = c.id_clase
                        WHERE a.id_alumno = ? AND MONTH(c.fecha_hora) = ? AND YEAR(c.fecha_hora) = ?
                    ";
                    $stmt_asist = $conexion->prepare($sql_asist);
                    $stmt_asist->bind_param("iii", $usuario['id_alumno'], $mes_anterior, $ano_anterior);
                    $stmt_asist->execute();
                    $datos_asist = $stmt_asist->get_result()->fetch_assoc();

                    // 3. La regla de oro: Tiene que haber tenido clases y NO haber faltado a ninguna
                    if ($datos_asist['total_clases'] > 0 && $datos_asist['total_clases'] == $datos_asist['total_presente']) {
                        $fecha_hoy = date('Y-m-d');
                        $insert_logro = $conexion->prepare("INSERT INTO logros (id_alumno, titulo, descripcion, fecha_obtencion, tipo_icono) VALUES (?, 'Asistencia Perfecta', ?, ?, 'estrella')");
                        $insert_logro->bind_param("iss", $usuario['id_alumno'], $desc_logro, $fecha_hoy);
                        $insert_logro->execute();
                        $insert_logro->close();
                    }
                    $stmt_asist->close();
                }
                $check_logro->close();
            }
            // ==========================================
            // FIN DEL EVALUADOR SILENCIOSO
            // ==========================================

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