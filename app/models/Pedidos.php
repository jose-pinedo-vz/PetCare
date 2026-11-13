<?php
require_once __DIR__ . '/conexion_db.php';
require_once __DIR__ . '/Productos.php';

class Pedidos
{
    private static bool $tablaRecepcionesAsegurada = false;

    private static function asegurarTablaRecepciones(PDO $conexion): void
    {
        if (self::$tablaRecepcionesAsegurada) {
            return;
        }

        $conexion->exec(
            "CREATE TABLE IF NOT EXISTS recepciones_detalle_orden (
                id_detalle INT NOT NULL PRIMARY KEY,
                cantidad_recibida INT NOT NULL DEFAULT 0
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
        self::$tablaRecepcionesAsegurada = true;
    }

    public static function obtenerPedidos(): array
    {
        $conexion = ConexionDB::obtenerConexion();
        self::asegurarTablaRecepciones($conexion);
        $sql = "SELECT o.clave_compra, o.id_proveedor, o.fecha_solicitud,
                       o.fecha_esperada, o.total_estimado, o.estado,
                       p.nombre_comercial, p.razon_social
                FROM orden_compra o
                INNER JOIN proveedores p ON p.id_proveedor = o.id_proveedor
                ORDER BY o.fecha_solicitud DESC";
        $pedidos = $conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($pedidos as &$pedido) {
            $pedido['proveedor'] = trim((string) $pedido['nombre_comercial']) !== ''
                ? $pedido['nombre_comercial']
                : $pedido['razon_social'];
        }
        unset($pedido);

        return $pedidos;
    }

    public static function obtenerPedido(int $id): ?array
    {
        $conexion = ConexionDB::obtenerConexion();
        self::asegurarTablaRecepciones($conexion);
        $stmt = $conexion->prepare(
            "SELECT o.clave_compra, o.id_proveedor, o.fecha_solicitud,
                    o.fecha_esperada, o.total_estimado, o.estado,
                    p.razon_social, p.nombre_comercial, p.rfc, p.telefono,
                    p.correo, p.domicilio, p.ciudad, p.estado AS estado_proveedor,
                    p.codigo_postal
             FROM orden_compra o
             INNER JOIN proveedores p ON p.id_proveedor = o.id_proveedor
             WHERE o.clave_compra = ?"
        );
        $stmt->execute([$id]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pedido) {
            $pedido['proveedor'] = trim((string) $pedido['nombre_comercial']) !== ''
                ? $pedido['nombre_comercial']
                : $pedido['razon_social'];
        }

        return $pedido ?: null;
    }

    public static function obtenerDetalles(int $id, bool $soloPendientes = false): array
    {
        $conexion = ConexionDB::obtenerConexion();
        self::asegurarTablaRecepciones($conexion);
        $sql = "SELECT d.id_detalle, d.clave_compra, d.id_producto, d.cantidad,
                       d.precio_unitario, p.codigo, p.nombre, p.contenido,
                       p.unidad_medida, r.cantidad_recibida
                FROM detalle_orden_compra d
                INNER JOIN productos p ON p.id_producto = d.id_producto
                LEFT JOIN recepciones_detalle_orden r ON r.id_detalle = d.id_detalle
                WHERE d.clave_compra = ?";
        if ($soloPendientes) {
            $sql .= " AND d.cantidad > IFNULL(r.cantidad_recibida, 0)";
        }
        $sql .= " ORDER BY d.id_detalle";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id]);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($productos as &$producto) {
            $recibido = (int) ($producto['cantidad_recibida'] ?? 0);
            $producto['cantidad_recibida'] = $recibido;
            $producto['cantidad_pendiente'] = max((int) $producto['cantidad'] - $recibido, 0);
        }
        unset($producto);

        return $productos;
    }

    public static function obtenerPedidoCompleto(int $id): ?array
    {
        $pedido = self::obtenerPedido($id);
        if ($pedido === null) {
            return null;
        }

        $pedido['productos'] = self::obtenerDetalles($id);
        return $pedido;
    }

    public static function cancelarPedido(int $id): bool
    {
        $conexion = ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare(
            "UPDATE orden_compra
             SET estado = ?
             WHERE clave_compra = ? AND estado = ?"
        );
        $stmt->execute(['cancelado', $id, 'pendiente']);

        return $stmt->rowCount() > 0;
    }

    public static function existeLote(string $lote): bool
    {
        $conexion = ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare(
            "SELECT 1 FROM lote_productos WHERE lote = :lote LIMIT 1"
        );
        $stmt->execute([$lote]);

        return $stmt->fetchColumn() !== false;
    }

    public static function registrarRecepcion(
        int $idPedido,
        int $idDetalle,
        int $cantidad,
        array $datosLote
    ): bool {
        $conexion = ConexionDB::obtenerConexion();
        self::asegurarTablaRecepciones($conexion);
        $conexion->beginTransaction();

        try {
            $stmtPedido = $conexion->prepare(
                "SELECT estado FROM orden_compra WHERE clave_compra = ?"
            );
            $stmtPedido->execute([$idPedido]);
            if ($stmtPedido->fetchColumn() !== 'pendiente') {
                $conexion->rollBack();
                return false;
            }

            $stmtDetalle = $conexion->prepare(
                "SELECT d.id_producto, d.cantidad, d.precio_unitario, r.cantidad_recibida
                 FROM detalle_orden_compra d
                 LEFT JOIN recepciones_detalle_orden r ON r.id_detalle = d.id_detalle
                 WHERE d.id_detalle = ? AND d.clave_compra = ?"
            );
            $stmtDetalle->execute([$idDetalle, $idPedido]);
            $detalle = $stmtDetalle->fetch(PDO::FETCH_ASSOC);
            $cantidadRecibida = (int) ($detalle['cantidad_recibida'] ?? 0);
            if (!$detalle || $cantidad > (int) $detalle['cantidad'] - $cantidadRecibida) {
                $conexion->rollBack();
                return false;
            }

            $stmtLote = $conexion->prepare(
                "INSERT INTO lote_productos
                    (id_producto, lote, fecha_caducidad, precio_compra, ubicacion, cantidad)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmtLote->execute([
                (int) $detalle['id_producto'],
                $datosLote['lote'],
                $datosLote['fecha_caducidad'],
                $detalle['precio_unitario'],
                $datosLote['ubicacion'],
                $cantidad,
            ]);

            $stmtExisteRecepcion = $conexion->prepare(
                "SELECT cantidad_recibida
                 FROM recepciones_detalle_orden
                 WHERE id_detalle = ?"
            );
            $stmtExisteRecepcion->execute([$idDetalle]);
            $cantidadAnterior = $stmtExisteRecepcion->fetchColumn();

            if ($cantidadAnterior === false) {
                $stmtRecepcion = $conexion->prepare(
                    "INSERT INTO recepciones_detalle_orden (id_detalle, cantidad_recibida)
                     VALUES (?, ?)"
                );
                $stmtRecepcion->execute([$idDetalle, $cantidad]);
            } else {
                $stmtRecepcion = $conexion->prepare(
                    "UPDATE recepciones_detalle_orden
                     SET cantidad_recibida = cantidad_recibida + ?
                     WHERE id_detalle = ?"
                );
                $stmtRecepcion->execute([$cantidad, $idDetalle]);
            }

            $stmtPendientes = $conexion->prepare(
                "SELECT COUNT(*)
                 FROM detalle_orden_compra d
                 LEFT JOIN recepciones_detalle_orden r ON r.id_detalle = d.id_detalle
                 WHERE d.clave_compra = ?
                   AND d.cantidad > IFNULL(r.cantidad_recibida, 0)"
            );
            $stmtPendientes->execute([$idPedido]);
            if ((int) $stmtPendientes->fetchColumn() === 0) {
                $stmtEstado = $conexion->prepare(
                    "UPDATE orden_compra SET estado = ?
                     WHERE clave_compra = ? AND estado = ?"
                );
                $stmtEstado->execute(['entregado', $idPedido, 'pendiente']);
            }

            $conexion->commit();
            return true;
        } catch (Throwable $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $e;
        }
    }

    private static function siguienteClave(PDO $conexion): int {
        return (int) $conexion -> query (
            "
            SELECT COALESCE(MAX(clave_compra), 0) + 1 
            FROM orden_compra
            "
        ) -> fetchColumn();
    }

    public static function crearPedido(int $idProveedor, array $productos, array $cantidades): int {
        $conexion = ConexionDB::obtenerConexion();
        $conexion -> beginTransaction();

        try {
            $clave = self::siguienteClave($conexion);
            $dias = Productos::obtenerTiempoEntregaProveedor($idProveedor);
            $fechaSolicitud = date('Y-m-d');
            $fechaEsperada = date('Y-m-d', strtotime("+$dias days"));

            $detalles = [];
            $total = 0.0;
            foreach ($productos as $idProducto => $p) {
                $cantidad = max(1, (int) ($cantidades[$idProducto] ?? 1));
                $precio = Productos::obtenerPrecioCompra((int) $idProducto);
                $total += $precio * $cantidad;
                $detalles[] = [(int) $idProducto, $cantidad, $precio];
            }

            $sql_orden = "
                INSERT INTO orden_compra (
                    clave_compra, id_proveedor, fecha_solicitud, fecha_esperada, total_estimado, estado
                )
                VALUES (
                    ?, ?, ?, ?, ?, 'pendiente'
                )
            ";
            $consulta_consulta = $conexion -> prepare($sql_orden);
            $consulta_consulta -> execute([$clave, $idProveedor, $fechaSolicitud, $fechaEsperada, $total]);

            $sql_detalles = "
                INSERT INTO detalle_orden_compra (
                    clave_compra, id_producto, cantidad, precio_unitario
                )
                VALUES (
                    ?, ?, ?, ?
                )
            ";
            $consulta_detalles = $conexion -> prepare($sql_detalles);

            foreach ($detalles as $d) {
                $consulta_detalles -> execute([$clave, $d[0], $d[1], $d[2]]);
            }

            $conexion -> commit();
            return $clave;

            
        } catch (Throwable $e) {
            if ($conexion -> inTransaction()) {
                $conexion -> rollBack();
            } throw $e;
            
        }
    }
}
