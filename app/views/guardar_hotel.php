<?php
session_start();
require_once __DIR__ . '/../controllers/hotel.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $datos = [
        'id_cliente'              => trim($_POST['id_cliente'] ?? ''),
        'id_mascota'              => trim($_POST['id_mascota'] ?? ''),
        'id_jaula'                => trim($_POST['id_jaula'] ?? ''),
        'fecha_entrada'           => trim($_POST['fecha_entrada'] ?? ''),
        'fecha_salida'            => trim($_POST['fecha_salida'] ?? ''),
        'num_noches'              => trim($_POST['noches'] ?? ''),
        'precio_noche'            => trim($_POST['precio_noche'] ?? ''),
        'saldo'                   => trim($_POST['saldo'] ?? ''),
        'servicios_adicionales'   => trim($_POST['servicios_adicionales'] ?? ''),
        'alimentacion_habitual'   => trim($_POST['alimentacion'] ?? ''),
        'cantidad_alimento'       => trim($_POST['cantidad_alimento'] ?? ''),
        'horario_alimentacion'    => trim($_POST['horario_alimentacion'] ?? ''),
        'medicamentos'            => trim($_POST['medicamentos'] ?? ''),
        'horario_medicamentos'    => trim($_POST['horario_medicamentos'] ?? ''),
        'persona_recoge'          => trim($_POST['persona_recoge'] ?? ''),
        'telefono_emergencia'     => trim($_POST['telefono_emergencia'] ?? ''),
        'necesidades_especiales'  => trim($_POST['necesidades_especiales'] ?? ''),
        'observaciones'           => trim($_POST['observaciones'] ?? ''),
    ];

    validar_datos_hotel($datos);


} else {
    // Si intentan entrar directo a este archivo sin pasar por el formulario
    header("Location: reservacion_hotel.php");
    exit();
}

?>