<?php
$host = 'localhost';
$db   = 'banco_emisor';
$user = 'root';
$pass = '';

try {
    $pdoBanco = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die(json_encode(['status' => 'error', 'codigo' => '96', 'mensaje' => 'Error de conexión con el Banco Emisor']));
}
?>