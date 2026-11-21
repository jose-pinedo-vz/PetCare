<?php
// Datos publicos para la vista de clientes (no requiere sesion de empleado)
require_once __DIR__ . '/conexion_db.php';

class Publico
{
    // Servicios activos con su precio y duracion
    public static function servicios(): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $r = $db->query(
                "SELECT id_servicio, nombre, categoria, descripcion, duracion, precio
                 FROM servicios WHERE estado + 0 = 1 ORDER BY categoria, nombre"
            );
            return $r ? $r->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Exception $e) {
            error_log("Error en Publico::servicios: " . $e->getMessage());
            return [];
        }
    }

    // Productos activos y si hay existencia (suma de lotes sin vencer)
    public static function productos(string $buscar = ''): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio_venta, p.imagen,
                           COALESCE((SELECT SUM(l.cantidad) FROM lote_productos l
                                     WHERE l.id_producto = p.id_producto
                                       AND (l.fecha_caducidad IS NULL OR l.fecha_caducidad >= CURDATE())), 0) AS disponible
                    FROM productos p
                    WHERE p.estado IN ('activo', '1')";
            $params = [];
            if ($buscar !== '') {
                $sql .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ?)";
                $params = ['%' . $buscar . '%', '%' . $buscar . '%'];
            }
            $sql .= " ORDER BY p.nombre";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en Publico::productos: " . $e->getMessage());
            return [];
        }
    }

    // Equipo que atiende a las mascotas: veterinarios, estilistas y hotel
    public static function equipo(string $tipo = ''): array
    {
        $filtros = [
            'veterinarios' => 'veterinari',
            'estilistas'   => 'estilista',
            'hotel'        => 'hotel'
        ];
        try {
            $db = ConexionDB::obtenerConexion();
            $sql = "SELECT id_empleado, nombre, apellido, puesto, especialidad, num_cedula_profesional
                    FROM empleados WHERE esta_activo + 0 = 1
                      AND (LOWER(puesto) LIKE '%veterinari%' OR LOWER(puesto) LIKE '%estilista%' OR LOWER(puesto) LIKE '%hotel%')";
            $params = [];
            if (isset($filtros[$tipo])) {
                $sql .= " AND LOWER(puesto) LIKE ?";
                $params[] = '%' . $filtros[$tipo] . '%';
            }
            $sql .= " ORDER BY puesto, nombre";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en Publico::equipo: " . $e->getMessage());
            return [];
        }
    }
}
