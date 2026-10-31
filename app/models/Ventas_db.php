<?php
declare(strict_types=1);
if (file_exists(__DIR__ . '/conexion_db.php'))
{
    require_once __DIR__ . '/conexion_db.php';
}
elseif (file_exists(__DIR__ . '/../../conexion_db.php'))
{
    require_once __DIR__ . '/../../conexion_db.php';
}

class Ventas
{
    public static function obtenerServicios(PDO $conexion): array
    {
        $base = $conexion->prepare("SELECT * FROM servicios ORDER BY nombre ASC");
        $base->execute();
        return $base->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerProductos(PDO $conexion): array
    {
        $base = $conexion->prepare("SELECT * FROM productos ORDER BY nombre ASC");
        $base->execute();
        return $base->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function cargar(PDO $conexion, string $busqueda): ?array
    {
        $sql = "SELECT codigo, nombre, precio_venta, existencia FROM productos";
        if (!empty($busqueda)) {
            $sql .= " WHERE nombre LIKE ? OR codigo LIKE ?";
            $stmt = $conexion->prepare($sql);
            $param = "%" . $busqueda . "%";
            $stmt->execute([$param, $param]);
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $resultado = $conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }
        return $resultado;
    }
}

?>