<?php
/* codigo aqui*/

function validar_datos(){
    //require_once __DIR__ . '/../controllers/validar_mascota.php';
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $datos = [
            'razon_social'      => htmlspecialchars(trim($_POST['razon_social'] ?? '')),
            'nombre_comercial'  => htmlspecialchars(trim($_POST['nombre_comercial'] ?? '')),
            'rfc'               => htmlspecialchars(trim($_POST['rfc'] ?? '')),
            'telefono'          => htmlspecialchars(trim($_POST['telefono'] ?? '')),
            'correo'            => htmlspecialchars(trim($_POST['correo'] ?? '')),
            'domicilio'         => htmlspecialchars(trim($_POST['domicilio'] ?? '')),
            'ciudad'            => htmlspecialchars(trim($_POST['ciudad'] ?? '')),
            'estado'            => htmlspecialchars(trim($_POST['estado'] ?? '')),
            'codigo_postal'     => htmlspecialchars(trim($_POST['codigo_postal'] ?? '')),
            'condicion_pago'    => htmlspecialchars(trim($_POST['condicion_pago'] ?? '')),
            'tiempo_entrega'    => trim($_POST['tiempo_entrega'] ?? ''),
            'observaciones'     => htmlspecialchars(trim($_POST['observaciones'] ?? ''))
        ];

        //validacion($datos);
        $errores = [];
        // La razon social es obligatorio
        if (empty($datos['razon_social'])) {
            $errores[] = "La razon social es obligatoria.";
        }else if (strlen($datos['razon_social']) > 30) {
            $errores[] = "La razon no puede tener mas de 30 letras";
        }

        // Nombre comercial es obligatorio
        if (empty($datos['nombre_comercial'])) {
            $errores[] = "El nombre comercial es obligatoria.";
        }else if (strlen($datos['nombre_comercial']) > 30) {
            $errores[] = "El nombre comerrcial no puede tener mas de 30 letras.";
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
        }else if (strlen($datos['telefono']) > 15) {
            $errores[] = "El telefono no puede tener mas de 15 digitos.";
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
        }

        //La ciudad es obligatorio
        if (empty($datos['ciudad'])) {
            $errores[] = "La ciudad es obligatoria.";
            //$datos['ciudad'] = NULL;
        }else if (strlen($datos['ciudad']) > 20) {
            $errores[] = "La ciudad no puede tener mas de 20 letras";
        }

        //El estado es obligatorio
        if (empty($datos['estado'])) {
            $errores[] = "El estado es obligatoria.";
            //$datos['estado'] = NULL;
        }else if (strlen($datos['estado']) > 20) {
            $errores[] = "El estado no puede tener mas de 20 letras";
        }

        //El Codigo postal es obligatorio
        if (empty($datos['codigo_postal'])) {
            $errores[] = "El codigo postal es obligatoria.";
            //$datos['codigo_postal'] = NULL;
        }else if (strlen($datos['codigo_postal']) > 10) {
            $errores[] = "El codigo postal no puede tener mas de 10 letras";
        }

        //La condicion de pago es obligatorio
        if (empty($datos['condicion_pago'])) {
            $errores[] = "La condición de pago es obligatoria.";
            //$datos['condicion_pago'] = NULL;
        }else if (strlen($datos['condicion_pago']) > 10) {
            $errores[] = "La condición de pago no puede tener mas de 10 letras";
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
            echo "<div style='font-family: Arial; padding: 20px; border: 1px solid red; background: #ffe6e6; width: 400px; border-radius: 5px; margin: 20px auto;'>";
            echo "<h3 style='color: red;'>Fallo la validación:</h3><ul>";
            foreach ($errores as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
            echo "<a href='agregar_mascota.php'>Volver a intentarlo</a>";
            echo "</div>";

        } else {
            require_once __DIR__ . '/../models/proveedores.php';
            $alerta = insertar($datos);
            if($alerta[0] == "Error BD"){
                echo "<div style='font-family: Arial, sans-serif; padding: 12px 15px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 6px; max-width: 600px; margin: 10px auto;'>";
                echo "<strong>Nota de Base de Datos:</strong> No se pudo conectar a la base de datos: " . $alerta[1] . ". <em>Sin embargo, la imagen se guardó correctamente en el servidor.</em>";
                echo "</div>";
            }
            if($alerta[0] == "Error al Insertar"){
                echo "<div style='font-family: Arial, sans-serif; padding: 12px 15px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 6px; max-width: 600px; margin: 10px auto;'>";
                echo "<strong>Aviso al guardar en BD:</strong> " . $alerta[1];
            }
        }

    } else {
        // Si intentan entrar directo a este archivo sin pasar por el formulario
        header("Location: agregar_mascota.php");
        exit();
    }
}

?>