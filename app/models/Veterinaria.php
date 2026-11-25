<?php 

date_default_timezone_set('America/Mexico_City');

if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

function listaMascotas(): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "
            SELECT id_mascota, nombre
            FROM mascotas
            WHERE esta_activo = 1
        ";

        $resultado = $conexion -> query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado -> fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $error) {
        error_log("Error al obtener la lista de mascotas " . $error -> getMessage());
        return [];
    }
}

function obtenerNombreMascota(int $id_mascota): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "
            SELECT nombre, especie, raza, edad, peso, color, ultima_desparasitacion 
            FROM mascotas
            WHERE id_mascota = ?
        ";

        $consulta = $conexion -> prepare($sql);
        $consulta -> execute([$id_mascota]);

        $resultado = $consulta -> fetch(PDO::FETCH_ASSOC);

        if (!$resultado) {
            return [];
        }

        return $resultado;

    } catch (Exception $error) {
        error_log("Error en obtener el nombre de la mascota " . $error -> getMessage());
        return [];
    }
}

function obtenerDatosPropietario(int $id_mascota): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "
            SELECT clientes.id_cliente, clientes.nombre, telefono, correo
            FROM clientes, mascotas
            WHERE clientes.id_cliente = mascotas.id_cliente AND mascotas.id_mascota = ?
        ";
        $consulta = $conexion -> prepare($sql);
        $consulta -> execute([$id_mascota]);

        $resultado = $consulta -> fetch(PDO::FETCH_ASSOC);

        if (!$resultado) {
            return [];
        }

        return $resultado;


    } catch (Exception $error) {
        error_log("Error en en obtener los datos del propietario de la mascota " . $error -> getMessage());
        return [];
    }
}


function citasHoyVeterinaria(string $fecha): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "
            SELECT cv.id_consulta, cv.fecha, c.nombre AS cliente, cv.motivo_de_consulta, cv.atendido
            FROM consulta_veterinaria cv, clientes c
            WHERE DATE(cv.fecha) = ? AND cv.id_cliente = c.id_cliente
        ";
        $consulta = $conexion -> prepare($sql);
        $consulta -> execute([$fecha]);

        $resultado = $consulta -> fetchAll(PDO::FETCH_ASSOC);

        return $resultado;

    } catch (Exception $error) {
        error_log("Error en obtener las citas de hoy en veterinaria " . $error -> getMessage());
        return [];
    }
}

function obtenerVacunas(): array{
    try{
        $conexion=ConexionDB::obtenerConexion();
        /* $sql="select id, nombre, especie from lista_vacunas where estado=1"; */
        $sql="
            SELECT lv.id, lv.nombre, lv.especie, e.especie AS nombre_especie
            FROM lista_vacunas lv, especies e
            WHERE lv.especie = e.id AND lv.estado=1
        ";

        $resultado = $conexion -> query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado -> fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $error){
        error_log("Error al obtener las vacunas ".$error->getMessage());
        return [];
    }
}

function historialVacunasMascota(int $id_mascota): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "
            SELECT lv.nombre AS vacuna, v.fecha_aplicacion,
                   (SELECT CONCAT(e.nombre, ' ', e.apellido) 
                   FROM empleados e 
                   WHERE e.id_empleado = v.veterinario) AS veterinario
            FROM vacunas v, lista_vacunas lv
            WHERE v.vacuna_puesta = lv.id AND v.id_mascota = ?
            ORDER BY v.fecha_aplicacion IS NULL, v.fecha_aplicacion DESC
        ";
        $consulta = $conexion -> prepare($sql);
        $consulta -> execute([$id_mascota]);

        return $consulta -> fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $error) {
        error_log("Error al obtener el historial de las vacunas de la mascota " . $error -> getMessage());
        return [];
    }
}

function historialVacunas(): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "
            SELECT m.id_mascota, m.nombre AS mascota, lv.nombre AS tipo_vacunas,
                   v.fecha_aplicacion, v.lote, v.fecha_proxima
            FROM vacunas v, mascotas m, lista_vacunas lv
            WHERE v.id_mascota = m.id_mascota AND v.vacuna_puesta = lv.id AND m.esta_activo = 1
            ORDER BY v.fecha_aplicacion IS NULL, v.fecha_aplicacion DESC, v.id_vacuna DESC
            LIMIT 100
        ";

        $resultado = $conexion -> query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado -> fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $error) {
        error_log("Error al obtener el historial de vacunas " . $error -> getMessage());
        return [];
    }
}

function estaRepetida(string $nombre, int $especie, ?int $id = null): bool {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "
            SELECT COUNT(*)
            FROM lista_vacunas
            WHERE LOWER(nombre) = LOWER(?) AND especie = ? AND estado = 1 AND id != ?
        ";
        $consulta = $conexion -> prepare($sql);
        $consulta -> execute([$nombre, $especie, $id ?? 0]);

        $resultado = (int) $consulta -> fetchColumn() > 0;

        if (!$resultado) {
            return false;
        }

        return $resultado;

    } catch (Exception $error) {
        error_log("Error en obtener si esta repedida la vacuna " . $error -> getMessage());
        return false;
    }
}

function agregarVacuna(string $nombre, int $especie): bool {
    try {
        $conexion = ConexionDB::obtenerConexion();

        $sql = "INSERT INTO lista_vacunas (nombre, especie, estado)
                VALUES (?, ?, 1)";

        $consulta = $conexion->prepare($sql);

        return $consulta->execute([$nombre, $especie]);

    } catch (Exception $error) {
        error_log("Error al agregar vacuna: " . $error->getMessage());
        return false;
    }
}

function obtenerEspecies(): array {
    try {
        $conexion = ConexionDB::obtenerConexion();

        $sql = "SELECT id, especie as nombre FROM especies ORDER BY nombre ASC";

        $resultado = $conexion->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $error) {
        error_log("Error al obtener las especies " . $error->getMessage());
        return [];
    }
}

function editarVacuna(int $id, string $nombre, int $especie): bool {
    try {
        $conexion = ConexionDB::obtenerConexion();

        $sql = "update lista_vacunas set nombre = ?, especie = ? WHERE id = ? AND estado = 1";
        $consulta = $conexion->prepare($sql);

        return $consulta->execute([$nombre, $especie, $id]);

    } catch (Exception $error) {
        error_log("Error al editar vacuna: " . $error->getMessage());
        return false;
    }
}

function eliminarVacuna(int $id): bool {
    try {
        $conexion = ConexionDB::obtenerConexion();

        $sql = "update lista_vacunas set estado = 0 where id = ? and estado = 1";

        $consulta = $conexion->prepare($sql);

        return $consulta->execute([$id]);

    } catch (Exception $error) {
        error_log("Error al eliminar vacuna: " . $error->getMessage());
        return false;
    }
}

class Veterinaria {

    public static function listaMascotas(): array {
        return listaMascotas();
    }

    public static function obtenerNombreMascota(int $id_mascota): array {
        return obtenerNombreMascota($id_mascota);
    }

    public static function obtenerDatosPropietario(int $id_mascota): array {
        return obtenerDatosPropietario($id_mascota);
    }


    public static function citasHoyVeterinaria(string $fecha): array {
        return citasHoyVeterinaria($fecha);
    }

    public static function obtenerVacunas(): array {
        return obtenerVacunas();
    }

    public static function historialVacunasMascota(int $id_mascota): array {
        return historialVacunasMascota($id_mascota);
    }

    public static function historialVacunas(): array {
        return historialVacunas();
    }

    public static function estaRepetida(string $nombre, int $especie, ?int $id = null): bool {
        return estaRepetida($nombre, $especie, $id);
    }

    public static function obtenerEspecies(): array {
        return obtenerEspecies();
    }

    public static function agregarVacuna(string $nombre, int $especie): bool {
        return agregarVacuna($nombre, $especie);
    }

    public static function editarVacuna(int $id, string $nombre, int $especie): bool {
    return editarVacuna($id, $nombre, $especie);
    }
    
    public static function eliminarVacuna(int $id): bool {
        return eliminarVacuna($id);
    }
}
?>
