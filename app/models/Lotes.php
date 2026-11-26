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

    public static function eliminarLote(int $idProducto, string $lote): bool {
        $conexion = ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare(
            "DELETE FROM lote_productos
             WHERE id_producto = :id_producto AND lote = :lote"
        );
        $stmt->execute([
            ':id_producto' => $idProducto,
            ':lote' => $lote,
        ]);

        return $stmt->rowCount() === 1;
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
        $stmt->bindValue(':id_producto', (int) $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindValue(':lote', $datos['lote'], PDO::PARAM_STR);
        $stmt->bindValue(
            ':fecha_caducidad',
            $datos['fecha_caducidad'],
            $datos['fecha_caducidad'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR
        );
        $stmt->bindValue(':precio_compra',  (string)$datos['precio_compra'], PDO::PARAM_STR);
        $stmt->bindValue(':ubicacion',              $datos['ubicacion'],     PDO::PARAM_STR);
        $stmt->bindValue(':cantidad',          (int)$datos['cantidad'],      PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    public static function generarSiguienteLote(?PDO $conexion = null): string 
        {
            $conexion = $conexion ?? ConexionDB::obtenerConexion();
        
            $anioActual = date('Y');
            $prefijo = "L-" . $anioActual . "-";
        
            // Ordenar numéricamente por el último segmento después del guion
            $sql = "SELECT lote 
                    FROM lote_productos 
                    WHERE lote LIKE :prefijo 
                    ORDER BY CAST(SUBSTRING_INDEX(lote, '-', -1) AS UNSIGNED) DESC 
                    LIMIT 1";
                    
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':prefijo' => $prefijo . '%']);
            $ultimoLote = $stmt->fetchColumn();
        
            if ($ultimoLote) {
                $partes = explode('-', $ultimoLote);
                $numeroActual = (int) end($partes);
                $siguienteNumero = $numeroActual + 1;
            } else {
                $siguienteNumero = 1;
            }
        
            // Mantiene 4 dígitos (0001, 0002, 0003, 0004...)
            $consecutivoFormateado = str_pad((string) $siguienteNumero, 4, '0', STR_PAD_LEFT);
        
            return $prefijo . $consecutivoFormateado;
        }
}