<?php
// Carga la conexion
if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

class Ventas
{
    // Busca productos por nombre o codigo (vacio muestra todos)
    public static function buscarProductos(string $termino): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare(
                "SELECT id_producto, codigo, nombre, precio_venta, existencia
                 FROM productos
                 WHERE estado = 'activo'
                   AND (codigo LIKE :codigo OR nombre LIKE :nombre)
                 ORDER BY nombre
                 LIMIT 50"
            );
            $like = '%' . $termino . '%';
            $stmt->execute([':codigo' => $like, ':nombre' => $like]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en Ventas::buscarProductos: " . $e->getMessage());
            return [];
        }
    }

    // Busca un producto por su codigo exacto
    public static function buscarProductoPorCodigo(string $codigo): ?array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare(
                "SELECT id_producto, codigo, nombre, precio_venta, existencia
                 FROM productos
                 WHERE estado = 'activo' AND codigo = ?"
            );
            $stmt->execute([$codigo]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            return $producto ?: null;
        } catch (Exception $e) {
            error_log("Error en Ventas::buscarProductoPorCodigo: " . $e->getMessage());
            return null;
        }
    }
}