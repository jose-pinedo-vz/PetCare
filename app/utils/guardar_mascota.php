<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

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
        'ultima_desparasitacion' => trim($_POST['ultima_desparasitacion'] ?? '')
    ];

    validacion($datos);

} else {
    // Si intentan entrar directo a este archivo sin pasar por el formulario
    header("Location: agregar_mascotas.php");
    exit();
}



function validacion($datos){
    $errores = [];

    // Nombre obligatorio
    if (empty($datos['nombre'])) {
        $errores[] = "El nombre de la mascota es obligatorio.";
    }
    // Especie obligatorio
    if (empty($datos['especie'])) {
        $errores[] = "Debe especificar la especie.";
    }
    // Raza obligatorio
    if (empty($datos['raza'])) {
        $errores[] = "Debe especificar la raza.";
    }
    // Sexo obligatorio
    if (empty($datos['sexo'])) {
        $errores[] = "Debe seleccionar el sexo.";
    }
    // Edad obligatoria
    if (empty($datos['edad'])) {
        $errores[] = "Debe especificar la edad.";
    }
    // color opcional
    if (empty($datos['color'])) {
        $datos['color'] = NULL;
    }
    // Peso obligatorio
    if (empty($datos['peso'])) {
        $errores[] = "Debe especificar el peso.";
    }else{
        $pesopunt = str_replace(',', '.', $datos['peso']);

        $datos['peso'] = (float)$pesopunt;
    }
    // Tamanio obligatorio
    if (empty($datos['tamanio'])) {
        $errores[] = "Debe especificar el Tamaño.";
    }
    // Id cliente obligatorio
    if (empty($datos['id_cliente'])) {
        $errores[] = "Debe asociar un cliente válido.";
    }
    // Id veterinario opcional
    if (empty($datos['id_veterinario'])) {
        $datos['id_veterinario'] = NULL;
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
    }
    
    // Poniendo las rutasd de las imagenes
    $foto = $datos['fotografia'];

    $datos['fotografia'] = null;

    // Se verifica si se suvio una imagen correctamente
    if (is_array($foto) and !empty($foto['name']) and $foto['error'] === UPLOAD_ERR_OK) {

        // Creamos un nombre único
        $extension = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $nombreUnico = uniqid('pet_', true) . '.' . $extension;

        // Definimos la carpeta donde se va a guardar la imagen
        $carpetaDestino = 'image/' . $nombreUnico;

        // Si se guarda correctamente en la carpeta pasamos la direccion al diccionario
        if (move_uploaded_file($foto['tmp_name'], $carpetaDestino)) {
        $datos['fotografia'] = $nombreUnico;
    }
}




    if (count($errores) > 0) {
        // Se muestra errores de que campos no se llenaron
        echo "<div style='font-family: Arial; padding: 20px; border: 1px solid red; background: #ffe6e6; width: 400px; border-radius: 5px;'>";
        echo "<h3 style='color: red;'>Falló la validación:</h3><ul>";
        foreach ($errores as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        echo "<a href='agregar_mascota.php'>← Volver a intentarlo</a>";
        echo "</div>";

    } else {
        // se muestran los campos que se llenaron y con sus datos para corroborar si se aguardaron exactamente como se escribio en el formulario
        echo "<div style='font-family: Arial; padding: 20px; border: 1px solid green; background: #e6ffe6; width: 400px; border-radius: 5px;'>";
        echo "<h2 style='color: green;'>✅ ¡Formulario validado con éxito!</h2>";
        echo "<p>Todos los campos pasaron las validaciones correctamente.</p>";
        echo "<hr>";
        echo "<p><strong>Datos confirmados:</strong></p>";
        echo "<ul>";
        foreach ($datos as $campo => $valor){
            echo "<li>$campo: $valor</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
}


?>


