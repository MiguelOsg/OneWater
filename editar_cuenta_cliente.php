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

// Consulta para obtener la información del usuario
$query = $db->prepare('
    SELECT email 
    FROM users 
    WHERE id = :user_id
');
$query->bindValue(':user_id', $userId, SQLITE3_INTEGER);
$result = $query->execute();

$userInfo = $result->fetchArray(SQLITE3_ASSOC);

// Procesar el formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cifrar la nueva contraseña
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Actualizar la información del usuario
    $updateQuery = $db->prepare('
        UPDATE users 
        SET email = :email, password = :password 
        WHERE id = :user_id
    ');
    $updateQuery->bindValue(':email', $email, SQLITE3_TEXT);
    $updateQuery->bindValue(':password', $hashedPassword, SQLITE3_TEXT);
    $updateQuery->bindValue(':user_id', $userId, SQLITE3_INTEGER);

    if ($updateQuery->execute()) {
        echo "<script>alert('Información actualizada exitosamente.');</script>";
        header('Location: datos_cliente.php'); // Redirigir a la página de cuenta
        exit();
    } else {
        echo "<script>alert('Error al actualizar la información.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cuenta - One Water</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/editar_cuenta_cliente.css"> <!-- Enlace al CSS de editar información -->
</head>
<body>

<!-- Encabezado -->
<header onclick="location.href='datos_cliente.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<!-- Formulario de edición de información -->
<section class="edit-info">
    <h1>Editar Información de Cuenta</h1>
    <form method="POST">

        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userInfo['email']); ?>" required>

        <label for="password">Nueva Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Actualizar Información</button>
    </form>
</section>

</body>
</html>
