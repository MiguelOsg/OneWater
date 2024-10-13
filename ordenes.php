<?php
session_start();

// Verificar si el usuario está logueado y es un cliente
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'client') {
    header('Location: index.php'); // Redirigir si no es cliente
    exit();
}

// Conexión a la base de datos
$db = new SQLite3('db/database.sqlite');

// Obtener el ID del usuario de la sesión
$userId = $_SESSION['id'];

// Consulta para obtener las órdenes del usuario
$query = $db->prepare('
    SELECT o.id, p.name AS product_name, o.quantity, o.price, o.created_at, u.telefono, u.direccion, t.titular,
           o.tipo_envio, o.tipo_envio_precio,
           (o.quantity * o.price + o.tipo_envio_precio) AS total_price
    FROM orders o
    JOIN products p ON o.product_id = p.id
    JOIN datos_usuario u ON o.user_id = u.userd_id
    JOIN tarjetas_usuarios t ON o.tarjeta_id = t.id
    WHERE o.user_id = :user_id
');
$query->bindValue(':user_id', $userId, SQLITE3_INTEGER);
$result = $query->execute();

// Comprobar si hay pedidos
$orders = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - One Water</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/ordenes.css"> <!-- Enlace al CSS del dashboard cliente -->
</head>
<body>
<!-- Encabezado -->
<header onclick="location.href='dashboard_cliente.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<!-- Contenido del Dashboard -->
<section class="content">
    <h2>Historial de pedidos</h2>

    <?php if (empty($orders)): ?>
        <p>No tienes pedidos realizados.</p>
    <?php else: ?>
        <div class="orders-container">
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <p><strong>Estado: Entregado</strong> </p>
                    <p><strong>Llego el</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
                    <p><strong>Producto:</strong> <?php echo htmlspecialchars($order['product_name']); ?></p>
                    <p><strong>Titular de la Tarjeta:</strong> <?php echo htmlspecialchars($order['titular']); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($order['telefono']); ?></p>
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($order['direccion']); ?></p>
                    <p><strong>Tipo de Envío:</strong> <?php echo htmlspecialchars($order['tipo_envio']); ?></p>
                    <p><strong>Cantidad:</strong> <?php echo htmlspecialchars($order['quantity']); ?></p>
                    <p><strong>Precio Unitario:</strong> <?php echo number_format($order['price'], 2); ?> MXN</p>
                    <p><strong>Precio de Envío:</strong> <?php echo number_format($order['tipo_envio_precio'], 2); ?> MXN</p>
                    <p><strong>Total:</strong> <?php echo number_format($order['total_price'], 2); ?> MXN</p>

                    <!-- Botón Volver a Comprar (solo redirección) -->
                    <button onclick="window.location.href='vender.php';" class="buy-again-btn">Volver a Comprar</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

</body>
</html>
