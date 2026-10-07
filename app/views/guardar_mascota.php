<?php
require_once __DIR__ . '/../controllers/validar_mascota.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $datos = [
        'nombre'                 => trim($_POST['nombre'] ?? ''),
        'especie'                => trim($_POST['especie'] ?? ''),
        'raza'                   => trim($_POST['raza'] ?? ''),
        'sexo'                   => trim($_POST['sexo'] ?? ''),
        'edad'                   => trim($_POST['edad'] ?? ''),
        'color'                  => trim($_POST['color'] ?? ''),
        'peso'                   => trim($_POST['peso'] ?? ''),
        'tamanio'                => trim($_POST['tamanio'] ?? ''),
        'fotografia'             => $_FILES['fotografia'] ?? null,
        'id_cliente'             => trim($_POST['id_cliente'] ?? ''),
        'id_veterinario'         => trim($_POST['id_veterinario'] ?? ''),
        'alergias'               => trim($_POST['alergias'] ?? ''),
        'enfermedades'           => trim($_POST['enfermedades'] ?? ''),
        'medicamentos'           => trim($_POST['medicamentos'] ?? ''),
        'condiciones_especiales' => trim($_POST['condiciones_especiales'] ?? ''),
        'vacunas'                => trim($_POST['vacunas'] ?? ''),
        'ultima_desparasitacion' => $_POST['ultima_desparasitacion'] ?? '',
        'temperamento'           => trim($_POST['temperamento'] ?? ''),
        'restricciones_para_manejo' => trim($_POST['restricciones_para_manejo'] ?? ''),
        'observaciones'          => trim($_POST['observaciones'] ?? '')
    ];

    validacion($datos);

} else {
    // Si intentan entrar directo a este archivo sin pasar por el formulario
    header("Location: agregar_mascota.php");
    exit();
}

?>