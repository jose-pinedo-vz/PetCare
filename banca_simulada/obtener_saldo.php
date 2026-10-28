<?php
header('Content-Type: application/json');
require_once 'conexion_banco.php';

$tarjeta = $_GET['tarjeta'] ?? '4500123456789010';
$stmt = $pdoBanco->prepare("SELECT saldo, titular FROM cuentas_bancarias WHERE numero_tarjeta = ?");
$stmt->execute([$tarjeta]);
$cuenta = $stmt->fetch();

if ($cuenta) {
    echo json_encode([
        'status' => 'success',
        'saldo' => number_format($cuenta['saldo'], 2, '.', ','),
        'titular' => $cuenta['titular']
    ]);
} else {
    echo json_encode(['status' => 'error', 'saldo' => '0.00']);
}
?>