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
        $sql="select id, nombre, especie from lista_vacunas where estado=1";

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
