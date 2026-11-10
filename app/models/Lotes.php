<?php
if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

Class Lotes
{
    public static function obtenerProductosActivosConProveedor(): array {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $sql = "SELECT p.id_producto, p.codigo, p.nombre,
                           COALESCE(
                               NULLIF(TRIM(pr.nombre_comercial), ''),
                               NULLIF(TRIM(pr.razon_social), ''),
                               'Sin proveedor asignado'
                           ) AS proveedor
                    FROM productos AS p
                    LEFT JOIN proveedores AS pr ON pr.id_proveedor = p.id_proveedor
                    WHERE p.estado = 1
                    ORDER BY p.id_producto DESC";

            $resultado = $conexion->query($sql);
            return $resultado ? ($resultado->fetchAll(PDO::FETCH_ASSOC) ?: []) : [];
        } catch (Exception $e) {
            error_log("Error en obtenerProductosActivosConProveedor: " . $e->getMessage());
            return [];
        }
    }

    public static function obtenerLotes(): array {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $sql = "SELECT lp.id_producto, p.nombre AS nombre_producto,
                           COALESCE(NULLIF(pr.nombre_comercial, ''), pr.razon_social) AS proveedor,
                           lp.lote, lp.fecha_caducidad, lp.precio_compra, lp.ubicacion, lp.cantidad
                    FROM lote_productos AS lp
                    INNER JOIN productos AS p ON p.id_producto = lp.id_producto
                    LEFT JOIN proveedores AS pr ON pr.id_proveedor = p.id_proveedor
                    ORDER BY lp.id_producto DESC";

            $resultado = $conexion->query($sql);
            if (!$resultado) {
                return [];
            }

            return $resultado->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("Error en obtenerLotes: " . $e->getMessage());
            return [];
        }
    }

    public static function existeLote(string $lote): bool {
        $conexion = ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare(
            "SELECT 1 FROM lote_productos WHERE lote = :lote LIMIT 1"
        );
        $stmt->execute([':lote' => $lote]);

        return $stmt->fetchColumn() !== false;
    }

    public static function registrarLote(array $datos): bool {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "INSERT INTO lote_productos (
                    id_producto, lote, fecha_caducidad, precio_compra, ubicacion, cantidad
                )
                SELECT p.id_producto, :lote, :fecha_caducidad, :precio_compra, :ubicacion, :cantidad
                FROM productos AS p
                WHERE p.id_producto = :id_producto AND p.estado = 1";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':id_producto' => $datos['id_producto'],
            ':lote' => $datos['lote'],
            ':fecha_caducidad' => $datos['fecha_caducidad'],
            ':precio_compra' => $datos['precio_compra'],
            ':ubicacion' => $datos['ubicacion'],
            ':cantidad' => $datos['cantidad'],
        ]);

        return $stmt->rowCount() === 1;
    }
}