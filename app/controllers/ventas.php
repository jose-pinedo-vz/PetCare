<?php
// Carga el modelo
require_once __DIR__ . '/../models/Ventas.php';

// Datos fijos hasta que haya login
const ID_EMPLEADO_VENTA = 1;
const ID_CLIENTE_PUBLICO = 11;

$accion = $_GET['accion'] ?? '';

if ($accion === 'buscar_productos') {
    ventaBuscarProductos();
} elseif ($accion === 'producto_por_codigo') {
    ventaProductoPorCodigo();
} elseif ($accion === 'guardar_venta') {
    ventaGuardar();
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

    if (!is_array($datos) || empty($datos['items'])) {
        echo json_encode(['ok' => false, 'error' => 'El carrito esta vacio.']);
        exit;
    }

    $formaPago = strtolower(trim($datos['forma_pago'] ?? ''));
    $recibido = (float)($datos['recibido'] ?? 0);

    $resultado = Ventas::guardarVenta(
        $datos['items'],
        $formaPago,
        $recibido,
        ID_EMPLEADO_VENTA,
        ID_CLIENTE_PUBLICO
    );

    echo json_encode($resultado);
    exit;
}