<?php 
require '../includes/seguridad_admin.php'; 
require '../includes/conexion.php'; 

$pagina_actual = 'asistencias'; 

// ==========================================
// 1. PROCESAMIENTO DE FORMULARIOS
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- A. PROGRAMAR NUEVA CLASE ---
    if (isset($_POST['accion']) && $_POST['accion'] == 'crear_clase') {
        $id_disciplina = intval($_POST['id_disciplina']);
        $id_instructor = intval($_POST['id_instructor']);
        // Juntamos la fecha y la hora para el campo DATETIME de MySQL
        $fecha_hora = $_POST['fecha'] . ' ' . $_POST['hora'] . ':00';

        $stmt_clase = $conexion->prepare("INSERT INTO clases (id_disciplina, id_instructor, fecha_hora) VALUES (?, ?, ?)");
        $stmt_clase->bind_param("iis", $id_disciplina, $id_instructor, $fecha_hora);
        $stmt_clase->execute();
        $stmt_clase->close();

        header("Location: asistencias.php?exito=clase_creada");
        exit();
    }

    // --- B. GUARDAR PASE DE LISTA ---
    if (isset($_POST['accion']) && $_POST['accion'] == 'guardar_asistencia') {
        $id_clase = intval($_POST['id_clase']);
        $asistencias = $_POST['estatus']; // Esto es un arreglo: [id_alumno => 'Presente', id_alumno2 => 'Falta']

        foreach ($asistencias as $id_alumno => $estatus_valor) {
            $id_alum_int = intval($id_alumno);
            
            // Verificamos si ya le habíamos pasado lista antes a este alumno en esta clase
            $check = $conexion->query("SELECT id_asistencia FROM asistencias WHERE id_clase = $id_clase AND id_alumno = $id_alum_int");
            
            if ($check->num_rows > 0) {
                // Actualizamos si ya existía (por si el admin se equivocó y corrige)
                $conexion->query("UPDATE asistencias SET estatus = '$estatus_valor' WHERE id_clase = $id_clase AND id_alumno = $id_alum_int");
            } else {
                // Insertamos nuevo registro
                $conexion->query("INSERT INTO asistencias (id_alumno, id_clase, estatus) VALUES ($id_alum_int, $id_clase, '$estatus_valor')");
            }
        }
        
        header("Location: asistencias.php?id_clase=" . $id_clase . "&exito=lista_guardada");
        exit();
    }
}

// ==========================================
// 2. OBTENER DATOS PARA LA VISTA
// ==========================================
require '../includes/header_admin.php'; 

// Catálogos para el Modal de Nueva Clase
$disciplinas = $conexion->query("SELECT * FROM disciplinas");
$instructores = $conexion->query("SELECT * FROM instructores");

// Variable para saber si estamos viendo la lista de clases o pasando lista
$viendo_clase = isset($_GET['id_clase']) ? intval($_GET['id_clase']) : false;
?>

<main class="dashboard-container">

    <?php if (!$viendo_clase): ?>
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Control de Asistencias</h1>
                <p style="color: var(--text-muted); margin-top: 5px;">Programa clases y registra la asistencia de los alumnos.</p>
            </div>
            <button id="btn-nueva-clase" class="btn btn-red"><i class='bx bx-calendar-plus'></i> Programar Clase</button>
        </div>

        <div class="dash-table-wrapper">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Disciplina</th>
                        <th>Instructor</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Obtenemos las clases ordenadas por fecha (las más recientes/futuras primero)
                    $sql_clases = "SELECT c.id_clase, c.fecha_hora, d.nombre_disciplina, i.nombre AS nombre_instructor 
                                   FROM clases c 
                                   JOIN disciplinas d ON c.id_disciplina = d.id_disciplina 
                                   JOIN instructores i ON c.id_instructor = i.id_instructor 
                                   ORDER BY c.fecha_hora DESC LIMIT 20";
                    $clases = $conexion->query($sql_clases);

                    if ($clases->num_rows > 0): 
                        while($clase = $clases->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?php echo fechaEspanol($clase['fecha_hora'], 'larga'); ?></strong><br>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;"><i class='bx bx-time'></i> <?php echo date("g:i A", strtotime($clase['fecha_hora'])); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($clase['nombre_disciplina']); ?></td>
                                <td><?php echo htmlspecialchars($clase['nombre_instructor']); ?></td>
                                <td>
                                    <a href="asistencias.php?id_clase=<?php echo $clase['id_clase']; ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.8rem;">
                                        <i class='bx bx-list-check'></i> Pasar Lista
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; 
                    else: ?>
                        <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No hay clases programadas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <?php
        // Obtenemos los detalles de la clase seleccionada
        $stmt = $conexion->prepare("SELECT c.id_clase, c.id_disciplina, c.fecha_hora, d.nombre_disciplina, i.nombre AS nombre_instructor FROM clases c JOIN disciplinas d ON c.id_disciplina = d.id_disciplina JOIN instructores i ON c.id_instructor = i.id_instructor WHERE c.id_clase = ?");
        $stmt->bind_param("i", $viendo_clase);
        $stmt->execute();
        $clase_actual = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Obtenemos TODOS los alumnos inscritos a esta disciplina, y vemos si ya tienen asistencia registrada
        $sql_alumnos = "
            SELECT a.id_alumno, a.nombre, a.apellidos, c.nombre_cinturon, asis.estatus
            FROM alumnos a
            JOIN alumnos_disciplinas ad ON a.id_alumno = ad.id_alumno
            LEFT JOIN cinturones c ON ad.id_cinturon = c.id_cinturon
            LEFT JOIN asistencias asis ON asis.id_alumno = a.id_alumno AND asis.id_clase = ?
            WHERE ad.id_disciplina = ? AND a.rol = 'estudiante'
            ORDER BY a.nombre ASC
        ";
        $stmt_alum = $conexion->prepare($sql_alumnos);
        $stmt_alum->bind_param("ii", $viendo_clase, $clase_actual['id_disciplina']);
        $stmt_alum->execute();
        $lista_alumnos = $stmt_alum->get_result();
        $stmt_alum->close();
        ?>

        <div class="dashboard-header" style="display: flex; gap: 15px; align-items: center;">
            <a href="asistencias.php" class="btn btn-outline" style="padding: 8px;"><i class='bx bx-arrow-back'></i></a>
            <div>
                <h1>Pase de Lista: <?php echo htmlspecialchars($clase_actual['nombre_disciplina']); ?></h1>
                <p style="color: var(--text-muted); margin-top: 5px;"><?php echo fechaEspanol($clase_actual['fecha_hora'], 'corta'); ?> | Instructor: <?php echo htmlspecialchars($clase_actual['nombre_instructor']); ?></p>
            </div>
        </div>

        <form action="asistencias.php" method="POST">
            <input type="hidden" name="accion" value="guardar_asistencia">
            <input type="hidden" name="id_clase" value="<?php echo $viendo_clase; ?>">

            <div class="dash-table-wrapper" style="margin-bottom: 20px;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Nivel</th>
                            <th style="text-align: center;">Asistencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($lista_alumnos->num_rows > 0): 
                            while($alum = $lista_alumnos->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($alum['nombre'] . ' ' . $alum['apellidos']); ?></strong>
                                    </td>
                                    <td><span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo htmlspecialchars($alum['nombre_cinturon'] ?? 'Blanca'); ?></span></td>
                                    
                                    <td style="text-align: center; display: flex; justify-content: center; gap: 20px;">
                                        <label style="cursor: pointer; color: #2ecc71; font-weight: 700; display: flex; align-items: center; gap: 5px;">
                                            <input type="radio" name="estatus[<?php echo $alum['id_alumno']; ?>]" value="Presente" <?php echo ($alum['estatus'] == 'Presente') ? 'checked' : ''; ?> required style="transform: scale(1.2);"> Presente
                                        </label>
                                        
                                        <label style="cursor: pointer; color: var(--red-cobra); font-weight: 700; display: flex; align-items: center; gap: 5px;">
                                            <input type="radio" name="estatus[<?php echo $alum['id_alumno']; ?>]" value="Falta" <?php echo ($alum['estatus'] == 'Falta') ? 'checked' : ''; ?> required style="transform: scale(1.2);"> Falta
                                        </label>
                                    </td>
                                </tr>
                            <?php endwhile; 
                        else: ?>
                            <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">No hay alumnos inscritos en esta disciplina.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($lista_alumnos->num_rows > 0): ?>
                <div style="text-align: right;">
                    <button type="submit" class="btn btn-red btn-large" style="margin-top: 0;"><i class='bx bx-save'></i> Guardar Lista</button>
                </div>
            <?php endif; ?>
        </form>

    <?php endif; ?>

</main>

<div id="modalNuevaClase" class="modal">
    <div class="modal-content">
        <i class='bx bx-x close-btn'></i>
        <h2>Programar Clase</h2>
        <form action="asistencias.php" method="POST">
            <input type="hidden" name="accion" value="crear_clase">
            
            <div class="input-group">
                <label>Disciplina</label>
                <select name="id_disciplina" class="input-form" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                    <option value="">Selecciona la disciplina...</option>
                    <?php while($d = $disciplinas->fetch_assoc()): ?>
                        <option value="<?php echo $d['id_disciplina']; ?>"><?php echo htmlspecialchars($d['nombre_disciplina']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="input-group">
                <label>Instructor Asignado</label>
                <select name="id_instructor" class="input-form" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                    <option value="">Selecciona un instructor...</option>
                    <?php while($i = $instructores->fetch_assoc()): ?>
                        <option value="<?php echo $i['id_instructor']; ?>"><?php echo htmlspecialchars($i['nombre']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label>Fecha de la Clase</label>
                    <input type="date" name="fecha" required style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;">
                </div>
                <div class="input-group">
                    <label>Hora (Formato 24h)</label>
                    <input type="time" name="hora" required style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;">
                </div>
            </div>

            <button type="submit" class="btn btn-red btn-full" style="margin-top: 15px;">Crear Clase</button>
        </form>
    </div>
</div>

<?php if (isset($_GET['exito'])): 
    $mensaje_toast = ($_GET['exito'] == 'clase_creada') ? 'Clase programada con éxito.' : 'Lista de asistencia guardada.';
?>
    <div id="toast-exito" class="toast-notification">
        <i class='bx bx-check-circle'></i> 
        <span><?php echo $mensaje_toast; ?></span>
    </div>

    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-exito');
            if (toast) {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }
        }, 3000);
        
        const urlLimpia = window.location.href.split('&exito=')[0].split('?exito=')[0];
        window.history.replaceState({}, document.title, urlLimpia);
    </script>
<?php endif; ?>

<script>
// Lógica para el Modal
const modal = document.getElementById("modalNuevaClase");
const btn = document.getElementById("btn-nueva-clase");
const span = document.getElementsByClassName("close-btn")[0];

if (btn) {
    btn.onclick = () => modal.classList.add("show");
}
if (span) {
    span.onclick = () => modal.classList.remove("show");
}
window.onclick = (event) => { if (event.target == modal) modal.classList.remove("show"); }
</script>

</body>
</html>