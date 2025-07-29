<?php
session_start();
require_once 'database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit();
}

if (!isset($_SESSION['idu'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit();
}

$usuario_id = $_SESSION['idu'];
$curso_id = $_POST['curso_id'] ?? null;

if (!$curso_id) {
    echo json_encode(['success' => false, 'error' => 'ID del curso no proporcionado']);
    exit();
}

try {
    // Obtener precio del curso
    $stmt = $conn->prepare("SELECT precio FROM cursos WHERE idc = ?");
    $stmt->execute([$curso_id]);
    $curso = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$curso) {
        echo json_encode(['success' => false, 'error' => 'Curso no encontrado']);
        exit();
    }

    $precio = (float)$curso['precio'];

    // Aplicar descuentos si quieres, por ejemplo, 20% si es usuario premium (opcional)
    // Aquí no tienes campo 'plan' en usuarios, puedes adaptar si tienes esa info
    $descuento = 0;
    // Ejemplo:
    // $stmt = $conn->prepare("SELECT idp FROM usuarios WHERE idu = ?");
    // $stmt->execute([$usuario_id]);
    // $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    // if ($usuario['idp'] == 2) { // idp 2 = premium
    //     $descuento = $precio * 0.2;
    // }
    $precio_final = $precio - $descuento;

    // Calcular comisión y pago profesor (por ejemplo 10% comisión)
    $comision = $precio_final * 0.10;
    $pago_profesor = $precio_final - $comision;

    // Insertar compra en compras_cursos
    $stmt = $conn->prepare("INSERT INTO compras_cursos (
        idu, idc, monto_pagado, descuento_aplicado, comision, pago_profesor, estado
    ) VALUES (?, ?, ?, ?, ?, ?, 'completado')");

    $stmt->execute([$usuario_id, $curso_id, $precio_final, $descuento, $comision, $pago_profesor]);

    echo json_encode(['success' => true, 'message' => 'Compra realizada con éxito', 'monto_pagado' => $precio_final]);

} catch (PDOException $e) {
    error_log("Error procesar_pago.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error en la base de datos', 'details' => $e->getMessage()]);
}
?>
