<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['usuario_id']) || !isset($_POST['curso_id'])) {
    header("Location: login.html");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$curso_id = $_POST['curso_id'];

// Verificar si el usuario ya compró el curso
$stmt = $conn->prepare("SELECT COUNT(*) FROM compras WHERE usuario_id = ? AND curso_id = ?");
$stmt->execute([$usuario_id, $curso_id]);
$ya_compro = $stmt->fetchColumn();

if ($ya_compro > 0) {
    header("Location: ../Frontend/mis_cursos.html?info=ya_comprado");
    exit();
}

// Obtener precio del curso
$stmt = $conn->prepare("SELECT precio FROM cursos WHERE id = ?");
$stmt->execute([$curso_id]);
$curso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$curso) {
    // Curso no existe
    header("Location: ../Frontend/detalle_curso.html?error=curso_no_existe");
    exit();
}

$precio = floatval($curso['precio']);

// Verificar si el usuario es premium
$stmt = $conn->prepare("SELECT es_premium FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$descuento = 0;
if (!empty($usuario['es_premium'])) {
    $descuento = $precio * 0.2;
}

$precio_final = $precio - $descuento;

// Registrar compra
$stmt = $conn->prepare("INSERT INTO compras (usuario_id, curso_id, precio_pagado) VALUES (?, ?, ?)");
$stmt->execute([$usuario_id, $curso_id, $precio_final]);

header("Location: ../Frontend/mis_cursos.html?exito=compra");
exit();
?>
