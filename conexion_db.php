<?php

$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $lineas = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        $lineaTrime = trim($linea);
        if ($lineaTrime === '' || str_starts_with($lineaTrime, '#')) continue;
        $partes = explode('=', $linea, 2);
        if (count($partes) === 2) {
            $clave = trim($partes[0]);
            $valor = trim($partes[1], " \"'");
            putenv("{$clave}={$valor}");
            $_ENV[$clave] = $valor;
            $_SERVER[$clave] = $valor;
        }
    }
}

$server = getenv('DB_HOST') ?: "localhost";
$user = getenv('DB_USER') ?: "root";
$passwword = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";
$db = getenv('DB_NAME') ?: "petcare";

$conexion = new mysqli($server, $user, $passwword, $db);

mysqli_set_charset($conexion, "utf8");