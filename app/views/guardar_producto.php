<?php

require_once __DIR__ . '/../controllers/producto.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $datos = [
        'codigo'                => trim($_POST['codigo'] ?? ''),
        'codigo_barra'          => trim($_POST['codigo_barra'] ?? ''),
        'nombre'                => trim($_POST['nombre'] ?? ''),
        'id_categoria'          => trim($_POST['id_categoria'] ?? ''),
        'id_proveedor'          => trim($_POST['id_proveedor'] ?? ''),
        'descripcion'           => trim($_POST['descripcion'] ?? ''),
        'contenido'             => trim($_POST['contenido'] ?? ''),
        'unidad_medida'         => trim($_POST['unidad_medida'] ?? ''),
        'precio_compra'         => trim($_POST['precio_compra'] ?? ''),
        'precio_venta'          => trim($_POST['precio_venta'] ?? ''),
        'existencia'            => trim($_POST['existencia'] ?? ''),
        'existencia_minima'     => trim($_POST['existencia_minima'] ?? ''),
        'existencia_maxima'     => trim($_POST['existencia_maxima'] ?? ''),
        'fecha_ingreso'         => trim($_POST['fecha_ingreso'] ?? ''),
        'fecha_caducidad'       => trim($_POST['fecha_caducidad'] ?? ''),
        'lote'                  => trim($_POST['lote'] ?? ''),
        'ubicacion'             => trim($_POST['ubicacion'] ?? ''),
        'imagen'                => trim($_POST['imagen'] ?? ''),
        'estado'                => trim($_POST['estado'] ?? '')
    ];

    validar_datos($datos);


} else {
    // Si intentan entrar directo a este archivo sin pasar por el formulario
    header("Location: agregar_producto.php");
    exit();
}