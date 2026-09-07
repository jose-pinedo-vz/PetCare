<?php

// 1. si el formulario es enviado por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // arreglo de errores
    $errores = [];

    // limpieza de datos

    // trim() quita espacios
    // htmlspecialchars() evita que envien codigo malicioso
    $nombre        = isset($_POST['nombre']) ? htmlspecialchars(trim($_POST['nombre'])) : '';
    $especie       = isset($_POST['especie']) ? htmlspecialchars(trim($_POST['especie'])) : '';
    $raza          = isset($_POST['raza']) ? htmlspecialchars(trim($_POST['raza'])) : '';
    $sexo          = isset($_POST['sexo']) ? htmlspecialchars(trim($_POST['sexo'])) : '';
    $color         = isset($_POST['color']) ? htmlspecialchars(trim($_POST['color'])) : '';
    $tamanio       = isset($_POST['tamanio']) ? htmlspecialchars(trim($_POST['tamanio'])) : '';
    $peso          = isset($_POST['peso']) ? trim($_POST['peso']) : '';
    $id_cliente    = isset($_POST['id_cliente']) ? trim($_POST['id_cliente']) : '';
    $observaciones = isset($_POST['observaciones']) ? htmlspecialchars(trim($_POST['observaciones'])) : '';


    // 2. filtrado y validacion de datos segun la base de datos

    // Validar NOMBRE, es olbigatorio
    if (empty($nombre)) {
        $errores[] = "El nombre de la mascota es obligatorio";
    } else if (strlen($nombre) > 20) {
        $errores[] = "El nombre no puede tener mas de 20 letras";
    }

    // Validar ESPECIE, es obligatorio
    if (empty($especie)) {
        $errores[] = "Debes indicar la especie del animal";
    }

    // Validar SEXO, solo puede ser Macho o Hembra
    if (empty($sexo)) {
        $errores[] = "Debes seleccionar el sexo de la mascota";
    } else if ($sexo != "Macho" && $sexo != "Hembra") {
        $errores[] = "El sexo seleccionado no es valido";
    }

    // Validar PESO, debe ser numero mayor a 0
    if (!empty($peso)) {
        if (!is_numeric($peso) || $peso <= 0) {
            $errores[] = "El peso debe ser un numero mayor a 0 (ejemplo: 5.5).";
        }
    }

    // Validar ID CLIENTE, debe ser entero positivo
    if (empty($id_cliente)) {
        $errores[] = "Debes seleccionar a que cliente pertenece la mascota";
    } else if (!is_numeric($id_cliente) || $id_cliente <= 0) {
        $errores[] = "El cliente seleccionado no es valido.";
    }

    // 3. respuesta al usuario

    if (count($errores) > 0) {
        // Si hay errores, los mostramos en pantalla
        echo "<h3>Hubo problemas con el formulario:</h3>";
        echo "<ul>";
        foreach ($errores as $error) {
            echo "<li style='color: red;'>" . $error . "</li>";
        }
        echo "</ul>";
        echo "<a href='javascript:history.back()'>Regresar al formulario</a>";
    } else {
        // todo esta bien
        echo "<h3 style='color: green;'>¡Datos recibidos y validados con éxito!</h3>";
        echo "<p><strong>Mascota:</strong> " . $nombre . "</p>";
        echo "<p><strong>Especie:</strong> " . $especie . "</p>";
        echo "<p><strong>Sexo:</strong> " . $sexo . "</p>";
        echo "<p><strong>Peso:</strong> " . $peso . " kg</p>";
        echo "<p><strong>ID Cliente:</strong> " . $id_cliente . "</p>";
    }
}
