<?php
header('Content-Type: application/json; charset=utf-8');

function responder(string $status, string $mensaje, int $codigo = 200): void {
    http_response_code($codigo);
    echo json_encode(['status' => $status, 'mensaje' => $mensaje], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder('error', 'Método no permitido.', 405);
}

require_once 'conexion_banco.php';

$titular = trim((string)($_POST['titular'] ?? ''));
$numero = trim((string)($_POST['numero_tarjeta'] ?? ''));
$expiracion = trim((string)($_POST['expiracion'] ?? ''));
$cvv = trim((string)($_POST['cvv'] ?? ''));
$saldo = trim((string)($_POST['saldo'] ?? ''));

if ($titular === '' || mb_strlen($titular, 'UTF-8') > 100) {
    responder('error', 'El titular debe tener entre 1 y 100 caracteres.', 422);
}
if (!preg_match('/^[0-9]{16}$/D', $numero)) {
    responder('error', 'El número de tarjeta debe tener exactamente 16 dígitos.', 422);
}
if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/D', $expiracion, $partes)) {
    responder('error', 'La expiración debe tener formato MM/AA.', 422);
}
$anio = 2000 + (int)$partes[2];
$mes = (int)$partes[1];
$hoy = new DateTimeImmutable('now');
if ($anio < (int)$hoy->format('Y') || ($anio === (int)$hoy->format('Y') && $mes < (int)$hoy->format('n'))) {
    responder('error', 'La fecha de expiración no puede estar vencida.', 422);
}
if (!preg_match('/^[0-9]{3,4}$/D', $cvv)) {
    responder('error', 'El CVV ficticio debe tener 3 o 4 dígitos.', 422);
}
if (!preg_match('/^(?:0|[1-9][0-9]{0,7})(?:\.[0-9]{1,2})?$/D', $saldo)) {
    responder('error', 'El saldo debe estar entre 0 y 99,999,999.99, con máximo dos decimales.', 422);
}

try {
    $consulta = $pdoBanco->prepare(
        "INSERT INTO cuentas_bancarias (titular, numero_tarjeta, expiracion, cvv, saldo, estado)
         VALUES (?, ?, ?, ?, ?, 'activa')"
    );
    $consulta->execute([$titular, $numero, $expiracion, $cvv, $saldo]);
    responder('success', 'Cuenta ficticia creada correctamente.');
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        responder('error', 'Ya existe una cuenta con ese número de tarjeta.', 409);
    }
    responder('error', 'Ocurrió un error al guardar la cuenta.', 500);
}
