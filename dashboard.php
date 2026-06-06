<?php require 'includes/seguridad_estudiante.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Cobra Team</title>
    <link rel="stylesheet" href="assets/css/styles.css"> </head>
<body style="background-color: var(--bg-dark); color: white;">

    <div style="padding: 50px; text-align: center;">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></h1>
        <p>Tu ID de alumno es: <?php echo $_SESSION['id_alumno']; ?></p>
        
        <a href="logout.php" class="btn btn-outline" style="margin-top: 30px; display: inline-block;">Cerrar Sesión</a>
    </div>

</body>
</html>