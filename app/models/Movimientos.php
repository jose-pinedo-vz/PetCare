<?php
if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

class Movimientos{
    public static function totalMovimientos(): ?int {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $consulta = $conexion->query("SELECT COUNT(*) FROM movimientos_inventario");
            return (int) $consulta->fetchColumn();
        } catch (Throwable $e) {
            error_log('Error al consultar el total de movimientos de inventario: ' . $e->getMessage());
            return null;
        }
    }

    public static function obtenerTodos(): ?array {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $consulta = $conexion->query(
                "SELECT m.id_movimiento, m.producto AS id_producto, m.tipo_movimiento, m.cantidad,
                        m.existencia_anterior, m.existencia_posterior, m.motivo,
                        m.empleado_responsable AS id_empleado, m.fecha, m.numero_factura, m.observaciones,
                        p.codigo AS codigo_producto, p.nombre AS nombre_producto,
                        e.nombre AS nombre_empleado, e.apellido AS apellido_empleado
                 FROM movimientos_inventario AS m
                 LEFT JOIN productos AS p ON p.id_producto = m.producto
                 LEFT JOIN empleados AS e ON e.id_empleado = m.empleado_responsable
                 ORDER BY m.fecha DESC, m.id_movimiento DESC"
            );
            return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            error_log('Error al consultar movimientos de inventario: ' . $e->getMessage());
            return null;
        }
    }

    public static function movimientosHoy() {
        $movimientosHoy = null;
        try {
            $conexion = ConexionDB::obtenerConexion();
            $consultaMovimientos = $conexion->query(
              "SELECT COUNT(*) FROM movimientos_inventario WHERE DATE(fecha) = CURDATE()"
            );
            return $movimientosHoy = (int) $consultaMovimientos->fetchColumn();
        } catch (Throwable $e) {
           error_log('Error al consultar movimientos de inventario del día: ' . $e->getMessage());
        }
    }
}