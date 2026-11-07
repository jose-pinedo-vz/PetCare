<?php
// procesar_pago.php

// 1. Recibir datos POST
$id_cliente         =$_POST['id_cliente'];
$tipo_servicio      =$_POST['tipo_servicio']; // 'venta', 'cita', 'reservacion'
$id_servicio        =$_POST['id_servicio'];
$tipo_pago          =$_POST['tipo_pago'];     // 'Total' o 'Anticipo'
$porcentaje_anticipo=$_POST['porcentaje_anticipo'] ?? null;
$importe            =$_POST['importe'];
$id_empleado_receptor =$_SESSION['id_empleado'] ?? null; // Null si cobra el cliente

// Datos temporales de tarjeta
$num_tarjeta        =$_POST['numero_tarjeta'];
$titular            =$_POST['nombre_titular'];
$vencimiento        =$_POST['fecha_vencimiento'];
$cvv                =$_POST['cvv'];

// 2. Ejecutar la Pasarela Simulada
$resultadoPasarela = PasarelaBancariaSimulada::procesarPago($num_tarjeta, $titular,$vencimiento, $cvv,$importe);

// 3. Extraer solo los últimos 4 dígitos (NUNCA guardar número completo ni CVV)
$ultimos_digitos = substr(preg_replace('/\D/', '',$num_tarjeta), -4);

if ($resultadoPasarela['estatus'] === 'Aprobado') {
    $estado_pago     = 'Aprobado';$referencia_pago = $resultadoPasarela['referencia_pago'];$motivo_rechazo  = null;

    // ACTUALIZAR ENTIDAD ASOCIADA
    if ($tipo_servicio === 'venta') {
        // UPDATE ventas SET estado = 'Pagada' WHERE id_venta = $id_servicio
    } elseif ($tipo_servicio === 'cita') {
        // UPDATE citas SET estado = 'Confirmada' WHERE id_cita = $id_servicio
    } elseif ($tipo_servicio === 'reservacion') {
        // UPDATE reservacion SET anticipo = anticipo + $importe, saldo = total - anticipo WHERE id_reservacion =$id_servicio
    }

} else {
    $estado_pago     = 'Rechazado';$referencia_pago = null;
    $motivo_rechazo  =$resultadoPasarela['motivo'];
}

// 4. GUARDAR EN LA TABLA `pagos`
/*

*/

// 5. Responder al cliente/cajero
if ($estado_pago === 'Aprobado') {
    echo json_encode([
        'success' => true,
        'mensaje' => 'Pago aprobado exitosamente',
        'folio' => $referencia_pago,
        'ultimos_digitos' => $ultimos_digitos
    ]);
} else {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Pago rechazado: ' . $motivo_rechazo,
        'habilitar_reintentar' => true
    ]);
}