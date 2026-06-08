<?php 
require '../includes/seguridad_admin.php'; 
require '../includes/conexion.php'; 

$pagina_actual = 'pagos'; 

// ==========================================
// 1. PROCESAMIENTO DE FORMULARIOS
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- A. REGISTRAR NUEVO PAGO ---
    if (isset($_POST['accion']) && $_POST['accion'] == 'registrar_pago') {
        $id_alumno = intval($_POST['id_alumno']);
        $concepto = trim($_POST['concepto']);
        $monto = floatval($_POST['monto']);
        $fecha_pago = $_POST['fecha_pago'];
        $estatus = $_POST['estatus'];
        $metodo_pago = $_POST['metodo_pago'];

        $stmt = $conexion->prepare("INSERT INTO pagos (id_alumno, concepto, monto, fecha_pago, estatus, metodo_pago) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdsss", $id_alumno, $concepto, $monto, $fecha_pago, $estatus, $metodo_pago);
        $stmt->execute();
        $stmt->close();

        header("Location: pagos.php?exito=pago_registrado");
        exit();
    }

    // --- B. ACTUALIZAR ESTATUS DE PAGO ---
    if (isset($_POST['accion']) && $_POST['accion'] == 'actualizar_pago') {
        $id_pago = intval($_POST['id_pago']);
        $estatus = $_POST['estatus'];
        $metodo_pago = $_POST['metodo_pago'];

        $stmt = $conexion->prepare("UPDATE pagos SET estatus = ?, metodo_pago = ? WHERE id_pago = ?");
        $stmt->bind_param("ssi", $estatus, $metodo_pago, $id_pago);
        $stmt->execute();
        $stmt->close();

        header("Location: pagos.php?exito=pago_actualizado");
        exit();
    }
}

// ==========================================
// 2. OBTENER DATOS PARA LA VISTA
// ==========================================
require '../includes/header_admin.php'; 

// Lista de alumnos para el select de "Nuevo Pago"
$alumnos = $conexion->query("SELECT id_alumno, nombre, apellidos FROM alumnos WHERE rol = 'estudiante' ORDER BY nombre ASC");

// Historial completo de pagos uniendo la tabla de alumnos para ver sus nombres
$sql_pagos = "
    SELECT p.*, a.nombre, a.apellidos 
    FROM pagos p
    JOIN alumnos a ON p.id_alumno = a.id_alumno
    ORDER BY p.fecha_pago DESC
";
$resultado_pagos = $conexion->query($sql_pagos);
?>

<main class="dashboard-container">

    <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Control Financiero</h1>
            <p style="color: var(--text-muted); margin-top: 5px;">Gestiona los ingresos, adeudos y métodos de pago.</p>
        </div>
        <button id="btn-nuevo-pago" class="btn btn-red"><i class='bx bx-money'></i> Registrar Pago</button>
    </div>

    <div style="margin-bottom: 20px;">
        <input type="text" id="buscador-pagos" placeholder="Buscar por alumno, concepto o estatus..." style="width: 100%; max-width: 400px; padding: 10px 15px; border-radius: 6px; background-color: #1a1a1a; border: 1px solid #333; color: var(--text-light); outline: none; font-family: inherit;">
    </div>

    <div class="dash-table-wrapper">
        <table class="dash-table" id="tabla-pagos">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Alumno</th>
                    <th>Concepto</th>
                    <th>Monto</th>
                    <th>Estatus</th>
                    <th>Método</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if($resultado_pagos->num_rows > 0): ?>
                    <?php while($pago = $resultado_pagos->fetch_assoc()): 
                        // Colores para los badges
                        $clase_badge = 'badge-green'; 
                        if ($pago['estatus'] == 'Atrasado') $clase_badge = 'badge-red';
                        if ($pago['estatus'] == 'Pendiente') $clase_badge = 'badge-warning';
                    ?>
                        <tr>
                            <td><?php echo date("d M, Y", strtotime($pago['fecha_pago'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($pago['nombre'] . ' ' . $pago['apellidos']); ?></strong></td>
                            <td><?php echo htmlspecialchars($pago['concepto']); ?></td>
                            <td style="font-weight: 700;">$<?php echo number_format($pago['monto'], 2); ?></td>
                            <td><span class="badge <?php echo $clase_badge; ?>"><?php echo htmlspecialchars($pago['estatus']); ?></span></td>
                            <td style="color: var(--text-muted); font-size: 0.85rem;"><?php echo htmlspecialchars($pago['metodo_pago']); ?></td>
                            <td>
                                <button class="btn btn-outline btn-editar" style="padding: 5px 10px; font-size: 0.8rem;" 
                                    data-id="<?php echo $pago['id_pago']; ?>" 
                                    data-estatus="<?php echo $pago['estatus']; ?>" 
                                    data-metodo="<?php echo $pago['metodo_pago']; ?>">
                                    <i class='bx bx-edit-alt'></i> Editar
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted);">No hay registros de pagos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<div id="modalNuevoPago" class="modal">
    <div class="modal-content">
        <i class='bx bx-x close-btn' id="close-nuevo"></i>
        <h2>Registrar Nuevo Cobro</h2>
        <form action="pagos.php" method="POST">
            <input type="hidden" name="accion" value="registrar_pago">
            
            <div class="input-group">
                <label>Alumno</label>
                <select name="id_alumno" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                    <option value="">Selecciona al alumno...</option>
                    <?php while($a = $alumnos->fetch_assoc()): ?>
                        <option value="<?php echo $a['id_alumno']; ?>"><?php echo htmlspecialchars($a['nombre'] . ' ' . $a['apellidos']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label>Concepto</label>
                    <input type="text" name="concepto" placeholder="Ej. Mensualidad Junio" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                </div>
                <div class="input-group">
                    <label>Monto ($)</label>
                    <input type="number" step="0.01" name="monto" placeholder="500.00" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label>Estatus</label>
                    <select name="estatus" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                        <option value="Pagado">Pagado</option>
                        <option value="Pendiente">Pendiente (Validando)</option>
                        <option value="Atrasado">Atrasado (Deuda)</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Método de Pago</label>
                    <select name="metodo_pago" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="N/A">N/A (No aplica aún)</option>
                    </select>
                </div>
            </div>

            <div class="input-group">
                <label>Fecha del Pago / Vencimiento</label>
                <input type="date" name="fecha_pago" value="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
            </div>

            <button type="submit" class="btn btn-red btn-full" style="margin-top: 10px;">Guardar Registro</button>
        </form>
    </div>
</div>

<div id="modalEditarPago" class="modal">
    <div class="modal-content">
        <i class='bx bx-x close-btn' id="close-editar"></i>
        <h2>Actualizar Estatus</h2>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 20px;">Modifica el estado de este cobro y el método con el que se liquidó.</p>
        
        <form action="pagos.php" method="POST">
            <input type="hidden" name="accion" value="actualizar_pago">
            <input type="hidden" name="id_pago" id="edit_id_pago">
            
            <div class="form-row">
                <div class="input-group">
                    <label>Nuevo Estatus</label>
                    <select name="estatus" id="edit_estatus" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                        <option value="Pagado">Pagado</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Atrasado">Atrasado</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Método de Pago</label>
                    <select name="metodo_pago" id="edit_metodo" style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; color: white;" required>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="N/A">N/A</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-outline btn-full" style="margin-top: 10px;">Aplicar Cambios</button>
        </form>
    </div>
</div>

<?php if (isset($_GET['exito'])): 
    $mensaje_toast = ($_GET['exito'] == 'pago_registrado') ? 'Cobro registrado correctamente.' : 'Estatus de pago actualizado.';
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
        const urlLimpia = window.location.href.split('?exito=')[0];
        window.history.replaceState({}, document.title, urlLimpia);
    </script>
<?php endif; ?>

<script>
// 1. Buscador en tiempo real
document.getElementById('buscador-pagos').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#tabla-pagos tbody tr');

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// 2. Lógica del Modal Nuevo Pago
const modalNuevo = document.getElementById("modalNuevoPago");
const btnNuevo = document.getElementById("btn-nuevo-pago");
const closeNuevo = document.getElementById("close-nuevo");

btnNuevo.onclick = () => modalNuevo.classList.add("show");
closeNuevo.onclick = () => modalNuevo.classList.remove("show");

// 3. Lógica del Modal Editar Pago (Inyectar datos del botón al form)
const modalEditar = document.getElementById("modalEditarPago");
const closeEditar = document.getElementById("close-editar");
const btnsEditar = document.querySelectorAll(".btn-editar");

btnsEditar.forEach(btn => {
    btn.onclick = function() {
        // Extraemos los datos ocultos en el botón clickeado
        const idPago = this.getAttribute('data-id');
        const estatus = this.getAttribute('data-estatus');
        const metodo = this.getAttribute('data-metodo');

        // Los inyectamos en el formulario del modal
        document.getElementById('edit_id_pago').value = idPago;
        document.getElementById('edit_estatus').value = estatus;
        document.getElementById('edit_metodo').value = metodo;

        // Mostramos el modal
        modalEditar.classList.add("show");
    }
});

closeEditar.onclick = () => modalEditar.classList.remove("show");

// Cerrar modales si se hace clic afuera del cuadro negro
window.onclick = (event) => { 
    if (event.target == modalNuevo) modalNuevo.classList.remove("show");
    if (event.target == modalEditar) modalEditar.classList.remove("show"); 
}
</script>

</body>
</html>