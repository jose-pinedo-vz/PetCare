<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Pedidos.php';
require_once __DIR__ . '/../models/CorteCaja.php';

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
            'precio_compra' => $obtenerCampo('precio_compra'),
        ];
        $errores = [];

        $idDetalle = filter_var($datos['id_detalle'], FILTER_VALIDATE_INT);
        if ($idDetalle === false || $idDetalle < 1) {
            $errores[] = 'Selecciona un producto válido del pedido.';
        }

        $detallePedido = null;
        if ($idDetalle !== false && $idDetalle > 0) {
            foreach (Pedidos::obtenerDetalles($idPedido) as $detalle) {
                if ((int) $detalle['id_detalle'] === $idDetalle) {
                    $detallePedido = $detalle;
                    break;
                }
            }
            if ($detallePedido === null) {
                $errores[] = 'Selecciona un producto válido del pedido.';
            }
        }

        if ($detallePedido !== null) {
            if ($datos['precio_compra'] === '') {
                $errores[] = 'El precio de compra es obligatorio.';
            } elseif (
                !preg_match('/^\d+(?:\.\d{1,2})?$/D', $datos['precio_compra'])
                || (float) $datos['precio_compra'] <= 0
                || (float) $datos['precio_compra'] > 99999999.99
            ) {
                $errores[] = 'El precio de compra debe ser mayor que cero, tener hasta dos decimales y estar dentro del rango permitido.';
            }
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
        } elseif (!preg_match('/^(?=.*[a-zA-ZñÑ])(?=.*\d)[a-zA-Z0-9ñÑ_-]+$/', $datos['ubicacion'])) {
            $errores[] = 'La ubicación debe contener al menos una letra, un número y solo se permiten guiones (-) o guiones bajos (_) ejemplo: A-001/estante-1A.';
        }

        if ($datos['fecha_caducidad'] !== '') {
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
                'fecha_caducidad' => $datos['fecha_caducidad'] !== '' ? $datos['fecha_caducidad'] : null,
                'ubicacion' => $datos['ubicacion'],
                'precio_compra' => $datos['precio_compra'],
            ]
        );
        if (!$registrado) {
            $responder(409, ['ok' => false, 'mensaje' => 'El pedido ya no está pendiente o la cantidad supera lo que falta por recibir.']);
        }

        // El pago al proveedor sale de la caja abierta como 'gasto' (si no hay caja abierta, solo se avisa)
        $avisoCaja = '';
        $idEmpleadoCaja = (int) ($_SESSION['id_empleado'] ?? 0);
        $gasto = round($cantidad * (float) $datos['precio_compra'], 2);
        if ($idEmpleadoCaja > 0 && $gasto > 0) {
            try {
                $dbCaja = ConexionDB::obtenerConexion();
                $corteCaja = CorteCaja::obtenerCorteAbierto($dbCaja, $idEmpleadoCaja);
                if ($corteCaja) {
                    CorteCaja::registrarMovimiento(
                        $dbCaja, (int) $corteCaja['id'], $idEmpleadoCaja, 'gasto',
                        'Compra a proveedor, pedido #' . $idPedido, $gasto, null,
                        'Recepción de ' . $cantidad . ' pza(s), lote ' . $datos['lote']
                    );
                    $avisoCaja = ' Se registró un gasto de $' . number_format($gasto, 2) . ' en caja.';
                } else {
                    $avisoCaja = ' No hay caja abierta: el gasto de $' . number_format($gasto, 2) . ' no se registró en caja.';
                }
            } catch (Throwable $e) {
                error_log('Gasto de pedido #' . $idPedido . ': ' . $e->getMessage());
                $avisoCaja = ' No se pudo registrar el gasto en caja.';
            }
        }

        $pedidoActualizado = Pedidos::obtenerPedido($idPedido);
        $productosPendientes = Pedidos::obtenerDetalles($idPedido, true);
        $responder(200, [
            'ok' => true,
            'mensaje' => 'Producto recibido y agregado al inventario.' . $avisoCaja,
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
