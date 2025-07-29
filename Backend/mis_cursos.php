<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
  header("Location: login.php");
  exit();
}

require_once 'backend/conexion.php';

// Obtener ID del usuario desde la sesión
$idUsuario = $_SESSION['id_usuario'];

try {
  $stmt = $conn->prepare("
    SELECT c.nombre, c.descripcion
    FROM cursos c
    INNER JOIN compras co ON c.id = co.curso_id
    WHERE co.usuario_id = ?
  ");
  $stmt->execute([$idUsuario]);
  $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error al obtener los cursos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mis Cursos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <?php include 'components/header.html'; ?>

  <div class="container py-5">
    <h1 class="mb-4 text-center">Mis Cursos</h1>

    <div id="cursos-container" class="row gy-4">
      <?php if (empty($cursos)) : ?>
        <div class="col-12 text-center">
          <div class="alert alert-info">No has comprado ningún curso aún.</div>
        </div>
      <?php else : ?>
        <?php foreach ($cursos as $curso) : ?>
          <div class="col-md-6">
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($curso['nombre']) ?></h5>
                <p class="card-text"><?= htmlspecialchars($curso['descripcion']) ?></p>
                <a href="#" class="btn btn-primary">Ir al curso</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
