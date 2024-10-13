<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'client') {
    header('Location: index.php'); // Redirigir si no es cliente
    exit();
}

// Conexión a la base de datos
$db = new SQLite3('db/database.sqlite');

// Obtener el ID del usuario de la sesión
$userId = $_SESSION['id'];

// Consulta para obtener la última información del usuario
$query = $db->prepare('
    SELECT u.email
    FROM datos_usuario du
    JOIN users u ON du.userd_id = u.id
    WHERE du.userd_id = :userd_id
    ORDER BY du.id DESC
    LIMIT 1
');
$query->bindValue(':userd_id', $userId, SQLITE3_INTEGER);
$result = $query->execute();

$userInfo = $result->fetchArray(SQLITE3_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Información - One Water</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Enlace a estilos personalizados -->
    <link rel="stylesheet" href="css/ver_informacion.css">
</head>
<body>

<!-- Encabezado -->
<header onclick="location.href='datos_cliente.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<!-- Contenido de ver información -->
<section class="view-info">
    <h1>Cuenta</h1>
    
    <?php if ($userInfo): ?>
        <p><strong>Correo:</strong> <?php echo htmlspecialchars($userInfo['email']); ?></p>

    <?php else: ?>
        <p>No hay información disponible. Por favor, ingresa tu información.</p>
        <button onclick="location.href='agregar_informacion.php'">Agregar Información</button>
    <?php endif; ?>
</section>

</body>
</html>
