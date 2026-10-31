<?php
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ../views/inventario.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id_proveedor', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id <= 0) {
    header('Location: ../views/inventario.php');
    exit;
}

require_once __DIR__ . '/../models/Productos.php';

Productos::eliminar_producto($id);

header('Location: ../views/inventario.php');
exit;