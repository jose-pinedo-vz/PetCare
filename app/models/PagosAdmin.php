<?php
// Pagos de servicios (citas) y pagos a empleados, conectados al corte de caja
require_once __DIR__ . '/conexion_db.php';
require_once __DIR__ . '/CorteCaja.php';
if (file_exists(__DIR__ . '/../../banca_simulada/banco_servicio.php')) {
    require_once __DIR__ . '/../../banca_simulada/banco_servicio.php';
}

class PagosAdmin
{
    private static function agregarColumna(PDO $db, string $tabla, string $col, string $ddl): void
    {
        if (!$db->query("SHOW COLUMNS FROM `$tabla` LIKE '$col'")->fetch()) {
            $db->exec("ALTER TABLE `$tabla` ADD COLUMN $ddl");
        }
    }

    // Crea lo que haga falta (solo la primera vez) para no depender de scripts SQL.
    // Ojo: el ALTER/CREATE hace commit implicito, no llamarlo dentro de una transaccion.
    private static bool $listo = false;

    public static function preparar(PDO $db): void
    {
        if (self::$listo) {
            return;
        }
        self::$listo = true;
        $db->exec(
            "CREATE TABLE IF NOT EXISTS pagos_cita (
                id_pago INT AUTO_INCREMENT PRIMARY KEY,
                id_cita INT NOT NULL,
                monto DECIMAL(10,2) NOT NULL,
                metodo VARCHAR(20) NOT NULL,
                referencia VARCHAR(40) NULL,
                origen VARCHAR(10) NOT NULL DEFAULT 'caja',
                empleado INT NULL,
                fecha DATETIME NOT NULL,
                INDEX idx_pagos_cita (id_cita)
            )"
        );
        $db->exec(
            "CREATE TABLE IF NOT EXISTS pagos_hotel (
                id_pago INT AUTO_INCREMENT PRIMARY KEY,
                id_reservacion INT NOT NULL,
                monto DECIMAL(10,2) NOT NULL,
                metodo VARCHAR(20) NOT NULL,
                referencia VARCHAR(40) NULL,
                origen VARCHAR(10) NOT NULL DEFAULT 'caja',
                empleado INT NULL,
                fecha DATETIME NOT NULL,
                INDEX idx_pagos_hotel (id_reservacion)
            )"
        );
        foreach (['pagos_cita', 'pagos_hotel'] as $t) {
            self::agregarColumna($db, $t, 'corte', 'corte INT NULL');
            self::agregarColumna($db, $t, 'id_venta', 'id_venta INT NULL');
            self::agregarColumna($db, $t, 'devuelto', 'devuelto TINYINT NOT NULL DEFAULT 0');
            self::agregarColumna($db, $t, 'folio_devolucion', 'folio_devolucion VARCHAR(40) NULL');
        }
        self::agregarColumna($db, 'empleados', 'sueldo', 'sueldo DECIMAL(10,2) NOT NULL DEFAULT 0');
    }

    private static function momento(PDO $db): string
    {
        return $db->query("SHOW COLUMNS FROM citas LIKE 'hora'")->fetch() ? "TIMESTAMP(c.fecha, c.hora)" : "c.fecha";
    }

    private static function tablaPagos(string $tipo): array
    {
        return $tipo === 'hotel' ? ['pagos_hotel', 'id_reservacion'] : ['pagos_cita', 'id_cita'];
    }

    // ---------- Pagos de servicios (citas y hotel) ----------
    public static function servicios(PDO $db): array
    {
        self::preparar($db);
        $filas = [];

        $sql = "SELECT c.id_cita AS id, c.servicio AS id_servicio, " . self::momento($db) . " AS fecha, c.estado_cita,
                       CONCAT(cl.nombre, ' ', cl.apellido) AS cliente, m.nombre AS mascota, m.especie,
                       s.nombre AS servicio, s.categoria, COALESCE(s.precio, 0) AS total,
                       COALESCE((SELECT SUM(p.monto) FROM pagos_cita p WHERE p.id_cita = c.id_cita AND p.devuelto = 0), 0) AS abonado
                FROM citas c
                LEFT JOIN clientes cl ON cl.id_cliente = c.cliente
                LEFT JOIN mascotas m ON m.id_mascota = c.mascota
                LEFT JOIN servicios s ON s.id_servicio = c.servicio
                ORDER BY fecha DESC LIMIT 200";
        foreach ($db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $f) {
            $f['tipo'] = 'cita';
            $f['folio'] = 'CT-' . $f['id'];
            $f['cancelada'] = in_array(strtolower((string)$f['estado_cita']), ['cancelada', 'cancelado'], true);
            $filas[] = $f;
        }

        $sql = "SELECT h.id_reservacion AS id, h.fecha_entrada AS fecha, h.estado AS estado_cita,
                       CONCAT(cl.nombre, ' ', cl.apellido) AS cliente, m.nombre AS mascota, m.especie,
                       CONCAT('Hotel · ', h.num_noches, ' noche(s)') AS servicio, 'Hotel' AS categoria,
                       h.num_noches * h.precio_noche AS total, COALESCE(h.anticipo, 0) AS anticipo_hotel,
                       COALESCE((SELECT SUM(p.monto) FROM pagos_hotel p WHERE p.id_reservacion = h.id_reservacion AND p.devuelto = 0), 0) AS abonado
                FROM hotel h
                LEFT JOIN clientes cl ON cl.id_cliente = h.cliente
                LEFT JOIN mascotas m ON m.id_mascota = h.mascota
                ORDER BY h.fecha_entrada DESC LIMIT 200";
        foreach ($db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $f) {
            $f['tipo'] = 'hotel';
            $f['folio'] = 'HT-' . $f['id'];
            $f['cancelada'] = in_array(strtolower((string)$f['estado_cita']), ['cancelada', 'cancelado', 'no asistio', 'vencida', 'abandonada'], true);
            // Anticipo cobrado fuera de caja (reservacion pagada en linea): cuenta como abonado
            if (!$f['cancelada']) {
                $f['abonado'] = (float)$f['abonado'] + (float)$f['anticipo_hotel'];
            }
            $filas[] = $f;
        }

        $salida = [];
        foreach ($filas as $f) {
            $f['total'] = (float)$f['total'];
            $f['abonado'] = (float)$f['abonado'];
            if ($f['cancelada']) {
                // Solo interesa si hay dinero por devolver
                if ($f['abonado'] <= 0) {
                    continue;
                }
                $f['saldo'] = 0.0;
                $f['estado'] = 'devolver';
            } else {
                $f['saldo'] = max(0, round($f['total'] - $f['abonado'], 2));
                $f['estado'] = $f['saldo'] <= 0 ? 'pagado' : ($f['abonado'] > 0 ? 'parcial' : 'pendiente');
            }
            $salida[] = $f;
        }
        usort($salida, fn($a, $b) => strcmp((string)$b['fecha'], (string)$a['fecha']));
        return $salida;
    }

    private static function buscarServicio(PDO $db, string $tipo, int $id): ?array
    {
        foreach (self::servicios($db) as $f) {
            if ($f['tipo'] === $tipo && (int)$f['id'] === $id) {
                return $f;
            }
        }
        return null;
    }

    public static function registrarPagoServicio(PDO $db, string $tipo, int $id, float $monto, string $metodo, array $tarjeta, string $referencia, int $empleado): array
    {
        self::preparar($db);
        $tipo = $tipo === 'hotel' ? 'hotel' : 'cita';
        [$tabla, $colId] = self::tablaPagos($tipo);
        $monto = round($monto, 2);
        $metodo = strtolower($metodo);
        if (!in_array($metodo, ['efectivo', 'tarjeta', 'transferencia'], true)) {
            return ['ok' => false, 'error' => 'Método de pago no válido.'];
        }
        $corte = CorteCaja::obtenerCorteAbierto($db, $empleado);
        if (!$corte) {
            return ['ok' => false, 'error' => 'Debes abrir caja para registrar pagos.'];
        }
        $fila = self::buscarServicio($db, $tipo, $id);
        if (!$fila || $fila['cancelada']) {
            return ['ok' => false, 'error' => 'El servicio no existe o está cancelado.'];
        }
        if ($monto <= 0 || $monto > $fila['saldo'] + 0.001) {
            return ['ok' => false, 'error' => 'El abono debe ser mayor a cero y no superar el saldo ($' . number_format($fila['saldo'], 2) . ').'];
        }
        $referencia = mb_substr(trim($referencia), 0, 40);
        $folioBanco = null;
        if ($metodo === 'tarjeta') {
            $num = preg_replace('/\D/', '', (string)($tarjeta['numero'] ?? ''));
            $res = bancoAutorizarCobro($num, (string)($tarjeta['vencimiento'] ?? ''), (string)($tarjeta['cvv'] ?? ''), $monto, 'PetCare - ' . $fila['folio']);
            if (!$res['ok']) {
                return ['ok' => false, 'error' => $res['error']];
            }
            $folioBanco = $res['folio'];
            $referencia = $folioBanco;
        }
        $idPago = 0;
        try {
            $s = $db->prepare("INSERT INTO $tabla ($colId, monto, metodo, referencia, origen, empleado, corte, fecha) VALUES (?, ?, ?, ?, 'caja', ?, ?, NOW())");
            $s->execute([$id, $monto, $metodo, $referencia !== '' ? $referencia : null, $empleado, (int)$corte['id']]);
            $idPago = (int)$db->lastInsertId();
            if ($metodo === 'efectivo') {
                CorteCaja::registrarMovimiento($db, (int)$corte['id'], $empleado, 'ingreso manual', 'Pago de servicio ' . $fila['folio'], $monto, null, 'Cobro en efectivo de ' . $fila['folio']);
            }
            if ($tipo === 'hotel') {
                // Mantener el saldo de la reservacion igual al que se muestra en Hotel
                $db->prepare(
                    "UPDATE hotel SET saldo = GREATEST(ROUND(num_noches * precio_noche - COALESCE(anticipo, 0)
                        - (SELECT COALESCE(SUM(p.monto), 0) FROM pagos_hotel p WHERE p.id_reservacion = hotel.id_reservacion AND p.devuelto = 0), 2), 0)
                     WHERE id_reservacion = ?"
                )->execute([$id]);
            }
            if ($tipo === 'hotel' && round($fila['saldo'] - $monto, 2) <= 0) {
                // Reservacion de recepcion: Por pagar -> Pagado. Reservacion del cliente: Pendiente de pago -> Confirmada.
                $db->prepare("UPDATE hotel SET estado = 'Pagado' WHERE id_reservacion = ? AND LOWER(TRIM(estado)) = 'por pagar'")->execute([$id]);
                $db->prepare("UPDATE hotel SET estado = 'Confirmada' WHERE id_reservacion = ? AND estado = 'Pendiente de pago'")->execute([$id]);
            }
        } catch (Throwable $e) {
            if ($idPago > 0) {
                $db->prepare("DELETE FROM $tabla WHERE id_pago = ?")->execute([$idPago]);
            }
            if ($folioBanco) {
                bancoReversarCobro($folioBanco);
            }
            error_log('PagosAdmin::registrarPagoServicio ' . $e->getMessage());
            return ['ok' => false, 'error' => 'No se pudo registrar el pago.'];
        }
        return ['ok' => true, 'autorizacion' => $folioBanco];
    }

    // Saldo de una cita para cobrarla desde el punto de venta (null si no se puede cobrar)
    public static function citaPorCobrar(PDO $db, int $idCita): ?array
    {
        $f = self::buscarServicio($db, 'cita', $idCita);
        return ($f && !$f['cancelada'] && $f['saldo'] > 0) ? $f : null;
    }

    public static function citasPorCobrar(PDO $db): array
    {
        return array_values(array_filter(self::servicios($db), fn($f) => $f['tipo'] === 'cita' && !$f['cancelada'] && $f['saldo'] > 0));
    }

    // El punto de venta ya cobro la cita: se registra el abono ligado a la venta (no cuenta doble en el corte)
    public static function registrarCobroPos(PDO $db, int $idCita, float $monto, string $metodo, string $folioVenta, int $idVenta, int $empleado, int $corte): void
    {
        $s = $db->prepare("INSERT INTO pagos_cita (id_cita, monto, metodo, referencia, origen, empleado, corte, id_venta, fecha) VALUES (?, ?, ?, ?, 'pos', ?, ?, ?, NOW())");
        $s->execute([$idCita, round($monto, 2), $metodo, $folioVenta, $empleado, $corte, $idVenta]);
    }

    // ---------- Devoluciones ----------
    // $empleado = null cuando la cancelacion la hace el cliente: solo se regresa lo pagado con tarjeta.
    public static function devolver(PDO $db, string $tipo, int $id, ?int $empleado): array
    {
        self::preparar($db);
        $tipo = $tipo === 'hotel' ? 'hotel' : 'cita';
        [$tabla, $colId] = self::tablaPagos($tipo);
        $s = $db->prepare("SELECT * FROM $tabla WHERE $colId = ? AND devuelto = 0 AND id_venta IS NULL ORDER BY id_pago");
        $s->execute([$id]);
        $pagos = $s->fetchAll(PDO::FETCH_ASSOC);
        $devuelto = 0.0;
        $pendiente = 0.0;
        $folio = ($tipo === 'hotel' ? 'HT-' : 'CT-') . $id;
        $corte = $empleado ? CorteCaja::obtenerCorteAbierto($db, $empleado) : null;
        foreach ($pagos as $p) {
            $monto = (float)$p['monto'];
            $ref = '';
            if ($p['metodo'] === 'tarjeta' && str_starts_with((string)$p['referencia'], 'AUT-')) {
                if (!bancoReversarCobro((string)$p['referencia'])) {
                    return ['ok' => false, 'error' => 'El banco no pudo reversar ' . $p['referencia'] . '.', 'devuelto' => $devuelto];
                }
                $ref = 'REV-' . $p['referencia'];
            } elseif ($empleado === null) {
                $pendiente += $monto; // efectivo/transferencia: lo devuelve el personal en sucursal
                continue;
            } elseif ($p['metodo'] === 'efectivo') {
                if (!$corte) {
                    return ['ok' => false, 'error' => 'Abre caja para devolver efectivo.', 'devuelto' => $devuelto];
                }
                try {
                    CorteCaja::registrarMovimiento($db, (int)$corte['id'], $empleado, 'retiro', 'Devolución de ' . $folio, $monto, null, 'Devolución en efectivo');
                } catch (Throwable $e) {
                    return ['ok' => false, 'error' => $e->getMessage(), 'devuelto' => $devuelto];
                }
                $ref = 'EFE-' . $folio;
            } else {
                $ref = 'TRF-' . $folio; // transferencia: se marca; la regresa tesoreria
            }
            $db->prepare("UPDATE $tabla SET devuelto = 1, folio_devolucion = ? WHERE id_pago = ?")->execute([$ref, $p['id_pago']]);
            $devuelto += $monto;
        }
        return ['ok' => true, 'devuelto' => round($devuelto, 2), 'pendiente' => round($pendiente, 2)];
    }

    // ---------- Pagos a empleados ----------
    public static function nomina(PDO $db, string $periodo): array
    {
        self::preparar($db);
        $s = $db->prepare(
            "SELECT e.id_empleado, CONCAT(e.nombre, ' ', e.apellido) AS nombre, e.puesto, e.sueldo,
                    (SELECT COUNT(*) FROM movimientos_de_caja m
                     WHERE m.tipo = 'nomina' AND m.empleado_beneficiario = e.id_empleado
                       AND m.observaciones = ?) AS pagado
             FROM empleados e WHERE e.esta_activo + 0 = 1 ORDER BY e.nombre"
        );
        $s->execute(['Periodo ' . $periodo]);
        $filas = $s->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as &$f) {
            $f['sueldo'] = (float)$f['sueldo'];
            $f['pagado'] = (int)$f['pagado'] > 0;
        }
        return $filas;
    }

    public static function guardarSueldo(PDO $db, int $idEmpleado, float $sueldo): bool
    {
        self::preparar($db);
        if ($sueldo < 0 || $sueldo > 999999) {
            return false;
        }
        $s = $db->prepare("UPDATE empleados SET sueldo = ? WHERE id_empleado = ?");
        return $s->execute([round($sueldo, 2), $idEmpleado]);
    }

    public static function pagarEmpleado(PDO $db, int $empleadoSesion, int $idEmpleado, string $periodo): array
    {
        self::preparar($db);
        $corte = CorteCaja::obtenerCorteAbierto($db, $empleadoSesion);
        if (!$corte) {
            return ['ok' => false, 'error' => 'Debes abrir caja para pagar nómina.'];
        }
        $fila = null;
        foreach (self::nomina($db, $periodo) as $f) {
            if ((int)$f['id_empleado'] === $idEmpleado) {
                $fila = $f;
            }
        }
        if (!$fila) {
            return ['ok' => false, 'error' => 'El empleado no existe o no está activo.'];
        }
        if ($fila['pagado']) {
            return ['ok' => false, 'error' => 'Ese empleado ya fue pagado en este periodo.'];
        }
        if ($fila['sueldo'] <= 0) {
            return ['ok' => false, 'error' => 'Captura primero el sueldo del empleado.'];
        }
        $disponible = CorteCaja::efectivoDisponible($db, (int)$corte['id']);
        if ($fila['sueldo'] > $disponible) {
            return ['ok' => false, 'faltante' => round($fila['sueldo'] - $disponible, 2),
                    'error' => 'No hay efectivo suficiente. Faltan $' . number_format($fila['sueldo'] - $disponible, 2) . '; abona a caja.'];
        }
        try {
            CorteCaja::registrarMovimiento($db, (int)$corte['id'], $empleadoSesion, 'nomina', 'Pago de nómina: ' . $fila['nombre'], $fila['sueldo'], $idEmpleado, 'Periodo ' . $periodo);
        } catch (Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
        return ['ok' => true];
    }

    public static function abonarCaja(PDO $db, int $empleadoSesion, float $monto, string $concepto): array
    {
        $corte = CorteCaja::obtenerCorteAbierto($db, $empleadoSesion);
        if (!$corte) {
            return ['ok' => false, 'error' => 'Debes abrir caja para abonar efectivo.'];
        }
        $concepto = mb_substr(trim($concepto), 0, 100);
        if ($monto <= 0 || $concepto === '') {
            return ['ok' => false, 'error' => 'Captura un monto mayor a cero y un concepto.'];
        }
        try {
            CorteCaja::registrarMovimiento($db, (int)$corte['id'], $empleadoSesion, 'ingreso manual', $concepto, $monto, null, 'Abono a caja');
        } catch (Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
        return ['ok' => true];
    }

    public static function resumenCaja(PDO $db, int $empleadoSesion): array
    {
        $corte = CorteCaja::obtenerCorteAbierto($db, $empleadoSesion);
        if (!$corte) {
            return ['abierta' => false, 'efectivo' => 0.0, 'movimientos' => []];
        }
        $mov = array_filter(
            CorteCaja::listarMovimientos($db, (int)$corte['id']),
            fn($m) => in_array($m['tipo'], ['nomina', 'ingreso manual'], true)
        );
        return ['abierta' => true, 'efectivo' => CorteCaja::efectivoDisponible($db, (int)$corte['id']), 'movimientos' => array_values($mov)];
    }

    // ---------- Anticipo de cliente (cita) ----------
    public static function pagadoCita(PDO $db, int $idCita): float
    {
        self::preparar($db);
        $s = $db->prepare("SELECT COALESCE(SUM(monto), 0) FROM pagos_cita WHERE id_cita = ? AND devuelto = 0");
        $s->execute([$idCita]);
        return (float)$s->fetchColumn();
    }

    public static function pagarAnticipoCliente(PDO $db, int $idCita, float $monto, array $tarjeta): array
    {
        self::preparar($db);
        $num = preg_replace('/\D/', '', (string)($tarjeta['numero'] ?? ''));
        $res = bancoAutorizarCobro($num, (string)($tarjeta['vencimiento'] ?? ''), (string)($tarjeta['cvv'] ?? ''), $monto, 'PetCare - Anticipo cita CT-' . $idCita);
        if (!$res['ok']) {
            return ['ok' => false, 'error' => $res['error']];
        }
        try {
            $s = $db->prepare("INSERT INTO pagos_cita (id_cita, monto, metodo, referencia, origen, fecha) VALUES (?, ?, 'tarjeta', ?, 'cliente', NOW())");
            $s->execute([$idCita, round($monto, 2), $res['folio']]);
        } catch (Throwable $e) {
            bancoReversarCobro($res['folio']);
            error_log('PagosAdmin::pagarAnticipoCliente ' . $e->getMessage());
            return ['ok' => false, 'error' => 'No se pudo registrar el pago.'];
        }
        return ['ok' => true, 'folio' => $res['folio']];
    }
}
