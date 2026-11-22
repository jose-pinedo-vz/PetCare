<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/citas.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    
}

?>