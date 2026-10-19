<?php
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ../views/proveedores.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id_proveedor', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id <= 0) {
    header('Location: ../views/proveedores.php');
    exit;
}

require_once __DIR__ . '/../models/proveedores.php';

eliminar($id);

header('Location: ../views/proveedores.php');
exit;