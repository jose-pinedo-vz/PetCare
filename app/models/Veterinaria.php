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
}
?>
