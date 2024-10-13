<?php
session_start(); // Iniciar la sesión

// Verificar si el usuario ha iniciado sesión
$isLoggedIn = isset($_SESSION['username']);
$isAdmin = $isLoggedIn && $_SESSION['role'] === 'admin'; // Verificar si el usuario es administrador
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One Water</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Enlace a estilos personalizados -->
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<!-- Encabezado con imagen de fondo -->
<header>
    <img src="img/logo.png" alt="Logo One Water" class="logo">
</header>

<!-- Icono de menú de hamburguesa que redirige a formulario.php -->
<?php if (!$isLoggedIn): ?>
    <div class="menu-icon" onclick="location.href='formulario.php'">
        <i class="fas fa-bars"></i> <!-- Icono de tres líneas -->
    </div>
<?php else: ?>
    <!-- Botón para ir al panel de usuario o administrador -->
    <?php if ($isAdmin): ?>
        <div class="menu-icon" onclick="location.href='admin_dashboard.php'">
            <i class="fas fa-user-shield"></i> <!-- Icono de usuario administrador -->
        </div>
    <?php else: ?>
        <div class="menu-icon" onclick="location.href='dashboard_cliente.php'">
            <i class="fas fa-user"></i> <!-- Icono de usuario cliente -->
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Sección de contenido principal -->
<section>
    <div class="content">
        <h2>La evolución del agua — perfeccionada para ayudarte a prosperar.</h2>
    <!-- Tarjeta con imagen local que redirige a vender.php si el usuario está autenticado -->
    <div class="card">
        <img src="img/OneWaterAdds.png" alt="Publicidad de One Water" class="imagen-card" id="comprarImagen">
    </div>

    <script type="text/javascript">
        // Verificar si el usuario ha iniciado sesión
        var isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
        
        // Añadir un evento al hacer clic en la imagen
        document.getElementById('comprarImagen').addEventListener('click', function() {
            if (!isLoggedIn) {
                alert("Por favor, inicia sesión o regístrate para realizar una compra.");
            } else {
                // Redirigir a la página de ventas si el usuario ha iniciado sesión
                window.location.href = "vender.php";
            }
        });
    </script>

        <!-- Sección de iconos -->
        <div class="features">
            <div class="feature">
                <img src="img/certificacion.png" alt="ISO 22000" class="feature-icon">
                <p>ISO 22000: Certificación por cumplir con estándares de seguridad alimentaria.</p>
            </div>
            <div class="feature">
                <img src="img/certificacion.png" alt="NSF International" class="feature-icon">
                <p>NSF International: Reconocimiento de pureza y calidad del agua.</p>
            </div>
            <div class="feature">
                <img src="img/certificacion.png" alt="FDA" class="feature-icon">
                <p>FDA: Aprobación para cumplir normativas de seguridad en EE.UU.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pie de página -->
<footer>
    <p>© 2024 One Water. Todos los derechos reservados.</p>
</footer>

<script type="text/javascript">
  (function(d, t) {
      var v = d.createElement(t), s = d.getElementsByTagName(t)[0];
      v.onload = function() {
        window.voiceflow.chat.load({
          verify: { projectID: '67080bd5a410ad0f945dbccb' },
          url: 'https://general-runtime.voiceflow.com',
          versionID: 'production'
        });
      }
      v.src = "https://cdn.voiceflow.com/widget/bundle.mjs"; v.type = "text/javascript"; s.parentNode.insertBefore(v, s);
  })(document, 'script');
</script>
</body>
</html>
