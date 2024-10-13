<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'client') {
    header('Location: index.php'); // Redirigir si no es cliente
    exit();
}

$db = new SQLite3('db/database.sqlite');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numeracion = $_POST['numeracion'];

    // Consulta para eliminar la tarjeta
    $deleteQuery = $db->prepare('DELETE FROM tarjetas_usuarios WHERE numeracion = :numeracion');
    $deleteQuery->bindValue(':numeracion', $numeracion, SQLITE3_TEXT);
    $deleteQuery->execute();

    header('Location: tarjetas_cliente.php'); // Redirigir de nuevo a la página de tarjetas
}
?>
