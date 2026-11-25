<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/citas.php';
require_once __DIR__ . '/../models/abonoCitas.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    exit();
}

header('Content-Type: application/json; charset=utf-8'); // responde con un json
if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // exit();
    $idCliente     = (int)($_POST['id_cliente'] ?? 0);
    $idEmpleado    = (int)($_POST['id_empleado'] ?? 0);
    $metodoPago    = trim((string)($_POST['metodo_pago'] ?? 'efectivo'));
    $total         = (float)($_POST['total'] ?? 0);
    $montoRecibido = (float)($_POST['monto_recibido'] ?? 0);
    $iva           = (float)($_POST['iva'] ?? 0);
    $referencia    = trim((string)($_POST['referencia'] ?? ''));

    // validaciones
    if ($idCliente <= 0 || $total <= 0 || $idEmpleado <= 0) 
    {
        echo json_encode([
            'ok' => false,
            'mensaje' => 'Faltan datos obligatorios del cliente, empleado o del monto.'
        ]);
        exit();
    }

    if ($montoRecibido < 0 || $total < 0 || $iva < 0)
    {
        echo json_encode([
            'ok' => false,
            'mensaje' => "Los mostos y cantidades son incorrectos."
        ]);
        exit();
    }

    $resultado = abonoCitas::inserrcionAVentasyPagos($idCliente, $idEmpleado, $total, $montoRecibido, $iva, $referencia, $metodoPago);


    echo json_encode($resultado);
    exit();
}



?>