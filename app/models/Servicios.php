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
}
?>