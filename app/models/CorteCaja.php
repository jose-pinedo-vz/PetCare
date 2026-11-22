<?php
declare(strict_types=1);

final class CorteCaja
{
    public const FONDO_INICIAL = 1000.00;
    private const IMPORTE_MAXIMO = 99999999.99;

    public static function puedeOperarCaja(PDO $db, int $empleado, string $rol): bool
    {
        return self::motivoDenegacionAcceso($db, $empleado, $rol) === null;
    }

    public static function motivoDenegacionAcceso(PDO $db, int $empleado, string $rol): ?string
    {
        if ($empleado < 1) {
            return 'sin_empleado';
        }

        $rolNormalizado = strtolower(trim($rol));
        $stmt = $db->prepare(
            'SELECT puesto, esta_activo
             FROM empleados
             WHERE id_empleado = :empleado
             LIMIT 1'
        );
        $stmt->execute([':empleado' => $empleado]);
        $empleadoDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$empleadoDatos) {
            return 'empleado_no_encontrado';
        }
        if ((int)$empleadoDatos['esta_activo'] !== 1) {
            return 'empleado_inactivo';
        }

        $puestoNormalizado = strtolower(trim((string)$empleadoDatos['puesto']));
        $rolesPorPuesto = [
            'gerente' => ['admin', 'gerente'],
            'vendedor' => ['vendedor'],
            'recepcionista' => ['recepcionista'],
        ];

        if (!isset($rolesPorPuesto[$puestoNormalizado])) {
            return 'puesto_no_autorizado';
        }
        if (!in_array($rolNormalizado, $rolesPorPuesto[$puestoNormalizado], true)) {
            return 'rol_puesto_no_coincide';
        }

        return null;
    }

    public static function obtenerCorteAbierto(PDO $db, int $empleado): ?array
    {
        $stmt = $db->prepare(
            "SELECT c.*, CONCAT(e.nombre, ' ', e.apellido) AS cajero_apertura
             FROM corte_de_caja c
             JOIN empleados e ON e.id_empleado = c.empleado_apertura
             WHERE c.empleado_apertura = :empleado AND c.tipo = 'diario' AND c.estado = 'abierto'
             ORDER BY c.apertura DESC
             LIMIT 1"
        );
        $stmt->execute([':empleado' => $empleado]);
        $corte = $stmt->fetch(PDO::FETCH_ASSOC);

        return $corte ?: null;
    }

    public static function listarEmpleados(PDO $db): array
    {
        return $db->query(
            "SELECT id_empleado, nombre, apellido
             FROM empleados
             WHERE esta_activo = 1
             ORDER BY apellido, nombre"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarMovimientos(PDO $db, int $corte): array
    {
        $stmt = $db->prepare(
            "SELECT m.*, CONCAT(e.nombre, ' ', e.apellido) AS responsable,
                    CONCAT(b.nombre, ' ', b.apellido) AS beneficiario
             FROM movimientos_de_caja m
             JOIN empleados e ON e.id_empleado = m.empleado
             LEFT JOIN empleados b ON b.id_empleado = m.empleado_beneficiario
             WHERE m.corte = :corte
             ORDER BY m.fecha, m.id"
        );
        $stmt->execute([':corte' => $corte]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerResumenPagos(PDO $db, int $corte): array
    {
        $stmt = $db->prepare(
            "SELECT
                COALESCE(SUM(CASE WHEN forma_pago = 'efectivo' THEN
                    CASE WHEN tipo = 'devolucion' THEN -ABS(importe) ELSE importe END
                ELSE 0 END), 0) AS efectivo,
                COALESCE(SUM(CASE WHEN forma_pago = 'tarjeta' THEN
                    CASE WHEN tipo = 'devolucion' THEN -ABS(importe) ELSE importe END
                ELSE 0 END), 0) AS tarjeta,
                COALESCE(SUM(CASE WHEN forma_pago = 'transferencia' THEN
                    CASE WHEN tipo = 'devolucion' THEN -ABS(importe) ELSE importe END
                ELSE 0 END), 0) AS transferencia,
                COUNT(*) AS pagos,
                COUNT(DISTINCT venta) AS ventas
             FROM pago
             WHERE corte = :corte AND estado = 'aplicado'
               AND (tipo IS NULL OR tipo IN ('pago', 'devolucion'))"
        );
        $stmt->execute([':corte' => $corte]);
        $resumen = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $resumen['efectivo'] = (float)($resumen['efectivo'] ?? 0);
        $citas = self::pagosCitaNoEfectivo($db, $corte);
        $resumen['tarjeta'] = (float)($resumen['tarjeta'] ?? 0) + $citas['tarjeta'];
        $resumen['transferencia'] = (float)($resumen['transferencia'] ?? 0) + $citas['transferencia'];
        $resumen['general'] = round($resumen['efectivo'] + $resumen['tarjeta'] + $resumen['transferencia'], 2);
        $resumen['pagos'] = (int)($resumen['pagos'] ?? 0) + $citas['pagos'];
        $resumen['ventas'] = (int)($resumen['ventas'] ?? 0);

        return $resumen;
    }

    public static function listarPagos(PDO $db, int $corte): array
    {
        $stmt = $db->prepare(
            "SELECT p.id_pago, p.fecha, p.importe, p.forma_pago, p.referencia_pago,
                    v.folio AS folio_venta, v.total AS total_venta, v.estado AS estado_venta,
                    CONCAT(c.nombre, ' ', c.apellido) AS cliente
             FROM pago p
             JOIN ventas v ON v.id_venta = p.venta
             LEFT JOIN clientes c ON c.id_cliente = p.cliente
             WHERE p.corte = :corte AND p.estado = 'aplicado'
               AND (p.tipo IS NULL OR p.tipo IN ('pago', 'devolucion'))
             ORDER BY p.fecha DESC, p.id_pago DESC"
        );
        $stmt->execute([':corte' => $corte]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarCortes(
        PDO $db,
        DateTimeImmutable $inicio,
        DateTimeImmutable $fin,
        int $empleado,
        bool $esAdministrador,
        ?int $empleadoFiltro = null,
        string $estado = 'todos'
    ): array {
        $sql = self::consultaCortes()
            . " WHERE c.tipo = 'diario' AND c.apertura >= :inicio AND c.apertura < :fin";
        $parametros = [
            ':inicio' => $inicio->format('Y-m-d H:i:s'),
            ':fin' => $fin->format('Y-m-d H:i:s'),
        ];

        if (!$esAdministrador) {
            $sql .= ' AND c.empleado_apertura = :empleado';
            $parametros[':empleado'] = $empleado;
        } elseif ($empleadoFiltro !== null) {
            $sql .= ' AND c.empleado_apertura = :empleado_filtro';
            $parametros[':empleado_filtro'] = $empleadoFiltro;
        }

        if (in_array($estado, ['abierto', 'cerrado'], true)) {
            $sql .= ' AND c.estado = :estado';
            $parametros[':estado'] = $estado;
        }

        $sql .= ' ORDER BY c.apertura DESC';
        $stmt = $db->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarMovimientosPeriodo(
        PDO $db,
        DateTimeImmutable $inicio,
        DateTimeImmutable $fin,
        ?int $empleadoFiltro = null
    ): array {
        $sql = "SELECT m.fecha, m.tipo, m.concepto, m.importe, m.observaciones,
                       c.folio AS folio_corte,
                       CONCAT(e.nombre, ' ', e.apellido) AS responsable,
                       CONCAT(b.nombre, ' ', b.apellido) AS beneficiario
                FROM movimientos_de_caja m
                JOIN corte_de_caja c ON c.id = m.corte
                JOIN empleados e ON e.id_empleado = m.empleado
                LEFT JOIN empleados b ON b.id_empleado = m.empleado_beneficiario
                WHERE c.tipo = 'diario' AND c.estado = 'cerrado'
                  AND c.apertura >= :inicio AND c.apertura < :fin";
        $parametros = [
            ':inicio' => $inicio->format('Y-m-d H:i:s'),
            ':fin' => $fin->format('Y-m-d H:i:s'),
        ];
        if ($empleadoFiltro !== null) {
            $sql .= ' AND c.empleado_apertura = :empleado';
            $parametros[':empleado'] = $empleadoFiltro;
        }
        $sql .= ' ORDER BY m.fecha, m.id';

        $stmt = $db->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarPagosPeriodo(
        PDO $db,
        DateTimeImmutable $inicio,
        DateTimeImmutable $fin,
        ?int $empleadoFiltro = null
    ): array {
        $sql = "SELECT p.fecha, p.importe, p.forma_pago, p.tipo, p.referencia_pago,
                       v.folio AS folio_venta, v.total AS total_venta,
                       c.folio AS folio_corte,
                       CONCAT(cl.nombre, ' ', cl.apellido) AS cliente
                FROM pago p
                JOIN corte_de_caja c ON c.id = p.corte
                JOIN ventas v ON v.id_venta = p.venta
                LEFT JOIN clientes cl ON cl.id_cliente = p.cliente
                WHERE c.tipo = 'diario' AND c.estado = 'cerrado'
                  AND c.apertura >= :inicio AND c.apertura < :fin
                  AND p.estado = 'aplicado'
                  AND (p.tipo IS NULL OR p.tipo IN ('pago', 'devolucion'))";
        $parametros = [
            ':inicio' => $inicio->format('Y-m-d H:i:s'),
            ':fin' => $fin->format('Y-m-d H:i:s'),
        ];
        if ($empleadoFiltro !== null) {
            $sql .= ' AND c.empleado_apertura = :empleado';
            $parametros[':empleado'] = $empleadoFiltro;
        }
        $sql .= ' ORDER BY p.fecha, p.id_pago';

        $stmt = $db->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerDetalle(
        PDO $db,
        int $id,
        int $empleado,
        bool $esAdministrador
    ): ?array {
        $sql = self::consultaCortes() . ' WHERE c.id = :id';
        $parametros = [':id' => $id];
        if (!$esAdministrador) {
            $sql .= ' AND c.empleado_apertura = :empleado';
            $parametros[':empleado'] = $empleado;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($parametros);
        $corte = $stmt->fetch(PDO::FETCH_ASSOC);

        return $corte ?: null;
    }

    public static function crearCorte(PDO $db, int $empleado, string $turno, float $fondoInicial = self::FONDO_INICIAL): int
    {
        if (!in_array($turno, ['matutino', 'vespertino', 'ambos'], true)) {
            throw new DomainException('Selecciona un turno válido.');
        }
        if ($fondoInicial < 0 || $fondoInicial > self::IMPORTE_MAXIMO || !is_finite($fondoInicial)) {
            throw new DomainException('El fondo inicial debe ser igual o mayor a cero y caber en el campo monetario.');
        }

        for ($intento = 0; $intento < 3; $intento++) {
            try {
                $db->beginTransaction();
                $empleadoLock = $db->prepare(
                    'SELECT id_empleado FROM empleados WHERE id_empleado = :empleado FOR UPDATE'
                );
                $empleadoLock->execute([':empleado' => $empleado]);
                if (!$empleadoLock->fetchColumn()) {
                    throw new DomainException('No se encontró el empleado asociado a tu sesión.');
                }

                $stmt = $db->prepare(
                    "SELECT id
                     FROM corte_de_caja
                     WHERE empleado_apertura = :empleado AND tipo = 'diario' AND estado = 'abierto'
                     FOR UPDATE"
                );
                $stmt->execute([':empleado' => $empleado]);
                if ($stmt->fetchColumn()) {
                    throw new DomainException('Ya tienes un corte abierto.');
                }

                $folio = self::generarFolio();
                $stmt = $db->prepare(
                    "INSERT INTO corte_de_caja
                        (folio, tipo, turno, empleado_apertura, apertura, fondo_inicial, estado)
                     VALUES (:folio, 'diario', :turno, :empleado, NOW(), :fondo, 'abierto')"
                );
                $stmt->execute([
                    ':folio' => $folio,
                    ':turno' => $turno,
                    ':empleado' => $empleado,
                    ':fondo' => round($fondoInicial, 2),
                ]);
                $id = (int)$db->lastInsertId();
                $db->commit();

                return $id;
            } catch (PDOException $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                if ($e->getCode() === '23000' && $intento < 2) {
                    continue;
                }
                throw $e;
            } catch (Throwable $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                throw $e;
            }
        }

        throw new RuntimeException('No se pudo generar un folio único para el corte.');
    }

    public static function registrarMovimiento(
        PDO $db,
        int $corte,
        int $empleado,
        string $tipo,
        string $concepto,
        float $importe,
        ?int $beneficiario,
        ?string $observaciones
    ): void {
        $tipos = ['ingreso manual', 'nomina', 'retiro', 'gasto'];
        if (!in_array($tipo, $tipos, true)) {
            throw new DomainException('Selecciona un tipo de movimiento válido.');
        }
        if ($importe < 0 || $importe > self::IMPORTE_MAXIMO || !is_finite($importe)) {
            throw new DomainException('El importe debe ser igual o mayor a cero y caber en el campo monetario.');
        }
        if ($concepto === '') {
            throw new DomainException('Escribe el concepto del movimiento.');
        }
        if ($tipo === 'nomina' && $beneficiario === null) {
            throw new DomainException('Selecciona al empleado que recibe la nómina.');
        }

        $db->beginTransaction();
        try {
            self::bloquearCorteAbierto($db, $corte, $empleado);
            if ($tipo === 'retiro') {
                $efectivoDisponible = max(self::calcularEfectivoEsperado($db, $corte), 0);
                if (round($importe, 2) > $efectivoDisponible) {
                    throw new DomainException(
                        'No puedes retirar más efectivo del disponible en caja ($'
                        . number_format($efectivoDisponible, 2)
                        . ').'
                    );
                }
            }
            if ($beneficiario !== null) {
                $verificarEmpleado = $db->prepare(
                    'SELECT id_empleado FROM empleados WHERE id_empleado = :id AND esta_activo = 1'
                );
                $verificarEmpleado->execute([':id' => $beneficiario]);
                if (!$verificarEmpleado->fetchColumn()) {
                    throw new DomainException('El empleado beneficiario no es válido.');
                }
            }

            $stmt = $db->prepare(
                "INSERT INTO movimientos_de_caja
                    (corte, fecha, tipo, concepto, importe, empleado, empleado_beneficiario, observaciones)
                 VALUES (:corte, NOW(), :tipo, :concepto, :importe, :empleado, :beneficiario, :observaciones)"
            );
            $stmt->execute([
                ':corte' => $corte,
                ':tipo' => $tipo,
                ':concepto' => $concepto,
                ':importe' => round($importe, 2),
                ':empleado' => $empleado,
                ':beneficiario' => $beneficiario,
                ':observaciones' => $observaciones,
            ]);
            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public static function prepararArqueo(PDO $db, int $corte, int $empleado, float $contado): array
    {
        if ($contado < 0 || $contado > self::IMPORTE_MAXIMO || !is_finite($contado)) {
            throw new DomainException('El efectivo contado debe ser igual o mayor a cero y caber en el campo monetario.');
        }

        self::bloquearCorteAbierto($db, $corte, $empleado);
        $esperado = self::calcularEfectivoEsperado($db, $corte);
        $diferencia = round($contado - $esperado, 2);
        if (abs($esperado) > self::IMPORTE_MAXIMO || abs($diferencia) > self::IMPORTE_MAXIMO) {
            throw new DomainException('Los totales del corte exceden el límite monetario de la base de datos.');
        }

        return [
            'efectivo_contado' => round($contado, 2),
            'efectivo_esperado' => $esperado,
            'diferencia' => $diferencia,
        ];
    }

    public static function cerrarCorte(
        PDO $db,
        int $corte,
        int $empleado,
        float $contado,
        float $esperadoDeclarado,
        ?string $observaciones
    ): array {
        $db->beginTransaction();
        try {
            $fila = self::bloquearCorteAbierto($db, $corte, $empleado);
            $esperadoActual = self::calcularEfectivoEsperado($db, $corte);
            if (round($esperadoActual, 2) !== round($esperadoDeclarado, 2)) {
                throw new DomainException(
                    'Los movimientos cambiaron después del arqueo. Vuelve a contar el efectivo antes de cerrar.'
                );
            }

            $totales = self::obtenerTotalesPagos($db, $corte);
            $diferencia = round($contado - $esperadoActual, 2);
            foreach ([$totales['efectivo'], $totales['tarjeta'], $totales['transferencia'], $totales['general']] as $total) {
                if (abs($total) > self::IMPORTE_MAXIMO) {
                    throw new DomainException('Los totales del corte exceden el límite monetario de la base de datos.');
                }
            }
            $excedente = round(max($contado - (float)$fila['fondo_inicial'], 0), 2);

            if ($excedente > 0) {
                $retiro = $db->prepare(
                    "INSERT INTO movimientos_de_caja
                        (corte, fecha, tipo, concepto, importe, empleado, empleado_beneficiario, observaciones)
                     VALUES (:corte, NOW(), 'retiro', 'Entrega de excedente a resguardo',
                             :importe, :empleado, NULL, NULL)"
                );
                $retiro->execute([
                    ':corte' => $corte,
                    ':importe' => $excedente,
                    ':empleado' => $empleado,
                ]);
            }

            $stmt = $db->prepare(
                "UPDATE corte_de_caja
                 SET empleado_cierre = :empleado_cierre,
                     cierre = NOW(),
                     efectivo_esperado = :esperado,
                     efectivo_contado = :contado,
                     diferencia = :diferencia,
                     total_efectivo = :efectivo,
                     total_tarjeta = :tarjeta,
                     total_transferencia = :transferencia,
                     total_general = :general,
                     estado = 'cerrado',
                     observaciones = :observaciones
                 WHERE id = :id AND empleado_apertura = :empleado AND estado = 'abierto'"
            );
            $stmt->execute([
                ':empleado_cierre' => $empleado,
                ':esperado' => $esperadoActual,
                ':contado' => round($contado, 2),
                ':diferencia' => $diferencia,
                ':efectivo' => $totales['efectivo'],
                ':tarjeta' => $totales['tarjeta'],
                ':transferencia' => $totales['transferencia'],
                ':general' => $totales['general'],
                ':observaciones' => $observaciones,
                ':id' => $corte,
                ':empleado' => $empleado,
            ]);
            if ($stmt->rowCount() !== 1) {
                throw new DomainException('El corte ya fue cerrado o no te pertenece.');
            }

            $db->commit();

            return [
                'excedente' => $excedente,
                'faltante_fondo' => round(max((float)$fila['fondo_inicial'] - $contado, 0), 2),
            ];
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public static function obtenerResumenMensual(
        PDO $db,
        DateTimeImmutable $inicio,
        DateTimeImmutable $fin,
        ?int $empleadoFiltro = null
    ): array {
        $filtroEmpleadoSql = $empleadoFiltro === null ? '' : ' AND empleado_apertura = :empleado';
        $parametros = [
            ':inicio' => $inicio->format('Y-m-d H:i:s'),
            ':fin' => $fin->format('Y-m-d H:i:s'),
        ];
        if ($empleadoFiltro !== null) {
            $parametros[':empleado'] = $empleadoFiltro;
        }
        $stmt = $db->prepare(
            "SELECT
                COALESCE(SUM(total_efectivo), 0) AS efectivo,
                COALESCE(SUM(total_tarjeta), 0) AS tarjeta,
                COALESCE(SUM(total_transferencia), 0) AS transferencia,
                COALESCE(SUM(total_general), 0) AS comercial,
                COALESCE(SUM(fondo_inicial), 0) AS fondos_iniciales,
                COUNT(*) AS cortes
             FROM corte_de_caja
             WHERE tipo = 'diario' AND estado = 'cerrado'
               AND apertura >= :inicio AND apertura < :fin"
            . $filtroEmpleadoSql
        );
        $stmt->execute($parametros);
        $totales = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $stmt = $db->prepare(
            "SELECT m.tipo, COALESCE(SUM(m.importe), 0) AS importe
             FROM movimientos_de_caja m
             JOIN corte_de_caja c ON c.id = m.corte
             WHERE c.tipo = 'diario' AND c.estado = 'cerrado'
               AND c.apertura >= :inicio AND c.apertura < :fin
               AND m.tipo IN ('ingreso manual', 'nomina', 'retiro', 'gasto')
             " . ($empleadoFiltro === null ? '' : ' AND c.empleado_apertura = :empleado') . "
             GROUP BY m.tipo"
        );
        $stmt->execute($parametros);
        $movimientos = [
            'ingreso manual' => 0.0,
            'nomina' => 0.0,
            'retiro' => 0.0,
            'gasto' => 0.0,
        ];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $movimiento) {
            $movimientos[$movimiento['tipo']] = (float)$movimiento['importe'];
        }

        $stmt = $db->prepare(
            "SELECT COUNT(*) AS cantidad, COALESCE(SUM(total), 0) AS importe
             FROM ventas
             WHERE fecha >= :inicio AND fecha < :fin
               AND LOWER(estado) NOT LIKE 'cancel%'"
            . ($empleadoFiltro === null ? '' : ' AND empleado = :empleado')
        );
        $stmt->execute($parametros);
        $ventas = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $productosSql = "
            SELECT p.id_producto, p.nombre,
                   COALESCE(SUM(CASE WHEN v.id_venta IS NULL THEN 0 ELSE dp.cantidad END), 0) AS cantidad,
                   COALESCE(SUM(CASE WHEN v.id_venta IS NULL THEN 0 ELSE dp.subtotal + dp.iva END), 0) AS importe
            FROM productos p
            LEFT JOIN detalles_productos dp ON dp.id_producto = p.id_producto
            LEFT JOIN ventas v
                ON v.id_venta = dp.id_venta
                AND v.fecha >= :inicio AND v.fecha < :fin
                AND LOWER(v.estado) NOT LIKE 'cancel%'
                " . ($empleadoFiltro === null ? '' : ' AND v.empleado = :empleado') . "
            GROUP BY p.id_producto, p.nombre";
        $stmt = $db->prepare($productosSql . ' HAVING cantidad > 0 ORDER BY cantidad DESC, p.nombre ASC LIMIT 5');
        $stmt->execute($parametros);
        $masVendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare($productosSql . ' HAVING importe > 0 ORDER BY importe DESC, p.nombre ASC LIMIT 5');
        $stmt->execute($parametros);
        $masIngresos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare($productosSql . ' ORDER BY cantidad ASC, p.nombre ASC');
        $stmt->execute($parametros);
        $menosVendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $comercial = (float)($totales['comercial'] ?? 0);
        $gastosOperativos = $movimientos['nomina'] + $movimientos['gasto'];

        return [
            'efectivo' => (float)($totales['efectivo'] ?? 0),
            'tarjeta' => (float)($totales['tarjeta'] ?? 0),
            'transferencia' => (float)($totales['transferencia'] ?? 0),
            'comercial' => $comercial,
            'fondos_iniciales' => (float)($totales['fondos_iniciales'] ?? 0),
            'cortes' => (int)($totales['cortes'] ?? 0),
            'ventas_cantidad' => (int)($ventas['cantidad'] ?? 0),
            'ventas_importe' => (float)($ventas['importe'] ?? 0),
            'movimientos' => $movimientos,
            'gastos_operativos' => $gastosOperativos,
            'retiros_resguardo' => $movimientos['retiro'],
            'mas_vendidos' => $masVendidos,
            'mas_ingresos' => $masIngresos,
            'menos_vendidos' => $menosVendidos,
        ];
    }

    private static function consultaCortes(): string
    {
        return "SELECT c.*,
                       CONCAT(ea.nombre, ' ', ea.apellido) AS cajero_apertura,
                       CONCAT(ec.nombre, ' ', ec.apellido) AS cajero_cierre
                FROM corte_de_caja c
                JOIN empleados ea ON ea.id_empleado = c.empleado_apertura
                LEFT JOIN empleados ec ON ec.id_empleado = c.empleado_cierre";
    }

    private static function generarFolio(): string
    {
        $sufijo = strtoupper(str_pad(base_convert((string)random_int(0, 46655), 10, 36), 3, '0', STR_PAD_LEFT));

        return 'C' . date('ym') . $sufijo;
    }

    private static function bloquearCorteAbierto(PDO $db, int $corte, int $empleado): array
    {
        $stmt = $db->prepare(
            "SELECT *
             FROM corte_de_caja
             WHERE id = :id AND empleado_apertura = :empleado
               AND tipo = 'diario' AND estado = 'abierto'
             FOR UPDATE"
        );
        $stmt->execute([':id' => $corte, ':empleado' => $empleado]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fila) {
            throw new DomainException('El corte no está abierto o no te pertenece.');
        }

        return $fila;
    }

    // Efectivo que hay ahora en la caja del corte (fondo + cobros en efectivo +/- movimientos)
    public static function efectivoDisponible(PDO $db, int $corte): float
    {
        return self::calcularEfectivoEsperado($db, $corte);
    }

    private static function calcularEfectivoEsperado(PDO $db, int $corte): float
    {
        $stmt = $db->prepare(
            "SELECT fondo_inicial
             FROM corte_de_caja
             WHERE id = :id"
        );
        $stmt->execute([':id' => $corte]);
        $fondo = $stmt->fetchColumn();
        if ($fondo === false) {
            throw new DomainException('No se encontró el corte.');
        }

        $stmt = $db->prepare(
            "SELECT COALESCE(SUM(
                CASE WHEN tipo = 'devolucion' THEN -ABS(importe) ELSE importe END
             ), 0)
             FROM pago
             WHERE corte = :corte AND estado = 'aplicado'
               AND forma_pago = 'efectivo'
               AND (tipo IS NULL OR tipo IN ('pago', 'devolucion'))"
        );
        $stmt->execute([':corte' => $corte]);
        $pagosEfectivo = (float)$stmt->fetchColumn();

        $stmt = $db->prepare(
            "SELECT COALESCE(SUM(
                CASE WHEN tipo = 'ingreso manual' THEN importe ELSE -importe END
             ), 0)
             FROM movimientos_de_caja
             WHERE corte = :corte"
        );
        $stmt->execute([':corte' => $corte]);
        $impactoMovimientos = (float)$stmt->fetchColumn();

        return round((float)$fondo + $pagosEfectivo + $impactoMovimientos, 2);
    }

    // Pagos de citas con tarjeta o transferencia cobrados en este corte (el efectivo ya entra como movimiento)
    private static function pagosCitaNoEfectivo(PDO $db, int $corte): array
    {
        $res = ['tarjeta' => 0.0, 'transferencia' => 0.0, 'pagos' => 0];
        try {
            foreach (['pagos_cita', 'pagos_hotel'] as $tabla) {
                if (!$db->query("SHOW TABLES LIKE '$tabla'")->fetch()
                    || !$db->query("SHOW COLUMNS FROM $tabla LIKE 'devuelto'")->fetch()) {
                    continue;
                }
                $stmt = $db->prepare(
                    "SELECT metodo, COALESCE(SUM(monto), 0) AS total, COUNT(*) AS n
                     FROM $tabla
                     WHERE corte = :corte AND metodo IN ('tarjeta', 'transferencia')
                       AND id_venta IS NULL AND devuelto = 0
                     GROUP BY metodo"
                );
                $stmt->execute([':corte' => $corte]);
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $f) {
                    $res[$f['metodo']] = round($res[$f['metodo']] + (float)$f['total'], 2);
                    $res['pagos'] += (int)$f['n'];
                }
            }
        } catch (Throwable $e) {
            error_log('CorteCaja::pagosCitaNoEfectivo ' . $e->getMessage());
        }
        return $res;
    }

    private static function obtenerTotalesPagos(PDO $db, int $corte): array
    {
        $stmt = $db->prepare(
            "SELECT forma_pago,
                    SUM(CASE WHEN tipo = 'devolucion' THEN -ABS(importe) ELSE importe END) AS total
             FROM pago
             WHERE corte = :corte AND estado = 'aplicado'
               AND forma_pago IN ('efectivo', 'tarjeta', 'transferencia')
               AND (tipo IS NULL OR tipo IN ('pago', 'devolucion'))
             GROUP BY forma_pago"
        );
        $stmt->execute([':corte' => $corte]);
        $totales = ['efectivo' => 0.0, 'tarjeta' => 0.0, 'transferencia' => 0.0];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $totales[$fila['forma_pago']] = round((float)$fila['total'], 2);
        }
        $citas = self::pagosCitaNoEfectivo($db, $corte);
        $totales['tarjeta'] = round($totales['tarjeta'] + $citas['tarjeta'], 2);
        $totales['transferencia'] = round($totales['transferencia'] + $citas['transferencia'], 2);
        $totales['general'] = round(array_sum($totales), 2);

        return $totales;
    }
}
