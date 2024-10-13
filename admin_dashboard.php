<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirigir si no es administrador
    exit();
}

// Conexión a la base de datos
$db = new SQLite3('db/database.sqlite');

// Consulta para obtener el número total de órdenes
$orderCountQuery = $db->query('SELECT COUNT(*) as total FROM orders');
$orderCount = $orderCountQuery->fetchArray(SQLITE3_ASSOC)['total'];

// Consulta para obtener el número total de usuarios
$userCountQuery = $db->query('SELECT COUNT(*) as total FROM users');
$userCount = $userCountQuery->fetchArray(SQLITE3_ASSOC)['total'];

// Consulta para obtener el stock total de productos
$stockCountQuery = $db->query('SELECT SUM(quantity) as total FROM products');
$stockCount = $stockCountQuery->fetchArray(SQLITE3_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - One Water</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard_admin.css"> <!-- Estilos CSS para el dashboard -->
</head>
<body>

<header onclick="location.href='index.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<section class="dashboard-section">
    <div class="sidebar">
        <h3>Panel de Control</h3>
        <ul>
            <li><a href="admin_orden.php">Administrar Pedidos de Clientes</a></li>
            <li><a href="admin_stock.php">Administrar Stock de Productos</a></li>
            <li><a href="admin_clientes.php">Administrar Clientes Registrados</a></li>
            <li><a href="admin_analisis_ventas.php">Análisis de Ventas</a></li> <!-- Nueva opción añadida -->
        </ul>
    </div>

    <div class="content">
        <h1>Bienvenido, <?php echo $_SESSION['username']; ?>!</h1>
        <p>Este es el panel de control administrativo. Aquí puedes gestionar las operaciones de One Water.</p>
        <div class="card">
            <h3>Estadísticas Generales</h3>
            <p>Órdenes completadas: <?php echo $orderCount; ?></p>
            <p>Clientes registrados: <?php echo $userCount; ?></p>
            <p>Stock actual: <?php echo $stockCount; ?> botellas</p>
        </div>
    </div>
</section>

<a href="logout.php" class="logout-btn">Cerrar sesión</a>

</body>
</html>
