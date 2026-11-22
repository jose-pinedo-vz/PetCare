<?php
require_once __DIR__ . '/../models/Hotel.php';
date_default_timezone_set('America/Mexico_City');

function validar_datos_hotel(array $datos) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $errores = [];

    //El id_cliente es obligatorio
    if (empty($datos['id_cliente'])) {
        $errores[] = "El id del cliente es obligatorio.";
    }else if(!(int)$datos['id_cliente']){
        $errores[] = "El id del cliente debe ser un valor numerico.";
    }else if ((int)$datos['id_cliente'] < 1) {
        $errores[] = "El id del cliente debe ser un valor mayor a 0.";
    }else if (!Hotel::existeCliente((int)$datos['id_cliente'])) {
        $errores[] = "El id del cliente no existe en la base de datos.";
    }

    //El id_mascota es obligatorio
    if (empty($datos['id_mascota'])) {
        $errores[] = "El id de la mascota es obligatorio.";
    }else if(!(int)$datos['id_mascota']){
        $errores[] = "El id de la mascota debe ser un valor numerico.";
    }else if ((int)$datos['id_mascota'] < 1) {
        $errores[] = "El id de la mascota debe ser un valor mayor a 0.";
    }else if (!Hotel::existeMascota((int)$datos['id_mascota'])) {
        $errores[] = "El id de la mascota no existe en la base de datos.";
    }

    // Un cliente no puede tener otra reservación activa para la misma mascota.
    // Permite reservar cuando cambia a una mascota diferente.
    if (
        !empty($datos['id_cliente']) &&
        !empty($datos['id_mascota']) &&
        Hotel::existeCliente((int)$datos['id_cliente']) &&
        Hotel::existeMascota((int)$datos['id_mascota']) &&
        Hotel::existeReservaActiva((int)$datos['id_cliente'], (int)$datos['id_mascota'])
    ) {
        $errores[] = "El cliente ya tiene una reservación registrada para esta mascota con estado Por pagar o Pagado.";
    }

    //El id_jaula es obligatorio
    if (empty($datos['id_jaula'])) {
        $errores[] = "El id de la jaula es obligatorio.";
    }else if(!(int)$datos['id_jaula']){
        $errores[] = "El id de la jaula debe ser un valor numerico.";
    }else if ((int)$datos['id_jaula'] < 1) {
        $errores[] = "El id de la jaula debe ser un valor mayor a 0.";
    }else if (!Hotel::existeJaula((int)$datos['id_jaula'])) {
        $errores[] = "El id de la jaula no existe en la base de datos.";
    }

    //La fecha de entrada es obligatoria
    if (empty($datos['fecha_entrada'])) {
        $errores[] = "La fecha de entrada es obligatoria.";
    }else if(!DateTime::createFromFormat('Y-m-d', $datos['fecha_entrada'])){
        $errores[] = "La fecha de entrada no tiene un formato válido.";
    }else if (new DateTime($datos['fecha_entrada']) < new DateTime(date('Y-m-d'))) {
        $errores[] = "La fecha de entrada no puede ser anterior a la fecha actual.";
    }

    //La fecha de salida es obligatoria
    if (empty($datos['fecha_salida'])) {
        $errores[] = "La fecha de salida es obligatoria.";
    }else if(!DateTime::createFromFormat('Y-m-d', $datos['fecha_salida'])){
        $errores[] = "La fecha de salida no tiene un formato válido.";
    }else if (new DateTime($datos['fecha_salida']) < new DateTime(date('Y-m-d'))) {
        $errores[] = "La fecha de salida no puede ser anterior a la fecha actual.";
    }else if (!empty($datos['fecha_entrada']) && DateTime::createFromFormat('Y-m-d', $datos['fecha_entrada'])) {
        $fechaEntrada = new DateTime($datos['fecha_entrada']);
        $fechaSalida = new DateTime($datos['fecha_salida']);
        if ($fechaSalida <= $fechaEntrada) {
            $errores[] = "La fecha de salida debe ser posterior a la fecha de entrada.";
        }
    }

    //El numero de noches es obligatoria
    if (empty($datos['num_noches'])) {
        $errores[] = "El numero de noches es obligatorio.";
    }else if(!(int)$datos['num_noches']){
        $errores[] = "El numero de noches debe ser un valor numerico.";
    }else if ((int)$datos['num_noches'] < 1) {
        $errores[] = "El numero de noches debe ser un valor mayor a 0.";
    }

    //El precio por noche es obligatorio
    if (empty($datos['precio_noche'])) {
        $errores[] = "El precio por noche es obligatorio.";
    }else if(!(float)$datos['precio_noche']){
        $errores[] = "El precio por noche debe ser un valor numerico.";
    }else if ((float)$datos['precio_noche'] < 1) {
        $errores[] = "El precio por noche debe ser un valor mayor a 0.";
    }else if (!preg_match('/^\d+(?:\.\d{1,2})?$/D', $datos['precio_noche'])) {
    $errores[] = 'El precio por noche debe ser numérico y no puede tener más de dos decimales.';
    }

    //El saldo es obligatorio
    if (empty($datos['saldo'])) {
        $errores[] = "El saldo es obligatorio.";
    }else if(!(float)$datos['saldo']){
        $errores[] = "El saldo debe ser un valor numerico.";
    }else if ((float)$datos['saldo'] < 1) {
        $errores[] = "El saldo debe ser un valor mayor a 0.";
    }else if (!preg_match('/^\d+(?:\.\d{1,2})?$/D', $datos['saldo'])) {
    $errores[] = 'El saldo debe ser numérico y no puede tener más de dos decimales.';
    }

    //Los Servicios adicionales no son obligatorios
    if (!empty($datos['servicios_adicionales'])) {
        if (strlen($datos['servicios_adicionales']) > 255) {
            $errores[] = "Los servicios adicionales no pueden exceder los 255 caracteres.";
        }else if (!preg_match('/^[a-zA-Z0-9\s.,;:()\-]*$/', $datos['servicios_adicionales'])) {
            $errores[] = "Los servicios adicionales contienen caracteres no permitidos.";
        }else if ((int)$datos['servicios_adicionales']){
            $errores[] = "Los servicios adicionales no pueden ser un valor numérico.";
        }
    }

    //La alimentacion habitual no es obligatoria
    if (!empty($datos['alimentacion_habitual'])) {
        if (strlen($datos['alimentacion_habitual']) > 100) {
            $errores[] = "La alimentacion habitual no puede exceder los 100 caracteres.";
        }else if (!preg_match('/^[a-zA-Z0-9\s.,;:()\-]*$/', $datos['alimentacion_habitual'])) {
            $errores[] = "La alimentacion habitual contiene caracteres no permitidos.";
        }else if ((int)$datos['alimentacion_habitual']){
            $errores[] = "La alimentacion habitual no puede ser un valor numérico.";
        }
    }

    //La cantidad de alimento no es obligatoria
    if (!empty($datos['cantidad_alimento'])) {
        if (strlen($datos['cantidad_alimento']) > 50) {
            $errores[] = "La cantidad de alimento no puede exceder los 50 caracteres.";
        }else if (!preg_match('/^[a-zA-Z0-9\s.,;:()\-]*$/', $datos['cantidad_alimento'])) {
            $errores[] = "La cantidad de alimento contiene caracteres no permitidos.";
        }else if ((int)$datos['cantidad_alimento'] < 0){
            $errores[] = "La cantidad de alimento no puede ser un valor negativo.";
        }else if ((int)$datos['cantidad_alimento'] > 1000){
            $errores[] = "La cantidad de alimento no puede exceder los 1000g.";
        }
    }

    //El horario de alimentación no es obligatorio
    if (!empty($datos['horario_alimentacion'])) {
        if (strlen($datos['horario_alimentacion']) > 50) {
            $errores[] = "El horario de alimentación no puede exceder los 50 caracteres.";
        }elseif (!preg_match('/^\s*\d{1,2}(?::[0-5]\d)?\s*(?:a\.?m\.?|p\.?m\.?)(?:\s*(?:,|\/|-|\by\b)\s*\d{1,2}(?::[0-5]\d)?\s*(?:a\.?m\.?|p\.?m\.?))*\s*$/iu', $datos['horario_alimentacion'])) {
            $errores[] = "Ingresa un horario válido, por ejemplo: 8 am, 3 pm y 10 pm.";
        }
    }

    //Los medicamentos no son obligatorios
    if (!empty($datos['medicamentos'])) {
        if (strlen($datos['medicamentos']) > 255) {
            $errores[] = "Los medicamentos no pueden exceder los 255 caracteres.";
        }else if (!preg_match('/^[a-zA-Z0-9\s.,;:()\-]*$/', $datos['medicamentos'])) {
            $errores[] = "Los medicamentos contienen caracteres no permitidos.";
        }else if ((int)$datos['medicamentos']){
            $errores[] = "Los medicamentos no pueden ser un valor numérico.";
        }
    }

    //El horario de medicamentos no es obligatorio
    if (!empty($datos['horario_medicamentos'])) {
        if (strlen($datos['horario_medicamentos']) > 100) {
            $errores[] = "El horario de medicamentos no puede exceder los 100 caracteres.";
        }else if (!preg_match('/^\s*\d{1,2}(?::[0-5]\d)?\s*(?:a\.?m\.?|p\.?m\.?)(?:\s*(?:,|\/|-|\by\b)\s*\d{1,2}(?::[0-5]\d)?\s*(?:a\.?m\.?|p\.?m\.?))*\s*$/iu', $datos['horario_medicamentos'])) {
            $errores[] = "Ingresa un horario válido, por ejemplo: 8 am, 3 pm y 10 pm.";
        }
    }

    //La persona que recoge no es obligatoria
    if (!empty($datos['persona_recoge'])) {
        if (strlen($datos['persona_recoge']) > 100) {
            $errores[] = "La persona que recoge no puede exceder los 100 caracteres.";
        }else if (!preg_match('/^[a-zA-Z\s]+$/', $datos['persona_recoge'])) {
            $errores[] = "La persona que recoge solo puede contener letras y espacios.";
        }
    }

    //El telefono de emergencia no es obligatorio
    if (!empty($datos['telefono_emergencia'])) {
        if (strlen($datos['telefono_emergencia']) > 15) {
            $errores[] = "El teléfono de emergencia no puede exceder los 15 caracteres.";
        }else if (!preg_match('/^\+?\d{1,3}?[-.\s]?\(?\d{1,4}?\)?[-.\s]?\d{1,4}[-.\s]?\d{1,9}$/', $datos['telefono_emergencia'])) {
            $errores[] = "El teléfono de emergencia no tiene un formato válido.";
        }else {
            // Extraer solo los números ignorando guiones, espacios y paréntesis
            $soloNumerosTel = preg_replace('/\D/', '', $datos['telefono_emergencia']);
            $datos['telefono_emergencia'] = $soloNumerosTel;
            if (strlen($datos['telefono_emergencia']) !== 10) {
                $errores[] = "El teléfono de emergencia debe contener exactamente 10 dígitos (ej: 3312345678).";
            }
        }
    }

    //Las necesidades especiales no son obligatorias
    if (!empty($datos['necesidades_especiales'])) {
        if (strlen($datos['necesidades_especiales']) > 255) {
            $errores[] = "Las necesidades especiales no pueden exceder los 255 caracteres.";
        }else if (!preg_match('/^[a-zA-Z0-9\s.,;:()\-]*$/', $datos['necesidades_especiales'])) {
            $errores[] = "Las necesidades especiales contienen caracteres no permitidos.";
        }else if ((int)$datos['necesidades_especiales']){
            $errores[] = "Las necesidades especiales no pueden ser un valor numérico.";
        }
    }

    //Las observaciones no son obligatorias
    if (!empty($datos['observaciones'])) {
        if (strlen($datos['observaciones']) > 255) {
            $errores[] = "Las observaciones no pueden exceder los 255 caracteres.";
        }else if (!preg_match('/^[a-zA-Z0-9\s.,;:()\-]*$/', $datos['observaciones'])) {
            $errores[] = "Las observaciones contienen caracteres no permitidos.";
        }else if ((int)$datos['observaciones']){
            $errores[] = "Las observaciones no pueden ser un valor numérico.";
        }
    }

    if (count($errores) > 0) {
        $_SESSION['errores'] = $errores;
        $_SESSION['old']     = $datos;
        header("Location: reservacion_hotel.php");
        exit();
    } else {
        $advertencias = Hotel::insertarHotel($datos);
        if (is_array($advertencias) && isset($advertencias['error'])) {
            $_SESSION['errores'] = [$advertencias['error']];
            $_SESSION['old']     = $datos;
            header("Location: reservacion_hotel.php");
            exit();
        }

        if (is_array($advertencias) && count($advertencias) > 0){
            if ($advertencias[0] === "Error BD") {
                $_SESSION['errores'] = ["Error de base de datos: " . $advertencias[1]];
                $_SESSION['old']     = $datos;
                header("Location: reservacion_hotel.php");
                exit();
            } else {
                $_SESSION['advertencias'] = $advertencias;
                $_SESSION['old']          = $datos;
                header("Location: reservacion_hotel.php");
                exit();
            }
        }else {
            header("Location: hotel.php");
            exit();
        }
    }
}