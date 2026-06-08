<?php 
require '../includes/seguridad_admin.php'; 
require '../includes/conexion.php'; 

$pagina_actual = 'inicio'; 

// ==========================================
// 1. CÁLCULO DE MÉTRICAS (STAT CARDS)
// ==========================================

// Total de Alumnos Activos
$res_alumnos = $conexion->query("SELECT COUNT(id_alumno) as total FROM alumnos WHERE rol = 'estudiante'");
$total_alumnos = $res_alumnos->fetch_assoc()['total'];

// Ingresos del Mes Actual
$mes_actual = date('m');
$ano_actual = date('Y');
$res_ingresos = $conexion->query("SELECT SUM(monto) as total FROM pagos WHERE estatus = 'Pagado' AND MONTH(fecha_pago) = $mes_actual AND YEAR(fecha_pago) = $ano_actual");
$ingresos_mes = $res_ingresos->fetch_assoc()['total'] ?? 0;

// Pagos Pendientes y Atrasados
$res_deudas = $conexion->query("SELECT COUNT(id_pago) as total, SUM(monto) as monto FROM pagos WHERE estatus IN ('Atrasado', 'Pendiente')");
$datos_deudas = $res_deudas->fetch_assoc();
$total_deudas = $datos_deudas['total'];

// Próximas Clases
$res_clases = $conexion->query("SELECT COUNT(id_clase) as total FROM clases WHERE DATE(fecha_hora) >= CURDATE()");
$total_clases = $res_clases->fetch_assoc()['total'];

// ==========================================
// 2. DATOS PARA LA GRÁFICA (Últimos 5 meses)
// ==========================================
$datos_grafica = ['labels' => [], 'valores' => []];
$meses_nombres = ['01'=>'Ene', '02'=>'Feb', '03'=>'Mar', '04'=>'Abr', '05'=>'May', '06'=>'Jun', '07'=>'Jul', '08'=>'Ago', '09'=>'Sep', '10'=>'Oct', '11'=>'Nov', '12'=>'Dic'];

for ($i = 4; $i >= 0; $i--) {
    $mes_query = date('m', strtotime("-$i months"));
    $ano_query = date('Y', strtotime("-$i months"));
    
    $q = $conexion->query("SELECT SUM(monto) as total FROM pagos WHERE estatus = 'Pagado' AND MONTH(fecha_pago) = $mes_query AND YEAR(fecha_pago) = $ano_query");
    $suma = $q->fetch_assoc()['total'] ?? 0;
    
    $datos_grafica['labels'][] = $meses_nombres[$mes_query];
    $datos_grafica['valores'][] = $suma;
}

// Convertimos los arrays a formato JSON para que JavaScript (Chart.js) los pueda leer
$labels_js = json_encode($datos_grafica['labels']);
$valores_js = json_encode($datos_grafica['valores']);

// ==========================================
// 3. ALERTAS DE COBRANZA (Para la lista rápida)
// ==========================================
$sql_morosos = "SELECT p.monto, p.concepto, a.nombre, a.apellidos FROM pagos p JOIN alumnos a ON p.id_alumno = a.id_alumno WHERE p.estatus = 'Atrasado' ORDER BY p.fecha_pago ASC LIMIT 4";
$morosos = $conexion->query($sql_morosos);

require '../includes/header_admin.php'; 
?>

<!-- Importamos Chart.js para la gráfica -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="dashboard-container">
    
    <div class="dashboard-header" style="margin-bottom: -25px;">
        <h1>Panel Administrativo</h1>
        <p style="color: var(--text-muted); margin-top: 5px;">Bienvenido al centro de control financiero y académico de Cobra Team.</p>
    </div>

    <!-- LAS 4 TARJETAS DE MÉTRICAS -->
    <section class="summary-cards-grid" style="margin-bottom: -55px;">
        <div class="dash-card" style="padding: 20px; border-left: 4px solid var(--text-light);">
            <div class="dash-card-info">
                <h3 style="font-size: 0.85rem; color: var(--text-muted);">Alumnos Activos</h3>
                <p style="font-size: 1.8rem; font-weight: 900;"><?php echo $total_alumnos; ?></p>
            </div>
            <div class="dash-card-icon" style="color: var(--text-muted);"><i class='bx bx-group'></i></div>
        </div>

        <div class="dash-card" style="padding: 20px; border-left: 4px solid #2ecc71;">
            <div class="dash-card-info">
                <h3 style="font-size: 0.85rem; color: var(--text-muted);">Ingresos del Mes</h3>
                <p style="font-size: 1.8rem; font-weight: 900; color: #2ecc71;">$<?php echo number_format($ingresos_mes, 2); ?></p>
            </div>
            <div class="dash-card-icon" style="color: #2ecc71;"><i class='bx bx-trending-up'></i></div>
        </div>

        <div class="dash-card" style="padding: 20px; border-left: 4px solid var(--red-cobra);">
            <div class="dash-card-info">
                <h3 style="font-size: 0.85rem; color: var(--text-muted);">Trámites Pendientes</h3>
                <p style="font-size: 1.8rem; font-weight: 900; color: var(--red-cobra);"><?php echo $total_deudas; ?></p>
            </div>
            <div class="dash-card-icon" style="color: var(--red-cobra);"><i class='bx bx-error-circle'></i></div>
        </div>

        <div class="dash-card" style="padding: 20px; border-left: 4px solid #3498db;">
            <div class="dash-card-info">
                <h3 style="font-size: 0.85rem; color: var(--text-muted);">Clases Programadas</h3>
                <p style="font-size: 1.8rem; font-weight: 900; color: #3498db;"><?php echo $total_clases; ?></p>
            </div>
            <div class="dash-card-icon" style="color: #3498db;"><i class='bx bx-calendar-event'></i></div>
        </div>
    </section>

    <!-- SECCIÓN INFERIOR: GRÁFICA Y ALERTAS -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 30px; align-items: start;">
        
        <!-- COLUMNA IZQUIERDA: Gráfica de Ingresos -->
        <div class="dash-card" style="padding: 25px;">
            <h3 style="margin-bottom: 20px; font-size: 1.1rem;"><i class='bx bx-bar-chart-alt-2'></i> Historial de Ingresos</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="graficaIngresos"></canvas>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Alertas Rápidas -->
        <div class="list-column">
            <h3 style="font-size: 1.1rem; margin-bottom: 15px;"><i class='bx bx-bell'></i> Alertas de Cobranza</h3>
            
            <?php if($morosos->num_rows > 0): while($moroso = $morosos->fetch_assoc()): ?>
                <div class="list-item" style="padding: 12px 0;">
                    <div class="list-item-icon" style="color: var(--red-cobra); background: rgba(212,24,61,0.1);"><i class='bx bx-money-withdraw'></i></div>
                    <div class="list-item-content">
                        <div class="list-item-title" style="font-size: 0.95rem;"><?php echo htmlspecialchars($moroso['nombre'] . ' ' . $moroso['apellidos']); ?></div>
                        <div class="list-item-desc"><?php echo htmlspecialchars($moroso['concepto']); ?></div>
                    </div>
                    <div class="list-item-badge badge-red" style="font-weight: 700;">
                        $<?php echo number_format($moroso['monto'], 2); ?>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <div style="text-align: center; padding: 30px 10px; color: var(--text-muted);">
                    <i class='bx bx-check-shield' style="font-size: 3rem; color: #2ecc71; margin-bottom: 10px; display: block;"></i>
                    <p style="font-size: 0.9rem;">Todo al corriente. No hay pagos vencidos.</p>
                </div>
            <?php endif; ?>

            <a href="pagos.php" class="btn btn-outline" style="display: block; width: 100%; box-sizing: border-box; text-align: center; margin: 30px 0 0 0; font-size: 0.85rem;">Ir a Finanzas</a>
        </div>

    </div>

</main>

<!-- SCRIPT PARA DIBUJAR LA GRÁFICA -->
<script>
const ctx = document.getElementById('graficaIngresos').getContext('2d');
const myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo $labels_js; ?>, // Inyectamos los meses desde PHP
        datasets: [{
            label: 'Ingresos ($ MXN)',
            data: <?php echo $valores_js; ?>, // Inyectamos las sumas desde PHP
            backgroundColor: '#d4183d', // Rojo Cobra Team
            borderRadius: 4,
            barThickness: 30
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#333' }, // Cuadrícula oscura para combinar con el tema
                ticks: { color: '#888' }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#888' }
            }
        }
    }
});
</script>

</body>
</html>