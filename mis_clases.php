<?php 
require 'includes/seguridad_estudiante.php'; 
require 'includes/conexion.php';
$pagina_actual = 'mis_clases'; 
require 'includes/header_estudiante.php'; 

$id_alumno = $_SESSION['id_alumno'];

$sql_proxima_clase = "
    SELECT c.fecha_hora, d.nombre_disciplina, i.nombre AS nombre_instructor, i.rango 
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
$resultado_prox = $stmt_prox->get_result();
$proxima_clase = $resultado_prox->fetch_assoc();
$stmt_prox->close();

// ==========================================
// CONSULTA 2: OBTENER EL HISTORIAL DE ASISTENCIAS
// Buscamos las clases que ya pasaron y si asistió o faltó
// ==========================================
$sql_historial = "
    SELECT c.fecha_hora, d.nombre_disciplina, i.nombre AS nombre_instructor, a.estatus
    FROM asistencias a
    JOIN clases c ON a.id_clase = c.id_clase
    JOIN disciplinas d ON c.id_disciplina = d.id_disciplina
    JOIN instructores i ON c.id_instructor = i.id_instructor
    WHERE a.id_alumno = ?
    ORDER BY c.fecha_hora DESC LIMIT 10
";

$stmt_hist = $conexion->prepare($sql_historial);
$stmt_hist->bind_param("i", $id_alumno);
$stmt_hist->execute();
$resultado_historial = $stmt_hist->get_result();
?>

<main class="dashboard-container">
    <div class="dashboard-header">
        <h1>Mis Clases</h1>
    </div>

    <div class="dashboard-cards-grid">
        
        <?php if ($proxima_clase): ?>
            <div class="dash-card">
                <div class="dash-card-info">
                    <h3>Próxima Clase</h3>
                    <p><?php echo fechaEspanol($proxima_clase['fecha_hora'], 'corta'); ?></p>
                    <div class="sub-text"><?php echo htmlspecialchars($proxima_clase['nombre_disciplina']); ?> - Dojo Principal</div>
                </div>
                <div class="dash-card-icon">
                    <i class='bx bx-time-five'></i>
                </div>
            </div>

            <div class="dash-card">
                <div class="dash-card-info">
                    <h3>Instructor Asignado</h3>
                    <p><?php echo htmlspecialchars($proxima_clase['nombre_instructor']); ?></p>
                    <div class="sub-text"><?php echo htmlspecialchars($proxima_clase['rango']); ?></div>
                </div>
                <div class="dash-card-icon">
                    <i class='bx bx-user-circle'></i>
                </div>
            </div>
        <?php else: ?>
            <div class="dash-card" style="grid-column: span 2;">
                <div class="dash-card-info">
                    <h3>Atención</h3>
                    <p>No tienes clases programadas próximamente.</p>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <h2 style="margin-bottom: 20px; font-size: 1.2rem; color: var(--text-light);">Historial de Asistencia Reciente</h2>
    
    <div class="dash-table-wrapper">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Clase</th>
                    <th>Instructor</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado_historial->num_rows > 0): ?>
                    <?php while($fila = $resultado_historial->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo fechaEspanol($fila['fecha_hora'], 'larga'); ?></td>
                            <td><?php echo htmlspecialchars($fila['nombre_disciplina']); ?></td>
                            <td><?php echo htmlspecialchars($fila['nombre_instructor']); ?></td>
                            <td>
                                <?php if($fila['estatus'] == 'Presente'): ?>
                                    <span class="badge badge-green">Asistió</span>
                                <?php else: ?>
                                    <span class="badge badge-red">Falta</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted);">Aún no hay registros de asistencia.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<?php $stmt_hist->close(); ?>
</body>
</html>