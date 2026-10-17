<?php

// la direccion del archivo que conecta la base de datos
require __DIR__ . '/../models/conexion_db.php';
require_once __DIR__ . '/../controllers/validar_mascota.php';
require_once __DIR__ . '/../controllers/ImagenController.php';

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

        // Si se hicieron cambios se usan, si no se quedan los anteriores
        // !empty() = para que el campo vacio coserve el valor anterior si lo tenia
        $valoresFinales = [];
        foreach ($camposEditables as $campo) {
            // $valoresFinales[$campo] = isset($_POST[$campo]) ? $_POST[$campo] : $datosActuales[$campo];
            $valoresFinales[$campo] = !empty($_POST[$campo]) ? $_POST[$campo] : $datosActuales[$campo];
        }


        $errores = validacion($valoresFinales, true);

        if (count($errores) > 0) {
            $msg = urlencode(implode(' | ', $errores));
            header("Location: ../views/editar_mascota.php?id_mascota={$idMascota}&error={$msg}");
            exit;
        }
        // edad
//         if (!empty($valoresFinales['edad'])) {
//             try {
//                 $fechaActual = new DateTime('today');
//                 $fechaIngresada = new DateTime($valoresFinales['edad']);
//                 if ($fechaIngresada > $fechaActual) {
//                     $msg = urlencode("La fecha de nacimiento no puede ser posterior a hoy.");
//                     header("Location: ../views/editar_mascota.php?id_mascota={$idMascota}&error={$msg}");
//                     exit;
//                 }
//             }
//             catch (\Exception $error) {
//                 $msg = urlencode("Error: la fecha de edad no es valida");
//                 header("Location: ../views/editar_mascota.php?id_mascota={$idMascota}&error={$msg}");
//                 exit;
//             }
//         }
//
//         // ultima desparasitacion
//         if (!empty($valoresFinales['ultima_desparasitacion'])) {
//             try {
//                 $fechaActual = new DateTime('today');
//                 $fechaIngresada = new DateTime($valoresFinales['ultima_desparasitacion']);
//                 if ($fechaIngresada > $fechaActual) {
//                     $msg = urlencode("Error: La fecha de la ultima desparasitacion no puede ser una fecha despues de hoy");
//                     header("Location: ../views/editar_mascota.php?id_mascota={$idMascota}&error={$msg}");
//                     exit;
//                 }
//             }
//             catch (\Exception $error) {
//                 $msg = urlencode("Error: la fecha de desparasitacion no es valida");
//                 header("Location: ../views/editar_mascota.php?id_mascota={$idMascota}&error={$msg}");
//                 exit;
//             }
//         }

        // la foto se agrega al final por si no se cambio (se envia por $_FILE)
        $valoresFinales['fotografia'] = $datosActuales['fotografia'];

        // si llego una foto nueva

        // se evalua si se subio un archivo nuevo o que tenga una archivo
        // !empty($_FILES['fotografia']['name'] = si el nombre del archivo no esta vacio
        // $_FILES['fotografia']['error'] !== UPLOAD_ERR_NO_FILE = el codigo de error es diferente a no se subio un archivo
        if (!empty($_FILES['fotografia']['name']) && $_FILES['fotografia']['error'] !== UPLOAD_ERR_NO_FILE) {

            // carga el 'filtro para subir imagenes' y la guarda si es correcta
            $resultadoImagen = ImagenController::subir($_FILES['fotografia']);

            if ($resultadoImagen['exito']) {
                $valoresFinales['fotografia'] = $resultadoImagen['ruta'];
            }

            else {
                echo "Error en la foto: " . $resultadoImagen['mensaje'];
                exit;
            }
        }

        // agregamos fotografia al final de las columnas que se van a actualizar
        $camposEditables[] = 'fotografia';

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
