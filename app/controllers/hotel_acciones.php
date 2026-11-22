<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

function responderHotelAccion(int $codigo, array $respuesta): void
{
    http_response_code($codigo);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

function errorHotelAccion(int $codigo, string $mensaje): void
{
    responderHotelAccion($codigo, ['exito' => false, 'mensaje' => $mensaje]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorHotelAccion(405, 'Método no permitido.');
}

$rol = strtolower(trim((string) ($_SESSION['rol'] ?? '')));
if (
    empty($_SESSION['usuario']) ||
    !in_array($rol, ['admin', 'gerente', 'hotelero', 'recepcionista'], true)
) {
    errorHotelAccion(403, 'No tienes permiso para realizar esta acción.');
}

$cuerpo = file_get_contents('php://input');
if ($cuerpo === false || $cuerpo === '' || strlen($cuerpo) > 8192) {
    errorHotelAccion(400, 'La solicitud está vacía o supera el tamaño permitido.');
}

try {
    $datos = json_decode($cuerpo, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    errorHotelAccion(400, 'La solicitud no tiene un formato válido.');
}

if (!is_array($datos)) {
    errorHotelAccion(400, 'La solicitud no tiene un formato válido.');
}
if (
    !is_string($datos['csrf'] ?? null) ||
    !isset($_SESSION['csrf_hotel']) ||
    !hash_equals($_SESSION['csrf_hotel'], $datos['csrf'])
) {
    errorHotelAccion(403, 'La sesión expiró. Recarga la página e inténtalo nuevamente.');
}

require_once __DIR__ . '/../models/Hotel.php';

$accion = is_string($datos['accion'] ?? null) ? $datos['accion'] : '';
try {
    if ($accion === 'jaula_crear') {
        $tamano = is_string($datos['tamano'] ?? null) ? trim($datos['tamano']) : '';
        if (!in_array($tamano, ['chico', 'mediano', 'grande'], true)) {
            errorHotelAccion(422, 'Selecciona un tamaño de jaula válido.');
        }
        $jaula = Hotel::insertarJaula($tamano);
        responderHotelAccion(201, ['exito' => true, 'jaula' => $jaula]);
    }

    if (in_array($accion, ['jaula_eliminar', 'jaula_mantenimiento', 'jaula_disponible'], true)) {
        $id = filter_var($datos['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id === null || $id < 1) {
            errorHotelAccion(422, 'La jaula seleccionada no es válida.');
        }

        if ($accion === 'jaula_eliminar') {
            if (!Hotel::eliminarJaulaDisponible($id)) {
                errorHotelAccion(409, 'Solo se pueden eliminar jaulas disponibles.');
            }
            responderHotelAccion(200, ['exito' => true]);
        }

        $estado = $accion === 'jaula_mantenimiento' ? 'mantenimiento' : 'disponible';
        if (!Hotel::actualizarEstadoJaulaDisponible($id, $estado)) {
            errorHotelAccion(409, 'No se pudo actualizar el estado de la jaula.');
        }
        responderHotelAccion(200, ['exito' => true]);
    }

    if (in_array($accion, ['reservacion_cancelar', 'reservacion_no_asistio', 'reservacion_finalizar'], true)) {
        $id = filter_var($datos['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id === null || $id < 1) {
            errorHotelAccion(422, 'La reservación seleccionada no es válida.');
        }

        $accionModelo = substr($accion, strlen('reservacion_'));
        $resultado = Hotel::actualizarEstadoReservacion($id, $accionModelo);
        if (isset($resultado['error'])) {
            errorHotelAccion(409, $resultado['error']);
        }
        responderHotelAccion(200, ['exito' => true, 'estado' => $resultado['estado']]);
    }

    errorHotelAccion(422, 'La acción solicitada no es válida.');
} catch (Throwable $e) {
    error_log('Error al procesar una acción del hotel: ' . $e->getMessage());
    errorHotelAccion(500, 'No fue posible completar la acción. Intenta nuevamente.');
}
