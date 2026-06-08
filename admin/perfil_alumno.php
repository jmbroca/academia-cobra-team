<?php 
require '../includes/seguridad_admin.php'; 
require '../includes/conexion.php'; 

// ==========================================
// 1. PROCESAMIENTO DE FORMULARIOS (AUTOMATIZACIONES)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'];
    $id_alumno_post = intval($_POST['id_alumno']);
    $fecha_actual = date('Y-m-d');

    // --- A. ACTUALIZAR DATOS ACADÉMICOS (Disciplina, Cinta, Progreso) ---
    if ($accion == 'actualizar_academico') {
        $id_disciplina = intval($_POST['id_disciplina']);
        $id_cinturon = intval($_POST['id_cinturon']);
        $progreso = intval($_POST['progreso']);

        // Verificamos si ya tiene un registro en alumnos_disciplinas
        $check = $conexion->query("SELECT id_alumno FROM alumnos_disciplinas WHERE id_alumno = $id_alumno_post");
        if ($check->num_rows > 0) {
            $stmt = $conexion->prepare("UPDATE alumnos_disciplinas SET id_disciplina = ?, id_cinturon = ?, porcentaje_progreso = ? WHERE id_alumno = ?");
            $stmt->bind_param("iiii", $id_disciplina, $id_cinturon, $progreso, $id_alumno_post);
        } else {
            $stmt = $conexion->prepare("INSERT INTO alumnos_disciplinas (id_alumno, id_disciplina, id_cinturon, porcentaje_progreso) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiii", $id_alumno_post, $id_disciplina, $id_cinturon, $progreso);
        }
        $stmt->execute();
        $stmt->close();
    }

    // --- B. REGISTRAR EXAMEN (Y generar logro automático) ---
    if ($accion == 'registrar_examen') {
        $cinturon_obtenido = $_POST['cinturon_obtenido'];
        $calificacion = intval($_POST['calificacion']);
        $estatus = $_POST['estatus']; // 'Aprobado' o 'Reprobado'
        $fecha_examen = $_POST['fecha_examen'];

        // 1. Registrar en el historial de exámenes
        $stmt_ex = $conexion->prepare("INSERT INTO examenes_historial (id_alumno, cinturon_obtenido, calificacion, estatus, fecha_examen) VALUES (?, ?, ?, ?, ?)");
        $stmt_ex->bind_param("isiss", $id_alumno_post, $cinturon_obtenido, $calificacion, $estatus, $fecha_examen);
        $stmt_ex->execute();
        $stmt_ex->close();

        // 2. AUTOMATIZACIÓN: Si aprobó, darle el logro
        if ($estatus === 'Aprobado') {
            $titulo_logro = "Aprobación de Examen";
            $desc_logro = "Ascenso a " . $cinturon_obtenido;
            $stmt_logro = $conexion->prepare("INSERT INTO logros (id_alumno, titulo, descripcion, fecha_obtencion, tipo_icono) VALUES (?, ?, ?, ?, 'cinturon')");
            $stmt_logro->bind_param("isss", $id_alumno_post, $titulo_logro, $desc_logro, $fecha_actual);
            $stmt_logro->execute();
            $stmt_logro->close();
        }
    }

    // --- C. REGISTRAR COMPETENCIA (Y generar logro automático) ---
    if ($accion == 'registrar_competencia') {
        $torneo = $_POST['nombre_torneo'];
        $categoria = $_POST['categoria'];
        $resultado = $_POST['resultado'];
        $fecha_comp = $_POST['fecha_competencia'];

        // 1. Registrar en el historial de competencias
        $stmt_comp = $conexion->prepare("INSERT INTO competencias_historial (id_alumno, nombre_torneo, categoria, resultado, fecha_competencia) VALUES (?, ?, ?, ?, ?)");
        $stmt_comp->bind_param("issss", $id_alumno_post, $torneo, $categoria, $resultado, $fecha_comp);
        $stmt_comp->execute();
        $stmt_comp->close();

        // 2. AUTOMATIZACIÓN: Si quedó campeón, darle el logro
        if (strpos(strtolower($resultado), '1er') !== false || strtolower($resultado) == 'campeón') {
            $titulo_logro = "Campeón de Torneo";
            $stmt_logro = $conexion->prepare("INSERT INTO logros (id_alumno, titulo, descripcion, fecha_obtencion, tipo_icono) VALUES (?, ?, ?, ?, 'trofeo')");
            $stmt_logro->bind_param("isss", $id_alumno_post, $titulo_logro, $torneo, $fecha_actual);
            $stmt_logro->execute();
            $stmt_logro->close();
        }
    }

    // Recargar la página para evitar reenvío del formulario
    header("Location: perfil_alumno.php?id=" . $id_alumno_post . "&exito=1");
    exit();
}

// ==========================================
// 2. OBTENER LOS DATOS DEL ALUMNO
// ==========================================
if (!isset($_GET['id'])) {
    header("Location: alumnos.php");
    exit();
}
$id_alumno = intval($_GET['id']);
$pagina_actual = 'alumnos'; 
require '../includes/header_admin.php'; 

// Datos principales
$stmt = $conexion->prepare("
    SELECT a.*, ad.id_disciplina, ad.id_cinturon, ad.porcentaje_progreso 
    FROM alumnos a 
    LEFT JOIN alumnos_disciplinas ad ON a.id_alumno = ad.id_alumno 
    WHERE a.id_alumno = ?
");
$stmt->bind_param("i", $id_alumno);
$stmt->execute();
$alumno = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$alumno) { echo "Alumno no encontrado."; exit(); }

// Catálogos para los selects
$disciplinas = $conexion->query("SELECT * FROM disciplinas");
$cinturones = $conexion->query("SELECT * FROM cinturones ORDER BY id_disciplina, orden ASC");
?>

<main class="dashboard-container">
    
    <!-- HEADER DEL PERFIL -->
    <div class="dashboard-header">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="alumnos.php" class="btn btn-outline" style="padding: 8px;"><i class='bx bx-arrow-back'></i></a>
            <div>
                <h1><?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos']); ?></h1>
                <p style="color: var(--text-muted);">Expediente ID: #<?php echo str_pad($alumno['id_alumno'], 4, '0', STR_PAD_LEFT); ?> | <?php echo htmlspecialchars($alumno['email']); ?></p>
            </div>
        </div>
    </div>

    <!-- CUADRÍCULA DE FORMULARIOS -->
    <div class="three-col-grid" style="grid-template-columns: repeat(2, 1fr); align-items: start;">
        
        <!-- 1. ACADÉMICO -->
        <div class="list-column">
            <h3><i class='bx bx-book-reader'></i> Perfil Académico</h3>
            <form action="perfil_alumno.php?id=<?php echo $id_alumno; ?>" method="POST">
                <input type="hidden" name="accion" value="actualizar_academico">
                <input type="hidden" name="id_alumno" value="<?php echo $id_alumno; ?>">
                
                <div class="input-group">
                    <label>Disciplina</label>
                    <select name="id_disciplina" class="input-form" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                        <option value="">Selecciona una disciplina...</option>
                        <?php while($d = $disciplinas->fetch_assoc()): ?>
                            <option value="<?php echo $d['id_disciplina']; ?>" <?php echo ($alumno['id_disciplina'] == $d['id_disciplina']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($d['nombre_disciplina']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="input-group">
                    <label>Nivel / Cinturón Actual</label>
                    <select name="id_cinturon" class="input-form" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                        <option value="">Selecciona un cinturón...</option>
                        <?php 
                        $cinturones->data_seek(0);
                        while($c = $cinturones->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $c['id_cinturon']; ?>" <?php echo ($alumno['id_cinturon'] == $c['id_cinturon']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['nombre_cinturon']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="input-group">
                    <label>Progreso Actual (%)</label>
                    <input type="number" name="progreso" value="<?php echo $alumno['porcentaje_progreso'] ?? 0; ?>" min="0" max="100" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                </div>

                <button type="submit" class="btn btn-outline" style="width: 100%;">Actualizar Progreso</button>
            </form>
        </div>

        <!-- 2. REGISTRAR EXAMEN -->
        <div class="list-column">
            <h3><i class='bx bx-id-card'></i> Registrar Examen</h3>
            <form action="perfil_alumno.php?id=<?php echo $id_alumno; ?>" method="POST">
                <input type="hidden" name="accion" value="registrar_examen">
                <input type="hidden" name="id_alumno" value="<?php echo $id_alumno; ?>">
                
                <div class="form-row">
                    <div class="input-group">
                        <label>Cinturón a evaluar</label>
                        <select name="id_cinturon" class="input-form" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                            <option value="">Selecciona un cinturón...</option>
                            <?php 
                            $cinturones->data_seek(0);
                            while($c = $cinturones->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $c['id_cinturon']; ?>" <?php echo ($alumno['id_cinturon'] == $c['id_cinturon']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['nombre_cinturon']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Fecha</label>
                        <input type="date" name="fecha_examen" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label>Calificación (0-100)</label>
                        <input type="number" name="calificacion" min="0" max="100" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                    </div>
                    <div class="input-group">
                        <label>Resultado</label>
                        <select name="estatus" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                            <option value="Aprobado">Aprobado</option>
                            <option value="Reprobado">Reprobado</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-outline" style="width: 100%;">Guardar Examen</button>
            </form>
        </div>

        <!-- 3. REGISTRAR COMPETENCIA -->
        <div class="list-column" style="grid-column: span 2;">
            <h3><i class='bx bx-map-alt'></i> Registrar Torneo / Competencia</h3>
            <form action="perfil_alumno.php?id=<?php echo $id_alumno; ?>" method="POST" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; align-items: end;">
                <input type="hidden" name="accion" value="registrar_competencia">
                <input type="hidden" name="id_alumno" value="<?php echo $id_alumno; ?>">
                
                <div class="input-group" style="margin-bottom: 0;">
                    <label>Nombre del Torneo</label>
                    <input type="text" name="nombre_torneo" placeholder="Ej. Copa Estatal" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                </div>
                
                <div class="input-group" style="margin-bottom: 0;">
                    <label>Categoría</label>
                    <input type="text" name="categoria" placeholder="Ej. Kumite Varonil" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                </div>

                <div class="input-group" style="margin-bottom: 0;">
                    <label>Resultado Obtenido</label>
                    <select name="resultado" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                        <option value="1er Lugar">1er Lugar (Oro)</option>
                        <option value="2do Lugar">2do Lugar (Plata)</option>
                        <option value="3er Lugar">3er Lugar (Bronce)</option>
                        <option value="Participación">Participación</option>
                    </select>
                </div>

                <div class="input-group" style="margin-bottom: 0;">
                    <label>Fecha</label>
                    <input type="date" name="fecha_competencia" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                </div>

                <div style="grid-column: span 4;">
                    <button type="submit" class="btn btn-red" style="width: 100%;">Agregar al Historial Competitivo</button>
                </div>
            </form>
        </div>

    </div>

</main>
<?php if (isset($_GET['exito']) && $_GET['exito'] == 1): ?>
    <div id="toast-exito" class="toast-notification">
        <i class='bx bx-check-circle'></i> 
        <span>Cambios guardados exitosamente.</span>
    </div>

    <script>
        // Lógica para desaparecer la alerta después de 3 segundos
        setTimeout(() => {
            const toast = document.getElementById('toast-exito');
            if (toast) {
                // Primero la hacemos transparente suavemente
                toast.style.opacity = '0';
                
                // Medio segundo después, la borramos del código para que no estorbe
                setTimeout(() => toast.remove(), 500);
            }
        }, 2000); // 3000 milisegundos = 3 segundos
        
        // Limpiar la URL para que al recargar la página no vuelva a salir
        const urlSinExito = window.location.href.split('&exito=')[0];
        window.history.replaceState({}, document.title, urlSinExito);
    </script>
<?php endif; ?>
</body>
</html>