<?php
header('Content-Type: application/json');
require_once 'database.php';

try {
    // Obtener parámetros de filtrado
    $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;
    
    // Construir consulta SQL según tu estructura de base de datos
    $sql = "SELECT 
                c.idc as id,
                c.titulo, 
                c.descripcion, 
                c.precio, 
                '' as imagen_url, // Tu tabla no tiene este campo, puedes agregarlo o dejarlo vacío
                c.valoracion as rating, 
                c.categoria,
                CASE WHEN c.es_gratis = 1 THEN 'gratis' ELSE 'pago' END as tipo,
                CONCAT(u.nombre, ' ', u.apellidos) as instructor
            FROM cursos c
            JOIN usuarios u ON c.id_profesor = u.idu
            WHERE 1=1";
    
    $params = [];
    
    // Aplicar filtros
    if ($categoria) {
        $sql .= " AND c.categoria = :categoria";
        $params[':categoria'] = $categoria;
    }
    
    if ($tipo === 'gratis') {
        $sql .= " AND c.es_gratis = 1";
    } elseif ($tipo === 'pago') {
        $sql .= " AND c.es_gratis = 0";
    }
    
    $sql .= " ORDER BY c.fecha_creacion DESC";
    
    // Ejecutar consulta
    $stmt = $conn->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener categorías disponibles
    $stmt = $conn->prepare("SELECT DISTINCT categoria FROM cursos WHERE categoria IS NOT NULL");
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode([
        'success' => true,
        'data' => $cursos,
        'categorias' => $categorias,
        'count' => count($cursos)
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener los cursos: ' . $e->getMessage()
    ]);
}
?>