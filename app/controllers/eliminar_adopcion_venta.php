<?php
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ../views/adopcion_venta.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id_adopcion_venta', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id <= 0) {
    header('Location: ../views/adopcion_venta.php');
    exit;
}

require_once __DIR__ . '/../models/adopcion_venta.php';

eliminar($id);

header('Location: ../views/adopcion_venta.php');
exit;
