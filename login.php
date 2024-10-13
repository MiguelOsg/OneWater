<?php
// Archivo: login.php

session_start(); // Iniciar sesión

// Conexión a la base de datos SQLite
$db = new SQLite3('db/database.sqlite');

// Obtener los datos enviados por el formulario
$email = $_POST['email'];
$password = $_POST['password'];

// Consulta para verificar si el usuario existe
$query = $db->prepare('SELECT * FROM users WHERE email = :email');
$query->bindValue(':email', $email, SQLITE3_TEXT);
$result = $query->execute();

// Verificar si se encontró un usuario
if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    // Verificar si la contraseña es correcta
    if (password_verify($password, $row['password'])) {
        // Inicio de sesión exitoso
        $_SESSION['username'] = $row['username'];
        $_SESSION['email'] = $row['email']; // Añadir el email a la sesión
        $_SESSION['role'] = $row['role']; // Añadir el rol a la sesión
        $_SESSION['id'] = $row['id']; // Almacenar el ID del usuario en la sesión

        if ($row['role'] === 'admin') {
            header('Location: admin_dashboard.php'); // Redirigir al dashboard de admin si es admin
        } else {
            header('Location: dashboard_cliente.php'); // Redirigir al dashboard de cliente si es cliente
        }
        exit();
    } else {
        // Contraseña incorrecta
        echo "Contraseña incorrecta.";
    }
} else {
    // Usuario no encontrado
    echo "No existe una cuenta con ese email.";
}

// Cerrar la conexión a la base de datos
$db->close();
?>
