<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirigir si no es administrador
    exit();
}

// Conexión a la base de datos
$db = new SQLite3('db/database.sqlite');

// Obtener el ID del cliente a modificar
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Consulta para obtener los datos del usuario
    $query = $db->prepare('
        SELECT u.id, u.username, u.email, du.telefono, du.direccion, t.titular, t.numeracion, t.fecha_caducidad, t.ccv
        FROM users u
        LEFT JOIN datos_usuario du ON u.id = du.userd_id
        LEFT JOIN tarjetas_usuarios t ON u.id = t.usert_id
        WHERE u.id = :userd_id
    ');
    $query->bindValue(':userd_id', $userId, SQLITE3_INTEGER);
    $result = $query->execute();
    $userInfo = $result->fetchArray(SQLITE3_ASSOC);

    if (!$userInfo) {
        echo "<p>Usuario no encontrado.</p>";
        exit();
    }
} else {
    echo "<p>ID de usuario no proporcionado.</p>";
    exit();
}

// Procesar el formulario al enviarlo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $titular = $_POST['titular'];
    $numeracion = $_POST['numeracion'];
    $mes_caducidad = $_POST['mes_caducidad'];
    $anio_caducidad = $_POST['anio_caducidad'];
    $ccv = $_POST['ccv'];
    $password = $_POST['password'];

    // Actualizar datos en la tabla 'users'
    $updateUserQuery = $db->prepare('
        UPDATE users SET username = :username, email = :email WHERE id = :userd_id
    ');
    $updateUserQuery->bindValue(':username', $username, SQLITE3_TEXT);
    $updateUserQuery->bindValue(':email', $email, SQLITE3_TEXT);
    $updateUserQuery->bindValue(':userd_id', $userId, SQLITE3_INTEGER);
    $updateUserQuery->execute();

    // Actualizar la contraseña si se proporciona
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updatePasswordQuery = $db->prepare('
            UPDATE users SET password = :password WHERE id = :userd_id
        ');
        $updatePasswordQuery->bindValue(':password', $hashedPassword, SQLITE3_TEXT);
        $updatePasswordQuery->bindValue(':userd_id', $userId, SQLITE3_INTEGER);
        $updatePasswordQuery->execute();
    }

    // Actualizar 'datos_usuario'
    $updateDatosQuery = $db->prepare('
        INSERT INTO datos_usuario (userd_id, telefono, direccion) 
        VALUES (:userd_id, :telefono, :direccion)
        ON CONFLICT(userd_id) DO UPDATE SET telefono = :telefono, direccion = :direccion
    ');
    $updateDatosQuery->bindValue(':userd_id', $userId, SQLITE3_INTEGER);
    $updateDatosQuery->bindValue(':telefono', $telefono, SQLITE3_TEXT);
    $updateDatosQuery->bindValue(':direccion', $direccion, SQLITE3_TEXT);
    $updateDatosQuery->execute();

    // Actualizar 'tarjetas_usuarios'
    $updateTarjetaQuery = $db->prepare('
        INSERT INTO tarjetas_usuarios (usert_id, titular, numeracion, fecha_caducidad, ccv) 
        VALUES (:usert_id, :titular, :numeracion, :fecha_caducidad, :ccv)
        ON CONFLICT(usert_id) DO UPDATE SET 
            titular = excluded.titular, 
            numeracion = excluded.numeracion, 
            fecha_caducidad = excluded.fecha_caducidad, 
            ccv = excluded.ccv
    ');

    $updateTarjetaQuery->bindValue(':usert_id', $userId, SQLITE3_INTEGER);
    $updateTarjetaQuery->bindValue(':titular', $titular, SQLITE3_TEXT);
    $updateTarjetaQuery->bindValue(':numeracion', $numeracion, SQLITE3_TEXT);
    $fecha_caducidad = $mes_caducidad . '/' . $anio_caducidad; // Concatenar mes y año
    $updateTarjetaQuery->bindValue(':fecha_caducidad', $fecha_caducidad, SQLITE3_TEXT);
    $updateTarjetaQuery->bindValue(':ccv', $ccv, SQLITE3_TEXT);
    $updateTarjetaQuery->execute();

    echo "<script>alert('Datos actualizados exitosamente.');</script>";
    header('Location: admin_clientes.php'); // Redirigir a la página de administración
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente - One Water</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_editar_cliente.css"> <!-- Enlace al CSS -->
</head>
<body>

<header onclick="location.href='admin_clientes.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<section class="edit-client-section">
    <div class="content">
        <h2>Información del Cliente</h2>
        <form method="POST" action="">
            <label for="username">Nombre de Usuario:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userInfo['username']); ?>" required>

            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userInfo['email']); ?>" required>

            <label for="password">Nueva Contraseña:</label>
            <input type="password" id="password" name="password" placeholder="Introduce nueva contraseña (opcional)">

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($userInfo['telefono']); ?>" required>

            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($userInfo['direccion']); ?>" required>

            <h2>Información de la Tarjeta</h2>
            <label for="titular">Titular:</label>
            <input type="text" id="titular" name="titular" value="<?php echo htmlspecialchars($userInfo['titular']); ?>" required>

            <label for="numeracion">Número de Tarjeta:</label>
            <input type="text" id="numeracion" name="numeracion" value="<?php echo htmlspecialchars($userInfo['numeracion']); ?>" required>

            <label for="mes_caducidad">Mes de Caducidad (MM):</label>
            <input type="text" id="mes_caducidad" name="mes_caducidad" value="<?php echo isset($userInfo['fecha_caducidad']) ? explode('/', $userInfo['fecha_caducidad'])[0] : ''; ?>" required placeholder="Ej: 09">

            <label for="anio_caducidad">Año de Caducidad (YYYY):</label>
            <input type="text" id="anio_caducidad" name="anio_caducidad" value="<?php echo isset($userInfo['fecha_caducidad']) ? explode('/', $userInfo['fecha_caducidad'])[1] : ''; ?>" required placeholder="Ej: 2024">

            <label for="ccv">CCV:</label>
            <input type="text" id="ccv" name="ccv" value="<?php echo htmlspecialchars($userInfo['ccv']); ?>" required>

            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</section>

</body>
</html>
