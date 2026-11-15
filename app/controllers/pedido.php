<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Pedidos.php';

header('Content-Type: application/json; charset=utf-8');

$responder = static function (int $estado, array $respuesta): void {
    http_response_code($estado);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
};

$obtenerId = static function ($valor): int {
    $id = filter_var($valor, FILTER_VALIDATE_INT);
    return $id !== false && $id > 0 ? $id : 0;
};

$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$accion = $metodo === 'GET'
    ? (is_string($_GET['accion'] ?? null) ? $_GET['accion'] : '')
    : (is_string($_POST['accion'] ?? null) ? $_POST['accion'] : '');

try {
    if ($metodo === 'GET' && $accion === 'detalles') {
        $idPedido = $obtenerId($_GET['id'] ?? null);
        if ($idPedido === 0) {
            $responder(400, ['ok' => false, 'mensaje' => 'El pedido indicado no es válido.']);
        }

        $pedido = Pedidos::obtenerPedido($idPedido);
        if ($pedido === null) {
            $responder(404, ['ok' => false, 'mensaje' => 'No se encontró el pedido.']);
        }

        $pedido['productos'] = Pedidos::obtenerDetalles($idPedido);
        $pedido['productos_pendientes'] = Pedidos::obtenerDetalles($idPedido, true);
        $responder(200, ['ok' => true, 'pedido' => $pedido]);
    }

    if ($metodo !== 'POST') {
        $responder(405, ['ok' => false, 'mensaje' => 'Método no permitido.']);
    }

    $token = $_POST['csrf'] ?? '';
    if (
        !is_string($token)
        || !isset($_SESSION['csrf_pedidos'])
        || !hash_equals($_SESSION['csrf_pedidos'], $token)
    ) {
        $responder(403, ['ok' => false, 'mensaje' => 'La sesión expiró. Recarga la página e inténtalo nuevamente.']);
    }

    $idPedido = $obtenerId($_POST['id_pedido'] ?? null);
    if ($idPedido === 0) {
        $responder(400, ['ok' => false, 'mensaje' => 'El pedido indicado no es válido.']);
    }

    if ($accion === 'recibir') {
        $obtenerCampo = static function (string $campo): string {
            $valor = $_POST[$campo] ?? '';
            return is_string($valor) ? trim($valor) : '';
        };

        $datos = [
            'id_detalle' => $obtenerCampo('id_detalle'),
            'cantidad' => $obtenerCampo('cantidad'),
            'lote' => $obtenerCampo('lote'),
            'ubicacion' => $obtenerCampo('ubicacion'),
            'fecha_caducidad' => $obtenerCampo('fecha_caducidad'),
        ];
        $errores = [];

        $idDetalle = filter_var($datos['id_detalle'], FILTER_VALIDATE_INT);
        if ($idDetalle === false || $idDetalle < 1) {
            $errores[] = 'Selecciona un producto válido del pedido.';
        }

        if ($datos['cantidad'] === '') {
            $errores[] = 'La cantidad es obligatoria.';
        }
        $cantidad = filter_var($datos['cantidad'], FILTER_VALIDATE_INT);
        if ($datos['cantidad'] !== '' && ($cantidad === false || $cantidad < 1)) {
            $errores[] = 'La cantidad recibida debe ser un entero mayor que cero.';
        }

        if ($datos['lote'] === '') {
            $errores[] = 'El código de lote es obligatorio.';
        } elseif (strlen($datos['lote']) > 20) {
            $errores[] = 'El código de lote no puede tener más de 20 caracteres.';
        } elseif (!preg_match('/^(?=.*[a-zA-ZñÑ])(?=.*\d)[a-zA-Z0-9ñÑ\-_]+$/', $datos['lote'])) {
            $errores[] = 'El lote debe contener al menos una letra, un número y sin caracteres especiales.';
        }

        if ($datos['ubicacion'] === '') {
            $errores[] = 'La ubicación es obligatoria.';
        } elseif (strlen($datos['ubicacion']) > 20) {
            $errores[] = 'La ubicación no puede tener más de 20 caracteres.';
        } elseif (!preg_match('/^(?=.*[a-zA-ZñÑ])(?=.*\d)[a-zA-Z0-9ñÑ\-_]+$/', $datos['ubicacion'])) {
            $errores[] = 'La ubicación debe contener al menos una letra, un número y sin caracteres especiales.';
        }

        $fecha = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $datos['fecha_caducidad'],
            new DateTimeZone('America/Mexico_City')
        );
        $erroresFecha = DateTimeImmutable::getLastErrors();
        if (
            $fecha === false
            || ($erroresFecha !== false && ($erroresFecha['warning_count'] > 0 || $erroresFecha['error_count'] > 0))
            || $fecha->format('Y-m-d') !== $datos['fecha_caducidad']
        ) {
            $errores[] = 'La fecha de caducidad no tiene un formato válido.';
        } elseif ($fecha < new DateTimeImmutable('today', new DateTimeZone('America/Mexico_City'))) {
            $errores[] = 'La fecha de caducidad no puede ser anterior a hoy.';
        }

        if (
            $datos['lote'] !== ''
            && strlen($datos['lote']) <= 20
            && preg_match('/^(?=.*[a-zA-ZñÑ])(?=.*\d)[a-zA-Z0-9ñÑ\-_]+$/', $datos['lote'])
            && Pedidos::existeLote($datos['lote'])
        ) {
            $errores[] = 'Ya existe un lote con ese código.';
        }

        if ($errores !== []) {
            $responder(422, [
                'ok' => false,
                'mensaje' => 'Por favor corrige los siguientes errores:',
                'errores' => $errores,
            ]);
        }

        $registrado = Pedidos::registrarRecepcion(
            $idPedido,
            (int) $idDetalle,
            $cantidad,
            [
                'lote' => $datos['lote'],
                'fecha_caducidad' => $datos['fecha_caducidad'],
                'ubicacion' => $datos['ubicacion'],
            ]
        );
        if (!$registrado) {
            $responder(409, ['ok' => false, 'mensaje' => 'El pedido ya no está pendiente o la cantidad supera lo que falta por recibir.']);
        }

        $pedidoActualizado = Pedidos::obtenerPedido($idPedido);
        $productosPendientes = Pedidos::obtenerDetalles($idPedido, true);
        $responder(200, [
            'ok' => true,
            'mensaje' => 'Producto recibido y agregado al inventario.',
            'pedido' => $pedidoActualizado,
            'productos_pendientes' => $productosPendientes,
        ]);
    }

    if ($accion === 'cancelar') {
        if (!Pedidos::cancelarPedido($idPedido)) {
            $responder(409, ['ok' => false, 'mensaje' => 'Solo se pueden cancelar pedidos pendientes.']);
        }
        $responder(200, ['ok' => true, 'mensaje' => 'El pedido se canceló correctamente.']);
    }

    $responder(400, ['ok' => false, 'mensaje' => 'La acción solicitada no es válida.']);
} catch (Throwable $e) {
    error_log('Error en el controlador de pedidos: ' . $e->getMessage());
    $responder(500, ['ok' => false, 'mensaje' => 'No se pudo completar la operación. Verifica la conexión y la estructura de la base de datos.']);
}
