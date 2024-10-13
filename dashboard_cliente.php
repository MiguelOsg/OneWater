<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'client') {
    header('Location: index.php'); // Redirigir si no es cliente
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cliente - One Water</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard_cliente.css"> <!-- Enlace al CSS del dashboard cliente -->
</head>
<body>

<!-- Encabezado -->
<header onclick="location.href='index.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<!-- Menú de navegación -->
<nav>
    <ul>
        <li><a href="logout.php">Cerrar sesión</a></li>
    </ul>
</nav>

<!-- Contenido del Dashboard -->
<section class="content">
    <!-- Sección del perfil -->
    <div class="card" id="perfil">
        <h3>Mi Perfil</h3>
        <p>Nombre de usuario: <?php echo $_SESSION['username']; ?></p>
        <p>Email: <?php echo $_SESSION['email']; ?></p>
        <!-- Botón para ver los datos del cliente -->
        <button onclick="location.href='datos_cliente.php'" style="margin-top: 10px;">Ver Mis Datos</button>
    </div>

    <!-- Sección de pedidos -->
    <div class="card" id="pedidos">
        <h3>Mis Pedidos</h3>
        <p>Aquí puedes ver el estado de tus pedidos recientes.</p>
        <button onclick="location.href='ordenes.php'">Ver Pedidos</button>
    </div>
</section>

</body>
</html>
