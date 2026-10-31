<?php
require_once __DIR__ . '/../controllers/adopcion_venta.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $color = trim($_POST['color'] ?? '');
    if ($color === 'Otro' && !empty(trim($_POST['color_espec'] ?? ''))) {
        $color = trim($_POST['color_espec']);
    }

    $datos = [
        'especie'               => trim($_POST['especie'] ?? ''),
        'raza'                  => trim($_POST['raza'] ?? ''),
        'sexo'                  => trim($_POST['sexo'] ?? ''),
        'fecha_de_nacimiento'   => trim($_POST['fecha_de_nacimiento'] ?? ''),
        'color'                 => $color,
        'color_espec'           => trim($_POST['color_espec'] ?? ''),
        'peso'                  => trim($_POST['peso'] ?? ''),
        'fecha_de_ingreso'      => trim($_POST['fecha_de_ingreso'] ?? ''),
        'costo_de_adquisicion'  => trim($_POST['costo_de_adquisicion'] ?? ''),
        'precio_de_venta'       => trim($_POST['precio_de_venta'] ?? ''),
        'estado'                => trim($_POST['estado'] ?? ''),
        'caracteristicas'       => trim($_POST['caracteristicas'] ?? ''),
        'procedencia'           => trim($_POST['procedencia'] ?? ''),
        'vacunas'               => trim($_POST['vacunas'] ?? ''),
        'desparasitacion'       => trim($_POST['desparasitacion'] ?? ''),
        'observaciones'         => trim($_POST['observaciones'] ?? '')
    ];

    validar_datos($datos);

} else {
    header("Location: agregar_adopcion_venta.php");
    exit();
}
