<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirigir si no es administrador
    exit();
}

// Conexión a la base de datos
$db = new SQLite3('db/database.sqlite');

// Consulta para obtener la cantidad de botellas vendidas
$botellasVendidasQuery = $db->query('SELECT SUM(o.quantity) as total_botellas 
                                     FROM orders o');
$botellasVendidas = $botellasVendidasQuery->fetchArray(SQLITE3_ASSOC)['total_botellas'];

// Consulta para obtener el total de ganancias (multiplicación de cantidad por el precio del producto)
$gananciasQuery = $db->query('
    SELECT SUM(o.quantity * p.price) as total_ganancias
    FROM orders o
    JOIN products p ON o.product_id = p.id
');
$gananciasTotales = $gananciasQuery->fetchArray(SQLITE3_ASSOC)['total_ganancias'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis de Ventas - One Water</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_analisis_ventas.css"> <!-- Enlace al archivo CSS -->
</head>
<body>

<header onclick="location.href='admin_dashboard.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<section class="sales-analysis">
    <h1>Análisis de Ventas</h1>

    <div class="data-section">
        <div class="data-card">
            <h3>Cantidad de Botellas Vendidas</h3>
            <p><?php echo $botellasVendidas ? $botellasVendidas : '0'; ?> botellas</p>
        </div>

        <div class="data-card">
            <h3>Total en Ventas</h3>
            <p>$<?php echo $gananciasTotales ? number_format($gananciasTotales, 2) : '0.00'; ?></p>
        </div>
    </div>
</section>

</body>
</html>
