<?php
    /*declare(strict_types=1);
    require_once __DIR__ . '/../models/Empleados.php';

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($id !== false && $id !== null && $id > 0) {
        Empleados::Cambiar_estado_empleado($id);
    }

    header('Location: ../views/empleados.php');
    exit;*/

    require_once __DIR__ . '/../models/Empleados.php';

    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ../views/empleados.php');
    exit;
    }

    $id = filter_input(INPUT_POST, 'id_empleado', FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {
        header('Location: ../views/empleados.php');
        exit;
    }else{
        Empleados::Cambiar_estado_empleado($id);
    }

    header('Location: ../views/empleados.php');
    exit;
?>
