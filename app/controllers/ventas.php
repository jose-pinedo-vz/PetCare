<?php
// Carga el modelo
require_once __DIR__ . '/../models/Ventas.php';

$accion = $_GET['accion'] ?? '';

if ($accion === 'buscar_productos') {
    ventaBuscarProductos();
} elseif ($accion === 'producto_por_codigo') {
    ventaProductoPorCodigo();
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