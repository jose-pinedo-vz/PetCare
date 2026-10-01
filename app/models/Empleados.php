<?php
declare(strict_types = 1);
require_once __DIR__ . '//conexion_db.php';

class Empleados {

    public static function generarIdEmpleados(PDO $db): int {
        do {
            $aleatorio = random_int(100, 9999);
            $consulta = $db -> prepare(
                "
                SELECT 1
                FROM empleados
                WHERE id_empleado = ?
                "
            );
            $consulta -> execute([$aleatorio]);
            $exite = (bool) $consulta -> fetch();

        } while ($exite);

        return $aleatorio;
    }

    public static function obtenerTodos(): array {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $consulta_obtener = $conexion -> query(
            "
            SELECT *
            FROM empleados
            WHERE esta_activo=1
            ORDER BY id_empleado DESC
            "
            );

            return $consulta_obtener -> fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $error) {
            return [];
        }
    }

    public static function obtenerporId(int $id) : ?array {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $consulta = $conexion -> prepare(
            "
            SELECT *
            FROM empleados
            WHERE id_empleado = ?
            "
            );
            $consulta -> execute([$id]);

            $empleado = $consulta -> fetch(PDO::FETCH_ASSOC);
            return $empleado ? : null;
        }
        catch (Exception $error) {
            return null;
        }
    }

    public static function insertar(array $datos) : array {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $id = obtenerPorId($conexion);

            $consulta_sql = "
            INSERT INTO empleados (
                id_cliente, nombre, apellido, telefono, correo, calle,
                numero_exterior, numero_interior, colonia, ciudad, estado,
                codigo_postal, puesto, especialidad, num_cedula_profecional,
                Fecha_de_contratacion, horario
            )
            VALUES (
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?
            )
            ";

            $consulta = $conexion -> prepare($consulta_sql);
            $consulta -> execute([
                $datos['nombre'],
                $datos['apellido'],
                $datos['telefono'],
                $datos['correo'],
                $datos['calle'],
                $datos['numero_exterior'],
                !empty($datos['numero_interior']) ? $datos['numero_interior'] : null,
                $datos['colonia'],
                $datos['ciudad'],
                $datos['estado'],
                $datos['codigo_postal'],
                $datos['puesto'],
                !empty($datos['especialidad']) ? $datos['especialidad'] : null,
                !empty($datos['num_cedula_profecional']) ? $datos['num_cedula_profecional'] : null,
                $datos['horario'],
                $id
            ]);

            return ['exito' => true, 'mensaje' => 'Empleado actualizado con exito'];
        }
        catch (Exception $error) {
            return ['exito' => false, 'mensaje' => $error -> getMessage()];
        }
    }

    public static function eliminar (int $id) : bool {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $consulta = $conexion -> prepare(
            "
            UPDATE empleados
            SET esta_activo = 0
            WHERE id_empleado = ?
            AND esta_activo = 1
            "
            );
            return $consulta -> execute([$id]);
        }
        catch (Exception $error) {
            return false;
        }
    }

    public static function Cambiar_estado_empleado (int $id_empleado) : bool {
        $conexion = ConexionDB::obtenerConexion();
        $consulta_sql = "
            UPDATE empleados
            SET esta_activo = 0
            WHERE id_empleado = ?
        ";

        $consulta = $conexion -> prepare($consulta_sql);
        return $consulta -> execute([$id_empleado]);
    }

    public static function Listar_empleados_activos() : array {
        $conexion = ConexionDB::obtenerConexion();

        $consulta_sql = "
            SELECT *
            FROM empleados
            WHERE esta_activo = 1
            ORDER BY id_empleado
        ";

        $consulta = $conexion -> query($consulta_sql);
        return $consulta -> fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
