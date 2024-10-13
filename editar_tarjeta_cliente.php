<?php
session_start();

// Verificar si el usuario ha iniciado sesión y es cliente
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'client') {
    header('Location: index.php'); // Redirigir si no es cliente
    exit();
}

// Conexión a la base de datos
$db = new SQLite3('db/database.sqlite');

// Obtener el ID del usuario de la sesión
$userId = $_SESSION['id'];

// Consulta para obtener las tarjetas del usuario
$query = $db->prepare('
    SELECT usert_id, titular, numeracion, fecha_caducidad, ccv
    FROM tarjetas_usuarios
    WHERE usert_id = :usert_id
');
$query->bindValue(':usert_id', $userId, SQLITE3_INTEGER);
$result = $query->execute();

// Comprobar si se ha enviado un formulario de eliminación o actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // Eliminar tarjeta
        $numeracionTarjeta = $_POST['id_tarjeta'];
        $deleteQuery = $db->prepare('DELETE FROM tarjetas_usuarios WHERE usert_id = :usert_id AND numeracion = :numeracion');
        $deleteQuery->bindValue(':usert_id', $userId, SQLITE3_INTEGER);
        $deleteQuery->bindValue(':numeracion', $numeracionTarjeta, SQLITE3_TEXT);
        $deleteQuery->execute();
    } elseif (isset($_POST['update'])) {
        // Actualizar tarjeta
        $numeracionActual = $_POST['id_tarjeta']; // Numeración de la tarjeta original antes de la actualización
        $titular = $_POST['titular'];
        $nuevaNumeracion = $_POST['numeracion'];
        $fechaCaducidad = $_POST['fecha_caducidad'];
        $ccv = $_POST['ccv'];

        $updateQuery = $db->prepare('
            UPDATE tarjetas_usuarios 
            SET titular = :titular, numeracion = :nueva_numeracion, fecha_caducidad = :fecha_caducidad, ccv = :ccv 
            WHERE usert_id = :usert_id AND numeracion = :numeracion_actual
        ');
        $updateQuery->bindValue(':titular', $titular, SQLITE3_TEXT);
        $updateQuery->bindValue(':nueva_numeracion', $nuevaNumeracion, SQLITE3_TEXT);
        $updateQuery->bindValue(':fecha_caducidad', $fechaCaducidad, SQLITE3_TEXT);
        $updateQuery->bindValue(':ccv', $ccv, SQLITE3_TEXT);
        $updateQuery->bindValue(':usert_id', $userId, SQLITE3_INTEGER);
        $updateQuery->bindValue(':numeracion_actual', $numeracionActual, SQLITE3_TEXT); // La numeración de la tarjeta antes de ser actualizada
        $updateQuery->execute();
    }

    // Redirigir a la misma página para mostrar cambios
    header('Location: tarjetas_cliente.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tarjetas Guardadas - One Water</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/editar_tarjeta_cliente.css">
</head>
<body>

<!-- Encabezado -->
<header onclick="location.href='datos_cliente.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<!-- Sección de tarjetas -->
<section class="tarjetas-info">
    <h1>Mis Tarjetas Guardadas</h1>
    <div class="tarjetas-container">

    <?php
    // Mostrar cada tarjeta del usuario
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        echo '<div class="tarjeta">';
        echo '<form method="POST" action="">'; // Formulario para cada tarjeta
        echo '<p><strong>Titular:</strong> <input type="text" name="titular" value="' . htmlspecialchars($row['titular']) . '" required></p>';
        echo '<p><strong>Número de Tarjeta:</strong> <input type="text" name="numeracion" value="' . htmlspecialchars($row['numeracion']) . '" required></p>';
        echo '<p><strong>Fecha de Caducidad:</strong> <input type="text" name="fecha_caducidad" value="' . htmlspecialchars($row['fecha_caducidad']) . '" required></p>';
        echo '<p><strong>CCV:</strong> <input type="text" name="ccv" value="' . htmlspecialchars($row['ccv']) . '" required></p>';
        echo '<input type="hidden" name="id_tarjeta" value="' . htmlspecialchars($row['numeracion']) . '">'; // Usar 'numeracion' como referencia
        echo '<button type="submit" name="update">Actualizar</button>';
        echo '<button type="submit" name="delete">Eliminar</button>';
        echo '</form>';
        echo '</div>';
    }

    // Si no se encuentran tarjetas
    if ($result->numColumns() == 0) {
        echo '<p>No tienes tarjetas guardadas.</p>';
    }
    ?>
    </div>
</section>

</body>
</html>
