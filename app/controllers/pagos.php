<?php
// Pagos de servicios y de empleados (JSON). Requiere sesion de empleado.
session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['usuario']) || empty($_SESSION['id_empleado'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Sesión no válida.']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
    exit;
}

require_once __DIR__ . '/../models/PagosAdmin.php';

$rol = strtolower(trim((string)($_SESSION['rol'] ?? '')));
$idEmpleado = (int)$_SESSION['id_empleado'];
$d = json_decode(file_get_contents('php://input'), true) ?: [];
$accion = (string)($d['accion'] ?? '');
$soloAdmin = in_array($accion, ['abonar_caja', 'guardar_sueldo', 'pagar_empleado', 'devolver'], true);
if ($soloAdmin && !in_array($rol, ['admin', 'gerente'], true)) {
    echo json_encode(['ok' => false, 'error' => 'Solo el administrador o gerente puede hacer esta operación.']);
    exit;
}

try {
    $db = ConexionDB::obtenerConexion();
    switch ($accion) {
        case 'registrar_pago':
            $r = PagosAdmin::registrarPagoServicio(
                $db, (string)($d['tipo'] ?? 'cita'), (int)($d['id'] ?? $d['id_cita'] ?? 0), (float)($d['monto'] ?? 0), (string)($d['metodo'] ?? ''),
                (array)($d['tarjeta'] ?? []), (string)($d['referencia'] ?? ''), $idEmpleado
            );
            break;
        case 'devolver':
            $r = PagosAdmin::devolver($db, (string)($d['tipo'] ?? 'cita'), (int)($d['id'] ?? 0), $idEmpleado);
            break;
        case 'pagar_empleado':
            $periodo = (string)($d['periodo'] ?? date('Y-m'));
            if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periodo)) {
                $periodo = date('Y-m');
            }
            $r = PagosAdmin::pagarEmpleado($db, $idEmpleado, (int)($d['id_empleado'] ?? 0), $periodo);
            break;
        case 'abonar_caja':
            $r = PagosAdmin::abonarCaja($db, $idEmpleado, (float)($d['monto'] ?? 0), (string)($d['concepto'] ?? ''));
            break;
        case 'guardar_sueldo':
            $r = ['ok' => PagosAdmin::guardarSueldo($db, (int)($d['id_empleado'] ?? 0), (float)($d['sueldo'] ?? 0))];
            if (!$r['ok']) {
                $r['error'] = 'Sueldo no válido.';
            }
            break;
        default:
            $r = ['ok' => false, 'error' => 'Acción no válida.'];
    }
} catch (Throwable $e) {
    error_log('pagos.php ' . $e->getMessage());
    $r = ['ok' => false, 'error' => 'Ocurrió un error al procesar el pago.'];
}
echo json_encode($r);
