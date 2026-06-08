<?php 
require 'includes/seguridad_estudiante.php'; 
require 'includes/conexion.php'; 

$pagina_actual = 'pagos'; 
require 'includes/header_estudiante.php'; 

$id_alumno = $_SESSION['id_alumno'];

// ==========================================
// 1. OBTENER EL HISTORIAL DE PAGOS DEL ALUMNO
// ==========================================
$sql_pagos = "SELECT concepto, monto, fecha_pago, estatus, metodo_pago FROM pagos WHERE id_alumno = ? ORDER BY fecha_pago DESC";
$stmt_pagos = $conexion->prepare($sql_pagos);
$stmt_pagos->bind_param("i", $id_alumno);
$stmt_pagos->execute();
$resultado_pagos = $stmt_pagos->get_result();

// Variables para nuestro "Semáforo"
$historial_pagos = [];
$deuda_total = 0;
$hay_atrasados = false;
$hay_pendientes = false;

// Pasamos los resultados a un arreglo y calculamos la deuda al mismo tiempo
while($pago = $resultado_pagos->fetch_assoc()) {
    $historial_pagos[] = $pago;
    
    if($pago['estatus'] == 'Atrasado') {
        $deuda_total += $pago['monto'];
        $hay_atrasados = true;
    } elseif($pago['estatus'] == 'Pendiente') {
        $deuda_total += $pago['monto'];
        $hay_pendientes = true;
    }
}
$stmt_pagos->close();

// ==========================================
// 2. LÓGICA DEL SEMÁFORO DE ESTATUS
// ==========================================
// Por defecto, asumimos que está al corriente (Verde)
$clase_semaforo = 'status-ok';
$titulo_estatus = 'Al Corriente';
$icono_estatus = "<i class='bx bx-check-shield'></i>";

// Calculamos el primer día del próximo mes para mostrarlo como "Próximo vencimiento"
$proximo_mes = date('Y-m-01', strtotime('+1 month'));
$desc_estatus = "Próximo vencimiento: <strong>" . fechaEspanol($proximo_mes, 'corta') . "</strong>";

// Si tiene pagos atrasados (Rojo)
if ($hay_atrasados) {
    $clase_semaforo = 'status-danger';
    $titulo_estatus = 'Pago Vencido';
    $icono_estatus = "<i class='bx bx-error-circle'></i>";
    $desc_estatus = 'Tienes un saldo vencido de: <strong style="color: var(--red-cobra);">$' . number_format($deuda_total, 2) . ' MXN</strong>';
} 
// Si solo tiene pagos pendientes por verificar (Amarillo)
elseif ($hay_pendientes) {
    $clase_semaforo = 'status-warning';
    $titulo_estatus = 'Pago Pendiente';
    $icono_estatus = "<i class='bx bx-time-five'></i>";
    $desc_estatus = 'Saldo por cubrir o en validación: <strong style="color: #f1c40f;">$' . number_format($deuda_total, 2) . ' MXN</strong>';
}
?>

<main class="dashboard-container">
    
    <div class="dashboard-header">
        <h1>Control de Pagos</h1>
    </div>

    <div class="dashboard-cards-grid" style="align-items: stretch;">
        
        <div class="payment-status-card <?php echo $clase_semaforo; ?>">
            <div class="payment-icon"><?php echo $icono_estatus; ?></div>
            <div class="payment-status-title"><?php echo $titulo_estatus; ?></div>
            <div class="payment-status-desc"><?php echo $desc_estatus; ?></div>
        </div>

        <div class="bank-info-card">
            <h3><i class='bx bx-building-house'></i> Datos para Transferencia</h3>
            
            <div class="bank-detail-row">
                <span class="bank-label">Banco</span>
                <span class="bank-value">BBVA Bancomer</span>
            </div>
            <div class="bank-detail-row">
                <span class="bank-label">Titular</span>
                <span class="bank-value">Cobra Team Dojo</span>
            </div>
            <div class="bank-detail-row">
                <span class="bank-label">CLABE</span>
                <span class="bank-value">012 345 67890123456 7</span>
            </div>

            <div class="bank-instruction">
                <i class='bx bx-info-circle' style="color: var(--red-cobra);"></i> 
                <strong>Importante:</strong> Al realizar tu transferencia, pon tu nombre en el "Concepto". Envía tu comprobante a recepción para que tu pago pase de Pendiente a Pagado.
            </div>
        </div>

    </div>

    <h2 style="margin-bottom: 20px; font-size: 1.2rem; color: var(--text-light);">Historial de Pagos</h2>
    
    <div class="dash-table-wrapper">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Concepto</th>
                    <th>Método</th>
                    <th>Monto</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($historial_pagos) > 0): ?>
                    <?php foreach($historial_pagos as $pago): 
                        // Colores para las "etiquetas" de la tabla
                        $clase_badge = 'badge-green'; // Por defecto verde para 'Pagado'
                        if ($pago['estatus'] == 'Atrasado') $clase_badge = 'badge-red';
                        if ($pago['estatus'] == 'Pendiente') $clase_badge = 'badge-warning'; // Necesitamos crear este CSS
                    ?>
                        <tr>
                            <td><?php echo date("d M, Y", strtotime($pago['fecha_pago'])); ?></td>
                            <td><?php echo htmlspecialchars($pago['concepto']); ?></td>
                            <td><?php echo htmlspecialchars($pago['metodo_pago']); ?></td>
                            <td style="font-weight: 700;">$<?php echo number_format($pago['monto'], 2); ?></td>
                            <td><span class="badge <?php echo $clase_badge; ?>"><?php echo htmlspecialchars($pago['estatus']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted);">No tienes un historial de pagos registrado aún.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>
</body>
</html>