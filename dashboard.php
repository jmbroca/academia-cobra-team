<?php 
require 'includes/seguridad_estudiante.php'; 
require 'includes/conexion.php'; 

$pagina_actual = 'inicio'; 
require 'includes/header_estudiante.php'; 

$id_alumno = $_SESSION['id_alumno'];

// ==========================================
// 1. DATOS BASE (Registro, Disciplina y Cinturón)
// ==========================================
$sql_base = "
    SELECT a.fecha_registro, d.nombre_disciplina, c.nombre_cinturon, ad.porcentaje_progreso 
    FROM alumnos a
    LEFT JOIN alumnos_disciplinas ad ON a.id_alumno = ad.id_alumno
    LEFT JOIN disciplinas d ON ad.id_disciplina = d.id_disciplina
    LEFT JOIN cinturones c ON ad.id_cinturon = c.id_cinturon
    WHERE a.id_alumno = ? LIMIT 1
";
$stmt_base = $conexion->prepare($sql_base);
$stmt_base->bind_param("i", $id_alumno);
$stmt_base->execute();
$datos_base = $stmt_base->get_result()->fetch_assoc();
$stmt_base->close();

$mes_ingreso = fechaEspanol($datos_base['fecha_registro'], 'corta');

// ==========================================
// 2. CÁLCULO DE ASISTENCIA GLOBAL
// ==========================================
$sql_asist = "SELECT COUNT(id_asistencia) as total, SUM(CASE WHEN estatus='Presente' THEN 1 ELSE 0 END) as presentes FROM asistencias WHERE id_alumno = ?";
$stmt_asist = $conexion->prepare($sql_asist);
$stmt_asist->bind_param("i", $id_alumno);
$stmt_asist->execute();
$datos_asist = $stmt_asist->get_result()->fetch_assoc();
$stmt_asist->close();

$porcentaje_asistencia = ($datos_asist['total'] > 0) ? round(($datos_asist['presentes'] / $datos_asist['total']) * 100) : 0;

// ==========================================
// 3. PRÓXIMA CLASE (Reutilizamos la lógica)
// ==========================================
$sql_proxima_clase = "
    SELECT c.fecha_hora, d.nombre_disciplina, i.nombre AS nombre_instructor 
    FROM clases c
    JOIN disciplinas d ON c.id_disciplina = d.id_disciplina
    JOIN instructores i ON c.id_instructor = i.id_instructor
    JOIN alumnos_disciplinas ad ON ad.id_disciplina = c.id_disciplina
    WHERE ad.id_alumno = ? AND c.fecha_hora >= NOW()
    ORDER BY c.fecha_hora ASC LIMIT 1
";
$stmt_prox = $conexion->prepare($sql_proxima_clase);
$stmt_prox->bind_param("i", $id_alumno);
$stmt_prox->execute();
$proxima_clase = $stmt_prox->get_result()->fetch_assoc();
$stmt_prox->close();

// ==========================================
// 4. LOGROS RECIENTES (Los últimos 3)
// ==========================================
$sql_logros = "SELECT titulo, descripcion, fecha_obtencion, tipo_icono FROM logros WHERE id_alumno = ? ORDER BY fecha_obtencion DESC LIMIT 3";
$stmt_logros = $conexion->prepare($sql_logros);
$stmt_logros->bind_param("i", $id_alumno);
$stmt_logros->execute();
$logros = $stmt_logros->get_result();
$stmt_logros->close();
?>

<main class="dashboard-container">
    
    <div class="dashboard-header" style="border-bottom: none; margin-bottom: -45px;">
        <h1 style="text-transform: none;">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></h1>
        <p style="color: var(--text-muted); margin-top: 5px;">Miembro desde <?php echo $mes_ingreso; ?></p>
    </div>

    <section class="summary-cards-grid">
        <div class="dash-card" style="padding: 20px;">
            <div class="dash-card-info">
                <h3 style="font-size: 0.85rem;">Disciplina</h3>
                <p style="font-size: 1.2rem;"><?php echo htmlspecialchars($datos_base['nombre_disciplina'] ?? 'Sin asignar'); ?></p>
            </div>
            <div class="dash-card-icon" style="font-size: 2rem; color: var(--text-muted);"><i class='bx bx-trophy'></i></div>
        </div>

        <div class="dash-card" style="padding: 20px;">
            <div class="dash-card-info">
                <h3 style="font-size: 0.85rem;">Nivel Actual</h3>
                <p style="font-size: 1.2rem;"><?php echo htmlspecialchars($datos_base['nombre_cinturon'] ?? 'Cinta Blanca'); ?></p>
            </div>
            <div class="dash-card-icon" style="font-size: 2rem; color: var(--text-muted);"><i class='bx bx-medal'></i></div>
        </div>

        <div class="dash-card" style="padding: 20px;">
            <div class="dash-card-info">
                <h3 style="font-size: 0.85rem;">Asistencia</h3>
                <p style="font-size: 1.2rem;"><?php echo $porcentaje_asistencia; ?>%</p>
            </div>
            <div class="dash-card-icon" style="font-size: 2rem; color: var(--text-muted);"><i class='bx bx-line-chart'></i></div>
        </div>

        <div class="dash-card" style="padding: 20px;">
            <div class="dash-card-info">
                <h3 style="font-size: 0.85rem;">Progreso</h3>
                <p style="font-size: 1.2rem;"><?php echo $datos_base['porcentaje_progreso'] ?? 0; ?>%</p>
            </div>
            <div class="dash-card-icon" style="font-size: 2rem; color: var(--text-muted);"><i class='bx bx-target-lock'></i></div>
        </div>
    </section>

    <?php if ($proxima_clase): ?>
    <section class="next-class-banner">
        <div class="next-class-details">
            <h3>Próxima Clase</h3>
            <p><?php echo fechaEspanol($proxima_clase['fecha_hora'], 'corta'); ?></p>
            <p style="font-size: 0.9rem;"><?php echo htmlspecialchars($proxima_clase['nombre_disciplina']); ?> - <?php echo htmlspecialchars($proxima_clase['nombre_instructor']); ?></p>
        </div>
        <div class="next-class-icon">
            <i class='bx bx-time-five'></i>
        </div>
    </section>
    <?php endif; ?>

    <section class="list-column" style="width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 1.2rem; font-weight: 900;">Logros Recientes</h3>
        
        <?php if($logros->num_rows > 0): while($log = $logros->fetch_assoc()): 
            // Asignar el ícono correcto de Boxicons
            $icono = 'bx-star';
            if ($log['tipo_icono'] == 'trofeo') $icono = 'bx-trophy';
            if ($log['tipo_icono'] == 'medalla') $icono = 'bx-medal';
            if ($log['tipo_icono'] == 'estrella') $icono = 'bx-star';
            if ($log['tipo_icono'] == 'cinturon') $icono = 'bx-id-card';
        ?>
            <div class="list-item" style="padding: 15px 0;">
                <div class="list-item-icon"><i class='bx <?php echo $icono; ?>'></i></div>
                <div class="list-item-content">
                    <div class="list-item-title" style="font-size: 1.05rem;"><?php echo htmlspecialchars($log['titulo']); ?></div>
                    <div class="list-item-desc"><?php echo htmlspecialchars($log['descripcion']); ?></div>
                </div>
                <div class="list-item-badge" style="color: var(--text-muted); font-weight: normal;">
                    <?php echo date("M Y", strtotime($log['fecha_obtencion'])); ?>
                </div>
            </div>
        <?php endwhile; else: ?>
            <p style="color: var(--text-muted); margin-top: 15px;">Sigue entrenando para desbloquear tu primer logro.</p>
        <?php endif; ?>
    </section>

</main>
</body>
</html>