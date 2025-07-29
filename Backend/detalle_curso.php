<?php
require 'database.php';

header('Content-Type: application/json');

$curso_id = $_GET['id'] ?? 1;

try {
    // Obtener información completa del curso
    $stmt = $conn->prepare("SELECT c.*, 
                          CONCAT(u.nombre, ' ', u.apellidos) as profesor,
                          u.idu as id_profesor
                          FROM cursos c
                          JOIN usuarios u ON c.id_profesor = u.idu
                          WHERE c.idc = ?");
    $stmt->execute([$curso_id]);
    $curso = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$curso) {
        echo json_encode(['error' => 'Curso no encontrado']);
        exit;
    }

    // Obtener clases del curso
    $stmt = $conn->prepare("SELECT * FROM clases WHERE idc = ? ORDER BY orden");
    $stmt->execute([$curso_id]);
    $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener valoración promedio y conteo
    $stmt = $conn->prepare("SELECT 
                           AVG(puntuacion) as promedio, 
                           COUNT(*) as total_valoraciones
                           FROM valoraciones 
                           WHERE idc = ?");
    $stmt->execute([$curso_id]);
    $valoracion_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener valoraciones existentes con info de usuarios
    $stmt = $conn->prepare("SELECT v.*, 
                           CONCAT(u.nombre, ' ', u.apellidos) as usuario_nombre
                           FROM valoraciones v
                           JOIN usuarios u ON v.idu = u.idu
                           WHERE v.idc = ?
                           ORDER BY v.fecha DESC
                           LIMIT 5");
    $stmt->execute([$curso_id]);
    $valoraciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Preparar respuesta
    $response = [
        'idc' => $curso['idc'],
        'titulo' => $curso['titulo'],
        'descripcion' => $curso['descripcion'],
        'profesor' => $curso['profesor'],
        'id_profesor' => $curso['id_profesor'],
        'precio' => (float)$curso['precio'],
        'es_gratis' => (bool)$curso['es_gratis'],
        'categoria' => $curso['categoria'],
        'fecha_creacion' => $curso['fecha_creacion'],
        'valoracion' => number_format($valoracion_data['promedio'] ?? 0, 1),
        'num_valoraciones' => (int)$valoracion_data['total_valoraciones'] ?? 0,
        'clases' => $clases,
        'valoraciones' => $valoraciones
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Error de base de datos',
        'message' => $e->getMessage()
    ]);
}
?>