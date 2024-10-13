<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirigir si no es administrador
    exit();
}

// Conexión a la base de datos
$db = new SQLite3('db/database.sqlite');

// Manejo de eliminación de cliente
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $deleteQuery = $db->prepare('DELETE FROM users WHERE id = :id');
    $deleteQuery->bindValue(':id', $id, SQLITE3_INTEGER);
    $deleteQuery->execute();

    header('Location: admin_clientes.php'); // Redirigir después de eliminar
    exit();
}

// Consulta para obtener todos los clientes junto con los datos de contacto
$clientesQuery = $db->query('
    SELECT u.id, u.username, u.email, du.telefono, du.direccion 
    FROM users u
    LEFT JOIN datos_usuario du ON u.id = du.userd_id
');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Clientes - One Water</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_clientes.css"> <!-- Estilos CSS para la página de administración de clientes -->
</head>
<body>

<header onclick="location.href='admin_dashboard.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<section class="dashboard-section">
    <h1>Clientes Registrados</h1>
    <div class="clientes-container">
        <?php while ($cliente = $clientesQuery->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="cliente-card">
                <h3>Cliente: <?php echo htmlspecialchars($cliente['username']); ?></h3>
                <p><strong>ID:</strong> <?php echo htmlspecialchars($cliente['id']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($cliente['email']); ?></p>
                <p><strong>Teléfono:</strong> <?php echo $cliente['telefono'] ? htmlspecialchars($cliente['telefono']) : 'No disponible'; ?></p>
                <p><strong>Dirección:</strong> <?php echo $cliente['direccion'] ? htmlspecialchars($cliente['direccion']) : 'No disponible'; ?></p>
                <div class="actions">
                    <a href="admin_editar_cliente.php?id=<?php echo $cliente['id']; ?>">Modificar</a>
                    <a href="admin_clientes.php?action=delete&id=<?php echo $cliente['id']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este cliente?');">Eliminar</a>
                </div>

                <!-- Consultar tarjetas relacionadas a este cliente -->
                <?php
                $tarjetasQuery = $db->prepare('SELECT titular, fecha_caducidad FROM tarjetas_usuarios WHERE usert_id = :usert_id');
                $tarjetasQuery->bindValue(':usert_id', $cliente['id'], SQLITE3_INTEGER);
                $tarjetasResult = $tarjetasQuery->execute();
                ?>
                <div class="tarjetas">
                    <h4>Tarjetas:</h4>
                    <ul>
                        <?php while ($tarjeta = $tarjetasResult->fetchArray(SQLITE3_ASSOC)): ?>
                            <li>
                                <?php echo "Titular: " . htmlspecialchars($tarjeta['titular']) . " - Caducidad: " . htmlspecialchars($tarjeta['fecha_caducidad']); ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

</body>
</html>
