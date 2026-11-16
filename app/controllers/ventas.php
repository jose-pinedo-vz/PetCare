<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Ventas.php';

const ID_CLIENTE_PUBLICO = 11;

$accion = $_GET['accion'] ?? '';

if ($accion === 'buscar_productos') {
    ventaBuscarProductos();
} elseif ($accion === 'producto_por_codigo') {
    ventaProductoPorCodigo();
} elseif ($accion === 'buscar_servicios') {
    ventaBuscarServicios();
} elseif ($accion === 'guardar_venta') {
    ventaGuardar();
} elseif ($accion === 'historial_ventas') {
    ventaHistorial();
} elseif ($accion === 'detalle_venta') {
    ventaDetalle();
} elseif ($accion === 'guardar_factura') {
    ventaGuardarFactura();
} elseif ($accion === 'estado_caja') {
    ventaEstadoCaja();
}

// Lista el historial con filtros y paginas
function ventaHistorial()
{
    header('Content-Type: application/json; charset=utf-8');
    $filtros = [
        'q' => trim($_GET['q'] ?? ''),
        'desde' => trim($_GET['desde'] ?? ''),
        'hasta' => trim($_GET['hasta'] ?? ''),
        'estado' => trim($_GET['estado'] ?? ''),
        'pagina' => (int)($_GET['pagina'] ?? 1)
    ];
    echo json_encode(Ventas::listarHistorial($filtros));
    exit;
}

// Detalle de una venta por id
function ventaDetalle()
{
    header('Content-Type: application/json; charset=utf-8');
    $id = (int)($_GET['id'] ?? 0);
    $venta = ($id > 0) ? Ventas::detalleVenta($id) : null;

    if ($venta === null) {
        echo json_encode(['error' => 'Venta no encontrada.']);
        exit;
    }

    echo json_encode($venta);
    exit;
}

// Guarda los datos de factura de una venta (solo por POST)
function ventaGuardarFactura()
{
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['ok' => false, 'error' => 'Metodo no permitido.']);
        exit;
    }

    $datos = json_decode(file_get_contents('php://input'), true);
    if (!is_array($datos)) {
        echo json_encode(['ok' => false, 'error' => 'Datos no validos.']);
        exit;
    }

    $idVenta = (int)($datos['id_venta'] ?? 0);
    echo json_encode(Ventas::guardarFactura($idVenta, $datos));
    exit;
}

// Lista servicios activos por nombre o categoria (si esta vacio muestra todos)
function ventaBuscarServicios()
{
    header('Content-Type: application/json; charset=utf-8');
    $termino = trim($_GET['q'] ?? '');
    echo json_encode(Ventas::buscarServicios($termino));
    exit;
}

// Lista productos por nombre o codigo (si esta vacio muestra todos)
function ventaBuscarProductos()
{
    header('Content-Type: application/json; charset=utf-8');
    $termino = trim($_GET['q'] ?? '');
    echo json_encode(Ventas::buscarProductos($termino));
    exit;
}

// Busca un producto por codigo exacto
function ventaProductoPorCodigo()
{
    header('Content-Type: application/json; charset=utf-8');
    $codigo = trim($_GET['codigo'] ?? '');

    if ($codigo === '') {
        echo json_encode(['error' => 'Escribe un código.']);
        exit;
    }

    $producto = Ventas::buscarProductoPorCodigo($codigo);

    if ($producto === null) {
        echo json_encode(['error' => 'Producto no encontrado.']);
        exit;
    }

    echo json_encode($producto);
    exit;

}
// Guarda la venta que llega del carrito (solo por POST)
function ventaGuardar()
{
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['ok' => false, 'error' => 'Metodo no permitido.']);
        exit;
    }

    // El JS manda un JSON con los productos y el pago
    $datos = json_decode(file_get_contents('php://input'), true);

    // Productos y servicios, si no llegan como lista se toman vacios
    $items = (is_array($datos) && is_array($datos['items'] ?? null)) ? $datos['items'] : [];
    $servicios = (is_array($datos) && is_array($datos['servicios'] ?? null)) ? $datos['servicios'] : [];

    if (empty($items) && empty($servicios)) {
        echo json_encode(['ok' => false, 'error' => 'El carrito esta vacio.']);
        exit;
    }

    $formaPago = strtolower(trim($datos['forma_pago'] ?? ''));
    $recibido = (float)($datos['recibido'] ?? 0);

    // Datos de tarjeta o transferencia; solo se pasan al modelo, nunca se registran
    $datosPago = (is_array($datos) && is_array($datos['datos_pago'] ?? null)) ? $datos['datos_pago'] : [];

    $idEmpleado = isset($_SESSION['id_empleado']) ? (int)$_SESSION['id_empleado'] : 0;
    if ($idEmpleado <= 0) {
        echo json_encode(['ok' => false, 'error' => 'No hay una sesión de empleado activa. Inicia sesión para continuar.']);
        exit;
    }

    $resultado = Ventas::guardarVenta(
        $items,
        $formaPago,
        $recibido,
        $idEmpleado,
        ID_CLIENTE_PUBLICO,
        $servicios,
        $datosPago
    );
    unset($datos, $datosPago);

    echo json_encode($resultado);
    exit;
}

function ventaEstadoCaja()
{
    header('Content-Type: application/json; charset=utf-8');
    $idEmpleado = isset($_SESSION['id_empleado']) ? (int)$_SESSION['id_empleado'] : 0;
    if ($idEmpleado <= 0) {
        echo json_encode(['ok' => false, 'abierta' => false, 'error' => 'Sin sesión de empleado']);
        exit;
    }
    require_once __DIR__ . '/../models/conexion_db.php';
    require_once __DIR__ . '/../models/CorteCaja.php';
    try {
        $db = ConexionDB::obtenerConexion();
        $corte = CorteCaja::obtenerCorteAbierto($db, $idEmpleado);
        echo json_encode(['ok' => true, 'abierta' => $corte !== null, 'corte' => $corte]);
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'abierta' => false, 'error' => $e->getMessage()]);
    }
    exit;
}