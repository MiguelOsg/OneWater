<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirigir si no es administrador
    exit();
}

// Conexión a la base de datos
$db = new SQLite3('db/database.sqlite');

// Consulta para obtener todos los pedidos con la dirección del usuario, tipo de envío, tipo de envío precio y titular de la tarjeta
$query = 'SELECT o.id, u.username, u.email, du.telefono, du.direccion, 
                 o.tipo_envio_precio, o.tipo_envio, 
                 p.name AS product_name, o.quantity,  
                 (o.price * o.quantity + o.tipo_envio_precio) AS total, o.created_at,
                 o.tarjeta_titular
          FROM orders o
          JOIN users u ON o.user_id = u.id
          JOIN datos_usuario du ON u.id = du.userd_id
          JOIN products p ON o.product_id = p.id
          ORDER BY o.created_at DESC';

$result = $db->query($query);
if (!$result) {
    echo "Error en la consulta: " . $db->lastErrorMsg();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Pedidos - One Water</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_orden.css"> <!-- Enlace al CSS del administrador -->
</head>
<body>

<!-- Encabezado -->
<header onclick="location.href='admin_dashboard.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<!-- Sección de pedidos -->
<section class="orders">
    <h2>Pedidos de Clientes</h2>
    <div class="orders-container">
        <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="order-card">
                <h3>Pedido #<?php echo htmlspecialchars($row['id']); ?></h3>
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($row['username']); ?></p>
                <p><strong>Correo:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                <p><strong>Titular de la Tarjeta:</strong> <?php echo htmlspecialchars($row['tarjeta_titular']); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($row['telefono']); ?></p>
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($row['direccion']); ?></p>
                <p><strong>Tipo de Envío:</strong> <?php echo htmlspecialchars($row['tipo_envio']); ?></p>
                <p><strong>Producto:</strong> <?php echo htmlspecialchars($row['product_name']); ?></p>
                <p><strong>Cantidad:</strong> <?php echo htmlspecialchars($row['quantity']); ?></p>
                <p><strong>Total:</strong> <?php echo number_format($row['total'], 2); ?> MXN</p>
                <p><strong>Fecha:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</section>

</body>
</html>
