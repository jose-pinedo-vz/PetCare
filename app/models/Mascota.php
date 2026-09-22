<?php

function insertar(array $datos){
    require_once __DIR__ . '/../controllers/validar_mascota.php';
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

    $temperamento = !empty($datos['temperamento']) ? $datos['temperamento'] : 'Equilibrado'; 
    $restricciones = !empty($datos['restricciones_para_manejo']) ? $datos['restricciones_para_manejo'] : null;
    $observaciones = !empty($datos['observaciones']) ? $datos['observaciones'] : 'Sin observaciones iniciales';

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
    //Funcion para eliminar mascota 
function eliminar($id){
    //se eliminara mascota por medio del id 
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $cliente = $id > 0 ? Mascotas::obtenerPorId($id) : null;

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
//Funcion para obtener todas las mascotas 
function obtenerDatosMascotas() {
    if (file_exists(__DIR__ . '/../../conexion_db.php')) {
        include_once __DIR__ . '/../../conexion_db.php';
    } else {
        include_once "conexion_db.php";
    }

    if (!isset($conexion) || !$conexion || $conexion->connect_errno) {
        return [];
    }

    $sql = "SELECT id_mascota, nombre, especie, raza, sexo, edad, color, peso, tamanio,
                   id_cliente, observaciones
            FROM mascotas
            WHERE esta_activo = 1
            ORDER BY id_mascota DESC";

    $resultado = mysqli_query($conexion, $sql);

    if (!$resultado) {
        return [];
    }

    $mascotas = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $mascotas[] = $fila;
    }

    return $mascotas;
}