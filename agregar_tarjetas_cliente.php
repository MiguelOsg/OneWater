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

// Procesar el formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titular = $_POST['titular'];
    $numeracion = $_POST['numeracion'];
    $fecha_caducidad = $_POST['fecha_caducidad'];
    $ccv = $_POST['ccv'];

    // Verificar si ya existe una tarjeta para el usuario
    $checkQuery = $db->prepare('
        SELECT COUNT(*) as count 
        FROM tarjetas_usuarios 
        WHERE usert_id = :usert_id
    ');
    $checkQuery->bindValue(':usert_id', $userId, SQLITE3_INTEGER);
    $result = $checkQuery->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);

    if ($row['count'] > 0) {
        echo "<script>alert('Error: ya existe una tarjeta registrada para este usuario.');</script>";
    } else {
        // Insertar nueva tarjeta
        $insertQuery = $db->prepare('
            INSERT INTO tarjetas_usuarios (usert_id, titular, numeracion, fecha_caducidad, ccv)
            VALUES (:usert_id, :titular, :numeracion, :fecha_caducidad, :ccv)
        ');
        $insertQuery->bindValue(':usert_id', $userId, SQLITE3_INTEGER);
        $insertQuery->bindValue(':titular', $titular, SQLITE3_TEXT);
        $insertQuery->bindValue(':numeracion', $numeracion, SQLITE3_TEXT);
        $insertQuery->bindValue(':fecha_caducidad', $fecha_caducidad, SQLITE3_TEXT);
        $insertQuery->bindValue(':ccv', $ccv, SQLITE3_TEXT);

        if ($insertQuery->execute()) {
            echo "<script>alert('Tarjeta registrada exitosamente.');</script>";
            header('Location: datos_cliente.php'); // Redirigir a la página de cuenta
            exit();
        } else {
            // Manejo de errores
            $errorCode = $db->lastErrorCode();
            echo "<script>alert('Error al registrar la tarjeta. Código de error: $errorCode');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Tarjeta - One Water</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/agregar_tarjetas_cliente.css"> <!-- Enlace al CSS de editar tarjetas -->
</head>
<body>

<!-- Encabezado -->
<header onclick="location.href='datos_cliente.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<!-- Formulario para registrar tarjeta -->
<section class="edit-info">
    <h1>Registrar Nueva Tarjeta</h1>
    <form method="POST">

        <label for="titular">Titular de la Tarjeta:</label>
        <input type="text" id="titular" name="titular" required>

        <label for="numeracion">Numeración:</label>
        <input type="text" id="numeracion" name="numeracion" required>

        <label for="fecha_caducidad">Fecha de Caducidad:</label>
        <input type="text" id="fecha_caducidad" name="fecha_caducidad" placeholder="MM/AA" required>

        <label for="ccv">CCV:</label>
        <input type="text" id="ccv" name="ccv" required>

        <button type="submit">Registrar Tarjeta</button>
    </form>
</section>

</body>
</html>
