<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicia Sesión - One Water</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/formulario.css"> <!-- Estilos CSS -->
</head>
<body>

<header onclick="location.href='index.php'" style="cursor: pointer;">
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<section class="login-section">
    <div class="login-container">
        <h2>Inicia sesión en One Water</h2>
        
        <form action="login.php" method="post">
            <div class="input-group">
                <label for="email">Email o nombre de usuario</label>
                <input type="text" id="email" name="email" placeholder="Email o nombre de usuario" required>
            </div>
            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>
        
        <div class="extra-links">
            <a href="register.php">¿No tienes una cuenta? Registrarse en One Water</a>
        </div>
    </div>
</section>

</body>
</html>
