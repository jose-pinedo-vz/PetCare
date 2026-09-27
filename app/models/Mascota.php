<?php

function insertar(array $datos){
    require_once __DIR__ . '/../controllers/validar_mascota.php';
    if (file_exists(__DIR__ . '/../../conexion_db.php')) {
        include_once __DIR__ . '/../../conexion_db.php';
    } else {
        include_once "conexion_db.php";
    }

    try {
        $conexion = ConexionDB::obtenerConexion();
    } catch (Exception $e) {
        echo "<div style='font-family: Arial, sans-serif; padding: 12px 15px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 6px; max-width: 600px; margin: 10px auto;'>";
        echo "<strong>Nota de Base de Datos:</strong> No se pudo conectar a la base de datos: " . htmlspecialchars($e->getMessage()) . ". <em>Sin embargo, la imagen se guardó correctamente en el servidor.</em>";
        echo "</div>";
        return;
    }

    $id = IDmascota($conexion);


    $insert = "INSERT INTO mascotas (
        id_mascota, nombre, especie, raza, sexo, edad, color, peso, tamanio, fotografia,
        id_cliente, id_veterinario, alergias, enfermedades, medicamentos,
        condiciones_especiales, temperamento, restricciones_para_manejo,
        vacunas, ultima_desparasitacion, observaciones
    ) VALUES (
        :id, :nombre, :especie, :raza, :sexo, :edad, :color, :peso, :tamanio, :fotografia,
        :id_cliente, :id_veterinario, :alergias, :enfermedades, :medicamentos,
        :condiciones_especiales, :temperamento, :restricciones,
        :vacunas, :ultima_desparasitacion, :observaciones
    )";

    try {
        // Preparar la consulta con PDO
        $stmt = $conexion->prepare($insert);

        // Ejecutar pasando los valores mapeados
        $stmt->execute([
            ':id'                        => $id,
            ':nombre'                    => $datos['nombre'],
            ':especie'                   => $datos['especie'],
            ':raza'                      => $datos['raza'],
            ':sexo'                      => $datos['sexo'],
            ':edad'                      => $datos['edad'],
            ':color'                     => $datos['color'],
            ':peso'                      => $datos['peso'],
            ':tamanio'                   => $datos['tamanio'],
            ':fotografia'                => $datos['fotografia'],
            ':id_cliente'                => $datos['id_cliente'],
            ':id_veterinario'            => $datos['id_veterinario'],
            ':alergias'                  => $datos['alergias'],
            ':enfermedades'              => $datos['enfermedades'],
            ':medicamentos'              => $datos['medicamentos'],
            ':condiciones_especiales'    => $datos['condiciones_especiales'],
            ':temperamento'              => $datos['temperamento'],
            ':restricciones'             => $datos['restricciones_para_manejo'],
            ':vacunas'                   => $datos['vacunas'],
            ':ultima_desparasitacion'    => $datos['ultima_desparasitacion'],
            ':observaciones'             => $datos['observaciones']
        ]);
        echo "<div style='font-family: Arial, sans-serif; padding: 12px 15px; background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; border-radius: 6px; max-width: 600px; margin: 10px auto;'>";
        echo "<strong>La mascota fue registrada correctamente</strong> (ID Asignado: $id)";
        echo "</div>";
    }catch (PDOException $e) {
        echo "<div style='font-family: Arial, sans-serif; padding: 12px 15px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 6px; max-width: 600px; margin: 10px auto;'>";
        
        // Verificamos si el código de error interno del driver de MySQL es el 1452
        if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1452) {
            echo "<strong>Aviso de Base de Datos:</strong> El Cliente (ID: " . htmlspecialchars((string)$datos['id_cliente']) . ") o el Veterinario (ID: " . htmlspecialchars((string)$datos['id_veterinario']) . ") no existen en sus respectivas tablas. Se requiere que existan previamente para poder vincular la mascota.";
        } else {
            echo "<strong>Aviso al guardar en BD:</strong> " . htmlspecialchars($e->getMessage());
        }
        
        echo "<br><small>Nota: La fotografía ya quedó guardada en el servidor.</small>";
        echo "</div>";
    }
}

        

    //Funcion para eliminar mascota 
function eliminar($id){
    //se eliminara mascota por medio del id 
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $cliente = $id > 0 ? Mascotas::obtenerPorId($id) : null;

}

function IDmascota(PDO $conexion): int {
    try {
        // Consultamos el ID máximo actual y le sumamos 1
        $stmt = $conexion->query("SELECT COALESCE(MAX(id_mascota), 0) + 1 FROM mascotas");
        
        // Obtenemos el valor resultante directamente
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        // En caso de que ocurra un error con la consulta, retorna 1 como respaldo
        return 1;
    }
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
//Funcion para obtener una sola mascota por su ID (para precargar el formulario de editar)
function obtenerMascotaPorId(int $id) {
    if (file_exists(__DIR__ . '/../../conexion_db.php')) {
        include_once __DIR__ . '/../../conexion_db.php';
    } else {
        include_once "conexion_db.php";
    }

    if (!isset($conexion) || !$conexion || $conexion->connect_errno) {
        return null;
    }

    $sql = "SELECT * FROM mascotas WHERE id_mascota = ?";
    $consulta = mysqli_prepare($conexion, $sql);

    if (!$consulta) {
        return null;
    }

    mysqli_stmt_bind_param($consulta, "i", $id);
    mysqli_stmt_execute($consulta);
    $resultado = mysqli_stmt_get_result($consulta);
    $mascota = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($consulta);

    return $mascota ?: null;
}