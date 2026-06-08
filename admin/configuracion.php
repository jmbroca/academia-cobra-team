<?php 
require '../includes/seguridad_admin.php'; 
require '../includes/conexion.php'; 

$pagina_actual = 'config'; 

// ==========================================
// 1. PROCESAMIENTO DE FORMULARIOS (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- A. AGREGAR DISCIPLINA ---
    if (isset($_POST['accion']) && $_POST['accion'] == 'agregar_disciplina') {
        $nombre_disciplina = trim($_POST['nombre_disciplina']);
        
        $stmt = $conexion->prepare("INSERT INTO disciplinas (nombre_disciplina) VALUES (?)");
        $stmt->bind_param("s", $nombre_disciplina);
        $stmt->execute();
        $stmt->close();
        
        header("Location: configuracion.php?exito=disc_agregada");
        exit();
    }

    // --- B. ELIMINAR DISCIPLINA ---
    if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar_disciplina') {
        $id_disciplina = intval($_POST['id_disciplina']);
        
        $stmt = $conexion->prepare("DELETE FROM disciplinas WHERE id_disciplina = ?");
        $stmt->bind_param("i", $id_disciplina);
        $stmt->execute();
        $stmt->close();
        
        header("Location: configuracion.php?exito=disc_eliminada");
        exit();
    }

    // --- C. AGREGAR INSTRUCTOR ---
    if (isset($_POST['accion']) && $_POST['accion'] == 'agregar_instructor') {
        $nombre = trim($_POST['nombre']);
        $rango = trim($_POST['rango']);
        
        $stmt = $conexion->prepare("INSERT INTO instructores (nombre, rango) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $rango);
        $stmt->execute();
        $stmt->close();
        
        header("Location: configuracion.php?exito=inst_agregado");
        exit();
    }

    // --- D. ELIMINAR INSTRUCTOR ---
    if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar_instructor') {
        $id_instructor = intval($_POST['id_instructor']);
        
        $stmt = $conexion->prepare("DELETE FROM instructores WHERE id_instructor = ?");
        $stmt->bind_param("i", $id_instructor);
        $stmt->execute();
        $stmt->close();
        
        header("Location: configuracion.php?exito=inst_eliminado");
        exit();
    }
}

// ==========================================
// 2. OBTENER DATOS PARA LA VISTA
// ==========================================
require '../includes/header_admin.php'; 

$disciplinas = $conexion->query("SELECT * FROM disciplinas ORDER BY nombre_disciplina ASC");
$instructores = $conexion->query("SELECT * FROM instructores ORDER BY nombre ASC");
?>

<main class="dashboard-container">

    <div class="dashboard-header">
        <h1>Configuración del Sistema</h1>
        <p style="color: var(--text-muted); margin-top: 5px;">Administra el catálogo de artes marciales y el equipo de instructores.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; align-items: start;">
        
        <div class="list-column">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 15px;">
                <h3 style="margin: 0; border: none; padding: 0;"><i class='bx bx-medal'></i> Disciplinas</h3>
                <button id="btn-nueva-disc" class="btn btn-outline" style="padding: 5px 10px; font-size: 0.8rem;"><i class='bx bx-plus'></i> Añadir</button>
            </div>
            
            <div class="dash-table-wrapper" style="box-shadow: none;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Arte Marcial</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($disciplinas->num_rows > 0): while($d = $disciplinas->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($d['nombre_disciplina']); ?></strong></td>
                                <td style="text-align: right;">
                                    <form action="configuracion.php" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Estás seguro de eliminar esta disciplina? Se borrarán las clases vinculadas a ella.');">
                                        <input type="hidden" name="accion" value="eliminar_disciplina">
                                        <input type="hidden" name="id_disciplina" value="<?php echo $d['id_disciplina']; ?>">
                                        <button type="submit" class="btn btn-outline" style="padding: 4px 8px; font-size: 0.8rem; color: var(--red-cobra); border-color: transparent;"><i class='bx bx-trash'></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="2" style="text-align: center; color: var(--text-muted);">No hay disciplinas registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="list-column">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 15px;">
                <h3 style="margin: 0; border: none; padding: 0;"><i class='bx bx-group'></i> Instructores</h3>
                <button id="btn-nuevo-inst" class="btn btn-outline" style="padding: 5px 10px; font-size: 0.8rem;"><i class='bx bx-plus'></i> Añadir</button>
            </div>
            
            <div class="dash-table-wrapper" style="box-shadow: none;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Rango</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($instructores->num_rows > 0): while($i = $instructores->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($i['nombre']); ?></strong></td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);"><?php echo htmlspecialchars($i['rango']); ?></td>
                                <td style="text-align: right;">
                                    <form action="configuracion.php" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Estás seguro de eliminar a este instructor? Las clases que impartía podrían quedarse sin profesor asignado.');">
                                        <input type="hidden" name="accion" value="eliminar_instructor">
                                        <input type="hidden" name="id_instructor" value="<?php echo $i['id_instructor']; ?>">
                                        <button type="submit" class="btn btn-outline" style="padding: 4px 8px; font-size: 0.8rem; color: var(--red-cobra); border-color: transparent;"><i class='bx bx-trash'></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">No hay instructores registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<div id="modalDisciplina" class="modal">
    <div class="modal-content">
        <i class='bx bx-x close-btn' id="close-disc"></i>
        <h2>Añadir Disciplina</h2>
        <form action="configuracion.php" method="POST">
            <input type="hidden" name="accion" value="agregar_disciplina">
            
            <div class="input-group">
                <label>Nombre del Arte Marcial</label>
                <input type="text" name="nombre_disciplina" placeholder="Ej. Jiu-Jitsu Brasileño" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
            </div>

            <button type="submit" class="btn btn-red btn-full" style="margin-top: 15px;">Guardar Disciplina</button>
        </form>
    </div>
</div>

<div id="modalInstructor" class="modal">
    <div class="modal-content">
        <i class='bx bx-x close-btn' id="close-inst"></i>
        <h2>Añadir Instructor</h2>
        <form action="configuracion.php" method="POST">
            <input type="hidden" name="accion" value="agregar_instructor">
            
            <div class="input-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" placeholder="Ej. Sensei Alejandro" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
            </div>

            <div class="input-group">
                <label>Grado / Rango</label>
                <input type="text" name="rango" placeholder="Ej. Cinturón Negro 3er Dan" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
            </div>

            <button type="submit" class="btn btn-red btn-full" style="margin-top: 15px;">Guardar Instructor</button>
        </form>
    </div>
</div>

<?php if (isset($_GET['exito'])): 
    $mensaje = "";
    if ($_GET['exito'] == 'disc_agregada') $mensaje = "Disciplina añadida correctamente.";
    if ($_GET['exito'] == 'disc_eliminada') $mensaje = "Disciplina eliminada.";
    if ($_GET['exito'] == 'inst_agregado') $mensaje = "Instructor añadido correctamente.";
    if ($_GET['exito'] == 'inst_eliminado') $mensaje = "Instructor eliminado.";
?>
    <div id="toast-exito" class="toast-notification">
        <i class='bx bx-check-circle'></i> 
        <span><?php echo $mensaje; ?></span>
    </div>

    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-exito');
            if (toast) {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }
        }, 3000);
        const urlLimpia = window.location.href.split('?exito=')[0];
        window.history.replaceState({}, document.title, urlLimpia);
    </script>
<?php endif; ?>

<script>
// Modal Disciplinas
const modalDisc = document.getElementById("modalDisciplina");
const btnDisc = document.getElementById("btn-nueva-disc");
const closeDisc = document.getElementById("close-disc");

btnDisc.onclick = () => modalDisc.classList.add("show");
closeDisc.onclick = () => modalDisc.classList.remove("show");

// Modal Instructores
const modalInst = document.getElementById("modalInstructor");
const btnInst = document.getElementById("btn-nuevo-inst");
const closeInst = document.getElementById("close-inst");

btnInst.onclick = () => modalInst.classList.add("show");
closeInst.onclick = () => modalInst.classList.remove("show");

// Cerrar al hacer clic fuera
window.onclick = (event) => { 
    if (event.target == modalDisc) modalDisc.classList.remove("show");
    if (event.target == modalInst) modalInst.classList.remove("show"); 
}
</script>

</body>
</html>