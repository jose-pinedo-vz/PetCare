<?php
header('Content-Type: application/json');
require_once 'conexion_banco.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $tarjeta = str_replace([' ', '-'], '', trim($_POST['card_number'] ?? ''));    
    if (empty($tarjeta))
    {
        echo json_encode(['status' => 'error', 'mensaje' => 'Por favor ingresa un número de tarjeta.']);
        exit;
    }
    try
    {
        $stmt = $pdoBanco->prepare("SELECT titular, numero_tarjeta, expiracion, saldo, estado FROM cuentas_bancarias WHERE numero_tarjeta = ?");
        $stmt->execute([$tarjeta]);
        $cuenta = $stmt->fetch();
        if ($cuenta)
        {
            $bloqueTiempo = floor(time() / 300); 
            $hash = hash_hmac('sha256', $tarjeta . $bloqueTiempo, 'ClaveSecretaBanco123');
            $cvvDinamico = str_pad(hexdec(substr($hash, 0, 4)) % 1000, 3, '0', STR_PAD_LEFT);
            $segundosRestantes = 300 - (time() % 300);
            echo json_encode([
                'status' => 'success',
                'titular' => $cuenta['titular'],
                'numero_tarjeta' => chunk_split($cuenta['numero_tarjeta'], 4, ' '),
                'expiracion' => $cuenta['expiracion'],
                'cvv' => $cvvDinamico,
                'segundos_restantes' => $segundosRestantes,
                'saldo' => number_format($cuenta['saldo'], 2, '.', ','),
                'estado' => $cuenta['estado']
            ]);
        }
        else
        {
            echo json_encode(['status' => 'error', 'mensaje' => 'Tarjeta no encontrada en la banca.']);
        }
    }
    catch (PDOException $e)
    {
        echo json_encode(['status' => 'error', 'mensaje' => 'Error de conexión con la base de datos.']);
    }
}
?>