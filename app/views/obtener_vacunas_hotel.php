<?php
require_once __DIR__ . '/../models/Hotel.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'ok' => false,
        'errores' => ['Método no permitido.'],
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$idMascota = filter_var($_POST['id_mascota'] ?? null, FILTER_VALIDATE_INT);
$fechaSalida = trim($_POST['fecha_salida'] ?? '');

if ($idMascota === false || $idMascota === null || $idMascota < 1) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'errores' => ['Selecciona una mascota válida.'],
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$resultado = Hotel::validarVacunasMascota($idMascota, $fechaSalida);

if (!$resultado['consulta_ok']) {
    http_response_code(500);
}

echo json_encode([
    'ok' => $resultado['consulta_ok'],
    'valida' => $resultado['valida'],
    'vacunas' => $resultado['vacunas'],
    'errores' => $resultado['errores'],
], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
