<?php 
require '../includes/seguridad_admin.php'; 
require '../includes/conexion.php'; 

$pagina_actual = 'inicio'; 
require '../includes/header_admin.php'; 
?>

<main class="dashboard-container">
    <div class="dashboard-header" style="margin-bottom: -20px;">
        <h1>Panel Administrativo</h1>
        <p>Bienvenido al centro de control de Cobra Team.</p>
    </div>

    <section class="summary-cards-grid">
        <div class="dash-card" style="padding: 20px;">
            <div class="dash-card-info">
                <h3 style="font-size: 0.85rem;">Alumnos Activos</h3>
                <p style="font-size: 1.5rem; font-weight: 900;">24</p>
            </div>
            <div class="dash-card-icon"><i class='bx bx-group'></i></div>
        </div>

        <div class="dash-card" style="padding: 20px;">
            <div class="dash-card-info">
                <h3 style="font-size: 0.85rem;">Pagos Pendientes</h3>
                <p style="font-size: 1.5rem; font-weight: 900; color: var(--red-cobra);">3</p>
            </div>
            <div class="dash-card-icon"><i class='bx bx-error-circle'></i></div>
        </div>
    </section>

    <div class="list-column" style="margin-top: 10px;">
        <h3>Accesos Rápidos</h3>
        <p style="color: var(--text-muted);">Selecciona una pestaña superior para empezar a gestionar alumnos, asistencias o finanzas.</p>
    </div>
</main>

</body>
</html>