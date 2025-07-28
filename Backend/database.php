<?php
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD']);

try {
    $conn = new PDO(
        "mysql:host={$_ENV['DB_HOST']};port=3307;dbname={$_ENV['DB_NAME']}", 
        $_ENV['DB_USER'], 
        $_ENV['DB_PASSWORD']
    );
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
