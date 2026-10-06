<?php
/* codigo aqui*/

function datos(){
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $datos = [
            'razon_social'      => trim($_POST['razon_social'] ?? ''),
            'nombre_comercial'  => trim($_POST['nombre_comercial'] ?? ''),
            'rfc'               => trim($_POST['rfc'] ?? ''),
            'telefono'          => trim($_POST['telefono'] ?? ''),
            'correo'            => trim($_POST['correo'] ?? ''),
            'domicilio'         => trim($_POST['domicilio'] ?? ''),
            'ciudad'            => trim($_POST['ciudad'] ?? ''),
            'estado'            => trim($_POST['estado'] ?? ''),
            'codigo_postal'     => trim($_POST['codigo_postal'] ?? ''),
            'condicion_pago'    => trim($_POST['condicion_pago'] ?? ''),
            'tiempo_entrega'    => trim($_POST['tiempo_entrega'] ?? ''),
            'observaciones'     => trim($_POST['observaciones'] ?? '')
        ];

        validar_datos($datos);


    } else {
        // Si intentan entrar directo a este archivo sin pasar por el formulario
        header("Location: agregar_mascota.php");
        exit();
    }
}



function validar_datos($datos){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $errores = [];
    // La razon social es obligatorio
    if (empty($datos['razon_social'])) {
        $errores[] = "La razon social es obligatoria.";
    }else if (strlen($datos['razon_social']) > 30) {
        $errores[] = "La razon no puede tener mas de 30 letras";
    }else if (is_numeric($datos['razon_social'])) {
        $errores[] = "La razón social no puede ser únicamente números.";
    }

    // Nombre comercial es obligatorio
    if (empty($datos['nombre_comercial'])) {
        $errores[] = "El nombre comercial es obligatoria.";
    }else if (strlen($datos['nombre_comercial']) > 30) {
        $errores[] = "El nombre comerrcial no puede tener mas de 30 letras.";
    }else if (is_numeric($datos['nombre_comercial'])) {
        $errores[] = "El nombre comercial no puede ser únicamente números.";
    }

    // El RFC es obligatorio
    if (empty($datos['rfc'])) {
        $errores[] = "El RFC es obligatoria.";
    }else if (strlen($datos['rfc']) > 13) {
        $errores[] = "El RFC no puede tener mas de 13 letras.";
    }

    // El telefono es obligatorio
    if (empty($datos['telefono'])) {
        $errores[] = "El telefono es obligatoria.";
    }else if (preg_match('/[a-zA-Z]/', $datos['telefono'])) {
        $errores[] = "El teléfono no puede contener letras.";
    } else {
        // Extraer solo los números ignorando guiones, espacios y paréntesis
        $soloNumerosTel = preg_replace('/\D/', '', $datos['telefono']);
        if (strlen($soloNumerosTel) !== 10) {
            $errores[] = "El teléfono debe contener exactamente 10 dígitos (ej: 3312345678 o 345-103-2345).";
        }
    }

    // El correo es obligatorio
    if (empty($datos['correo'])) {
        $errores[] = "El correo es obligatoria.";
        //$datos['correo'] = NULL;
    }elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del correo no es válido.";
    }else{
        $dominio = explode('@', $datos['correo'])[1];
        // Verifica registros MX o A (algunos dominios usan registro A como respaldo)
        if(!checkdnsrr($dominio, 'MX') && !checkdnsrr($dominio, 'A')){
            $errores[] = "El dominio del correo no existe o no admite mensajes.";
        }
    }

    //El domicilio es obligatorio
    if (empty($datos['domicilio'])) {
        $errores[] = "El domicilio es obligatoria.";
        //$datos['domicilio'] = NULL;
    }else if (strlen($datos['domicilio']) > 35) {
        $errores[] = "El domicilio no puede tener mas de 35 letras";
    }else if (!preg_match('/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/u', $datos['domicilio'])) {
        $errores[] = "El domicilio debe incluir el nombre de la calle, no solo números.";
    }

    //La ciudad es obligatorio
    if (empty($datos['ciudad'])) {
        $errores[] = "La ciudad es obligatoria.";
        //$datos['ciudad'] = NULL;
    }else if (strlen($datos['ciudad']) > 20) {
        $errores[] = "La ciudad no puede tener mas de 20 letras";
    }else if (preg_match('/\d/', $datos['ciudad'])) {
        $errores[] = "La ciudad no puede contener números.";
    }

    //El estado es obligatorio
    if (empty($datos['estado'])) {
        $errores[] = "El estado es obligatoria.";
        //$datos['estado'] = NULL;
    }else if (strlen($datos['estado']) > 20) {
        $errores[] = "El estado no puede tener mas de 20 letras";
    }else if (preg_match('/\d/', $datos['estado'])) {
        $errores[] = "El estado no puede contener números.";
    }

    //El Codigo postal es obligatorio
    if (empty($datos['codigo_postal'])) {
        $errores[] = "El codigo postal es obligatoria.";
        //$datos['codigo_postal'] = NULL;
    }else if (strlen($datos['codigo_postal']) > 10) {
        $errores[] = "El codigo postal no puede tener mas de 10 letras";
    }else if(!is_numeric($datos['codigo_postal'])){
        $errores[] = "El codigo postal debe de ser puros numeros";
    }

    //La condicion de pago es obligatorio
    if (empty($datos['condicion_pago'])) {
        $errores[] = "La condición de pago es obligatoria.";
        //$datos['condicion_pago'] = NULL;
    }else if (strlen($datos['condicion_pago']) > 30) {
        $errores[] = "La condición de pago no puede tener mas de 30 caracteres";
    }

    // El tiempo de entrega debe ser obligatorio
    if (empty($datos['tiempo_entrega']) || !is_numeric($datos['tiempo_entrega'])) {
        $errores[] = "El tiempo de entrega es obligatorio y debe ser un valor numerico.";
    }else{
        $ent= $datos['tiempo_entrega'];
        $datos['tiempo_entrega'] = (int)$ent;
        
        if ($datos['tiempo_entrega'] < 0){
            $errores[] = "El tiempo de entrega debe ser un numero mayor a 0.";
        }
    }

    //Las observaciones es opcional
    if (empty($datos['observaciones'])) {
        //$errores[] = "Las observaciones son obligatorias.";
        $datos['observaciones'] = NULL;
    }

    if (count($errores) > 0) {
        // se muestra errores de que campos no se llenaron
        $_SESSION['errores'] = $errores;
        $_SESSION['old']     = $datos;
        header("Location: ../views/agregar_mascota.php");
        exit;
    } else {
        require_once __DIR__ . '/../models/proveedores.php';
        $advert = insertar($datos);
        if (is_array($advert) && count($advert) > 0) {
            if($advert[0] == "Error BD"){
                $_SESSION['errores'] = ["No se pudo conectar a la base de datos: " . $advert[1]];
            }
            if($advert[0] == "Error al Insertar"){
                $_SESSION['errores'] = ["Error al guardar en la base de datos: " . $advert[1]];
            }
        }
    }
}

?>