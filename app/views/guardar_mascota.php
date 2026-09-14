<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $datos = [
        'nombre'                 => htmlspecialchars(trim($_POST['nombre'] ?? '')),
        'especie'                => htmlspecialchars(trim($_POST['especie'] ?? '')),
        'raza'                   => htmlspecialchars(trim($_POST['raza'] ?? '')),
        'sexo'                   => htmlspecialchars(trim($_POST['sexo'] ?? '')),
        'edad'                   => trim($_POST['edad'] ?? ''),
        'color'                  => htmlspecialchars(trim($_POST['color'] ?? '')),
        'peso'                   => trim($_POST['peso'] ?? ''),
        'tamanio'                => htmlspecialchars(trim($_POST['tamanio'] ?? '')),
        'fotografia'             => $_FILES['fotografia'] ?? null,
        'id_cliente'             => trim($_POST['id_cliente'] ?? ''),
        'id_veterinario'         => trim($_POST['id_veterinario'] ?? ''),
        'alergias'               => htmlspecialchars(trim($_POST['alergias'] ?? '')),
        'enfermedades'           => htmlspecialchars(trim($_POST['enfermedades'] ?? '')),
        'medicamentos'           => htmlspecialchars(trim($_POST['medicamentos'] ?? '')),
        'condiciones_especiales' => htmlspecialchars(trim($_POST['condiciones_especiales'] ?? '')),
        'vacunas'                => htmlspecialchars(trim($_POST['vacunas'] ?? '')),
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
        // Se muestran los campos procesados
        echo "<div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #4CAF50; background: #e8f5e9; max-width: 600px; border-radius: 8px; margin: 20px auto;'>";
        echo "<h2 style='color: #2e7d32; margin-top: 0;'>formulario procesado correctamente</h2>";

        if ($resultadoImagen && $resultadoImagen['exito']) {
            echo "<div style='background: #c8e6c9; padding: 12px 15px; border-radius: 6px; margin-bottom: 15px;'>";
            echo "<p style='color: #1b5e20; margin: 0; font-size: 16px; font-weight: bold;'>✔ " . htmlspecialchars($resultadoImagen['mensaje']) . "</p>";
            echo "<p style='margin: 6px 0 0 0; font-size: 14px;'><strong>Ruta guardada para la BD:</strong> <code>" . htmlspecialchars($resultadoImagen['ruta']) . "</code></p>";
            echo "</div>";
            echo "<div style='text-align: center; margin: 15px 0;'>";
            echo "<p style='margin-bottom: 8px;'><strong>Vista previa de la imagen subida:</strong></p>";
            echo "<img src='../../" . htmlspecialchars($resultadoImagen['ruta']) . "' alt='Foto de " . htmlspecialchars($datos['nombre']) . "' style='max-width: 250px; max-height: 200px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); object-fit: cover;'>";
            echo "</div>";
        }

        echo "<p><strong>Datos confirmados de la mascota:</strong></p>";
        echo "<ul>";
        foreach ($datos as $campo => $valor){
            if ($campo === 'fotografia') continue;
            echo "<li><strong>" . htmlspecialchars((string)$campo) . ":</strong> " . htmlspecialchars((string)($valor ?? 'N/A')) . "</li>";
        }
        echo "</ul>";
        echo "<div style='margin-top: 20px;'>";
        echo "<a href='mascotas.php' style='display: inline-block; padding: 8px 16px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>← Volver a Mascotas</a> ";
        echo "<a href='agregar_mascota.php' style='display: inline-block; padding: 8px 16px; background: #2196F3; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;'>+ Registrar otra mascota</a>";
        echo "</div>";
        echo "</div>";

        insertar($datos);
    }
}



function insertar($datos){
    if (file_exists(__DIR__ . '/../../conexion_db.php')) {
        include_once __DIR__ . '/../../conexion_db.php';
    } else {
        include_once "conexion_db.php";
    }

    if (!isset($conexion) || !$conexion || $conexion->connect_errno) {
        echo "<div style='font-family: Arial, sans-serif; padding: 12px 15px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 6px; max-width: 600px; margin: 10px auto;'>";
        echo "<strong>Nota de Base de Datos:</strong> No se pudo conectar a la base de datos. <em>Sin embargo, la imagen se guardó correctamente en el servidor.</em>";
        echo "</div>";
        return;
    }

    $id = IDmascota($conexion);

    $temperamento = 'Equilibrado'; 
    $restricciones = null;
    $observaciones = 'Sin observaciones iniciales';

    $insert = "INSERT INTO mascotas (
        id_mascota, nombre, especie, raza, sexo, edad, color, peso, tamanio, fotografia,
        id_cliente, id_veterinario, alergias, enfermedades, medicamentos,
        condiciones_especiales, temperamento, restricciones_para_manejo,
        vacunas, ultima_desparasitacion, observaciones
    )VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    

    $consulta = mysqli_prepare($conexion, $insert);
    if ($consulta) {
        $tipos = "issssssdssiisssssssss";

        mysqli_stmt_bind_param($consulta,
            $tipos,
            $id,
            $datos['nombre'],
            $datos['especie'],
            $datos['raza'],
            $datos['sexo'],
            $datos['edad'],
            $datos['color'],
            $datos['peso'],
            $datos['tamanio'],
            $datos['fotografia'],
            $datos['id_cliente'],
            $datos['id_veterinario'],
            $datos['alergias'],
            $datos['enfermedades'],
            $datos['medicamentos'],
            $datos['condiciones_especiales'],
            $temperamento,
            $restricciones,
            $datos['vacunas'],
            $datos['ultima_desparasitacion'],
            $observaciones
        );

        try {
            // ejecutamos la consulta
            mysqli_stmt_execute($consulta);
            echo "<div style='font-family: Arial, sans-serif; padding: 12px 15px; background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; border-radius: 6px; max-width: 600px; margin: 10px auto;'>";
            echo "<strong>la mascota fue registrada correctamente</strong> (ID Asignado: $id)";
            echo "</div>";

        } catch (mysqli_sql_exception $e) {
            echo "<div style='font-family: Arial, sans-serif; padding: 12px 15px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 6px; max-width: 600px; margin: 10px auto;'>";
            if (mysqli_errno($conexion) === 1452) {
                echo "<strong>Aviso de Base de Datos:</strong> El Cliente (ID: " . htmlspecialchars((string)$datos['id_cliente']) . ") o el Veterinario no existen en sus respectivas tablas. Se requiere crear primero el cliente para vincular la mascota.";
            } else {
                echo "<strong>Aviso al guardar en BD:</strong> " . htmlspecialchars($e->getMessage());
            }
            echo "<br><small>Nota: La fotografia ya quedo guardada en el servidor.</small>";
            echo "</div>";
        } finally {
            // cerramos la consulta
            mysqli_stmt_close($consulta);
        }
    }
}


function IDmascota($conexion) {
    do {
        $aleatorio = random_int(100, 9999);

        // hacemos la consulta
        $consulta = @mysqli_prepare($conexion, "SELECT 1 FROM mascotas WHERE id_mascota = ?");
        if (!$consulta) {
            return $aleatorio;
        }

        // le paso el id que se creo
        mysqli_stmt_bind_param($consulta, "i", $aleatorio);

        // Enviamos el id creado
        @mysqli_stmt_execute($consulta);

        // Esperamos el resultado
        $resultado = @mysqli_stmt_get_result($consulta);

        // Cuenta las lineas que tenian el id
        $existe = ($resultado && mysqli_num_rows($resultado) > 0);

        // Cerramos la consulta
        mysqli_stmt_close($consulta);
    } while ($existe);

    return $aleatorio;
}

?>