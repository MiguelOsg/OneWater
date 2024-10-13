<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirigir si no es administrador
    exit();
}

// Conexión a la base de datos
$db = new SQLite3('db/database.sqlite');

// Verificar si se ha enviado un formulario para actualizar el stock
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $quantityChange = $_POST['quantity_change'];
    
    // Consulta para actualizar el stock
    $query = $db->prepare('UPDATE products SET quantity = quantity + :quantity_change WHERE id = :product_id');
    $query->bindValue(':quantity_change', $quantityChange, SQLITE3_INTEGER);
    $query->bindValue(':product_id', $productId, SQLITE3_INTEGER);
    
    // Ejecutar la consulta y verificar si se actualizó correctamente
    if ($query->execute()) {
        echo "<p>Stock actualizado con éxito.</p>";
    } else {
        echo "<p>Error al actualizar el stock.</p>";
    }
}

// Consulta para obtener todos los productos
$query = 'SELECT id, name, quantity FROM products';
$result = $db->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Stock - One Water</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_stock.css"> <!-- Enlace al CSS del administrador -->
</head>
<body>

<!-- Encabezado -->
<header onclick="location.href='admin_dashboard.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<!-- Sección de administración de stock -->
<section class="stock">
    <h2>Administrar Stock de Productos</h2>
    <div class="stock-container">
        <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="stock-card">
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <p><strong>ID Producto:</strong> <?php echo htmlspecialchars($row['id']); ?></p>
                <p><strong>Stock Actual:</strong> <?php echo htmlspecialchars($row['quantity']); ?></p>
                <form method="POST" action="">
                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                    <input type="number" name="quantity_change" required placeholder="Cantidad" min="1">
                    <button type="submit">Actualizar Stock</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</section>

</body>
</html>
