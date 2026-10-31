<?php
declare(strict_types=1);

if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

Class  Categoria
{
    public static function obtenerCategoriasActivas(): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "SELECT id_categoria, nombre, descripcion, estado, servicio
                FROM categoria_productos
                WHERE estado = 'Activo'
                ORDER BY id_categoria DESC";

        $resultado = $conexion->query($sql);
        return $resultado ? $resultado->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (Exception $e) {
        error_log("Error en obtenerCategoriasActivas: " . $e->getMessage());
        return [];
    }
}
}