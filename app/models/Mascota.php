<?php

if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

function IDmascota(PDO $conexion): int {
    try {
        // Consultamos el ID máximo actual y le sumamos 1
        $stmt = $conexion->query("SELECT COALESCE(MAX(id_mascota), 0) + 1 FROM mascotas");
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 1;
    }
}

function insertar(array $datos) {
    try {
        $conexion = ConexionDB::obtenerConexion();
    } catch (Exception $e) {
        echo "<div style='font-family: Arial, sans-serif; padding: 12px 15px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 6px; max-width: 600px; margin: 10px auto;'>";
        echo "<strong>Nota de Base de Datos:</strong> No se pudo conectar a la base de datos: " . htmlspecialchars($e->getMessage()) . ". <em>Sin embargo, la imagen se guardó correctamente en el servidor.</em>";
        echo "</div>";
        return;
    }

    $id = IDmascota($conexion);

    $temperamento  = !empty($datos['temperamento']) ? $datos['temperamento'] : 'Equilibrado'; 
    $restricciones = !empty($datos['restricciones_para_manejo']) ? $datos['restricciones_para_manejo'] : null;
    $observaciones = !empty($datos['observaciones']) ? $datos['observaciones'] : 'Sin observaciones iniciales';
    $fotografia    = !empty($datos['fotografia']) ? $datos['fotografia'] : null;
    $id_veterinario = !empty($datos['id_veterinario']) ? (int)$datos['id_veterinario'] : null;

    $insert = "INSERT INTO mascotas (
        id_mascota, nombre, especie, raza, sexo, edad, color, peso, tamanio, fotografia,
        id_cliente, id_veterinario, alergias, enfermedades, medicamentos,
        condiciones_especiales, temperamento, restricciones_para_manejo,
        vacunas, ultima_desparasitacion, observaciones, esta_activo
    ) VALUES (
        :id, :nombre, :especie, :raza, :sexo, :edad, :color, :peso, :tamanio, :fotografia,
        :id_cliente, :id_veterinario, :alergias, :enfermedades, :medicamentos,
        :condiciones_especiales, :temperamento, :restricciones,
        :vacunas, :ultima_desparasitacion, :observaciones, 1
    )";

    try {
        $stmt = $conexion->prepare($insert);
        $stmt->execute([
            ':id'                     => $id,
            ':nombre'                 => $datos['nombre'],
            ':especie'                => $datos['especie'],
            ':raza'                   => $datos['raza'],
            ':sexo'                   => $datos['sexo'],
            ':edad'                   => $datos['edad'],
            ':color'                  => $datos['color'] ?? null,
            ':peso'                   => $datos['peso'],
            ':tamanio'                => $datos['tamanio'] ?? null,
            ':fotografia'             => $fotografia,
            ':id_cliente'             => $datos['id_cliente'],
            ':id_veterinario'         => $id_veterinario,
            ':alergias'               => $datos['alergias'] ?? null,
            ':enfermedades'           => $datos['enfermedades'] ?? null,
            ':medicamentos'           => $datos['medicamentos'] ?? null,
            ':condiciones_especiales' => $datos['condiciones_especiales'] ?? null,
            ':temperamento'           => $temperamento,
            ':restricciones'          => $restricciones,
            ':vacunas'                => $datos['vacunas'] ?? null,
            ':ultima_desparasitacion' => $datos['ultima_desparasitacion'] ?? null,
            ':observaciones'          => $observaciones
        ]);

        echo "<div style='font-family: Arial, sans-serif; padding: 12px 15px; background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; border-radius: 6px; max-width: 600px; margin: 10px auto;'>";
        echo "<strong>La mascota fue registrada correctamente</strong> (ID Asignado: $id)";
        echo "</div>";
    } catch (PDOException $e) {
        echo "<div style='font-family: Arial, sans-serif; padding: 12px 15px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 6px; max-width: 600px; margin: 10px auto;'>";
        if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1452) {
            echo "<strong>Aviso de Base de Datos:</strong> El Cliente (ID: " . htmlspecialchars((string)$datos['id_cliente']) . ") o el Veterinario (ID: " . htmlspecialchars((string)($datos['id_veterinario'] ?? '')) . ") no existen en sus respectivas tablas. Se requiere que existan previamente para poder vincular la mascota.";
        } else {
            echo "<strong>Aviso al guardar en BD:</strong> " . htmlspecialchars($e->getMessage());
        }
        echo "<br><small>Nota: La fotografía ya quedó guardada en el servidor.</small>";
        echo "</div>";
    }
}

// Funcion para eliminar mascota (borrado logico)
function eliminar(int $id): bool {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "UPDATE mascotas SET esta_activo = 0 WHERE id_mascota = ?";
        $consulta = $conexion->prepare($sql);
        return $consulta->execute([$id]);
    } catch (Exception $e) {
        error_log("Error en eliminar mascota: " . $e->getMessage());
        return false;
    }
}

// Funcion para obtener todas las mascotas activas
function obtenerDatosMascotas(): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "SELECT id_mascota, nombre, especie, raza, sexo, edad, color, peso, tamanio, fotografia,
                       id_cliente, id_veterinario, observaciones, esta_activo
                FROM mascotas
                WHERE esta_activo = 1
                ORDER BY id_mascota DESC";

        $resultado = $conexion->query($sql);
        if (!$resultado) {
            return [];
        }

        return $resultado->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        error_log("Error en obtenerDatosMascotas: " . $e->getMessage());
        return [];
    }
}

// Funcion para obtener una sola mascota por su ID (para precargar el formulario de editar)
function obtenerMascotaPorId(int $id): ?array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "SELECT * FROM mascotas WHERE id_mascota = ?";
        $consulta = $conexion->prepare($sql);
        $consulta->execute([$id]);
        $mascota = $consulta->fetch(PDO::FETCH_ASSOC);

        return $mascota ?: null;
    } catch (Exception $e) {
        error_log("Error en obtenerMascotaPorId: " . $e->getMessage());
        return null;
    }
}

class Mascota {
    public static function obtenerTodos(): array {
        return obtenerDatosMascotas();
    }
    public static function obtenerPorId(int $id): ?array {
        return obtenerMascotaPorId($id);
    }
    public static function insertar(array $datos): void {
        insertar($datos);
    }
    public static function eliminar(int $id): bool {
        return eliminar($id);
    }
}

if (!class_exists('Mascotas', false)) {
    class_alias('Mascota', 'Mascotas');
}