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

// Obtener el nombre de usuario actual
$usernameQuery = $db->prepare('SELECT username FROM users WHERE id = :user_id');
$usernameQuery->bindValue(':user_id', $userId, SQLITE3_INTEGER);
$usernameResult = $usernameQuery->execute();
$currentUsername = $usernameResult->fetchArray(SQLITE3_ASSOC)['username'];

// Comprobar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    // Actualizar el nombre de usuario en la tabla users
    $updateQuery = $db->prepare('
        UPDATE users SET username = :username WHERE id = :user_id
    ');
    $updateQuery->bindValue(':username', $username, SQLITE3_TEXT);
    $updateQuery->bindValue(':user_id', $userId, SQLITE3_INTEGER);
    $updateQuery->execute();

    // Eliminar la información anterior de la tabla datos_usuario
    $deleteQuery = $db->prepare('
        DELETE FROM datos_usuario WHERE userd_id = :user_id
    ');
    $deleteQuery->bindValue(':user_id', $userId, SQLITE3_INTEGER);
    $deleteQuery->execute();

    // Insertar los nuevos datos en la tabla datos_usuario
    $insertQuery = $db->prepare('
        INSERT INTO datos_usuario (userd_id, telefono, direccion)
        VALUES (:user_id, :telefono, :direccion)
    ');
    $insertQuery->bindValue(':user_id', $userId, SQLITE3_INTEGER);
    $insertQuery->bindValue(':telefono', $telefono, SQLITE3_TEXT);
    $insertQuery->bindValue(':direccion', $direccion, SQLITE3_TEXT);

    if ($insertQuery->execute()) {
        header('Location: datos_cliente.php'); // Redirigir a la página de editar información
        exit();
    } else {
        echo "<p>Error al agregar la información.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Información - One Water</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Enlace a estilos personalizados -->
    <link rel="stylesheet" href="css/agregar_informacion.css">
</head>
<body>

<!-- Encabezado -->
<header onclick="location.href='datos_cliente.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<!-- Contenido de agregar información -->
<section class="add-info">
    <h1>Agregar Información</h1>
    
    <form method="POST" action="">
        <label for="username">Nombre de Usuario:</label>
        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($currentUsername); ?>" required>
        
        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" id="telefono" required>
        
        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" id="direccion" required>
        
        <button type="submit">Guardar Información</button>
    </form>
</section>

</body>
</html>
