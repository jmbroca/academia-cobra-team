<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Admin - Cobra Team</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700;900&display=swap" rel="stylesheet">
</head>
<body>

    <header class="navbar">
        <div class="logo">
            <div class="logo-icon-box" style="background-color: #d4183d;"><i class='bx bx-shield-quarter'></i></div>
            <span class="logo-text">ADMIN PANEL</span>
        </div>
        
        <nav class="nav-links">
            <a href="dashboard.php" class="<?php echo ($pagina_actual == 'inicio') ? 'active' : ''; ?>">Inicio</a>
            <a href="alumnos.php" class="<?php echo ($pagina_actual == 'alumnos') ? 'active' : ''; ?>">Alumnos</a>
            <a href="asistencias.php" class="<?php echo ($pagina_actual == 'asistencias') ? 'active' : ''; ?>">Asistencia</a>
            <a href="pagos.php" class="<?php echo ($pagina_actual == 'pagos') ? 'active' : ''; ?>">Finanzas</a>
            <a href="configuracion.php" class="<?php echo ($pagina_actual == 'config') ? 'active' : ''; ?>">Ajustes</a>
        </nav>

        <div class="nav-buttons" style="display: flex; align-items: center; gap: 15px;">
            <span style="color: var(--text-muted); font-size: 0.9rem;">
                Hola, <strong style="color: white;"><?php echo explode(' ', $_SESSION['nombre_completo'])[0]; ?></strong>
            </span>
            <a href="../logout.php" class="btn btn-outline">Cerrar Sesión</a>
        </div>
    </header>