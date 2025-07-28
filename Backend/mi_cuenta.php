<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    // Verifica que esta ruta sea correcta desde la ubicación de este script
    header("Location: ../Frontend/iniciosession.html");
    exit();
}

require_once 'database.php';

// Verifica que idusuario esté definido en la sesión
if (!isset($_SESSION['idusuario'])) {
    die("Error: ID de usuario no definido en la sesión");
}

$idusuario = $_SESSION['idusuario'];

try {
    $stmt = $conn->prepare("SELECT u.nombre, u.apellidos, u.correo, r.nombre AS rol
                            FROM usuarios u
                            JOIN roles r ON u.idr = r.idr
                            WHERE u.idu = :idusuario");
    $stmt->bindParam(':idusuario', $idusuario);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        die("Error: Usuario no encontrado");
    }
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Cuenta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #6c5ce7, #00b894);
      color: #fff;
      font-family: Arial, sans-serif;
    }
    .container {
      margin-top: 100px;
      max-width: 500px;
      background-color: rgba(255, 255, 255, 0.1);
      padding: 30px;
      border-radius: 10px;
    }
    .container h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .dato {
      margin-bottom: 15px;
    }
    .dato strong {
      display: inline-block;
      width: 120px;
    }
  </style>
</head>
<body>
    <?php 
    // Usa solo una forma de incluir el header
    // Verifica que esta ruta sea correcta
    include('../components/header.html'); 
    ?>

  <div class="container">
    <h2>Mi Cuenta</h2>
    <div class="dato"><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?></div>
    <div class="dato"><strong>Apellidos:</strong> <?= htmlspecialchars($usuario['apellidos']) ?></div>
    <div class="dato"><strong>Correo:</strong> <?= htmlspecialchars($usuario['correo']) ?></div>
    <div class="dato"><strong>Rol:</strong> <?= htmlspecialchars($usuario['rol']) ?></div>
  </div>

</body>
</html>