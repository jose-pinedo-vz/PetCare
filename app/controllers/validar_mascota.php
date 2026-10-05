<?php

date_default_timezone_set('America/Mexico_City');

function validacion(array $datos, bool $actualizacion = false){
    session_start();

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $errores = [];

    // Nombre obligatorio
    if (empty(trim($datos['nombre']))) {
        $errores[] = "El nombre de la mascota es obligatorio.";
    }else if (strlen($datos['nombre']) > 20) {
        $errores[] = "El nombre no puede tener mas de 20 letras";
    }
    // Especie obligatorio
    if (empty(trim($datos['especie']))) {
        $errores[] = "Debe especificar la especie.";
    }else if (strlen($datos['especie']) > 20) {
        $errores[] = "La especie no puede tener mas de 20 letras";
    }
    // Raza obligatorio
    if (empty(trim($datos['raza']))) {
        $errores[] = "Debe especificar la raza.";
    }else if (strlen($datos['raza']) > 20) {
        $errores[] = "La raza no puede tener mas de 20 letras";
    }
    // Sexo obligatorio
    if (empty(trim($datos['sexo']))) {
        $errores[] = "Debe seleccionar el sexo.";
    }
    // Edad obligatoria
    if (empty(trim($datos['edad']))) {
        $errores[] = "Debe especificar la edad.";
    } else {
        try {
            $fechaActual    = new DateTime('today');
            $fechaIngresada = new DateTime($datos['edad']);
            if ($fechaIngresada > $fechaActual) {
                $errores[] = "Edad: No puede ser una fecha posterior a hoy.";
            }
        } catch (Exception $e) {
            $errores[] = "Edad: Formato de fecha no válido.";
        }
    }
    // color opcional
    if (empty(trim($datos['color']))) {
        $datos['color'] = NULL;
    }else if (strlen($datos['color']) > 15) {
        $errores[] = "Elcolor no puede tener mas de 15 letras";
    }
    // Peso obligatorio
    $pesoLimpio = str_replace(',', '.', $datos['peso'] ?? '');
    if (empty(trim($datos['peso'])) || !is_numeric($pesoLimpio)) {
        $errores[] = "El peso debe ser un valor numérico.";
    } else {
        $datos['peso'] = (float)$pesoLimpio;
        if ($datos['peso'] <= 0) {
            $errores[] = "El peso debe ser un número mayor a 0 (ejemplo: 5.5).";
        }
    }
    // Tamanio obligatorio
    if (empty(trim($datos['tamanio']))) {
        $errores[] = "Debe especificar el Tamaño.";
    }
    // Id cliente obligatorio
    if (empty(trim($datos['id_cliente']))) {
        $errores[] = "Debe asociar un cliente válido.";
    }else {
        $datos['id_cliente'] = (int)$datos['id_cliente'];

        if (!is_numeric($datos['id_cliente']) || $datos['id_cliente'] <= 0) {
            $errores[] = "El cliente seleccionado no es valido.";
        }
    }

    // Id veterinario opcional
    if (empty(trim($datos['id_veterinario']))) {
        $datos['id_veterinario'] = NULL;
    }else {
        $datos['id_veterinario'] = (int)$datos['id_veterinario'];

        if (!is_numeric($datos['id_veterinario']) || $datos['id_veterinario'] <= 0) {
            $errores[] = "El cliente seleccionado no es valido.";
        }
    }
    // Alergias obligatorio
    if (empty(trim($datos['alergias']))) {
        $errores[] = "Debe especificar las alergias.";
    }
    // Enfermedades obligatorio
    if (empty(trim($datos['enfermedades']))) {
        $errores[] = "Debe especificar las enfermedades.";
    }
    // Medicamentos obligatorio
    if (empty(trim($datos['medicamentos']))) {
        $errores[] = "Debe especificar los medicamentos.";
    }
    // Condiciones especiales obligatorio
    if (empty(trim($datos['condiciones_especiales']))) {
        $errores[] = "Debe especificar las condiciones especiales.";
    }
    // Vacunas Opcional
    if (empty(trim($datos['vacunas']))) {
        $datos['vacunas'] = null;
    }
    // Ultima Desaparasitacion obligatorio
    if (empty(trim($datos['ultima_desparasitacion']))) {
        $errores[] = "Debe especificar la última desparasitación.";
    } else {
        try {
            $fechaActual    = new DateTime('today');
            $fechaIngresada = new DateTime($datos['ultima_desparasitacion']);
            if ($fechaIngresada > $fechaActual) {
                $errores[] = "Desparasitación: No puede ser una fecha posterior a hoy.";
            }
        } catch (Exception $e) {
            $errores[] = "Desparasitación: Formato de fecha no válido.";
        }
    }

    //temperamento Opcional
    if (empty(trim($datos['temperamento']))) {
        $datos['temperamento'] = null;
    }
    //restricciones para manejo Opcional
    if (empty(trim($datos['restricciones_para_manejo']))) {
        $datos['restricciones_para_manejo'] = null;
    }
    //observaciones Opcional
    if (empty(trim($datos['observaciones']))) {
        $datos['observaciones'] = null;
    }

    // subida de la imagen usando el controlador ImagenController
    require_once __DIR__ . '/../controllers/ImagenController.php';

    $resultadoImagen = null;
    $foto = $datos['fotografia'];
    $datos['fotografia'] = null;

    if (is_array($foto) && !empty($foto['name']) && $foto['error'] !== UPLOAD_ERR_NO_FILE) {
        $resultadoImagen = ImagenController::subir($foto);
        if ($resultadoImagen['exito']) {
            $datos['fotografia'] = $resultadoImagen['ruta'];
        } else {
            $errores[] = "Error en la foto: " . $resultadoImagen['mensaje'];
        }
    }

    if (count($errores) > 0) {
        // se muestra errores de que campos no se llenaron
        $_SESSION['errores'] = $errores;
        $_SESSION['old']     = $datos;

        header("Location: ../views/agregar_mascota.php");
        exit;

    } else {
        require_once __DIR__ . '/../models/Mascota.php';
        $advert = Mascota::insertar($datos);

        if (is_array($advert) && count($advert) > 0) {
            if ($advert[0] === "Error ID") {
                $_SESSION['errores'] = ["El Cliente (ID: " . htmlspecialchars((string)$datos['id_cliente']) . ") o el Veterinario no existen en el sistema."];
            } else if($advert[0] === "Error al aguardar") {
                $_SESSION['errores'] = ["Error al guardar en la base de datos: " . $advert[1]];
            }
            $_SESSION['old'] = $datos;

            header("Location: ../views/agregar_mascota.php");
            exit;
        } else {
            // Éxito: redirige a la lista de mascotas
            header('Location: ../views/mascotas.php');
            exit;
        }


    }
}
