<?php

declare(strict_types = 1);

require_once __DIR__ . '/../controllers/validar_mascota.php';
require_once __DIR__ . '/../models/Mascota.php';

session_start();
date_default_timezone_set('America/Mexico_City');

try {

    // verifica si el formulario fue enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // valida que el id sea un entero y si no llego o es invalido se va al if
        $idMascota = filter_input(INPUT_POST, 'id_mascota', FILTER_VALIDATE_INT);

        if ($idMascota === false || $idMascota === null) {
            echo "Error: no se recibio un ID de mascota valido";
            exit();
        }

        $datosActuales = Mascota::obtenerPorId($idMascota);

        // verifica que exita el id de la mascota
        if (!$datosActuales) {
            echo "Error: no existe una mascota con ese ID";
            exit();
        }

        $datos = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'especie' => trim($_POST['especie'] ?? ''),
            'raza' => trim($_POST['raza'] ?? ''),
            'sexo' => trim($_POST['sexo'] ?? ''),
            'edad' => trim($_POST['edad'] ?? ''),
            'color' => trim($_POST['color'] ?? ''),
            'peso' => trim($_POST['peso'] ?? ''),
            'tamanio' => trim($_POST['tamanio'] ?? ''),
            'fotografia' => $_FILES['fotografia'] ?? null,
            'id_cliente' => trim($_POST['id_cliente'] ?? ''),
            'id_veterinario' => trim($_POST['id_veterinario'] ?? ''),
            'alergias' => trim($_POST['alergias'] ?? ''),
            'enfermedades' => trim($_POST['enfermedades'] ?? ''),
            'medicamentos' => trim($_POST['medicamentos'] ?? ''),
            'condiciones_especiales' => trim($_POST['condiciones_especiales'] ?? ''),
            'vacunas' => trim($_POST['vacunas'] ?? ''),
            'ultima_desparasitacion' => $_POST['ultima_desparasitacion'] ?? '',
            'temperamento' => trim($_POST['temperamento'] ?? ''),
            'restricciones_para_manejo' => trim($_POST['restricciones_para_manejo'] ?? ''),
            'observaciones' => trim($_POST['observaciones'] ?? '')
        ];

        if (isset($_POST['activo'])) {
            // $camposEditables[] = 'esta_activo';
            $datos['esta_activo'] = (int) $_POST['activo'];
        }

        // Campos editables "llegan como texto/numero desde $_POST "
        $camposEditables = [
            'nombre', 'especie', 'raza', 'sexo', 'edad', 'color', 'peso',
            'tamanio', 'id_cliente', 'id_veterinario',
            'alergias', 'enfermedades', 'medicamentos', 'condiciones_especiales',
            'temperamento', 'restricciones_para_manejo', 'vacunas',
            'ultima_desparasitacion', 'observaciones', 'fotografia'
        ];

        if (isset($_POST['activo'])) {
            /* $camposEditables[] = 'esta_activo'; */
            $datos['esta_activo'] = (int) $_POST['activo'];
        }

        $sets = implode(', ', array_map(fn($camposEdi) => "$camposEdi = ?", $camposEditables));

        validacion($datos, true, $idMascota, $sets);

    }
}

catch (\Throwable $error) {
    // \Throwable detecta Excepciones y Errores

    error_log("Error en actualizar_mascota.php: " . $error -> getMessage()); // esto no se muestra al usuario

    echo "Ocurrio un error al actualizar los datos. Intente de nuevo mas tarde";
}