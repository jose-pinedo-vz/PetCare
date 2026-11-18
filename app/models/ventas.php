<?php
// Carga la conexion
if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}
require_once __DIR__ . '/CorteCaja.php';
require_once __DIR__ . '/../../banca_simulada/banco_servicio.php';

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
                 WHERE p.estado IN ('activo', '1')
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
                 WHERE p.estado IN ('activo', '1') AND p.codigo = ?"
            );
            $stmt->execute([$codigo]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            return $producto ?: null;
        } catch (Exception $e) {
            error_log("Error en Ventas::buscarProductoPorCodigo: " . $e->getMessage());
            return null;
        }
    }

    // Busca servicios activos por nombre o categoria (vacio muestra todos)
    public static function buscarServicios(string $termino): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare(
                "SELECT id_servicio, nombre, categoria, descripcion, duracion, precio
                 FROM servicios
                 WHERE estado = 1
                   AND (nombre LIKE :nombre OR categoria LIKE :categoria)
                 ORDER BY nombre
                 LIMIT 50"
            );
            $like = '%' . $termino . '%';
            $stmt->execute([':nombre' => $like, ':categoria' => $like]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en Ventas::buscarServicios: " . $e->getMessage());
            return [];
        }
    }

    // Guarda la venta completa en una sola transaccion
    public static function guardarVenta(array $items, string $formaPago, float $recibido, int $idEmpleado, int $idCliente, array $servicios = [], array $datosPago = []): array
    {
        $formasValidas = ['efectivo', 'tarjeta', 'transferencia'];
        if ((empty($items) && empty($servicios)) || !in_array($formaPago, $formasValidas, true)) {
            return ['ok' => false, 'error' => 'Datos de venta no validos.'];
        }

        // Referencia y observaciones del pago (efectivo no lleva)
        $referenciaPago = null;
        $observacionesPago = null;

        // Folio de autorizacion del banco y datos de tarjeta (solo viven hasta cobrar)
        $folioBanco = null;
        $cobroTarjeta = null;

        if ($formaPago === 'tarjeta') {
            // Se valida ANTES de abrir la transaccion; el cobro al banco se hace cuando ya se conoce el total
            $tarjeta = self::validarTarjeta($datosPago);
            if (!$tarjeta['ok']) {
                return ['ok' => false, 'error' => $tarjeta['error']];
            }
            $observacionesPago = 'Tarjeta ****' . substr($tarjeta['numero'], -4);
            $cobroTarjeta = $tarjeta;
            unset($tarjeta, $datosPago);
        } elseif ($formaPago === 'transferencia') {
            $ref = $datosPago['referencia'] ?? '';
            $ref = is_scalar($ref) ? trim((string)$ref) : '';
            if ($ref !== '' && !preg_match('/^[A-Za-z0-9-]{1,30}$/', $ref)) {
                return ['ok' => false, 'error' => 'La referencia de transferencia debe tener maximo 30 caracteres (solo letras, numeros y guiones).'];
            }
            $referenciaPago = ($ref === '') ? null : $ref;
            $observacionesPago = 'Transferencia';
        }

        $db = ConexionDB::obtenerConexion();

        $corteAbierto = CorteCaja::obtenerCorteAbierto($db, $idEmpleado);
        if (!$corteAbierto) {
            return ['ok' => false, 'error' => 'No tienes un corte de caja abierto. Debes abrir caja antes de registrar ventas.'];
        }
        $idCorte = (int)$corteAbierto['id'];

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
                     WHERE id_producto = ? AND estado IN ('activo', '1')"
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

            // Servicios: el precio siempre sale de la BD
            $lineasServ = [];
            $vistosServ = [];
            foreach ($servicios as $serv) {
                if (!is_array($serv)) {
                    throw new Exception('Servicio o descuento no validos.');
                }
                $idServicio = (int)($serv['id_servicio'] ?? 0);
                $descServ = (float)($serv['descuento'] ?? 0);

                if ($idServicio <= 0 || $descServ < 0 || $descServ > 100) {
                    throw new Exception('Servicio o descuento no validos.');
                }
                if (isset($vistosServ[$idServicio])) {
                    throw new Exception('Un servicio esta repetido en la venta.');
                }
                $vistosServ[$idServicio] = true;

                $stmt = $db->prepare(
                    "SELECT precio FROM servicios WHERE id_servicio = ? AND estado = 1"
                );
                $stmt->execute([$idServicio]);
                $srv = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$srv) {
                    throw new Exception('Un servicio ya no esta disponible.');
                }

                $precio = (float)$srv['precio'];
                $desc = round($precio * $descServ / 100, 2);
                $subtotal = round($precio - $desc, 2);
                $iva = round($subtotal * 0.16, 2);

                $subtotalVenta += $subtotal;
                $ivaVenta += $iva;

                $lineasServ[] = [
                    'id' => $idServicio,
                    'precio' => $precio,
                    'descuento' => $descServ,
                    'subtotal' => $subtotal,
                    'iva' => $iva
                ];
            }

            $total = round($subtotalVenta + $ivaVenta, 2);

            // En efectivo el monto debe alcanzar
            if ($formaPago === 'efectivo' && round($recibido, 2) < $total) {
                throw new Exception('El monto recibido no alcanza el total.');
            }
            $cambio = ($formaPago === 'efectivo') ? round($recibido - $total, 2) : 0;

            // Tarjeta: se cobra en la banca con el total real; si algo falla despues se reversa
            if ($formaPago === 'tarjeta') {
                $cobro = bancoAutorizarCobro(
                    $cobroTarjeta['numero'],
                    $cobroTarjeta['vencimiento'],
                    $cobroTarjeta['cvv'],
                    $total,
                    'PetCare Veterinaria'
                );
                $cobroTarjeta = null; // el numero y el CVV ya no se usan
                if (!$cobro['ok']) {
                    throw new Exception($cobro['error']);
                }
                $folioBanco = $cobro['folio'];
                $referenciaPago = $folioBanco;
            }

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

            // Detalle de cada servicio (no mueve inventario)
            foreach ($lineasServ as $s) {
                $stmt = $db->prepare(
                    "INSERT INTO detalles_servicios
                     (id_venta, id_servicio, precio_unitario, iva, descuento, subtotal)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([$idVenta, $s['id'], $s['precio'], $s['iva'], $s['descuento'], $s['subtotal']]);
            }

            // Pago de la venta, solo efectivo lleva cambio
            $montoRecibido = ($formaPago === 'efectivo') ? round($recibido, 2) : $total;
            $stmt = $db->prepare(
                "INSERT INTO pago (venta, cliente, fecha, importe, forma_pago, referencia_pago, empleado_receptor, estado, tipo, monto_recibido, cambio, observaciones, corte)
                 VALUES (?, ?, NOW(), ?, ?, ?, ?, 'aplicado', 'pago', ?, ?, ?, ?)"
            );
            $stmt->execute([$idVenta, $idCliente, $total, $formaPago, $referenciaPago, $idEmpleado, $montoRecibido, $cambio, $observacionesPago, $idCorte]);

            $db->commit();
            return ['ok' => true, 'id_venta' => $idVenta, 'folio' => $folio, 'total' => $total, 'cambio' => $cambio, 'autorizacion' => $folioBanco];
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            // Si ya se cobro en el banco y la venta no se guardo, se devuelve el dinero
            if ($folioBanco !== null && !bancoReversarCobro($folioBanco)) {
                error_log("No se pudo reversar el cobro " . $folioBanco . " de una venta no guardada");
            }
            error_log("Error en Ventas::guardarVenta: " . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    // Valida los datos de tarjeta; nunca incluye el numero ni el CVV en los mensajes.
    // No se usa Luhn: las tarjetas del banco simulado no lo cumplen y el banco decide si existe.
    private static function validarTarjeta(array $datos): array
    {
        $leer = function (string $clave) use ($datos): string {
            $v = $datos[$clave] ?? '';
            return is_scalar($v) ? trim((string)$v) : '';
        };

        $numero = $leer('numero_tarjeta');
        $titular = preg_replace('/\s+/u', ' ', $leer('titular'));
        $vencimiento = $leer('vencimiento');
        $cvv = $leer('cvv');

        if (!preg_match('/^\d{13,19}$/', $numero)) {
            return ['ok' => false, 'error' => 'El numero de tarjeta debe tener de 13 a 19 digitos.'];
        }

        if (!preg_match('/^\p{L}[\p{L} .\'\-]{2,59}$/u', $titular)) {
            return ['ok' => false, 'error' => 'El titular debe tener de 3 a 60 caracteres y solo letras, espacios, punto, apostrofe o guion.'];
        }

        if (!preg_match('/^(\d{4})-(0[1-9]|1[0-2])$/', $vencimiento)) {
            return ['ok' => false, 'error' => 'El vencimiento debe tener el formato AAAA-MM.'];
        }
        $ahora = new DateTime('now', new DateTimeZone('America/Mexico_City'));
        if (strcmp($vencimiento, $ahora->format('Y-m')) < 0) {
            return ['ok' => false, 'error' => 'La tarjeta esta vencida.'];
        }
        if (strcmp($vencimiento, $ahora->modify('+15 years')->format('Y-m')) > 0) {
            return ['ok' => false, 'error' => 'La fecha de vencimiento no es valida.'];
        }

        if (!preg_match('/^\d{3,4}$/', $cvv)) {
            return ['ok' => false, 'error' => 'El CVV debe tener 3 o 4 digitos.'];
        }

        return ['ok' => true, 'numero' => $numero, 'titular' => $titular, 'vencimiento' => $vencimiento, 'cvv' => $cvv];
    }

    // Revisa que la fecha venga como YYYY-MM-DD y sea real
    private static function fechaValida(string $fecha): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        return $d !== false && $d->format('Y-m-d') === $fecha;
    }

    // Lista las ventas del historial con filtros y paginas de 10
    public static function listarHistorial(array $f): array
    {
        $porPagina = 10;
        $pagina = max(1, (int)($f['pagina'] ?? 1));
        $vacio = ['total' => 0, 'pagina' => $pagina, 'por_pagina' => $porPagina, 'ventas' => []];

        try {
            $db = ConexionDB::obtenerConexion();

            $where = [];
            $params = [];

            // Texto: cada uso lleva su propio parametro
            $q = trim((string)($f['q'] ?? ''));
            if ($q !== '') {
                $where[] = "(v.folio LIKE :q1 OR c.nombre LIKE :q2 OR c.apellido LIKE :q3
                             OR e.nombre LIKE :q4 OR e.apellido LIKE :q5
                             OR CONCAT(c.nombre, ' ', c.apellido) LIKE :q6
                             OR CONCAT(e.nombre, ' ', e.apellido) LIKE :q7)";
                $like = '%' . $q . '%';
                for ($i = 1; $i <= 7; $i++) {
                    $params[':q' . $i] = $like;
                }
            }

            // Fechas, si no son validas se ignoran
            $desde = trim((string)($f['desde'] ?? ''));
            $hasta = trim((string)($f['hasta'] ?? ''));
            $desdeOk = $desde !== '' && self::fechaValida($desde);
            $hastaOk = $hasta !== '' && self::fechaValida($hasta);
            if ($desdeOk && $hastaOk) {
                $where[] = "DATE(v.fecha) BETWEEN :desde AND :hasta";
                $params[':desde'] = $desde;
                $params[':hasta'] = $hasta;
            } elseif ($desdeOk) {
                $where[] = "DATE(v.fecha) >= :desde";
                $params[':desde'] = $desde;
            } elseif ($hastaOk) {
                $where[] = "DATE(v.fecha) <= :hasta";
                $params[':hasta'] = $hasta;
            }

            // Estado, solo los permitidos
            $estado = trim((string)($f['estado'] ?? ''));
            if (in_array($estado, ['Pagada', 'Pendiente', 'Cancelada'], true)) {
                $where[] = "v.estado = :estado";
                $params[':estado'] = $estado;
            }

            $from = " FROM ventas v
                      LEFT JOIN clientes c ON c.id_cliente = v.cliente
                      LEFT JOIN empleados e ON e.id_empleado = v.empleado";
            $sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

            // Total de ventas que cumplen el filtro
            $stmt = $db->prepare("SELECT COUNT(*)" . $from . $sqlWhere);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v, PDO::PARAM_STR);
            }
            $stmt->execute();
            $total = (int)$stmt->fetchColumn();

            // Ventas de la pagina pedida
            $stmt = $db->prepare(
                "SELECT v.id_venta, v.folio, v.fecha,
                        CASE WHEN c.id_cliente IS NULL THEN 'Publico en general'
                             ELSE TRIM(CONCAT(COALESCE(c.nombre, ''), ' ', COALESCE(c.apellido, ''))) END AS cliente_nombre,
                        c.telefono AS cliente_telefono,
                        TRIM(CONCAT(COALESCE(e.nombre, ''), ' ', COALESCE(e.apellido, ''))) AS empleado_nombre,
                        COALESCE((SELECT SUM(dp.cantidad) FROM detalles_productos dp WHERE dp.id_venta = v.id_venta), 0)
                          + (SELECT COUNT(*) FROM detalles_servicios ds WHERE ds.id_venta = v.id_venta) AS num_items,
                        v.total,
                        (SELECT p.forma_pago FROM pago p WHERE p.venta = v.id_venta ORDER BY p.id_pago LIMIT 1) AS forma_pago,
                        v.estado,
                        (v.factura IS NOT NULL AND v.factura <> '') AS tiene_factura"
                . $from . $sqlWhere .
                " ORDER BY v.fecha DESC, v.id_venta DESC
                  LIMIT :limite OFFSET :inicio"
            );
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v, PDO::PARAM_STR);
            }
            $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
            $stmt->bindValue(':inicio', ($pagina - 1) * $porPagina, PDO::PARAM_INT);
            $stmt->execute();
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($ventas as &$v) {
                $v['id_venta'] = (int)$v['id_venta'];
                $v['num_items'] = (int)$v['num_items'];
                $v['total'] = (float)$v['total'];
                $v['tiene_factura'] = (bool)$v['tiene_factura'];
            }
            unset($v);

            return ['total' => $total, 'pagina' => $pagina, 'por_pagina' => $porPagina, 'ventas' => $ventas];
        } catch (Exception $e) {
            error_log("Error en Ventas::listarHistorial: " . $e->getMessage());
            return $vacio;
        }
    }

    // Detalle completo de una venta (null si no existe)
    public static function detalleVenta(int $idVenta): ?array
    {
        try {
            $db = ConexionDB::obtenerConexion();

            // Encabezado con el primer pago de la venta
            $stmt = $db->prepare(
                "SELECT v.id_venta, v.folio, v.fecha,
                        CASE WHEN c.id_cliente IS NULL THEN 'Publico en general'
                             ELSE TRIM(CONCAT(COALESCE(c.nombre, ''), ' ', COALESCE(c.apellido, ''))) END AS cliente_nombre,
                        c.telefono AS cliente_telefono,
                        TRIM(CONCAT(COALESCE(e.nombre, ''), ' ', COALESCE(e.apellido, ''))) AS empleado_nombre,
                        v.estado, p.forma_pago, p.monto_recibido, p.cambio, p.referencia_pago, v.total, v.factura
                 FROM ventas v
                 LEFT JOIN clientes c ON c.id_cliente = v.cliente
                 LEFT JOIN empleados e ON e.id_empleado = v.empleado
                 LEFT JOIN pago p ON p.id_pago = (SELECT MIN(p2.id_pago) FROM pago p2 WHERE p2.venta = v.id_venta)
                 WHERE v.id_venta = ?"
            );
            $stmt->execute([$idVenta]);
            $venta = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$venta) {
                return null;
            }

            // Productos vendidos
            $stmt = $db->prepare(
                "SELECT pr.codigo, pr.nombre, dp.cantidad, dp.precio_unitario, dp.descuento, dp.iva, dp.subtotal
                 FROM detalles_productos dp
                 LEFT JOIN productos pr ON pr.id_producto = dp.id_producto
                 WHERE dp.id_venta = ?"
            );
            $stmt->execute([$idVenta]);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Servicios vendidos
            $stmt = $db->prepare(
                "SELECT s.nombre, s.categoria, ds.precio_unitario, ds.descuento, ds.iva, ds.subtotal
                 FROM detalles_servicios ds
                 LEFT JOIN servicios s ON s.id_servicio = ds.id_servicio
                 WHERE ds.id_venta = ?"
            );
            $stmt->execute([$idVenta]);
            $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Totales a partir de los renglones
            $subtotal = 0;
            $iva = 0;
            foreach (array_merge($productos, $servicios) as $r) {
                $subtotal += (float)$r['subtotal'];
                $iva += (float)$r['iva'];
            }

            $facturaTexto = trim((string)($venta['factura'] ?? ''));
            unset($venta['factura']);

            $venta['id_venta'] = (int)$venta['id_venta'];
            $venta['total'] = (float)$venta['total'];
            $venta['tiene_factura'] = $facturaTexto !== '';
            $venta['productos'] = $productos;
            $venta['servicios'] = $servicios;
            $venta['subtotal'] = round($subtotal, 2);
            $venta['iva'] = round($iva, 2);

            // Tarjeta: datos que dejo el banco al autorizar el cobro
            if (($venta['forma_pago'] ?? '') === 'tarjeta' && trim((string)($venta['referencia_pago'] ?? '')) !== '') {
                $venta['banco'] = self::datosBanco($db, trim((string)$venta['referencia_pago']));
            }

            // Datos de factura guardados como JSON
            if ($facturaTexto !== '') {
                $factura = json_decode($facturaTexto, true);
                if (is_array($factura)) {
                    $venta['factura'] = $factura;
                }
            }

            return $venta;
        } catch (Exception $e) {
            error_log("Error en Ventas::detalleVenta: " . $e->getMessage());
            return null;
        }
    }

    // Comprobante del banco para un folio de autorizacion (null si no se encuentra).
    // Solo expone los ultimos 4 digitos de la tarjeta.
    private static function datosBanco(PDO $db, string $folio): ?array
    {
        try {
            $stmt = $db->prepare(
                "SELECT folio, numero_tarjeta, monto, comercio, codigo_respuesta, estado_transaccion, fecha
                 FROM transacciones_bancarias WHERE folio = ? LIMIT 1"
            );
            $stmt->execute([$folio]);
            $t = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$t) {
                return null;
            }
            return [
                'folio' => $t['folio'],
                'tarjeta' => '**** **** **** ' . substr((string)$t['numero_tarjeta'], -4),
                'monto' => (float)$t['monto'],
                'comercio' => $t['comercio'],
                'codigo' => $t['codigo_respuesta'],
                'estado' => $t['estado_transaccion'],
                'fecha' => $t['fecha']
            ];
        } catch (Exception $e) {
            error_log("Error en Ventas::datosBanco: " . $e->getMessage());
            return null;
        }
    }

    // Guarda la solicitud de factura de una venta pagada
    public static function guardarFactura(int $idVenta, array $datos): array
    {
        $rfc = trim((string)($datos['rfc'] ?? ''));
        $rfc = function_exists('mb_strtoupper') ? mb_strtoupper($rfc, 'UTF-8') : strtoupper($rfc);
        $razon = trim((string)($datos['razon_social'] ?? ''));
        $cp = trim((string)($datos['codigo_postal'] ?? ''));
        $correo = trim((string)($datos['correo'] ?? ''));
        $largoRazon = function_exists('mb_strlen') ? mb_strlen($razon, 'UTF-8') : strlen($razon);

        if ($idVenta <= 0) {
            return ['ok' => false, 'error' => 'Venta no valida.'];
        }
        if (!preg_match('/^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/u', $rfc)) {
            return ['ok' => false, 'error' => 'El RFC no tiene un formato valido.'];
        }
        if ($largoRazon < 3 || $largoRazon > 100) {
            return ['ok' => false, 'error' => 'La razon social debe tener de 3 a 100 caracteres.'];
        }
        if (!preg_match('/^[0-9]{5}$/', $cp)) {
            return ['ok' => false, 'error' => 'El codigo postal debe tener 5 digitos.'];
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'El correo no es valido.'];
        }

        try {
            $db = ConexionDB::obtenerConexion();

            $stmt = $db->prepare("SELECT estado, factura FROM ventas WHERE id_venta = ?");
            $stmt->execute([$idVenta]);
            $venta = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$venta) {
                return ['ok' => false, 'error' => 'Venta no encontrada.'];
            }
            if ($venta['estado'] !== 'Pagada') {
                return ['ok' => false, 'error' => 'Solo se puede facturar una venta pagada.'];
            }
            if (trim((string)$venta['factura']) !== '') {
                return ['ok' => false, 'error' => 'La venta ya tiene factura solicitada.'];
            }

            $json = json_encode([
                'rfc' => $rfc,
                'razon_social' => $razon,
                'codigo_postal' => $cp,
                'correo' => $correo,
                // Hora de Mexico sin cambiar la zona global
                'fecha_solicitud' => (new DateTime('now', new DateTimeZone('America/Mexico_City')))->format('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);

            // Solo guarda si nadie la facturo antes
            $stmt = $db->prepare(
                "UPDATE ventas SET factura = ?
                 WHERE id_venta = ? AND (factura IS NULL OR factura = '')"
            );
            $stmt->execute([$json, $idVenta]);

            if ($stmt->rowCount() === 0) {
                return ['ok' => false, 'error' => 'La venta ya tiene factura solicitada.'];
            }

            return ['ok' => true];
        } catch (Exception $e) {
            error_log("Error en Ventas::guardarFactura: " . $e->getMessage());
            return ['ok' => false, 'error' => 'No se pudo guardar la factura.'];
        }
    }

    // Busca un cliente activo por telefono (devuelve su id o null)
    public static function buscarClientePorTelefono(string $telefono): ?int
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare(
                "SELECT id_cliente FROM clientes
                 WHERE telefono = ? AND esta_activo = 1 AND id_cliente <> 11
                 ORDER BY id_cliente LIMIT 1"
            );
            $stmt->execute([$telefono]);
            $id = $stmt->fetchColumn();
            return $id === false ? null : (int)$id;
        } catch (PDOException $e) {
            return null;
        }
    }
}
