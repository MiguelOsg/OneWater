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
    SELECT titular, numeracion, fecha_caducidad, ccv
    FROM tarjetas_usuarios
    WHERE usert_id = :usert_id
');
$query->bindValue(':usert_id', $userId, SQLITE3_INTEGER);
$result = $query->execute();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarjetas Guardadas - One Water</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/tarjetas_cliente.css"> <!-- Enlace a los estilos CSS -->
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
        echo '<p><strong>Titular:</strong> ' . htmlspecialchars($row['titular']) . '</p>';
        echo '<p><strong>Número de Tarjeta:</strong> ' . htmlspecialchars($row['numeracion']) . '</p>';
        echo '<p><strong>Fecha de Caducidad:</strong> ' . htmlspecialchars($row['fecha_caducidad']) . '</p>';
        echo '<p><strong>CCV:</strong> ' . htmlspecialchars($row['ccv']) . '</p>';
        
        // Botón de Modificar
        echo '<form action="editar_tarjeta_cliente.php" method="GET" style="display:inline;">
                <input type="hidden" name="numeracion" value="' . htmlspecialchars($row['numeracion']) . '">
                <button type="submit">Modificar</button>
              </form>';

        // Botón de Eliminar
        echo '<form action="eliminar_tarjeta_cliente.php" method="POST" style="display:inline;">
                <input type="hidden" name="numeracion" value="' . htmlspecialchars($row['numeracion']) . '">
                <button type="submit" onclick="return confirm(\'¿Estás seguro de que quieres eliminar esta tarjeta?\');">Eliminar</button>
              </form>';
        
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
