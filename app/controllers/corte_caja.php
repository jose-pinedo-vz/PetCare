<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

require_once __DIR__ . '/../models/conexion_db.php';
require_once __DIR__ . '/../models/CorteCaja.php';

$db = ConexionDB::obtenerConexion();
$idEmpleado = isset($_SESSION['id_empleado']) ? (int)$_SESSION['id_empleado'] : 0;
$rolSesion = strtolower(trim((string)($_SESSION['rol'] ?? '')));
$esAdministrador = $rolSesion === 'admin';
$puedeOperarCaja = CorteCaja::puedeOperarCaja($db, $idEmpleado, $rolSesion);

if (!$puedeOperarCaja && !$esAdministrador && $rolSesion !== 'gerente') {
    $redirectUrl = (strpos($_SERVER['REQUEST_URI'] ?? '', 'controllers') !== false)
        ? '../views/inicio.php'
        : 'inicio.php';
    header('Location: ' . $redirectUrl);
    exit();
}
$periodo = $_POST['periodo'] ?? $_GET['periodo'] ?? date('Y-m');
if (!is_string($periodo) || !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periodo)) {
    $periodo = date('Y-m');
}
$inicioPeriodo = new DateTimeImmutable($periodo . '-01 00:00:00');
$finPeriodo = $inicioPeriodo->modify('+1 month');
$activePanel = $_GET['panel'] ?? 'diario';
if (!is_string($activePanel) || !in_array($activePanel, ['diario', 'historial', 'mensual', 'estadisticas'], true)) {
    $activePanel = 'diario';
}
if ($activePanel === 'estadisticas' && !$esAdministrador) {
    $activePanel = 'diario';
}
if ($activePanel === 'mensual' && !$esAdministrador) {
    $activePanel = 'diario';
}
$nombresMeses = [
    'January' => 'enero',
    'February' => 'febrero',
    'March' => 'marzo',
    'April' => 'abril',
    'May' => 'mayo',
    'June' => 'junio',
    'July' => 'julio',
    'August' => 'agosto',
    'September' => 'septiembre',
    'October' => 'octubre',
    'November' => 'noviembre',
    'December' => 'diciembre',
];
$periodoTexto = $nombresMeses[$inicioPeriodo->format('F')] . ' ' . $inicioPeriodo->format('Y');
$leerTexto = static function (array $datos, string $campo): string {
    $valor = $datos[$campo] ?? '';

    return is_string($valor) ? $valor : '';
};
$leerEntero = static function (array $datos, string $campo) {
    $valor = $datos[$campo] ?? null;

    return is_scalar($valor) ? filter_var($valor, FILTER_VALIDATE_INT) : false;
};
$validarFecha = static function ($fecha): ?DateTimeImmutable {
    if (!is_string($fecha) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        return null;
    }

    $resultado = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
    $errores = DateTimeImmutable::getLastErrors();
    if (!$resultado || ($errores && ($errores['warning_count'] || $errores['error_count'])) || $resultado->format('Y-m-d') !== $fecha) {
        return null;
    }

    return $resultado;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $token = $leerTexto($_POST, 'csrf_token');
        if (empty($_SESSION['csrf_corte']) || !hash_equals($_SESSION['csrf_corte'], $token)) {
            throw new DomainException('La sesión del formulario venció. Intenta de nuevo.');
        }

        $accion = $leerTexto($_POST, 'accion');
        if (!$puedeOperarCaja) {
            throw new DomainException('Tu usuario no tiene permiso para abrir ni operar una caja.');
        }
        if ($idEmpleado < 1 && !$esAdministrador) {
            throw new DomainException('Tu usuario no está asociado a un empleado.');
        }

        switch ($accion) {
            case 'abrir':
                if ($idEmpleado < 1) {
                    throw new DomainException('Tu usuario no está asociado a un empleado.');
                }
                $fondoPost = $_POST['fondo_inicial'] ?? null;
                $fondoInicial = (is_scalar($fondoPost) && is_numeric($fondoPost)) ? (float)$fondoPost : CorteCaja::FONDO_INICIAL;
                CorteCaja::crearCorte($db, $idEmpleado, $leerTexto($_POST, 'turno'), $fondoInicial);
                unset($_SESSION['corte_arqueo']);
                $_SESSION['corte_flash'] = ['tipo' => 'exito', 'mensaje' => 'Caja abierta con fondo inicial de $' . number_format($fondoInicial, 2) . '.'];
                break;

            case 'movimiento':
                if ($idEmpleado < 1) {
                    throw new DomainException('Tu usuario no está asociado a un empleado.');
                }
                $corteId = $leerEntero($_POST, 'corte');
                $importe = $_POST['importe'] ?? null;
                if (!$corteId || !is_scalar($importe) || !is_numeric($importe)) {
                    throw new DomainException('Revisa el corte y el importe del movimiento.');
                }
                $beneficiario = $leerEntero($_POST, 'empleado_beneficiario');
                CorteCaja::registrarMovimiento(
                    $db,
                    $corteId,
                    $idEmpleado,
                    $leerTexto($_POST, 'tipo'),
                    trim($leerTexto($_POST, 'concepto')),
                    (float)$importe,
                    $beneficiario ?: null,
                    trim($leerTexto($_POST, 'observaciones')) ?: null
                );
                $_SESSION['corte_flash'] = ['tipo' => 'exito', 'mensaje' => 'Movimiento guardado en el corte.'];
                break;

            case 'declarar_arqueo':
                if ($idEmpleado < 1) {
                    throw new DomainException('Tu usuario no está asociado a un empleado.');
                }
                $corteId = $leerEntero($_POST, 'corte');
                $contado = $_POST['efectivo_contado'] ?? null;
                if (!$corteId || !is_scalar($contado) || !is_numeric($contado)) {
                    throw new DomainException('Ingresa el total de efectivo contado.');
                }
                $_SESSION['corte_arqueo'] = [
                    'corte' => $corteId,
                    'observaciones' => trim($leerTexto($_POST, 'observaciones')),
                    'datos' => CorteCaja::prepararArqueo($db, $corteId, $idEmpleado, (float)$contado),
                    'creado' => time(),
                ];
                $_SESSION['corte_flash'] = ['tipo' => 'info', 'mensaje' => 'Revisa el resultado del arqueo y confirma el cierre.'];
                break;

            case 'confirmar_cierre':
                $pendiente = $_SESSION['corte_arqueo'] ?? null;
                if ($idEmpleado < 1 || !is_array($pendiente) || (int)$pendiente['corte'] !== (int)($leerEntero($_POST, 'corte') ?: 0)) {
                    throw new DomainException('No hay un arqueo pendiente para este corte. Vuelve a contar el efectivo.');
                }
                if (time() - (int)$pendiente['creado'] > 1200) {
                    unset($_SESSION['corte_arqueo']);
                    throw new DomainException('El arqueo venció. Vuelve a contar el efectivo.');
                }
                $resultado = CorteCaja::cerrarCorte(
                    $db,
                    (int)$pendiente['corte'],
                    $idEmpleado,
                    (float)$pendiente['datos']['efectivo_contado'],
                    (float)$pendiente['datos']['efectivo_esperado'],
                    $pendiente['observaciones'] !== '' ? $pendiente['observaciones'] : null
                );
                unset($_SESSION['corte_arqueo']);
                $mensaje = 'Corte cerrado correctamente.';
                if ($resultado['excedente'] > 0) {
                    $mensaje .= ' Excedente enviado a resguardo; el fondo fijo permanece en caja: $' . number_format($resultado['excedente'], 2) . '.';
                } elseif ($resultado['faltante_fondo'] > 0) {
                    $mensaje .= ' Faltan $' . number_format($resultado['faltante_fondo'], 2) . ' para conservar el fondo fijo; no se envió efectivo a resguardo.';
                }
                $_SESSION['corte_flash'] = ['tipo' => 'exito', 'mensaje' => $mensaje];
                break;

            case 'cancelar_arqueo':
                unset($_SESSION['corte_arqueo']);
                $_SESSION['corte_flash'] = ['tipo' => 'info', 'mensaje' => 'Se canceló la confirmación del arqueo.'];
                break;

            default:
                throw new DomainException('La acción solicitada no es válida.');
        }
    } catch (DomainException | InvalidArgumentException $e) {
        if (($accion ?? '') === 'confirmar_cierre') {
            unset($_SESSION['corte_arqueo']);
        }
        $_SESSION['corte_flash'] = ['tipo' => 'error', 'mensaje' => $e->getMessage()];
    } catch (PDOException $e) {
        error_log('Error de base de datos en CorteCaja: ' . $e->getMessage());
        $_SESSION['corte_flash'] = ['tipo' => 'error', 'mensaje' => 'No se pudo guardar la operación de caja. Intenta de nuevo.'];
    } catch (RuntimeException $e) {
        error_log('Error en CorteCaja: ' . $e->getMessage());
        $_SESSION['corte_flash'] = ['tipo' => 'error', 'mensaje' => 'No se pudo completar la operación de caja. Intenta de nuevo.'];
    }

    header('Location: ../views/corte_caja.php?panel=diario&periodo=' . rawurlencode((string)$periodo));
    exit();
}

if (empty($_SESSION['csrf_corte'])) {
    $_SESSION['csrf_corte'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_corte'];
$corteAbierto = $idEmpleado > 0 ? CorteCaja::obtenerCorteAbierto($db, $idEmpleado) : null;
$movimientosCorte = $corteAbierto
    ? CorteCaja::listarMovimientos($db, (int)$corteAbierto['id'])
    : [];
$arqueoPendiente = $_SESSION['corte_arqueo'] ?? null;
if (
    !is_array($arqueoPendiente)
    || !$corteAbierto
    || (int)$arqueoPendiente['corte'] !== (int)$corteAbierto['id']
    || time() - (int)$arqueoPendiente['creado'] > 1200
) {
    unset($_SESSION['corte_arqueo']);
    $arqueoPendiente = null;
}

$estadoHistorial = is_string($_GET['estado'] ?? null) && in_array($_GET['estado'], ['todos', 'abierto', 'cerrado'], true)
    ? $_GET['estado']
    : 'todos';
$empleadoHistorial = $esAdministrador ? ($leerEntero($_GET, 'empleado') ?: null) : null;
$historialCortes = $idEmpleado > 0 || $esAdministrador
    ? CorteCaja::listarCortes(
        $db,
        $inicioPeriodo,
        $finPeriodo,
        $idEmpleado,
        $esAdministrador,
        $empleadoHistorial,
        $estadoHistorial
    )
    : [];
$pagosCorte = $corteAbierto
    ? CorteCaja::listarPagos($db, (int)$corteAbierto['id'])
    : [];
$resumenPagosCorte = $corteAbierto
    ? CorteCaja::obtenerResumenPagos($db, (int)$corteAbierto['id'])
    : ['efectivo' => 0.0, 'tarjeta' => 0.0, 'transferencia' => 0.0, 'general' => 0.0, 'pagos' => 0, 'ventas' => 0];
$fechaInicioEstadisticas = $validarFecha($_GET['fecha_inicio'] ?? null) ?? $inicioPeriodo;
$fechaFinEstadisticas = $validarFecha($_GET['fecha_fin'] ?? null) ?? $inicioPeriodo->modify('last day of this month');
if ($fechaFinEstadisticas < $fechaInicioEstadisticas || $fechaFinEstadisticas->diff($fechaInicioEstadisticas)->days > 366) {
    $fechaInicioEstadisticas = $inicioPeriodo;
    $fechaFinEstadisticas = $inicioPeriodo->modify('last day of this month');
}
$finExclusivoEstadisticas = $fechaFinEstadisticas->modify('+1 day');
$filtroEmpleadoEstadisticas = $esAdministrador ? ($leerEntero($_GET, 'empleado_estadisticas') ?: null) : null;
$empleados = $esAdministrador || ($idEmpleado > 0 && $corteAbierto)
    ? CorteCaja::listarEmpleados($db)
    : [];
$resumenMensual = $esAdministrador
    ? CorteCaja::obtenerResumenMensual($db, $fechaInicioEstadisticas, $finExclusivoEstadisticas, $filtroEmpleadoEstadisticas)
    : null;
$cortesEstadisticas = $esAdministrador
    ? CorteCaja::listarCortes(
        $db,
        $fechaInicioEstadisticas,
        $finExclusivoEstadisticas,
        $idEmpleado,
        true,
        $filtroEmpleadoEstadisticas,
        'cerrado'
    )
    : [];
$movimientosEstadisticas = $esAdministrador
    ? CorteCaja::listarMovimientosPeriodo(
        $db,
        $fechaInicioEstadisticas,
        $finExclusivoEstadisticas,
        $filtroEmpleadoEstadisticas
    )
    : [];
$pagosEstadisticas = $esAdministrador
    ? CorteCaja::listarPagosPeriodo(
        $db,
        $fechaInicioEstadisticas,
        $finExclusivoEstadisticas,
        $filtroEmpleadoEstadisticas
    )
    : [];
$filtroEmpleadoMensual = $esAdministrador ? ($leerEntero($_GET, 'empleado_mensual') ?: null) : null;
$resumenMensualPeriodo = $esAdministrador
    ? CorteCaja::obtenerResumenMensual($db, $inicioPeriodo, $finPeriodo, $filtroEmpleadoMensual)
    : null;
$cortesResumenMensual = $esAdministrador
    ? CorteCaja::listarCortes(
        $db,
        $inicioPeriodo,
        $finPeriodo,
        $idEmpleado,
        true,
        $filtroEmpleadoMensual,
        'todos'
    )
    : [];
$movimientosMensuales = $esAdministrador
    ? CorteCaja::listarMovimientosPeriodo($db, $inicioPeriodo, $finPeriodo, $filtroEmpleadoMensual)
    : [];
$pagosMensuales = $esAdministrador
    ? CorteCaja::listarPagosPeriodo($db, $inicioPeriodo, $finPeriodo, $filtroEmpleadoMensual)
    : [];
$detalleCorte = null;
$movimientosDetalle = [];
$detalleId = $leerEntero($_GET, 'detalle');
if ($detalleId) {
    $detalleCorte = CorteCaja::obtenerDetalle($db, $detalleId, $idEmpleado, $esAdministrador);
    if ($detalleCorte) {
        $movimientosDetalle = CorteCaja::listarMovimientos($db, $detalleId);
    } else {
        $_SESSION['corte_flash'] = [
            'tipo' => 'error',
            'mensaje' => 'El corte solicitado no existe o no tienes permiso para consultarlo.',
        ];
    }
}
$avisoFlash = $_SESSION['corte_flash'] ?? null;
unset($_SESSION['corte_flash']);
