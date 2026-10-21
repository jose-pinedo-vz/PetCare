<?php
session_start();
require_once __DIR__ . '/../controllers/adopcion_venta_editar.php';

if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    $id=trim($_POST['id'] ?? '');

    if (empty($id)) {
        $_SESSION['errores'] = ['El identificador del registro no es válido.'];
        header("Location: ../views/adopcion_venta.php");
        exit();
    }

    $datos = [
        'id_animal'                    => $id,
        'especie'               => trim($_POST['especie'] ?? ''),
        'raza'                  => trim($_POST['raza'] ?? ''),
        'sexo'                  => trim($_POST['sexo'] ?? ''),
        'fecha_de_nacimiento'   => trim($_POST['fecha_de_nacimiento'] ?? ''),
        'color'                 => trim($_POST['color'] ?? ''),
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

    if (isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] === UPLOAD_ERR_OK)
    {
        echo "<pre>"; print_r($_FILES['fotografia']); echo "</pre>";
        $datos['fotografia'] = $_FILES['fotografia'];
    }
    validar_datos($datos);

} else {
    header("Location: ../views/adopcion_venta.php");
    exit();
}