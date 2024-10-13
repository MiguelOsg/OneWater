<?php
// Archivo: register.php

// Comprobar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conexión a la base de datos SQLite
    $db = new SQLite3('db/database.sqlite');

    // Obtener los datos del formulario
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Cifrar la contraseña
    $role = 'client'; // Asignar rol predeterminado

    // Validar que los campos no estén vacíos
    if (empty($username) || empty($email) || empty($password)) {
        echo "<p>Todos los campos son obligatorios.</p>";
        exit();
    }

    // Consulta para insertar el nuevo usuario
    $query = $db->prepare('INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)');
    $query->bindValue(':username', $username, SQLITE3_TEXT);
    $query->bindValue(':email', $email, SQLITE3_TEXT);
    $query->bindValue(':password', $password, SQLITE3_TEXT);
    $query->bindValue(':role', $role, SQLITE3_TEXT); // Asignar el rol

    // Ejecutar la consulta
    if ($query->execute()) {
        header('Location: formulario.php'); // Redirigir a la página de cuenta

    } else {
        echo "<p>Error al registrar el usuario: " . $db->lastErrorMsg() . "</p>";
    }

    // Cerrar la conexión a la base de datos
    $db->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - One Water</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/formulario.css"> <!-- Estilos CSS -->
</head>
<body>

<!-- Encabezado que redirige a index.php al hacer clic -->
<header onclick="location.href='formulario.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<!-- Contenedor principal del registro -->
<section class="login-section">
    <div class="login-container">
        <h2>Registro en One Water</h2>
        
        <form action="register.php" method="post">
            <div class="input-group">
                <label for="username">Nombre de usuario</label>
                <input type="text" id="username" name="username" placeholder="Nombre de usuario" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Correo electrónico" required>
            </div>
            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn-login">Registrar</button>
        </form>
        
        <div class="extra-links">
            <a href="formulario.php">¿Ya tienes una cuenta? Inicia sesión aquí</a>
        </div>
    </div>
</section>

</body>
</html>
