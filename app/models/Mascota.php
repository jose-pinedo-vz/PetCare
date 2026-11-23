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

    $temperamento   = !empty($datos['temperamento']) ? $datos['temperamento'] : 'Equilibrado';
    $restricciones  = !empty($datos['restricciones_para_manejo']) ? $datos['restricciones_para_manejo'] : null;
    $observaciones  = !empty($datos['observaciones']) ? $datos['observaciones'] : 'Sin observaciones iniciales';
    $fotografia     = !empty($datos['fotografia']) ? $datos['fotografia'] : null;
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
        $conexion->beginTransaction();
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
            ':temperamento'           => $datos['temperamento'] ?? null,
            ':restricciones'          => $restricciones,
            // ':vacunas'                => $datos['vacunas'] ?? null,
            ':vacunas'                => null,
            ':ultima_desparasitacion' => $datos['ultima_desparasitacion'] ?? null,
            ':observaciones'          => $datos['observaciones'] ?? null
        ]);

        $insertarVacuna = $conexion->prepare("
            INSERT INTO vacunas (id_mascota, vacuna_puesta, fecha_aplicacion, veterinario)
            VALUES (?, ?, ?, NULL)
        ");

        foreach ((is_array($datos['vacunas'] ?? null) ? $datos['vacunas'] : []) as $idVacuna) {
            $insertarVacuna->execute([$id, (int)$idVacuna, $datos['fechas_vacunas'][$idVacuna] ?? null]);
        }

        $conexion->commit();

    } catch (PDOException $e) {

        if ($conexion->inTransaction()) {   // NUEVO
            $conexion->rollBack();
        }

        if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1452) {
            return ["Error ID"];
        } else {
            return["Error al aguardar",htmlspecialchars($e->getMessage())];
        }
    }
}

function actualizar(int $idMascota, string $sets, array $valoresFinales): bool {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "
        UPDATE mascotas
        SET $sets
        WHERE id_mascota = ?
        ";
        $sentenciaActualizacion = $conexion -> prepare($sql);
        $sentenciaActualizacion -> execute([...array_values($valoresFinales), $idMascota]);

        return  true;

    } catch (Exception $e) {
        error_log("Error en actualizar mascota: " . $e -> getMessage());
        return false;
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

// Funcion para obtener las mascotas activas de UN cliente (lado cliente)
function obtenerMascotasPorCliente(int $idCliente): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "SELECT id_mascota, nombre, especie, raza, sexo, edad, peso, fotografia, vacunas
                FROM mascotas
                WHERE id_cliente = ? AND esta_activo = 1
                ORDER BY id_mascota DESC";

        $consulta = $conexion->prepare($sql);
        $consulta->execute([$idCliente]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        error_log("Error en obtenerMascotasPorCliente: " . $e->getMessage());
        return [];
    }
}

// Convierte la fecha de nacimiento (columna `edad`) en un texto como "3 años" o "5 meses"
function textoEdadMascota(?string $fechaNacimiento): string {
    if (empty($fechaNacimiento)) {
        return 'Edad no registrada';
    }

    try {
        $diferencia = (new DateTime($fechaNacimiento))->diff(new DateTime('today'));
    } catch (Exception $e) {
        return 'Edad no registrada';
    }

    if ($diferencia->y >= 1) {
        return $diferencia->y . ($diferencia->y === 1 ? ' año' : ' años');
    }

    return $diferencia->m . ($diferencia->m === 1 ? ' mes' : ' meses');
}

// Devuelve la ruta de la foto (ej. "uploads/mascotas/pet_x.jpg") solo si el archivo existe en disco.
// Si no hay foto, o el archivo no esta, devuelve null.
function rutaFotoMascota(?string $ruta): ?string {
    $ruta = trim((string) $ruta);
    if ($ruta === '') {
        return null;
    }

    $rutaEnDisco = __DIR__ . '/../../' . $ruta;

    return file_exists($rutaEnDisco) ? $ruta : null;
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

function catalogoVacunasMascota(): array {
    try {
        $conexion = ConexionDB::obtenerConexion();

        $sql = "SELECT id, nombre, especie FROM lista_vacunas WHERE estado = 1 ORDER BY nombre ASC";
        $resultado = $conexion -> query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado -> fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $error) {
        error_log("Error al obtener el catalogo de vacunas " . $error -> getMessage());
        return [];
    }
}

function obtenerVacunasMascota(int $idMascota): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "
            SELECT vacuna_puesta, fecha_aplicacion, veterinario
            FROM vacunas
            WHERE id_mascota = ? 
            ORDER BY fecha_aplicacion DESC
        ";
        $consulta = $conexion -> prepare($sql);
        $consulta -> execute([$idMascota]);

        if (!$consulta) {
            return [];
        }

        return $consulta -> fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $error) {
        error_log("Error en obtener vacunas de la mascota " . $error -> getMessage());
        return [];
    }
}

function sincronizarVacunasMascota(int $idMascota, array $ids, array $fechas): bool {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $conexion -> beginTransaction();

        $administradas = array_map('intval', $conexion -> query("SELECT id FROM lista_vacunas WHERE estado = 1") -> fetchAll(PDO::FETCH_COLUMN));

        // aplicadas en petcare
        $consulta = $conexion -> prepare("SELECT DISTINCT vacuna_puesta FROM vacunas WHERE id_mascota = ? AND veterinario IS NOT NULL");
        $consulta -> execute([$idMascota]);
        $deClinica = array_map('intval', $consulta -> fetchAll(PDO::FETCH_COLUMN));
        
        //quitar la que esten desmarcadas
        $quitar = array_values(array_diff($administradas, array_map('intval', $ids), $deClinica));

        if ($quitar) {
            $marcadas = implode(',', array_fill(0, count($quitar), '?'));
            $consulta = $conexion -> prepare("DELETE FROM vacunas WHERE id_mascota = ? AND veterinario IS NULL AND vacuna_puesta IN ($marcadas)");
            $consulta -> execute([$idMascota, ...$quitar]);
        }

        $buscar = $conexion -> prepare("SELECT COUnt(*) FROM vacunas WHERE id_mascota = ? AND vacuna_puesta = ? AND veterinario IS NULL");
        $actualizar = $conexion -> prepare("UPDATE vacunas SET fecha_aplicacion = ? WHERE id_mascota = ? AND vacuna_puesta = ? AND veterinario IS NULL");
        $insertar = $conexion -> prepare("INSERT INTO vacunas (id_mascota, vacuna_puesta, fecha_aplicacion, veterinario) VALUES (?, ?, ?, NULL)");

        foreach ($ids as $idVacuna) {
            $idVacuna = (int) $idVacuna;

            if (in_array($idVacuna, $deClinica, true)) {
                continue;
            }

            $fecha = $fechas[$idVacuna] ?? null;

            $buscar -> execute([$idMascota, $idVacuna]);
            if ((int) $buscar -> fetchColumn() > 0) {
                $actualizar -> execute([$fecha, $idMascota, $idVacuna]);
            } else {
                $insertar -> execute([$idMascota, $idVacuna, $fecha]);
            }
        }

        $conexion -> commit();
        return true;

    } catch (Exception $error) {
        if (isset($conexion) && $conexion -> inTransaction()) {
            $conexion -> rollBack();
        }
        error_log("Error en sincronizar vacunas de la mascota " . $error -> getMessage());
        return false;
    }
}

class Mascota {
    public static function obtenerVeterinariosActivos(): array {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $stmt = $conexion->query("
                SELECT id_empleado, nombre, apellido
                FROM empleados
                WHERE esta_activo = 1
                    AND LOWER(TRIM(puesto)) LIKE 'veterinario%'
                ORDER BY nombre, apellido
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("Error al consultar veterinarios activos: " . $e->getMessage());
            return [];
        }
    }

    public static function esVeterinarioActivo(int $id_empleado): bool {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $stmt = $conexion->prepare("
                SELECT 1
                FROM empleados
                WHERE id_empleado = ?
                    AND esta_activo = 1
                    AND LOWER(TRIM(puesto)) LIKE 'veterinario%'
                LIMIT 1
            ");
            $stmt->execute([$id_empleado]);
            return $stmt->fetchColumn() !== false;
        } catch (Exception $e) {
            error_log("Error al validar veterinario activo: " . $e->getMessage());
            return false;
        }
    }

    public static function obtenerEspecies(): array {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $stmt = $conexion->query("SELECT id, especie FROM especies ORDER BY especie");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("Error al consultar especies: " . $e->getMessage());
            return [];
        }
    }

    public static function obtenerRazas(): array {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $stmt = $conexion->query("SELECT id, especie, raza FROM razas ORDER BY raza");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("Error al consultar razas: " . $e->getMessage());
            return [];
        }
    }

    public static function especieRazaExiste(string $especie, string $raza): bool {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $stmt = $conexion->prepare("
                SELECT 1
                FROM especies e
                INNER JOIN razas r ON r.especie = e.id
                WHERE e.especie = ? AND r.raza = ?
                LIMIT 1
            ");
            $stmt->execute([$especie, $raza]);
            return $stmt->fetchColumn() !== false;
        } catch (Exception $e) {
            error_log("Error al validar especie y raza de mascota: " . $e->getMessage());
            return false;
        }
    }

    public static function obtenerTodos(): array {
        return obtenerDatosMascotas();
    }
    public static function obtenerPorCliente(int $idCliente): array {
        return obtenerMascotasPorCliente($idCliente);
    }
    public static function obtenerPorId(int $id): ?array {
        return obtenerMascotaPorId($id);
    }
    public static function insertar(array $datos){
        return insertar($datos);
    }
    public static function actualizar(int $idMascota, string $sets, array $valoresFinales): bool {
        return actualizar($idMascota, $sets, $valoresFinales);
    }
    public static function eliminar(int $id): bool {
        return eliminar($id);
    }
    public static function catalogoVacunasMascota(): array {
        return catalogoVacunasMascota();
    }
    public static function obtenerVacunasMascota(int $idMascota): array {
        return obtenerVacunasMascota($idMascota);
    }
    public static function sincronizarVacunasMascota(int $idMascota, array $ids, array $fechas): bool {
        return sincronizarVacunasMascota($idMascota, $ids, $fechas);
    }
}

if (!class_exists('Mascotas', false)) {
    class_alias('Mascota', 'Mascotas');
}
