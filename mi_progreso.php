<?php 
require 'includes/seguridad_estudiante.php'; 
require 'includes/conexion.php'; 

$pagina_actual = 'progreso'; 
require 'includes/header_estudiante.php'; 

$id_alumno = $_SESSION['id_alumno'];

// ==========================================
// 1. OBTENER ANTIGÜEDAD Y CINTURONES
// ==========================================
$sql_base = "
    SELECT a.fecha_registro, ad.porcentaje_progreso, c.nombre_cinturon AS cinturon_actual, c.orden, c.id_disciplina
    FROM alumnos a
    LEFT JOIN alumnos_disciplinas ad ON a.id_alumno = ad.id_alumno
    LEFT JOIN cinturones c ON ad.id_cinturon = c.id_cinturon
    WHERE a.id_alumno = ? LIMIT 1
";
$stmt_base = $conexion->prepare($sql_base);
$stmt_base->bind_param("i", $id_alumno);
$stmt_base->execute();
$datos_base = $stmt_base->get_result()->fetch_assoc();
$stmt_base->close();

// Calcular Antigüedad
$fecha_inicio = new DateTime($datos_base['fecha_registro']);
$fecha_actual = new DateTime();
$diferencia = $fecha_inicio->diff($fecha_actual);

$texto_antiguedad = "";
if ($diferencia->y > 0) $texto_antiguedad .= $diferencia->y . " Año" . ($diferencia->y > 1 ? "s " : " ");
if ($diferencia->m > 0) $texto_antiguedad .= $diferencia->m . " Mes" . ($diferencia->m > 1 ? "es" : "");
if (empty($texto_antiguedad)) $texto_antiguedad = "Nuevo Ingreso";
$mes_ingreso = fechaEspanol($datos_base['fecha_registro'], 'corta');

// LÓGICA NUEVA: Buscar el siguiente cinturón
$cinturon_siguiente = "Rango Máximo"; // Mensaje por defecto si ya es Cinta Negra
if (!empty($datos_base['id_disciplina']) && !empty($datos_base['orden'])) {
    // Buscamos la cinta de la misma disciplina que tenga el "orden" inmediatamente superior
    $sql_sig = "SELECT nombre_cinturon FROM cinturones WHERE id_disciplina = ? AND orden > ? ORDER BY orden ASC LIMIT 1";
    $stmt_sig = $conexion->prepare($sql_sig);
    $stmt_sig->bind_param("ii", $datos_base['id_disciplina'], $datos_base['orden']);
    $stmt_sig->execute();
    $res_sig = $stmt_sig->get_result();
    
    if ($res_sig->num_rows > 0) {
        $cinturon_siguiente = $res_sig->fetch_assoc()['nombre_cinturon'];
    }
    $stmt_sig->close();
}

// ==========================================
// 2. CALCULAR RÉCORD DE COMPETENCIAS
// ==========================================
$sql_comp = "SELECT resultado FROM competencias_historial WHERE id_alumno = ?";
$stmt_comp = $conexion->prepare($sql_comp);
$stmt_comp->bind_param("i", $id_alumno);
$stmt_comp->execute();
$res_comp = $stmt_comp->get_result();

$victorias = 0;
$derrotas = 0;

// Consideramos 1er, 2do y 3er lugar como "Victorias" (Podio)
while($comp = $res_comp->fetch_assoc()) {
    if(strpos(strtolower($comp['resultado']), 'lugar') !== false) {
        $victorias++;
    } else {
        $derrotas++;
    }
}
$total_peleas = $victorias + $derrotas;
$efectividad = ($total_peleas > 0) ? round(($victorias / $total_peleas) * 100) : 0;
$stmt_comp->close();

// ==========================================
// 3. TRAER HISTORIALES (Listas)
// ==========================================
$logros = $conexion->query("SELECT * FROM logros WHERE id_alumno = $id_alumno ORDER BY fecha_obtencion DESC LIMIT 4");
$examenes = $conexion->query("SELECT * FROM examenes_historial WHERE id_alumno = $id_alumno ORDER BY fecha_examen DESC LIMIT 4");
$competencias = $conexion->query("SELECT * FROM competencias_historial WHERE id_alumno = $id_alumno ORDER BY fecha_competencia DESC LIMIT 4");
?>

<main class="dashboard-container">
    
    <div class="dashboard-header">
        <h1>Mi Progreso</h1>
    </div>

    <section class="progress-section">
        <div class="progress-header">
            <div class="progress-title">
                <?php echo htmlspecialchars($datos_base['cinturon_actual'] ?? 'Sin asignar'); ?> 
                <i class='bx bx-right-arrow-alt'></i> 
                <span style="color: var(--text-muted);"><?php echo htmlspecialchars($cinturon_siguiente); ?></span>
            </div>
            <div class="progress-percentage"><?php echo $datos_base['porcentaje_progreso'] ?? 0; ?>%</div>
        </div>
        <div class="progress-track">
            <div class="progress-fill" style="width: <?php echo $datos_base['porcentaje_progreso'] ?? 0; ?>%;"></div>
        </div>
        <div class="progress-footer-text">Sigue entrenando para tu próxima promoción.</div>
    </section>

    <section class="stats-summary-grid">
        <div class="stat-box">
            <div class="stat-box-icon"><i class='bx bx-calendar'></i></div>
            <div class="stat-box-title">Antigüedad</div>
            <div class="stat-box-value"><?php echo $texto_antiguedad; ?></div>
            <div class="stat-box-sub" style="color: var(--text-muted); font-weight: normal;">Desde <?php echo $mes_ingreso; ?></div>
        </div>

        <div class="stat-box">
            <div class="stat-box-icon"><i class='bx bx-trophy'></i></div>
            <div class="stat-box-title">Récord Competencias</div>
            <div class="stat-box-value"><?php echo $victorias; ?> <span style="color: var(--text-muted);">-</span> <?php echo $derrotas; ?></div>
            <div class="stat-box-sub">Efectividad: <?php echo $efectividad; ?>%</div>
        </div>

        <div class="stat-box">
            <div class="stat-box-icon"><i class='bx bx-medal'></i></div>
            <div class="stat-box-title">Exámenes Presentados</div>
            <div class="stat-box-value"><?php echo $examenes->num_rows; ?></div>
            <div class="stat-box-sub" style="color: #2ecc71;">Sigue avanzando</div>
        </div>
    </section>

    <section class="three-col-grid">
        
        <div class="list-column">
            <h3><i class='bx bx-star'></i> Logros Recientes</h3>
            <?php if($logros->num_rows > 0): while($log = $logros->fetch_assoc()): ?>
                <div class="list-item">
                    <div class="list-item-icon"><i class='bx bx-trophy'></i></div>
                    <div class="list-item-content">
                        <div class="list-item-title"><?php echo htmlspecialchars($log['titulo']); ?></div>
                        <div class="list-item-desc"><?php echo htmlspecialchars($log['descripcion']); ?></div>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 15px;">Aún no hay logros registrados.</p>
            <?php endif; ?>
        </div>

        <div class="list-column">
            <h3><i class='bx bx-id-card'></i> Exámenes</h3>
            <?php if($examenes->num_rows > 0): while($ex = $examenes->fetch_assoc()): ?>
                <div class="list-item">
                    <div class="list-item-content">
                        <div class="list-item-title"><?php echo htmlspecialchars($ex['cinturon_obtenido']); ?></div>
                        <div class="list-item-desc"><?php echo date("M Y", strtotime($ex['fecha_examen'])); ?></div>
                    </div>
                    <div class="list-item-badge <?php echo ($ex['estatus'] == 'Aprobado') ? 'badge-aprobado' : 'badge-reprobado'; ?>">
                        <?php echo $ex['calificacion']; ?>/100
                    </div>
                </div>
            <?php endwhile; else: ?>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 15px;">No has presentado exámenes.</p>
            <?php endif; ?>
        </div>

        <div class="list-column">
            <h3><i class='bx bx-map-alt'></i> Competencias</h3>
            <?php if($competencias->num_rows > 0): while($comp = $competencias->fetch_assoc()): 
                // Asignar color según medalla
                $clase_medalla = "badge-aprobado";
                if(strpos(strtolower($comp['resultado']), '1er') !== false) $clase_medalla = "badge-oro";
                if(strpos(strtolower($comp['resultado']), '2do') !== false) $clase_medalla = "badge-plata";
                if(strpos(strtolower($comp['resultado']), '3er') !== false) $clase_medalla = "badge-bronce";
                if(strtolower($comp['resultado']) == 'participación') $clase_medalla = "badge-reprobado";
            ?>
                <div class="list-item">
                    <div class="list-item-content">
                        <div class="list-item-title"><?php echo htmlspecialchars($comp['nombre_torneo']); ?></div>
                        <div class="list-item-desc"><?php echo htmlspecialchars($comp['categoria']); ?></div>
                    </div>
                    <div class="list-item-badge <?php echo $clase_medalla; ?>">
                        <?php echo htmlspecialchars($comp['resultado']); ?>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 15px;">No hay competencias registradas.</p>
            <?php endif; ?>
        </div>

    </section>

</main>
</body>
</html>