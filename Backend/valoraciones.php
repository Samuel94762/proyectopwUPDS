<?php
require 'database.php';
session_start();

if (!isset($_SESSION['idu'])) {
    header("Location: ../Frontend/iniciosession.html");
    exit;
}

// Validar campos obligatorios
if (!isset($_POST['idc'], $_POST['puntuacion'], $_POST['comentario'])) {
    echo "Faltan datos obligatorios.";
    exit;
}

$idu = $_SESSION['idu'];
$idc = $_POST['idc'];
$puntuacion = $_POST['puntuacion'];
$comentario = $_POST['comentario'];

// Insertar o actualizar la valoración
$stmt = $conn->prepare("INSERT INTO valoraciones (idu, idc, puntuacion, comentario, fecha)
VALUES (?, ?, ?, ?, NOW())
ON DUPLICATE KEY UPDATE puntuacion = VALUES(puntuacion), comentario = VALUES(comentario), fecha = NOW()");
$stmt->execute([$idu, $idc, $puntuacion, $comentario]);

// Redirigir de vuelta al detalle del curso
header("Location: ../Frontend/detalle_curso.html?idc=" . $idc);
exit;
