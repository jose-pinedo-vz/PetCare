<?php

function validacion(array $datos){

    $errores = [];

    // Nombre obligatorio
    if (empty($datos['nombre'])) {
        $errores[] = "El nombre de la mascota es obligatorio.";
    }else if (strlen($datos['nombre']) > 20) {
        $errores[] = "El nombre no puede tener mas de 20 letras";
    }
    // Especie obligatorio
    if (empty($datos['especie'])) {
        $errores[] = "Debe especificar la especie.";
    }else if (strlen($datos['especie']) > 20) {
        $errores[] = "La especie no puede tener mas de 20 letras";
    }
    // Raza obligatorio
    if (empty($datos['raza'])) {
        $errores[] = "Debe especificar la raza.";
    }else if (strlen($datos['raza']) > 20) {
        $errores[] = "La raza no puede tener mas de 20 letras";
    }
    // Sexo obligatorio
    if (empty($datos['sexo'])) {
        $errores[] = "Debe seleccionar el sexo.";
    }
    // Edad obligatoria
    if (empty($datos['edad'])) {
        $errores[] = "Debe especificar la edad.";
    }else{
        $fechaActual  = new DateTime('today');
        $fechaIngresada = new DateTime($datos['edad']);
        if ($fechaIngresada > $fechaActual) {
            $errores[] = "Edad: No puede ser una fechadespues de hoy.";
        }
    }
    // color opcional
    if (empty($datos['color'])) {
        $datos['color'] = NULL;
    }else if (strlen($datos['color']) > 15) {
        $errores[] = "Elcolor no puede tener mas de 15 letras";
    }
    // Peso obligatorio
    if (empty($datos['peso']) || !is_numeric($datos['peso'])) {
        $errores[] = "El peso debe ser un valor numerico.";
    }else{
        $pesopunt = str_replace(',', '.', $datos['peso']);
        $datos['peso'] = (float)$pesopunt;

        if ($datos['peso'] <= 0){
            $errores[] = "El peso debe ser un numero mayor a 0 (ejemplo: 5.5).";
        }
    }
    // Tamanio obligatorio
    if (empty($datos['tamanio'])) {
        $errores[] = "Debe especificar el Tamaño.";
    }
    // Id cliente obligatorio
    if (empty($datos['id_cliente'])) {
        $errores[] = "Debe asociar un cliente válido.";
    }else {
        $datos['id_cliente'] = (int)$datos['id_cliente'];

        if (!is_numeric($datos['id_cliente']) || $datos['id_cliente'] <= 0) {
            $errores[] = "El cliente seleccionado no es valido.";
        }
    }

    // Id veterinario opcional
    if (empty($datos['id_veterinario'])) {
        $datos['id_veterinario'] = NULL;
    }else {
        $datos['id_veterinario'] = (int)$datos['id_veterinario'];

        if (!is_numeric($datos['id_veterinario']) || $datos['id_veterinario'] <= 0) {
            $errores[] = "El cliente seleccionado no es valido.";
        }
    }
    // Alergias obligatorio
    if (empty($datos['alergias'])) {
        $errores[] = "Debe especificar las alergias.";
    }
    // Enfermedades obligatorio
    if (empty($datos['enfermedades'])) {
        $errores[] = "Debe especificar las enfermedades.";
    }
    // Medicamentos obligatorio
    if (empty($datos['medicamentos'])) {
        $errores[] = "Debe especificar los medicamentos.";
    }
    // Condiciones especiales obligatorio
    if (empty($datos['condiciones_especiales'])) {
        $errores[] = "Debe especificar las condiciones especiales.";
    }
    // Vacunas Opcional
    if (empty($datos['vacunas'])) {
        $datos['vacunas'] = null;
    }
    // Ultima Desaparasitacion obligatorio
    if (empty($datos['ultima_desparasitacion'])) {
        $errores[] = "Debe especificar la ultima desaparasitación.";
    }else{
        $fechaActual  = new DateTime('today');
        $fechaIngresada = new DateTime($datos['ultima_desparasitacion']);
        if ($fechaIngresada > $fechaActual) {
            $errores[] = "Desparasitación: No puede ser una fechadespues de hoy.";
        }
    }

    //temperamento Opcional
    if (empty($datos['temperamento'])) {
        $datos['temperamento'] = null;
    }
    //restricciones para manejo Opcional
    if (empty($datos['restricciones_para_manejo'])) {
        $datos['restricciones_para_manejo'] = null;
    }
    //observaciones Opcional
    if (empty($datos['observaciones'])) {
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
        echo "<div style='font-family: Arial; padding: 20px; border: 1px solid red; background: #ffe6e6; width: 400px; border-radius: 5px; margin: 20px auto;'>";
        echo "<h3 style='color: red;'>Fallo la validación:</h3><ul>";
        foreach ($errores as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        echo "<a href='agregar_mascota.php'>Volver a intentarlo</a>";
        echo "</div>";

    } else {
        require_once __DIR__ . '/../models/Mascota.php';
        $advert = Mascota::insertar($datos);
        $advert = insertar($datos);
        //$advert = insertar($datos);
        // echo "<div style='font-family: Arial, sans-serif; padding: 12px 15px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 6px; max-width: 600px; margin: 10px auto;'>";
        // if($advert[0]=="Error ID"){
        //     echo "<strong>Aviso de Base de Datos:</strong> El Cliente (ID: " . htmlspecialchars((string)$datos['id_cliente']) . ") o el Veterinario (ID: " . htmlspecialchars((string)($datos['id_veterinario'] ?? '')) . ") no existen en sus respectivas tablas. Se requiere que existan previamente para poder vincular la mascota.";
        // }elseif($advert[0]=="Error al aguardar"){
        //     echo "<strong>Aviso al guardar en BD:</strong> " . $advert[1];
        // }else{
        
        header('Location: ../views/mascotas.php');
        exit;
        //}


    }
}
