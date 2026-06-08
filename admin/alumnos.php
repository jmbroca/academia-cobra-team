<?php 
require '../includes/seguridad_admin.php'; 
require '../includes/conexion.php'; 

$pagina_actual = 'alumnos'; 
require '../includes/header_admin.php'; 

// ==========================================
// CONSULTA: OBTENER TODOS LOS ALUMNOS
// ==========================================
$sql_alumnos = "
    SELECT 
        a.id_alumno, 
        a.nombre, 
        a.apellidos, 
        a.email,
        a.fecha_registro,
        d.nombre_disciplina, 
        c.nombre_cinturon,
        ad.porcentaje_progreso
    FROM alumnos a
    LEFT JOIN alumnos_disciplinas ad ON a.id_alumno = ad.id_alumno
    LEFT JOIN disciplinas d ON ad.id_disciplina = d.id_disciplina
    LEFT JOIN cinturones c ON ad.id_cinturon = c.id_cinturon
    ORDER BY a.fecha_registro DESC
";
$resultado = $conexion->query($sql_alumnos);
?>

<main class="dashboard-container">
    
    <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Directorio de Alumnos</h1>
            <p style="color: var(--text-muted); margin-top: 5px;">Gestiona a los estudiantes del Dojo.</p>
        </div>
        <button id="btn-nuevo-alumno" class="btn btn-red"><i class='bx bx-user-plus'></i> Nuevo Alumno</button>
    </div>

    <!-- BUSCADOR -->
    <div style="margin-bottom: 20px;">
        <input type="text" id="buscador-alumnos" placeholder="Buscar por nombre, correo o ID..." style="width: 100%; max-width: 400px; padding: 10px 15px; border-radius: 6px; background-color: #1a1a1a; border: 1px solid #333; color: var(--text-light); outline: none; font-family: inherit;">
    </div>

    <div class="dash-table-wrapper">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Nombre Completo</th>
                    <th>Correo</th>
                    <th>Disciplina / Nivel</th>
                    <th>Progreso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if($resultado->num_rows > 0): ?>
                    <?php while($alumno = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos']); ?></strong>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">ID: #<?php echo str_pad($alumno['id_alumno'], 4, '0', STR_PAD_LEFT); ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($alumno['email']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($alumno['nombre_disciplina'] ?? 'Sin asignar'); ?><br>
                                <span style="font-size: 0.85rem; color: var(--red-cobra); font-weight: 700;">
                                    <?php echo htmlspecialchars($alumno['nombre_cinturon'] ?? 'N/A'); ?>
                                </span>
                            </td>
                            <td>
                                <div class="progress-track" style="height: 8px; margin-bottom: 0; width: 80px;">
                                    <div class="progress-fill" style="width: <?php echo $alumno['porcentaje_progreso'] ?? 0; ?>%;"></div>
                                </div>
                                <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo $alumno['porcentaje_progreso'] ?? 0; ?>%</span>
                            </td>
                            <td>
                                <a href="perfil_alumno.php?id=<?php echo $alumno['id_alumno']; ?>" class="btn btn-outline" style="padding: 5px 10px; font-size: 0.8rem;">
                                    <i class='bx bx-folder-open'></i> Expediente
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted);">No hay alumnos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<div id="modalNuevoAlumno" class="modal">
    <div class="modal-content">
        <i class='bx bx-x close-btn'></i>
        <h2>Registrar Alumno</h2>
        <!-- action apunta a la raíz -->
        <form action="../procesar_registro.php" method="POST">
            <!-- Identificador para saber que venimos del panel admin -->
            <input type="hidden" name="origen" value="admin">
            
            <div class="form-row">
                <div class="input-group">
                    <label>Nombre(s)</label>
                    <input type="text" name="nombre" required>
                </div>
                <div class="input-group">
                    <label>Apellidos</label>
                    <input type="text" name="apellidos" required>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label>Correo Electrónico</label>
                    <input type="email" name="email" required> <!-- Cambiado a 'email' -->
                </div>
                <div class="input-group">
                    <label>Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" required> <!-- Agregado de nuevo -->
                </div>
            </div>

            <div class="input-group">
                <label>Contraseña Inicial</label>
                <input type="text" name="password" placeholder="••••••••" required minlength="6">
            </div>

            <button type="submit" class="btn btn-red btn-full" style="margin-top: 15px;">Guardar Alumno</button>
        </form>
    </div>
</div>

<script>
// Lógica simple para abrir y cerrar el Modal
const modal = document.getElementById("modalNuevoAlumno");
const btn = document.getElementById("btn-nuevo-alumno");
const span = document.getElementsByClassName("close-btn")[0];

btn.onclick = () => modal.classList.add("show");
span.onclick = () => modal.classList.remove("show");
window.onclick = (event) => { if (event.target == modal) modal.classList.remove("show"); }

// Lógica del Buscador en tiempo real
document.getElementById('buscador-alumnos').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('.dash-table tbody tr');

    rows.forEach(row => {
        // Obtenemos todo el texto de la fila actual (nombre, ID, correo)
        let text = row.innerText.toLowerCase();
        // Ocultamos o mostramos la fila según si coincide con la búsqueda
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

</body>
</html>