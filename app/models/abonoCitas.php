<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/abonoCitas.php';
if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

class abonoCitas
{
    public static function inserrcionAVentasyPagos($idCliente, $idEmpleado, $total, $montoRecibido, $iva, $referencia, $metodoPago)
    {
        $pdo = ConexionDB::obtenerConexion();
        try
        {   
            $pdo-> beginTransaction();
            $folio = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

            $saldo = 0.00;
            $subtotal = round((float)$total - (float)$iva, 2);

            $queryVenta = "
                INSERT INTO ventas (fecha, cliente, empleado, total, estado, folio, subtotal, iva, saldo)
                VALUES (now(), :cliente, :empleado, :total, 'Pendiente', :folio, :subtotal, :iva, :saldo);
            ";

            $stml = $pdo->prepare($queryVenta);
            $stml->execute([
                ':cliente'=> $idCliente ?: null,
                ':empleado'=> $idEmpleado,
                ':total'=> $total,
                ':folio'=> $folio,
                ':subtotal'=> $subtotal,
                ':iva'=> $iva,
                ':saldo'=> $saldo
                ]);
            
            $cambio = max(0, $montoRecibido - $total);

            $idVenta = (int)$pdo->lastInsertId();

            $queryPago = "
                INSERT INTO pago (venta, cliente, importe, forma_pago, referencia_pago, empleado_receptor, estado, monto_recibido, cambio, tipo)
                Values (:venta, :cliente, :importe, :forma_pago, :referencia_pago, :empleado_receptor, 'aplicado', :monto_recibido, :cambio, 'pago');
            ";

            $stmlPago = $pdo->prepare($queryPago);
            $stmlPago->execute([
                ':venta' => $idVenta,
                ':cliente' => $idCliente ?: null,
                ':importe'=> $total,
                ':forma_pago'=> strtolower($metodoPago),
                ':referencia_pago'=> $referencia !== '' ? mb_substr($referencia, 0, 30) : null,
                ':empleado_receptor' => $idEmpleado,
                ':monto_recibido'=> $montoRecibido,
                ':cambio' => $cambio
            ]);

            $pdo->commit();

            return [
                'ok'       => true,
                'mensaje'  => 'Anticipo registrado correctamente.',
                'id_venta' => $idVenta,
                'folio'    => $folio
            ];
        }
        catch (Exception $e)
        {
            if ($pdo->inTransaction()) 
            {
                $pdo->rollBack();
            }

            return [
                'ok'      => false,
                'mensaje' => 'Error al registrar el pago: ' . $e->getMessage()
            ];
        }
    }
}


?>