<?php
/* codigo aqui*/

function validar_datos(){
    require_once __DIR__ . '/../controllers/validar_mascota.php';
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $datos = [
            'razon_social'                 => htmlspecialchars(trim($_POST['nombre'] ?? '')),
            'nombre_comercial'                => htmlspecialchars(trim($_POST['especie'] ?? '')),
            'rfc'                   => htmlspecialchars(trim($_POST['raza'] ?? '')),
            'telefono'                   => htmlspecialchars(trim($_POST['sexo'] ?? '')),
            'correo'                  => htmlspecialchars(trim($_POST['color'] ?? '')),
            'domicilio'                => htmlspecialchars(trim($_POST['tamanio'] ?? '')),
            'ciudad'             => htmlspecialchars(trim($_POST['tamanio'] ?? '')),
            'estado'             => trim($_POST['id_cliente'] ?? ''),
            'codigo_postal'         => trim($_POST['id_veterinario'] ?? ''),
            'condicion_pago'               => htmlspecialchars(trim($_POST['alergias'] ?? '')),
            'tiempo_entrega'                   => trim($_POST['peso'] ?? ''),
            'observaciones'               => htmlspecialchars(trim($_POST['alergias'] ?? ''))
        ];

        //validacion($datos);

    } else {
        // Si intentan entrar directo a este archivo sin pasar por el formulario
        header("Location: agregar_mascota.php");
        exit();
    }
}

?>