<?php
require_once __DIR__ . '/../controllers/validar_mascota.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $datos = [
        'nombre'                 => htmlspecialchars(trim($_POST['nombre'] ?? '')),
        'especie'                => htmlspecialchars(trim($_POST['especie'] ?? '')),
        'raza'                   => htmlspecialchars(trim($_POST['raza'] ?? '')),
        'sexo'                   => htmlspecialchars(trim($_POST['sexo'] ?? '')),
        'edad'                   => trim($_POST['edad'] ?? ''),
        'color'                  => htmlspecialchars(trim($_POST['color'] ?? '')),
        'peso'                   => trim($_POST['peso'] ?? ''),
        'tamanio'                => htmlspecialchars(trim($_POST['tamanio'] ?? '')),
        'fotografia'             => $_FILES['fotografia'] ?? null,
        'id_cliente'             => trim($_POST['id_cliente'] ?? ''),
        'id_veterinario'         => trim($_POST['id_veterinario'] ?? ''),
        'alergias'               => htmlspecialchars(trim($_POST['alergias'] ?? '')),
        'enfermedades'           => htmlspecialchars(trim($_POST['enfermedades'] ?? '')),
        'medicamentos'           => htmlspecialchars(trim($_POST['medicamentos'] ?? '')),
        'condiciones_especiales' => htmlspecialchars(trim($_POST['condiciones_especiales'] ?? '')),
        'vacunas'                => htmlspecialchars(trim($_POST['vacunas'] ?? '')),
        'ultima_desparasitacion' => trim($_POST['ultima_desparasitacion'] ?? ''),
        'temperamento'           => htmlspecialchars(trim($_POST['temperamento'] ?? '')),
        'restricciones_para_manejo' => htmlspecialchars(trim($_POST['restricciones_para_manejo'] ?? '')),
        'observaciones'          => htmlspecialchars(trim($_POST['observaciones'] ?? ''))
    ];

    validacion($datos);

} else {
    // Si intentan entrar directo a este archivo sin pasar por el formulario
    header("Location: agregar_mascota.php");
    exit();
}

?>