<?php
session_start();
require 'database.php'; // Asegúrate de que este archivo existe y se conecta bien

$accion = $_POST['accion'] ?? null;

if ($accion == 'crear_curso') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];
    $usuario_id = $_SESSION['id'] ?? 1; // Temporal (cambiar por el ID del usuario logueado)

    // Subir imagen (local)
    $imagen = $_FILES['imagen']['name'];
    $ruta_temporal = $_FILES['imagen']['tmp_name'];
    $ruta_final = "../imagenes/" . $imagen;
    move_uploaded_file($ruta_temporal, $ruta_final);

    $stmt = $conn->prepare("INSERT INTO cursos (titulo, descripcion, precio, categoria, imagen, usuario_id)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$titulo, $descripcion, $precio, $categoria, $ruta_final, $usuario_id]);

    echo "Curso creado correctamente";
}

if ($accion == 'crear_clase') {
    $curso_id = $_POST['curso_id'];
    $titulo_clase = $_POST['titulo_clase'];
    $tipo = $_POST['tipo'];
    $url = $_POST['url'];
    $usuario_id = $_SESSION['id'] ?? 1;

    // Validar que el curso sea del usuario
    $stmt = $conn->prepare("SELECT * FROM cursos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$curso_id, $usuario_id]);
    if ($stmt->rowCount() == 0) {
        die("No tienes permiso para agregar clases a este curso.");
    }

    $stmt = $conn->prepare("INSERT INTO clases (curso_id, titulo, tipo, url)
                            VALUES (?, ?, ?, ?)");
    $stmt->execute([$curso_id, $titulo_clase, $tipo, $url]);

    echo "Clase añadida correctamente";
}
?>
