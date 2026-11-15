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
                                "SELECT p.id_producto, p.codigo, p.nombre, p.precio_venta,
                        COALESCE((SELECT SUM(l.cantidad) FROM lote_productos l
                                WHERE l.id_producto = p.id_producto
                                    AND (l.fecha_caducidad IS NULL OR l.fecha_caducidad >= CURDATE())), 0) AS existencia
                 FROM productos p
                 WHERE p.estado = 'activo'
                   AND (p.codigo LIKE :codigo OR p.nombre LIKE :nombre)
                 ORDER BY p.nombre
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
                                "SELECT p.id_producto, p.codigo, p.nombre, p.precio_venta,
                        COALESCE((SELECT SUM(l.cantidad) FROM lote_productos l
                                  WHERE l.id_producto = p.id_producto
                                    AND (l.fecha_caducidad IS NULL OR l.fecha_caducidad >= CURDATE())), 0) AS existencia
                 FROM productos p
                 WHERE p.estado = 'activo' AND p.codigo = ?"
            );
            $stmt->execute([$codigo]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            return $producto ?: null;
        } catch (Exception $e) {
            error_log("Error en Ventas::buscarProductoPorCodigo: " . $e->getMessage());
            return null;
        }
    }
        // Guarda la venta completa en una sola transaccion
        // Guarda la venta completa en una sola transaccion
    public static function guardarVenta(array $items, string $formaPago, float $recibido, int $idEmpleado, int $idCliente): array
    {
        $formasValidas = ['efectivo', 'tarjeta', 'transferencia'];
        if (empty($items) || !in_array($formaPago, $formasValidas, true)) {
            return ['ok' => false, 'error' => 'Datos de venta no validos.'];
        }

        $db = ConexionDB::obtenerConexion();

        try {
            $db->beginTransaction();

            $lineas = [];
            $vistos = [];
            $subtotalVenta = 0;
            $ivaVenta = 0;

            foreach ($items as $item) {
                $idProducto = (int)($item['id_producto'] ?? 0);
                $cantidad = (int)($item['cantidad'] ?? 0);
                $descuento = (float)($item['descuento'] ?? 0);

                if ($idProducto <= 0 || $cantidad <= 0 || $descuento < 0 || $descuento > 100) {
                    throw new Exception('Producto, cantidad o descuento no validos.');
                }
                if (isset($vistos[$idProducto])) {
                    throw new Exception('Un producto esta repetido en la venta.');
                }
                $vistos[$idProducto] = true;

                // El producto debe estar activo
                $stmt = $db->prepare(
                    "SELECT nombre, precio_venta FROM productos
                     WHERE id_producto = ? AND estado = 'activo'"
                );
                $stmt->execute([$idProducto]);
                $prod = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$prod) {
                    throw new Exception('Un producto ya no esta disponible.');
                }

                // Lotes sin vencer, primero el que caduca antes (FOR UPDATE los bloquea)
                $stmt = $db->prepare(
                    "SELECT lote, cantidad FROM lote_productos
                     WHERE id_producto = ? AND cantidad > 0
                       AND (fecha_caducidad IS NULL OR fecha_caducidad >= CURDATE())
                     ORDER BY fecha_caducidad IS NULL, fecha_caducidad ASC, lote ASC
                     FOR UPDATE"
                );
                $stmt->execute([$idProducto]);
                $lotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $stockTotal = 0;
                foreach ($lotes as $lt) {
                    $stockTotal += (int)$lt['cantidad'];
                }
                if ($stockTotal < $cantidad) {
                    throw new Exception('No hay existencia suficiente de ' . $prod['nombre'] . '.');
                }

                // Reparte la cantidad entre los lotes
                $faltan = $cantidad;
                $usados = [];
                foreach ($lotes as $lt) {
                    if ($faltan <= 0) break;
                    $toma = min($faltan, (int)$lt['cantidad']);
                    $usados[] = ['lote' => $lt['lote'], 'toma' => $toma];
                    $faltan -= $toma;
                }

                $precio = (float)$prod['precio_venta'];
                $desc = round($precio * $cantidad * $descuento / 100, 2);
                $subtotal = round($precio * $cantidad - $desc, 2);
                $iva = round($subtotal * 0.16, 2);

                $subtotalVenta += $subtotal;
                $ivaVenta += $iva;

                $lineas[] = [
                    'id' => $idProducto,
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                    'descuento' => $descuento,
                    'subtotal' => $subtotal,
                    'iva' => $iva,
                    'stock' => $stockTotal,
                    'usados' => $usados
                ];
            }

            $total = round($subtotalVenta + $ivaVenta, 2);

            // En efectivo el monto debe alcanzar
            if ($formaPago === 'efectivo' && round($recibido, 2) < $total) {
                throw new Exception('El monto recibido no alcanza el total.');
            }
            $cambio = ($formaPago === 'efectivo') ? round($recibido - $total, 2) : 0;

            // Venta principal con folio temporal
            $folioTemp = strtoupper(substr(md5(uniqid('', true)), 0, 8));
            $stmt = $db->prepare(
                "INSERT INTO ventas (folio, fecha, cliente, empleado, total, estado, observaciones)
                 VALUES (?, NOW(), ?, ?, ?, 'Pagada', '')"
            );
            $stmt->execute([$folioTemp, $idCliente, $idEmpleado, $total]);
            $idVenta = (int)$db->lastInsertId();

            // Folio final, ej. V0000012
            $folio = 'V' . str_pad((string)$idVenta, 7, '0', STR_PAD_LEFT);
            $stmt = $db->prepare("UPDATE ventas SET folio = ? WHERE id_venta = ?");
            $stmt->execute([$folio, $idVenta]);

            foreach ($lineas as $l) {
                // Detalle del producto
                $stmt = $db->prepare(
                    "INSERT INTO detalles_productos
                     (id_venta, id_producto, cantidad, precio_unitario, iva, descuento, subtotal)
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([$idVenta, $l['id'], $l['cantidad'], $l['precio'], $l['iva'], $l['descuento'], $l['subtotal']]);

                // Descuenta cada lote y deja su movimiento
                $stockActual = $l['stock'];
                foreach ($l['usados'] as $u) {
                    $stmt = $db->prepare(
                        "UPDATE lote_productos SET cantidad = cantidad - ?
                         WHERE id_producto = ? AND lote = ?"
                    );
                    $stmt->execute([$u['toma'], $l['id'], $u['lote']]);

                    $stmt = $db->prepare(
                        "INSERT INTO movimientos_inventario
                         (producto, fecha, tipo_movimiento, cantidad, existencia_anterior, existencia_posterior, motivo, empleado_responsable, numero_factura, observaciones, venta_origen)
                         VALUES (?, CURDATE(), 'salida', ?, ?, ?, 'Venta', ?, ?, ?, ?)"
                    );
                    $stmt->execute([
                        $l['id'], $u['toma'], $stockActual, $stockActual - $u['toma'],
                        $idEmpleado, $folio, 'Lote ' . $u['lote'], $idVenta
                    ]);
                    $stockActual -= $u['toma'];
                }
            }

            // Pago de la venta, solo efectivo lleva cambio
            $montoRecibido = ($formaPago === 'efectivo') ? round($recibido, 2) : $total;
            $stmt = $db->prepare(
                "INSERT INTO pago (venta, cliente, fecha, importe, forma_pago, referencia_pago, empleado_receptor, estado, tipo, monto_recibido, cambio, observaciones)
                 VALUES (?, ?, NOW(), ?, ?, NULL, ?, 'aplicado', 'pago', ?, ?, NULL)"
            );
            $stmt->execute([$idVenta, $idCliente, $total, $formaPago, $idEmpleado, $montoRecibido, $cambio]);

            $db->commit();
            return ['ok' => true, 'id_venta' => $idVenta, 'folio' => $folio, 'total' => $total, 'cambio' => $cambio];
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Error en Ventas::guardarVenta: " . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
                