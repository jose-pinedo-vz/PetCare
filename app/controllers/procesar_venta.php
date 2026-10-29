<?php
// procesar_venta.php
require_once '../models/conexion_db.php';
$conexion = ConexionDB::obtenerConexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si los datos vienen de un formulario HTML tradicional
    $cliente_id = !empty($_POST['id_cliente']) ? $_POST['id_cliente'] : null;
    $forma_pago = $_POST['forma_pago'] ?? 'Efectivo';
    $monto_recibido = isset($_POST['monto_recibido']) ? floatval($_POST['monto_recibido']) : 0.00;
    
    // ID de empleado por defecto (mientras no hay sesiones activas)
    $id_empleado = 1; 

    // Los productos y servicios se pueden recibir serializados o como arrays desde el formulario
    // (Por ejemplo, mediante campos ocultos o decodificando un JSON enviado en un input hidden)
    $productos = isset($_POST['productos_json']) ? json_decode($_POST['productos_json'], true) : [];
    $servicios = isset($_POST['servicios_json']) ? json_decode($_POST['servicios_json'], true) : [];

    if (empty($productos) && empty($servicios)) {
        // Redirigir o mostrar error si el carrito está vacío
        header("Location: ventas.php?error=carrito_vacio");
        exit;
    }

    try {
        $conexion->beginTransaction();

        $subtotal_general = 0;
        foreach ($productos as $p) {
            $subtotal_general += ($p['precio'] * $p['cantidad']) - ($p['descuento'] ?? 0);
        }
        foreach ($servicios as $s) {
            $subtotal_general += ($s['precio'] * ($s['cantidad'] ?? 1)) - ($s['descuento'] ?? 0);
        }
        
        $iva_general = $subtotal_general * 0.16;
        $total_general = $subtotal_general + $iva_general;
        $cambio = ($forma_pago === 'Efectivo') ? max(0, $monto_recibido - $total_general) : 0.00;

        if ($forma_pago === 'Efectivo' && $monto_recibido < $total_general) {
            throw new Exception('El monto recibido en efectivo es menor al total de la venta.');
        }

        // Insertar en tabla 'ventas'
        $sqlVenta = "INSERT INTO ventas (fecha, id_cliente, id_empleado, productos_vendidos, servicios_contratados, total, forma_pago, estado) 
                     VALUES (NOW(), :id_cliente, :id_empleado, :prod_count, :serv_count, :total, :forma_pago, 'Pagada')";
        $stmtVenta = $conexion->prepare($sqlVenta);
        $stmtVenta->execute([
            ':id_cliente' => $cliente_id,
            ':id_empleado' => $id_empleado,
            ':prod_count' => count($productos),
            ':serv_count' => count($servicios),
            ':total' => $total_general,
            ':forma_pago' => $forma_pago
        ]);
        $id_venta = $conexion->lastInsertId();

        // Procesar productos, stock y movimientos...
        foreach ($productos as $prod) {
            $cantidad = $prod['cantidad'];
            $precio = $prod['precio'];
            $descuento = $prod['descuento'] ?? 0;
            $subtotal_item = ($precio * $cantidad) - $descuento;
            $iva_item = $subtotal_item * 0.16;

            $stmtStock = $conexion->prepare("SELECT existencia FROM productos WHERE id_producto = ?");
            $stmtStock->execute([$prod['id']]);
            $stockActual = $stmtStock->fetchColumn();

            if ($stockActual === false || $stockActual < $cantidad) {
                throw new Exception("Stock insuficiente para el producto ID: {$prod['id']}");
            }

            $sqlDetalleP = "INSERT INTO detalles_productos (id_venta, id_producto, cantidad, precio_unitario, iva, descuento, subtotal) 
                            VALUES (:id_venta, :id_producto, :cantidad, :precio, :iva, :descuento, :subtotal)";
            $conexion->prepare($sqlDetalleP)->execute([
                ':id_venta' => $id_venta,
                ':id_producto' => $prod['id'],
                ':cantidad' => $cantidad,
                ':precio' => $precio,
                ':iva' => $iva_item,
                ':descuento' => $descuento,
                ':subtotal' => $subtotal_item
            ]);

            $conexion->prepare("UPDATE productos SET existencia = existencia - ? WHERE id_producto = ?")
                     ->execute([$cantidad, $prod['id']]);

            $conexion->prepare("INSERT INTO movimientos_inventario (id_producto, tipo_movimiento, motivo, id_empleado) VALUES (?, 'Salida', 'Venta', ?)")
                     ->execute([$prod['id'], $id_empleado]);
        }

        // Procesar servicios...
        foreach ($servicios as $serv) {
            $precio = $serv['precio'];
            $descuento = $serv['descuento'] ?? 0;
            $subtotal_item = $precio - $descuento;
            $iva_item = $subtotal_item * 0.16;

            $conexion->prepare("INSERT INTO detalles_servicios (id_venta, id_servicio, precio_unitario, iva, descuento, subtotal) VALUES (:id_venta, :id_servicio, :precio, :iva, :descuento, :subtotal)")
                     ->execute([
                         ':id_venta' => $id_venta,
                         ':id_servicio' => $serv['id'],
                         ':precio' => $precio,
                         ':iva' => $iva_item,
                         ':descuento' => $descuento,
                         ':subtotal' => $subtotal_item
                     ]);
        }

        // Registrar pago
        $conexion->prepare("INSERT INTO pago (id_venta, id_cliente, fecha, importe, forma_pago, monto_recibido, cambio, id_empleado_receptor, estado) VALUES (:id_venta, :id_cliente, NOW(), :importe, :forma_pago, :monto_recibido, :cambio, :id_empleado, 'Aplicado')")
                 ->execute([
                     ':id_venta' => $id_venta,
                     ':id_cliente' => $cliente_id,
                     ':importe' => $total_general,
                     ':forma_pago' => $forma_pago,
                     ':monto_recibido' => $monto_recibido,
                     ':cambio' => $cambio,
                     ':id_empleado' => $id_empleado
                 ]);

        $conexion->commit();
        
        // Redirigir a la vista de ventas con éxito (o mostrar ticket)
        header("Location: ventas.php?exito=1&id_venta=" . $id_venta);
        exit;

    } catch (Exception $e) {
        $conexion->rollBack();
        header("Location: ventas.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}