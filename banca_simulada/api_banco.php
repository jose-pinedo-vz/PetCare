<?php
header('Content-Type: application/json');
require_once 'conexion_banco.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $tarjeta=str_replace([' ', '-'], '', trim($_POST['card_number'] ?? ''));
    $titular=trim($_POST['card_name'] ?? '');
    $expiracion=trim($_POST['card_expiry'] ?? '');
    $cvv=trim($_POST['card_cvv'] ?? '');
    $monto=floatval($_POST['monto'] ?? 0);
    $comercio=trim($_POST['comercio'] ?? 'Atención Veterinaria');
    $modoManual=$_POST['bank_response'] ?? 'auto';

    if (empty($tarjeta) || empty($expiracion) || empty($cvv) || $monto <= 0)
    {
        echo json_encode(['status' => 'declined', 'codigo' => '14', 'mensaje' => 'Datos de tarjeta o monto inválidos.']);
        exit;
    }
    $stmt=$pdoBanco->prepare("SELECT * FROM cuentas_bancarias WHERE numero_tarjeta=?");
    $stmt->execute([$tarjeta]);
    $cuenta=$stmt->fetch();

    $bloqueTiempo=floor(time() / 300);
    $hash=hash_hmac('sha256', $tarjeta . $bloqueTiempo, 'ClaveSecretaBanco123');
    $cvvEsperado=str_pad(hexdec(substr($hash, 0, 4)) % 1000, 3, '0', STR_PAD_LEFT);

    $codigoRespuesta='96';
    $estadoPago='Rechazado';
    $mensaje='';

    if ($modoManual === 'decline')
    {
        $codigoRespuesta='51';
        $mensaje='Transacción rechazada: Fondos insuficientes (Simulado).';
    }
    elseif ($modoManual === 'error')
    {
        $codigoRespuesta='96';
        $mensaje='Error del sistema bancario / Sin conexión (Simulado).';
    }
    else
    {
        if (!$cuenta)
        {
            $codigoRespuesta='14';
            $mensaje='Tarjeta no encontrada en el banco.';
        }
        elseif ($cuenta['estado'] !== 'activa')
        {
            $codigoRespuesta='05';
            $mensaje='Tarjeta bloqueada por el banco.';
        }
        elseif ($cuenta['expiracion'] !== $expiracion || $cvv !== $cvvEsperado)
        {
            $codigoRespuesta='55';
            $mensaje='Datos de la tarjeta incorrectos o el CVV dinámico ha expirado.';
        }
        elseif ($cuenta['saldo'] < $monto)
        {
            $codigoRespuesta='51';
            $mensaje='Transacción rechazada: Fondos insuficientes.';
        }
        else
        {
            $codigoRespuesta='00';
            $estadoPago='Autorizado';
            $mensaje='¡Transacción autorizada con éxito!';

            $nuevoSaldo=$cuenta['saldo'] - $monto;
            $updateStmt=$pdoBanco->prepare("UPDATE cuentas_bancarias SET saldo=? WHERE id=?");
            $updateStmt->execute([$nuevoSaldo, $cuenta['id']]);
        }
    }
    $prefijo=($estadoPago === 'Autorizado') ? 'AUT' : 'REC';
    $codigoAleatorio=strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    $folio=$prefijo . '-' . $codigoAleatorio;

    $logStmt=$pdoBanco->prepare("INSERT INTO transacciones_bancarias (folio, numero_tarjeta, monto, comercio, codigo_respuesta, estado_transaccion) VALUES (?, ?, ?, ?, ?, ?)");
    $logStmt->execute([$folio, $tarjeta, $monto, $comercio, $codigoRespuesta, $estadoPago]);

    $saldoActual=$cuenta ? ($estadoPago === 'Autorizado' ? $cuenta['saldo'] - $monto : $cuenta['saldo']) : 0;

    $carpetaComprobantes=__DIR__ . '/comprobantes/';
    if (!file_exists($carpetaComprobantes))
    {
        @mkdir($carpetaComprobantes, 0777, true);
    }
    $nombreArchivo=$carpetaComprobantes . "comprobante_" . $folio . ".txt";
    $contenidoText="========================================\n";
    $contenidoText.="     BANCO EMISOR - COMPROBANTE        \n";
    $contenidoText.="========================================\n";
    $contenidoText.="FOLIO OPERACIÓN : " . $folio . "\n";
    $contenidoText.="ESTADO          : " . strtoupper($estadoPago) . "\n";
    $contenidoText.="COMERCIO        : " . $comercio . "\n";
    $contenidoText.="TITULAR         : " . $titular . "\n";
    $contenidoText.="TARJETA         : **** **** **** " . substr($tarjeta, -4) . "\n";
    $contenidoText.="MONTO           : $" . number_format($monto, 2) . " MXN\n";
    $contenidoText.="FECHA Y HORA    : " . date('Y-m-d H:i:s') . "\n";
    $contenidoText.="========================================\n";
    $contenidoText.="MENSAJE BANCO   : " . $mensaje . "\n";
    $contenidoText.="========================================\n";
    @file_put_contents($nombreArchivo, $contenidoText);
    $rutaRelativaArchivo="comprobantes/comprobante_" . $folio . ".txt";

    echo json_encode([
        'status' => ($estadoPago === 'Autorizado') ? 'success' : 'declined',
        'folio' => $folio,
        'archivo' => $rutaRelativaArchivo,
        'codigo_respuesta' => $codigoRespuesta,
        'mensaje' => $mensaje,
        'nuevo_saldo' => number_format($saldoActual, 2, '.', ',')
    ]);
}
?>