<?php
require_once __DIR__ . '/conexion_db.php';
require_once __DIR__ . '/Productos.php';

class Hotel
{
    public static function existeMascota(int $id)
    {
        $conexion = ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare("SELECT id_mascota  FROM mascotas WHERE id_mascota = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public static function existeCliente(int $id)
    {
        $conexion = ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare("SELECT id_cliente FROM clientes WHERE id_cliente = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public static function existeJaula(int $id)
    {
        $conexion = ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare("SELECT id FROM jaula WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public static function existeReservaActiva(int $idCliente, int $idMascota): bool
    {
        $conexion = ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare(
            "SELECT 1
             FROM hotel
             WHERE cliente = ?
               AND mascota = ?
               AND LOWER(TRIM(estado)) IN (
                   'por pagar',
                   'pagado',
                   'activo',
                   'pendiente de pago',
                   'pendiente cartilla',
                   'confirmada'
               )
             LIMIT 1"
        );
        $stmt->execute([$idCliente, $idMascota]);
        return (bool) $stmt->fetchColumn();
    }

    public static function insertarHotel(array $datos)
    {
        try {
            $conexion = ConexionDB::obtenerConexion();
        } catch (Exception $e) {
            error_log('Error al conectar para registrar una reservación de hotel: ' . $e->getMessage());
            return ["Error BD", htmlspecialchars($e->getMessage())];
        }

        $insertar = "INSERT INTO hotel (
            cliente, mascota, fecha_entrada, fecha_salida, num_noches,
            servicios_adicionales, precio_noche, anticipo, saldo, estado,
            id_jaula, alimentacion_habitual, cantidad_alimento,
            horario_alimentacion, medicamentos, horario_medicamentos,
            persona_recoge, telefono_emergencia, necesidades_especiales,
            observaciones
        ) VALUES (
            :cliente, :mascota, :fecha_entrada, :fecha_salida, :num_noches,
            :servicios_adicionales, :precio_noche, :anticipo, :saldo, :estado,
            :jaula, :alimentacion_habitual, :cantidad_alimento,
            :horario_alimentacion, :medicamentos, :horario_medicamentos,
            :persona_recoge, :telefono_emergencia, :necesidades_especiales,
            :observaciones
        )";

        try {
            $conexion->beginTransaction();

            $reservacionActiva = $conexion->prepare(
                "SELECT 1
                 FROM hotel
                 WHERE cliente = :cliente
                   AND mascota = :mascota
                   AND LOWER(TRIM(estado)) IN (
                       'por pagar',
                       'pagado',
                       'activo',
                       'pendiente de pago',
                       'pendiente cartilla',
                       'confirmada'
                   )
                 LIMIT 1"
            );
            $reservacionActiva->execute([
                ':cliente' => (int) $datos['id_cliente'],
                ':mascota' => (int) $datos['id_mascota'],
            ]);
            if ($reservacionActiva->fetchColumn() !== false) {
                $conexion->rollBack();
                return ['error' => 'El cliente ya tiene una reservación registrada para esta mascota.'];
            }

            $actualizarJaula = $conexion->prepare("
                UPDATE jaula j
                SET estado = 'ocupado'
                WHERE j.id = :id_jaula
                    AND LOWER(TRIM(j.estado)) = 'disponible'
                    AND NOT EXISTS (
                        SELECT 1
                        FROM hotel h
                        WHERE h.id_jaula = j.id
                            AND LOWER(TRIM(h.estado)) IN ('por pagar', 'pagado', 'activo', 'pendiente de pago', 'pendiente cartilla', 'confirmada')
                            AND :fecha_entrada < h.fecha_salida
                            AND :fecha_salida > h.fecha_entrada
                    )
            ");
            $actualizarJaula->execute([
                ':id_jaula' => (int) $datos['id_jaula'],
                ':fecha_entrada' => $datos['fecha_entrada'],
                ':fecha_salida' => $datos['fecha_salida'],
            ]);
            if ($actualizarJaula->rowCount() !== 1) {
                $conexion->rollBack();
                return ['error' => 'La jaula seleccionada ya no está disponible para esas fechas. Elige otra jaula.'];
            }

            $stmt = $conexion->prepare($insertar);
            // El formulario no cobra anticipo; una reservación nueva queda pendiente del total.
            $anticipo = '0.00';
            $estado = 'Por pagar';
            $stmt->bindParam(':cliente',                $datos['id_cliente'],            PDO::PARAM_INT);
            $stmt->bindParam(':mascota',                $datos['id_mascota'],            PDO::PARAM_INT);
            $stmt->bindParam(':fecha_entrada',          $datos['fecha_entrada'],         PDO::PARAM_STR);
            $stmt->bindParam(':fecha_salida',           $datos['fecha_salida'],          PDO::PARAM_STR);
            $stmt->bindParam(':num_noches',             $datos['num_noches'],            PDO::PARAM_INT);
            $stmt->bindParam(':servicios_adicionales',  $datos['servicios_adicionales'], PDO::PARAM_STR);
            $stmt->bindParam(':precio_noche',           $datos['precio_noche'],          PDO::PARAM_STR);
            $stmt->bindParam(':anticipo',               $anticipo,                       PDO::PARAM_STR);
            $stmt->bindParam(':saldo',                  $datos['saldo'],                 PDO::PARAM_STR);
            $stmt->bindParam(':estado',                 $estado,                         PDO::PARAM_STR);
            $stmt->bindParam(':jaula',                  $datos['id_jaula'],              PDO::PARAM_INT);
            $stmt->bindParam(':alimentacion_habitual',  $datos['alimentacion_habitual'], PDO::PARAM_STR);
            $stmt->bindParam(':cantidad_alimento',      $datos['cantidad_alimento'],     PDO::PARAM_STR);
            $stmt->bindParam(':horario_alimentacion',   $datos['horario_alimentacion'],  PDO::PARAM_STR);
            $stmt->bindParam(':medicamentos',           $datos['medicamentos'],          PDO::PARAM_STR);
            $stmt->bindParam(':horario_medicamentos',   $datos['horario_medicamentos'],  PDO::PARAM_STR);
            $stmt->bindParam(':persona_recoge',         $datos['persona_recoge'],        PDO::PARAM_STR);
            $stmt->bindParam(':telefono_emergencia',    $datos['telefono_emergencia'],   PDO::PARAM_STR);
            $stmt->bindParam(':necesidades_especiales', $datos['necesidades_especiales'],PDO::PARAM_STR);
            $stmt->bindParam(':observaciones',          $datos['observaciones'],         PDO::PARAM_STR);

            $stmt->execute();

            $conexion->commit();
        } catch (PDOException $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log('Error al guardar la reservación y ocupar su jaula: ' . $e->getMessage());
            return ["Error BD", htmlspecialchars($e->getMessage())];
        }
    }

    private static function tieneColumna(PDO $conexion, string $tabla, string $columna): bool
    {
        try {
            $stmt = $conexion->prepare("SHOW COLUMNS FROM `{$tabla}` LIKE ?");
            $stmt->execute([$columna]);
            return (bool)$stmt->fetch();
        } catch (Throwable $e) {
            return false;
        }
    }

    public static function obtenerJaulas()
    {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $tieneIdJaula = self::tieneColumna($conexion, 'hotel', 'id_jaula');
            $tieneHotelEnJaula = self::tieneColumna($conexion, 'jaula', 'hotel');
            $sql = "SELECT j.id, j.tamano, j.estado FROM jaula j WHERE LOWER(TRIM(j.estado)) = 'disponible'";
            if ($tieneIdJaula) {
                $sql .= "
                    AND NOT EXISTS (
                        SELECT 1
                        FROM hotel h
                        WHERE h.id_jaula = j.id
                            AND LOWER(TRIM(h.estado)) IN ('por pagar', 'pagado', 'activo', 'pendiente de pago', 'pendiente cartilla', 'confirmada')
                            AND DATE(h.fecha_salida) >= CURRENT_DATE()
                    )";
            } elseif ($tieneHotelEnJaula) {
                $sql .= "
                    AND NOT EXISTS (
                        SELECT 1
                        FROM hotel h
                        WHERE h.id_reservacion = j.hotel
                            AND LOWER(TRIM(h.estado)) IN ('por pagar', 'pagado', 'activo', 'pendiente de pago', 'pendiente cartilla', 'confirmada')
                            AND DATE(h.fecha_salida) >= CURRENT_DATE()
                    )";
            }
            $sql .= ' ORDER BY j.tamano, j.id';
            $stmt = $conexion->query($sql);
            if ($stmt instanceof PDOStatement) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }
            return [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public static function obtenerJaulasParaListado(): array
    {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $tieneIdJaula = self::tieneColumna($conexion, 'hotel', 'id_jaula');
            $tieneHotelEnJaula = self::tieneColumna($conexion, 'jaula', 'hotel');

            if ($tieneIdJaula) {
                $sql = "
                    SELECT j.id, j.tamano, j.estado,
                        (
                            SELECT m.nombre
                            FROM hotel h
                            INNER JOIN mascotas m ON m.id_mascota = h.mascota
                            WHERE h.id_jaula = j.id
                                AND LOWER(TRIM(h.estado)) IN ('por pagar', 'pagado', 'activo', 'pendiente de pago', 'pendiente cartilla', 'confirmada')
                                AND CURRENT_DATE() >= DATE(h.fecha_entrada)
                                AND CURRENT_DATE() < DATE(h.fecha_salida)
                            ORDER BY DATE(h.fecha_salida) DESC, h.id_reservacion DESC
                            LIMIT 1
                        ) AS mascota_actual,
                        (
                            SELECT MAX(DATE(h.fecha_salida))
                            FROM hotel h
                            WHERE h.id_jaula = j.id
                                AND LOWER(TRIM(h.estado)) IN ('por pagar', 'pagado', 'activo', 'pendiente de pago', 'pendiente cartilla', 'confirmada')
                                AND DATE(h.fecha_salida) >= CURRENT_DATE()
                        ) AS libre_a_partir,
                        CASE
                            WHEN LOWER(TRIM(j.estado)) = 'mantenimiento' THEN 'mantenimiento'
                            WHEN LOWER(TRIM(j.estado)) = 'ocupado'
                                OR EXISTS (
                                    SELECT 1
                                    FROM hotel h
                                    WHERE h.id_jaula = j.id
                                        AND LOWER(TRIM(h.estado)) IN ('por pagar', 'pagado', 'activo', 'pendiente de pago', 'pendiente cartilla', 'confirmada')
                                        AND DATE(h.fecha_salida) >= CURRENT_DATE()
                                ) THEN 'ocupado'
                            ELSE LOWER(TRIM(j.estado))
                        END AS estado_actual
                    FROM jaula j
                    ORDER BY j.id
                ";
            } elseif ($tieneHotelEnJaula) {
                $sql = "
                    SELECT j.id, j.tamano, j.estado,
                        (
                            SELECT m.nombre
                            FROM hotel h
                            INNER JOIN mascotas m ON m.id_mascota = h.mascota
                            WHERE h.id_reservacion = j.hotel
                                AND LOWER(TRIM(h.estado)) IN ('por pagar', 'pagado', 'activo', 'pendiente de pago', 'pendiente cartilla', 'confirmada')
                                AND CURRENT_DATE() >= DATE(h.fecha_entrada)
                                AND CURRENT_DATE() < DATE(h.fecha_salida)
                            ORDER BY DATE(h.fecha_salida) DESC, h.id_reservacion DESC
                            LIMIT 1
                        ) AS mascota_actual,
                        (
                            SELECT MAX(DATE(h.fecha_salida))
                            FROM hotel h
                            WHERE h.id_reservacion = j.hotel
                                AND LOWER(TRIM(h.estado)) IN ('por pagar', 'pagado', 'activo', 'pendiente de pago', 'pendiente cartilla', 'confirmada')
                                AND DATE(h.fecha_salida) >= CURRENT_DATE()
                        ) AS libre_a_partir,
                        CASE
                            WHEN LOWER(TRIM(j.estado)) = 'mantenimiento' THEN 'mantenimiento'
                            WHEN LOWER(TRIM(j.estado)) = 'ocupado'
                                OR EXISTS (
                                    SELECT 1
                                    FROM hotel h
                                    WHERE h.id_reservacion = j.hotel
                                        AND LOWER(TRIM(h.estado)) IN ('por pagar', 'pagado', 'activo', 'pendiente de pago', 'pendiente cartilla', 'confirmada')
                                        AND DATE(h.fecha_salida) >= CURRENT_DATE()
                                ) THEN 'ocupado'
                            ELSE LOWER(TRIM(j.estado))
                        END AS estado_actual
                    FROM jaula j
                    ORDER BY j.id
                ";
            } else {
                $sql = "SELECT j.id, j.tamano, j.estado, j.estado AS estado_actual, NULL AS mascota_actual, NULL AS libre_a_partir FROM jaula j ORDER BY j.id";
            }

            $stmt = $conexion->query($sql);
            if ($stmt instanceof PDOStatement) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }
            return [];
        } catch (Throwable $e) {
            error_log('Error al cargar jaulas del hotel: ' . $e->getMessage());
            return ['error' => 'No fue posible cargar la disponibilidad de jaulas.'];
        }
    }

    public static function obtenerReservacionesParaListado(): array
    {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $tieneIdJaula = self::tieneColumna($conexion, 'hotel', 'id_jaula');
            $tieneHotelEnJaula = self::tieneColumna($conexion, 'jaula', 'hotel');

            if ($tieneIdJaula) {
                $sql = "
                    SELECT h.id_reservacion,
                        COALESCE(CONCAT_WS(' ', c.nombre, c.apellido), 'Cliente no disponible') AS cliente,
                        COALESCE(m.nombre, 'Mascota no disponible') AS mascota,
                        h.id_jaula,
                        j.tamano AS tamano_jaula,
                        h.fecha_entrada,
                        h.fecha_salida,
                        h.num_noches,
                        h.saldo,
                        h.estado
                    FROM hotel h
                    LEFT JOIN clientes c ON c.id_cliente = h.cliente
                    LEFT JOIN mascotas m ON m.id_mascota = h.mascota
                    LEFT JOIN jaula j ON j.id = h.id_jaula
                    ORDER BY h.fecha_entrada DESC, h.id_reservacion DESC
                ";
            } elseif ($tieneHotelEnJaula) {
                $sql = "
                    SELECT h.id_reservacion,
                        COALESCE(CONCAT_WS(' ', c.nombre, c.apellido), 'Cliente no disponible') AS cliente,
                        COALESCE(m.nombre, 'Mascota no disponible') AS mascota,
                        j.id AS id_jaula,
                        j.tamano AS tamano_jaula,
                        h.fecha_entrada,
                        h.fecha_salida,
                        h.num_noches,
                        h.saldo,
                        h.estado
                    FROM hotel h
                    LEFT JOIN clientes c ON c.id_cliente = h.cliente
                    LEFT JOIN mascotas m ON m.id_mascota = h.mascota
                    LEFT JOIN jaula j ON j.hotel = h.id_reservacion
                    ORDER BY h.fecha_entrada DESC, h.id_reservacion DESC
                ";
            } else {
                $sql = "
                    SELECT h.id_reservacion,
                        COALESCE(CONCAT_WS(' ', c.nombre, c.apellido), 'Cliente no disponible') AS cliente,
                        COALESCE(m.nombre, 'Mascota no disponible') AS mascota,
                        NULL AS id_jaula,
                        NULL AS tamano_jaula,
                        h.fecha_entrada,
                        h.fecha_salida,
                        h.num_noches,
                        h.saldo,
                        h.estado
                    FROM hotel h
                    LEFT JOIN clientes c ON c.id_cliente = h.cliente
                    LEFT JOIN mascotas m ON m.id_mascota = h.mascota
                    ORDER BY h.fecha_entrada DESC, h.id_reservacion DESC
                ";
            }

            $stmt = $conexion->query($sql);
            if ($stmt instanceof PDOStatement) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }
            return [];
        } catch (Throwable $e) {
            error_log('Error al cargar reservaciones del hotel: ' . $e->getMessage());
            return ['error' => 'No fue posible cargar las reservaciones.'];
        }
    }

    public static function validarVacunasMascota(int $idMascota, string $fechaSalida): array
    {
        $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $fechaSalida);
        $erroresFecha = DateTimeImmutable::getLastErrors();

        if (
            $fecha === false ||
            ($erroresFecha !== false && ($erroresFecha['warning_count'] > 0 || $erroresFecha['error_count'] > 0)) ||
            $fecha->format('Y-m-d') !== $fechaSalida
        ) {
            return [
                'consulta_ok' => true,
                'valida' => false,
                'vacunas' => [],
                'errores' => ['Selecciona una fecha de salida válida para revisar las vacunas.'],
            ];
        }

        try {
            $conexion = ConexionDB::obtenerConexion();
            $stmt = $conexion->prepare("
                SELECT lista_vacunas.nombre AS tipo_vacunas, vacunas.fecha_aplicacion,
                       vacunas.fecha_proxima, vacunas.veterinario,
                       CONCAT_WS(' ', empleados.nombre, empleados.apellido) AS veterinario_nombre,
                       vacunas.lote,
                       vacunas.fabricante, vacunas.observaciones
                FROM mascotas
                INNER JOIN especies
                    ON LOWER(TRIM(especies.especie)) COLLATE utf8mb4_general_ci
                        = LOWER(TRIM(mascotas.especie)) COLLATE utf8mb4_general_ci
                INNER JOIN lista_vacunas
                    ON lista_vacunas.especie = especies.id
                    AND lista_vacunas.estado = 1
                LEFT JOIN vacunas
                    ON vacunas.id_mascota = mascotas.id_mascota
                    AND vacunas.vacuna_puesta = lista_vacunas.id
                LEFT JOIN empleados
                    ON empleados.id_empleado = vacunas.veterinario
                WHERE mascotas.id_mascota = ?
                ORDER BY lista_vacunas.nombre, vacunas.fecha_aplicacion DESC, vacunas.id_vacuna DESC
            ");
            $stmt->execute([$idMascota]);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error al consultar vacunas para reservación de hotel: ' . $e->getMessage());
            return [
                'consulta_ok' => false,
                'valida' => false,
                'vacunas' => [],
                'errores' => ['No fue posible consultar el historial de vacunas. Intenta nuevamente.'],
            ];
        }

        $vacunas = [];
        $errores = [];
        $registrosPorVacuna = [];
        foreach ($registros as $registro) {
            $nombre = (string) $registro['tipo_vacunas'];
            $registrosPorVacuna[$nombre][] = $registro;
        }

        foreach ($registrosPorVacuna as $nombre => $registrosVacuna) {
            $registroSeleccionado = null;
            $hayRegistroVigente = false;

            foreach ($registrosVacuna as $registro) {
                if ($registro['fecha_aplicacion'] === null) {
                    continue;
                }

                if ($registroSeleccionado === null) {
                    $registroSeleccionado = $registro;
                }
                if (
                    $registro['fecha_aplicacion'] <= $fechaSalida &&
                    !empty($registro['fecha_proxima']) &&
                    $registro['fecha_proxima'] >= $fechaSalida
                ) {
                    $registroSeleccionado = $registro;
                    $hayRegistroVigente = true;
                    break;
                }
            }

            $registroSeleccionado = $registroSeleccionado ?? [
                'tipo_vacunas' => $nombre,
                'fecha_aplicacion' => null,
                'fecha_proxima' => null,
                'veterinario' => null,
                'veterinario_nombre' => null,
                'lote' => null,
                'fabricante' => null,
                'observaciones' => null,
            ];
            $registro = $registroSeleccionado;

            if ($registro['fecha_aplicacion'] === null) {
                $estado = 'Sin registro';
            } elseif ($registro['fecha_aplicacion'] > $fechaSalida) {
                $estado = 'Aplicada después de la salida';
            } elseif (empty($registro['fecha_proxima'])) {
                $estado = 'Sin fecha próxima';
            } elseif ($registro['fecha_proxima'] < $fechaSalida) {
                $estado = 'Vence antes de la salida';
            } else {
                $estado = 'Vigente hasta la salida';
            }

            $vacunas[] = array_merge($registro, [
                'requerida' => true,
                'valida' => $hayRegistroVigente,
                'estado_validacion' => $estado,
            ]);

            if (!$hayRegistroVigente) {
                $errores[] = $registro['fecha_aplicacion'] === null
                    ? "Falta registrar la vacuna {$nombre}."
                    : "La vacuna {$nombre} no está vigente hasta la fecha de salida.";
            }
        }

        return [
            'consulta_ok' => true,
            'valida' => count($errores) === 0,
            'vacunas' => $vacunas,
            'errores' => $errores,
        ];
    }

    public static function insertarJaula(string $tamano): array
    {
        $conexion = ConexionDB::obtenerConexion();
        $conexion->beginTransaction();
        try {
            $consultaId = $conexion->query("SELECT COALESCE(MAX(id), 0) + 1 FROM jaula");
            $id = (int) $consultaId->fetchColumn();
            $stmt = $conexion->prepare("
                INSERT INTO jaula (id, tamano, estado)
                VALUES (?, ?, 'disponible')
            ");
            $stmt->execute([$id, $tamano]);
            $conexion->commit();
        } catch (Throwable $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $e;
        }

        return [
            'id' => $id,
            'tamano' => $tamano,
            'estado' => 'disponible',
        ];
    }

    public static function eliminarJaulaDisponible(int $id): bool
    {
        $conexion = ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare("
            DELETE FROM jaula
            WHERE id = ?
                AND LOWER(TRIM(estado)) = 'disponible'
        ");
        $stmt->execute([$id]);

        return $stmt->rowCount() === 1;
    }

    public static function actualizarEstadoJaulaDisponible(int $id, string $estado): bool
    {
        if (!in_array($estado, ['disponible', 'mantenimiento'], true)) {
            return false;
        }

        $conexion = ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare("
            UPDATE jaula
            SET estado = ?
            WHERE id = ?
                AND LOWER(TRIM(estado)) IN ('disponible', 'mantenimiento')
        ");
        $stmt->execute([$estado, $id]);

        return $stmt->rowCount() === 1;
    }

    public static function actualizarEstadoReservacion(int $id, string $accion): array
    {
        $estadosAccion = [
            'cancelar' => 'Cancelado',
            'no_asistio' => 'No asistio',
            'finalizar' => 'Finalizado',
        ];
        if (!isset($estadosAccion[$accion])) {
            return ['error' => 'La acción solicitada no es válida.'];
        }

        $conexion = ConexionDB::obtenerConexion();
        try {
            $conexion->beginTransaction();
            $tieneIdJaula = self::tieneColumna($conexion, 'hotel', 'id_jaula');
            $tieneHotelEnJaula = self::tieneColumna($conexion, 'jaula', 'hotel');
            $sqlJaula = $tieneIdJaula ? 'id_jaula' : 'NULL AS id_jaula';
            $stmt = $conexion->prepare("
                SELECT estado, fecha_salida, {$sqlJaula}
                FROM hotel
                WHERE id_reservacion = ?
                FOR UPDATE
            ");
            $stmt->execute([$id]);
            $reservacion = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$reservacion) {
                $conexion->rollBack();
                return ['error' => 'No se encontró la reservación.'];
            }
            if (!$tieneIdJaula && $tieneHotelEnJaula) {
                $jaulaReservacion = $conexion->prepare(
                    "SELECT id FROM jaula WHERE hotel = ? FOR UPDATE"
                );
                $jaulaReservacion->execute([$id]);
                $reservacion['id_jaula'] = $jaulaReservacion->fetchColumn() ?: null;
            }

            $estadoActual = strtolower(trim((string) $reservacion['estado']));
            $estadosAbiertos = ['por pagar', 'pagado', 'activo', 'pendiente de pago', 'pendiente cartilla', 'confirmada'];
            if (!in_array($estadoActual, $estadosAbiertos, true)) {
                $conexion->rollBack();
                return ['error' => 'La reservación ya no permite esta acción.'];
            }
            if (
                $accion === 'finalizar' &&
                (empty($reservacion['fecha_salida']) || substr((string) $reservacion['fecha_salida'], 0, 10) > date('Y-m-d'))
            ) {
                $conexion->rollBack();
                return ['error' => 'La reservación solo se puede finalizar a partir de la fecha de salida.'];
            }

            $actualizar = $conexion->prepare("
                UPDATE hotel SET estado = ? WHERE id_reservacion = ?
            ");
            $actualizar->execute([$estadosAccion[$accion], $id]);

            if (!empty($reservacion['id_jaula'])) {
                $relacionReservacion = $tieneIdJaula ? 'h.id_jaula = j.id' : 'h.id_reservacion = j.hotel';
                $jaula = $conexion->prepare("
                    UPDATE jaula j
                    SET estado = 'disponible'
                    WHERE j.id = ?
                        AND LOWER(TRIM(j.estado)) <> 'mantenimiento'
                        AND NOT EXISTS (
                            SELECT 1 FROM hotel h
                            WHERE {$relacionReservacion}
                                AND LOWER(TRIM(h.estado)) IN ('por pagar', 'pagado', 'activo', 'pendiente de pago', 'pendiente cartilla', 'confirmada')
                                AND DATE(h.fecha_salida) >= CURRENT_DATE()
                        )
                ");
                $jaula->execute([(int) $reservacion['id_jaula']]);
            }

            $conexion->commit();
            return ['estado' => $estadosAccion[$accion]];
        } catch (Throwable $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log('Error al actualizar una reservación del hotel: ' . $e->getMessage());
            return ['error' => 'No fue posible actualizar la reservación.'];
        }
    }

    public static function obtenerJaulaPorId(int $id)
    {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $stmt = $conexion->prepare("SELECT id, tamano, estado FROM jaula WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["Error BD", htmlspecialchars($e->getMessage())];
        }
    }

    public static function actualizarEstadoJaula(int $id, string $nuevoEstado)
    {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $stmt = $conexion->prepare("UPDATE jaula SET estado = ? WHERE id = ?");
            return $stmt->execute([$nuevoEstado, $id]);
        } catch (PDOException $e) {
            return ["Error BD", htmlspecialchars($e->getMessage())];
        }
    }

}