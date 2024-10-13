<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'client') {
    header('Location: index.php'); // Redirigir si no es cliente
    exit();
}

// Aquí podrías añadir la lógica para obtener los datos del cliente
// Por ejemplo, consultas a la base de datos para recuperar la información necesaria
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos del Cliente - One Water</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Enlace a estilos personalizados -->
    <link rel="stylesheet" href="css/datos_cliente.css">
</head>
<body>

<!-- Encabezado -->
<header onclick="location.href='dashboard_cliente.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<!-- Sección de datos del cliente -->
<section class="customer-data">
    <h1>Datos Personales</h1>
    
    <div class="box">
        <h2>Información Personal</h2>
        <button onclick="location.href='editar_informacion_cliente.php'">Editar o Agregar</button>
        <button onclick="location.href='informacion_cliente.php'">Ver</button>
    </div>
    
    <div class="box">
        <h2>Cuenta</h2>
        <button onclick="location.href='editar_cuenta_cliente.php'">Editar</button>
        <button onclick="location.href='cuenta_cliente.php'">Ver</button>
    </div>
    
    <div class="box">
        <h2>Tarjetas</h2>
        <button onclick="location.href='agregar_tarjetas_cliente.php'">Agregar</button>
        <button onclick="location.href='tarjetas_cliente.php'">Ver o Editar</button>
    </div>
    

</section>

</body>
</html>
