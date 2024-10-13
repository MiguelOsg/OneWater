<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'client') {
    header('Location: index.php'); // Redirigir si no es cliente
    exit();
}

// Conexión a la base de datos
$db = new SQLite3('db/database.sqlite');

// ID del producto (ajusta esto según tu base de datos)
$productId = 1;

// Consulta para obtener los detalles del producto
$productQuery = $db->prepare('SELECT id, price, quantity FROM products WHERE id = :product_id');
$productQuery->bindValue(':product_id', $productId, SQLITE3_INTEGER);
$productResult = $productQuery->execute();
$product = $productResult->fetchArray(SQLITE3_ASSOC);

// Verificar si el producto fue encontrado
if (!$product) {
    echo "<p>Error: Producto no encontrado.</p>";
    exit();
}

// Consulta para obtener las tarjetas relacionadas al usuario
$cardQuery = $db->prepare('SELECT id, usert_id, titular FROM tarjetas_usuarios WHERE usert_id = :usert_id');
$cardQuery->bindValue(':usert_id', $_SESSION['id'], SQLITE3_INTEGER);
$cardResult = $cardQuery->execute();

// Consulta para obtener las direcciones del usuario desde la tabla datos_usuario
$addressQuery = $db->prepare('SELECT direccion FROM datos_usuario WHERE userd_id = :userd_id');
$addressQuery->bindValue(':userd_id', $_SESSION['id'], SQLITE3_INTEGER);
$addressResult = $addressQuery->execute();

// Consulta para obtener los tipos de envío
$shippingQuery = $db->query('SELECT id, tipo, precio, dias FROM tipo_envio');
$shippingResult = [];
while ($row = $shippingQuery->fetchArray(SQLITE3_ASSOC)) {
    $shippingResult[] = $row; // Agregar cada fila al array
}

// Verificar si se ha enviado el pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener la tarjeta seleccionada y dirección seleccionada
    $quantity = $_POST['quantity']; // Obtener la cantidad desde el formulario
    $tarjetaId = $_POST['tarjeta']; // Obtener el ID de la tarjeta
    $direccionSeleccionada = $_POST['direccion']; // Obtener la dirección seleccionada
    $tipoEnvioId = $_POST['tipo_envio'];

    // Obtener el titular de la tarjeta
    $titularQuery = $db->prepare('SELECT titular FROM tarjetas_usuarios WHERE id = :tarjeta_id');
    $titularQuery->bindValue(':tarjeta_id', $tarjetaId, SQLITE3_INTEGER);
    $tarjetaTitular = $titularQuery->execute()->fetchArray(SQLITE3_ASSOC)['titular'];

    // Obtener el tipo de envío
    $tipoEnvioQuery = $db->prepare('SELECT tipo, precio FROM tipo_envio WHERE id = :tipo_envio_id');
    $tipoEnvioQuery->bindValue(':tipo_envio_id', $tipoEnvioId, SQLITE3_INTEGER);
    $tipoEnvioSeleccionado = $tipoEnvioQuery->execute()->fetchArray(SQLITE3_ASSOC);

    // Consulta para insertar el nuevo pedido
    $query = $db->prepare('INSERT INTO orders (user_id, product_id, quantity, price, tarjeta_id, tarjeta_titular, direccion, tipo_envio, tipo_envio_precio, created_at) VALUES (:user_id, :product_id, :quantity, :price, :tarjeta_id, :tarjeta_titular, :direccion, :tipo_envio, :tipo_envio_precio, CURRENT_TIMESTAMP)');
    $query->bindValue(':user_id', $_SESSION['id'], SQLITE3_INTEGER);
    $query->bindValue(':product_id', $productId, SQLITE3_INTEGER);
    $query->bindValue(':quantity', $quantity, SQLITE3_INTEGER);
    $query->bindValue(':price', $product['price'], SQLITE3_FLOAT);
    $query->bindValue(':tarjeta_id', $tarjetaId, SQLITE3_INTEGER);
    $query->bindValue(':tarjeta_titular', $tarjetaTitular, SQLITE3_TEXT);
    $query->bindValue(':direccion', $direccionSeleccionada, SQLITE3_TEXT);
    $query->bindValue(':tipo_envio', $tipoEnvioSeleccionado['tipo'], SQLITE3_TEXT);
    $query->bindValue(':tipo_envio_precio', $tipoEnvioSeleccionado['precio'], SQLITE3_FLOAT);

    // Ejecutar la consulta y verificar si se insertó correctamente
    if ($query->execute()) {
        // Actualizar la cantidad del producto en la tabla products
        $updateQuery = $db->prepare('UPDATE products SET quantity = quantity - :quantity WHERE id = :product_id');
        $updateQuery->bindValue(':quantity', $quantity, SQLITE3_INTEGER);
        $updateQuery->bindValue(':product_id', $productId, SQLITE3_INTEGER);

        if ($updateQuery->execute()) {
            header('Location: ordenes.php');
        } else {
            echo "<p>Pedido realizado, pero no se pudo actualizar la cantidad del producto.</p>";
        }
    } else {
        echo "<p>Error al realizar el pedido.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vender - One Water</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Enlace a estilos personalizados -->
    <link rel="stylesheet" href="css/vender.css">
</head>
<body>

<!-- Encabezado -->
<header onclick="location.href='index.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<!-- Sección de contenido de venta -->
<section>
    <div class="content">
        <div class="card">
            <img src="img/OneWater1L.png" alt="Producto de One Water" class="imagen-card">
            <h3>One Water 1L</h3>
        </div>
        <div class="sidebar">
            <h3>Detalles del Producto</h3>
            <p>Capacidad: 1 Litro</p>
            <p>Precio: <?php echo number_format($product['price'], 2); ?> MXN</p>
            <p>Stock disponible: <?php echo $product['quantity']; ?> botellas</p>
            <form method="POST" action="">
                <label for="quantity">Seleccionar Cantidad:</label>
                <input type="number" name="quantity" id="quantity" min="1" max="<?php echo $product['quantity']; ?>" value="1" required>

                <!-- Selección de dirección -->
                <label for="direccion">Seleccionar Dirección:</label>
                <select name="direccion" id="direccion" required>
                    <?php 
                    $addressCount = 0; // Contador para las direcciones
                    while ($row = $addressResult->fetchArray(SQLITE3_ASSOC)): 
                        $addressCount++;
                    ?>
                        <option value="<?php echo htmlspecialchars($row['direccion']); ?>"><?php echo htmlspecialchars($row['direccion']); ?></option>
                    <?php endwhile; ?>
                    <?php if ($addressCount === 0): ?>
                        <option value="">No hay direcciones disponibles</option> <!-- Mensaje si no hay direcciones -->
                    <?php endif; ?>
                </select>

                <!-- Selección de tarjeta -->
                <label for="tarjeta">Seleccionar Tarjeta:</label>
                <select name="tarjeta" id="tarjeta" required>
                    <?php while ($row = $cardResult->fetchArray(SQLITE3_ASSOC)): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['titular']); ?></option>
                    <?php endwhile; ?>
                </select>

                <!-- Selección de tipo de envío -->
                <label for="tipo_envio">Seleccionar Tipo de Envío:</label>
                <select name="tipo_envio" id="tipo_envio" required>
                    <?php foreach ($shippingResult as $shipping): ?>
                        <option value="<?php echo $shipping['id']; ?>"><?php echo htmlspecialchars($shipping['tipo']); ?> (<?php echo number_format($shipping['precio'], 2); ?> MXN)</option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Realizar Pedido</button>
            </form>
        </div>
    </div>
</section>


</body>
</html>
