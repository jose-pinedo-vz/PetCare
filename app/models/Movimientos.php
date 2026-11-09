<?php
if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

class Movimientos{
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