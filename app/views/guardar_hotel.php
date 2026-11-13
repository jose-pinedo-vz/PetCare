<?php
session_start();
require_once __DIR__ . '/../controllers/hotel.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $datos = [
        'cliente'                 => trim($_POST['id_cliente'] ?? ''),
        'mascota'                 => trim($_POST['d_mascota'] ?? ''),
        'jaula'                   => trim($_POST['id_jaula'] ?? ''),
        'fecha_entrada'           => trim($_POST['fecha_entrada'] ?? ''),
        'fecha_salida'            => trim($_POST['fecha_salida'] ?? ''),
        'num_noches'              => trim($_POST['noches'] ?? ''),
        'precio_noche'            => trim($_POST['precio_noche'] ?? ''),
        'anticipo'                => trim($_POST['anticipo'] ?? ''),
        'saldo'                   => trim($_POST['saldo'] ?? ''),
        'servicios_adicionales'   => trim($_POST['servicios_adicionales'] ?? '')
    ];


} else {
    // Si intentan entrar directo a este archivo sin pasar por el formulario
    header("Location: reservacion_hotel.php");
    exit();
}

?>