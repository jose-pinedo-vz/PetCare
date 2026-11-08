<?php
// Modelo de servicios
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
}
?>