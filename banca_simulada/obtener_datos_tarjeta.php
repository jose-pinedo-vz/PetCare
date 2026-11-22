
<?php
header('Content-Type: application/json');
require_once 'conexion_banco.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tarjeta = str_replace([' ', '-'], '', trim($_POST['card_number'] ?? ''));

    if (!preg_match('/^\d{16}$/', $tarjeta)) {
        echo json_encode([
            'status' => 'error',
            'mensaje' => 'Ingresa un número de tarjeta válido.'
        ]);
        exit;
    }

    try {
        $stmt = $pdoBanco->prepare(
            "SELECT titular, numero_tarjeta, expiracion, cvv, saldo, estado
             FROM cuentas_bancarias
             WHERE numero_tarjeta = ?"
        );

        $stmt->execute([$tarjeta]);
        $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cuenta) {
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'Tarjeta no encontrada.'
            ]);
            exit;
        }

        echo json_encode([
            'status' => 'success',
            'titular' => $cuenta['titular'],
            'numero_tarjeta' => chunk_split($cuenta['numero_tarjeta'], 4, ' '),
            'expiracion' => $cuenta['expiracion'],
            'cvv' => $cuenta['cvv'],
            'saldo' => number_format($cuenta['saldo'], 2, '.', ','),
            'estado' => $cuenta['estado']
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'mensaje' => 'Error al consultar la base de datos.'
        ]);
    }
}
?>
