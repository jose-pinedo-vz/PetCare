<?php

require_once __DIR__ . '/../controllers/proveedor.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $datos = [
        'razon_social'      => trim($_POST['razon_social'] ?? ''),
        'nombre_comercial'  => trim($_POST['nombre_comercial'] ?? ''),
        'rfc'               => trim($_POST['rfc'] ?? ''),
        'telefono'          => trim($_POST['telefono'] ?? ''),
        'correo'            => trim($_POST['correo'] ?? ''),
        'domicilio'         => trim($_POST['domicilio'] ?? ''),
        'ciudad'            => trim($_POST['ciudad'] ?? ''),
        'estado'            => trim($_POST['estado'] ?? ''),
        'codigo_postal'     => trim($_POST['codigo_postal'] ?? ''),
        'condicion_pago'    => trim($_POST['condicion_pago'] ?? ''),
        'tiempo_entrega'    => trim($_POST['tiempo_entrega'] ?? ''),
        'observaciones'     => trim($_POST['observaciones'] ?? '')
    ];

    validar_datos($datos);


} else {
    // Si intentan entrar directo a este archivo sin pasar por el formulario
    header("Location: agregar_proveedor.php");
    exit();
}