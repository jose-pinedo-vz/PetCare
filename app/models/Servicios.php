<?php
// Modelo de servicios
if (!isset($_SESSION['usuario'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once __DIR__ . '/conexion_db.php';

class Servicios
{
    // Trae todos los servicios
    public static function obtenerTodos(): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $sql = "SELECT id_servicio, nombre, categoria, descripcion, duracion,
                           precio, id_empleados, estado + 0 AS estado
                    FROM servicios
                    ORDER BY id_servicio";
            $r = $db->query($sql);
            return $r ? $r->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Exception $e) {
            error_log("Error en Servicios::obtenerTodos: " . $e->getMessage());
            return [];
        }
    }
    // Historial de citas de servicios (datos reales de la BD)
    public static function obtenerHistorial(): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            // Las bases no son iguales: la hora puede venir en la columna hora o dentro de fecha
            $cols = $db->query("SHOW COLUMNS FROM citas")->fetchAll(PDO::FETCH_COLUMN);
            $horaSql = in_array('hora', $cols, true) ? 'ci.hora' : 'TIME(ci.fecha)';
            $estadoSql = in_array('estado_cita', $cols, true) ? 'ci.estado_cita' : 'ci.estado';
            $sql = "SELECT ci.id_cita, DATE(ci.fecha) AS fecha, $horaSql AS hora,
                           COALESCE(CONCAT(cl.nombre, ' ', cl.apellido), 'Sin cliente') AS cliente,
                           COALESCE(m.nombre, '-') AS mascota,
                           COALESCE(s.nombre, '-') AS servicio,
                           COALESCE(CONCAT(e.nombre, ' ', e.apellido), 'Sin asignar') AS empleado,
                           ci.duracion_estimada, $estadoSql AS estado, ci.observacion
                    FROM citas ci
                    LEFT JOIN clientes cl ON cl.id_cliente = ci.cliente
                    LEFT JOIN mascotas m ON m.id_mascota = ci.mascota
                    LEFT JOIN servicios s ON s.id_servicio = ci.servicio
                    LEFT JOIN empleados e ON e.id_empleado = ci.empleado
                    ORDER BY ci.fecha DESC, $horaSql DESC";
            $r = $db->query($sql);
            return $r ? $r->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Exception $e) {
            error_log("Error en Servicios::obtenerHistorial: " . $e->getMessage());
            return [];
        }
    }
        // Siguiente id (la tabla no es autoincremental)
    private static function siguienteId(PDO $db): int
    {
        $r = $db->query("SELECT COALESCE(MAX(id_servicio), 0) + 1 FROM servicios");
        return (int) $r->fetchColumn();
    }

    // Revisa si ya existe un servicio con ese nombre
    public static function existeNombre(string $nombre, int $excluirId = 0): bool
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare("SELECT COUNT(*) FROM servicios WHERE nombre = ? AND id_servicio <> ?");
            $stmt->execute([$nombre, $excluirId]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            error_log("Error en Servicios::existeNombre: " . $e->getMessage());
            return false;
        }
    }

    // Inserta un servicio nuevo
    public static function insertar(array $datos): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $id = self::siguienteId($db);
            $sql = "INSERT INTO servicios (id_servicio, nombre, categoria, descripcion,
                                           duracion, precio, id_empleados, estado)
                    VALUES (:id, :nombre, :categoria, :descripcion,
                            :duracion, :precio, :id_empleados, :estado)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindValue(':categoria', $datos['categoria'], PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindValue(':duracion', $datos['duracion'], PDO::PARAM_INT);
            $stmt->bindValue(':precio', $datos['precio'], PDO::PARAM_STR);
            $stmt->bindValue(':id_empleados', $datos['id_empleados'], PDO::PARAM_INT);
            $stmt->bindValue(':estado', $datos['estado'], PDO::PARAM_INT);
            $stmt->execute();
            return ['exito' => true, 'mensaje' => 'Servicio guardado', 'id' => $id];
        } catch (Exception $e) {
            error_log("Error en Servicios::insertar: " . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'No se pudo guardar el servicio'];
        }
    }
        // Trae un servicio por su id
    public static function obtenerPorId(int $id): ?array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare("SELECT id_servicio, nombre, categoria, descripcion, duracion,
                                         precio, id_empleados, estado + 0 AS estado
                                  FROM servicios
                                  WHERE id_servicio = ?");
            $stmt->execute([$id]);
            $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
            return $servicio ?: null;
        } catch (Exception $e) {
            error_log("Error en Servicios::obtenerPorId: " . $e->getMessage());
            return null;
        }
    }

    // Actualiza un servicio existente
    public static function actualizar(int $id, array $datos): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $sql = "UPDATE servicios SET
                        nombre = :nombre, categoria = :categoria, descripcion = :descripcion,
                        duracion = :duracion, precio = :precio,
                        id_empleados = :id_empleados, estado = :estado
                    WHERE id_servicio = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindValue(':categoria', $datos['categoria'], PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindValue(':duracion', $datos['duracion'], PDO::PARAM_INT);
            $stmt->bindValue(':precio', $datos['precio'], PDO::PARAM_STR);
            $stmt->bindValue(':id_empleados', $datos['id_empleados'], PDO::PARAM_INT);
            $stmt->bindValue(':estado', $datos['estado'], PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['exito' => true, 'mensaje' => 'Servicio actualizado', 'id' => $id];
        } catch (Exception $e) {
            error_log("Error en Servicios::actualizar: " . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'No se pudo actualizar el servicio'];
        }
    }
        // Activa (1) o desactiva (0) un servicio
    public static function cambiarEstado(int $id, int $estado): bool
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare("UPDATE servicios SET estado = :estado WHERE id_servicio = :id");
            $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            error_log("Error en Servicios::cambiarEstado: " . $e->getMessage());
            return false;
        }
    }
}
?>