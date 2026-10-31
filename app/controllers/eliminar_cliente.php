<?php

    require_once __DIR__ . '/../models/Cliente.php';

    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ../views/clientes.php');
    exit;
    }

    $id = filter_input(INPUT_POST, 'id_cliente', FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {
        header('Location: ../views/clientes.php');
        exit;
    }else{
        Cliente::eliminar($id);
    }

    header('Location: ../views/clientes.php');
    exit;
?>