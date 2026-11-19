<?php
require_once __DIR__ . '/conexion_db.php';
require_once __DIR__ . '/Productos.php';

class Hotel
{
    private const vacunas_requeridas = [
        'rabia' => 'Rabia',
        'parvovirus' => 'Parvovirus',
        'moquillo' => 'Moquillo',
    ];

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

            $stmt = $conexion->prepare($insertar);
            // El formulario no cobra anticipo; una reservación nueva queda pendiente del total.
            $anticipo = '0.00';
            $estado = 'activo';
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

            $actualizarJaula = $conexion->prepare("
                UPDATE jaula
                SET estado = 'ocupado'
                WHERE id = :id_jaula
                    AND LOWER(TRIM(estado)) = 'disponible'
            ");
            $actualizarJaula->execute([':id_jaula' => (int) $datos['id_jaula']]);

            if ($actualizarJaula->rowCount() !== 1) {
                $conexion->rollBack();
                return ['error' => 'La jaula seleccionada ya no está disponible. Elige otra jaula.'];
            }

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

    public static function desactivarReservacionesVencidas(): array
    {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $conexion->beginTransaction();
            $stmt = $conexion->prepare("
                UPDATE hotel
                SET estado = 'inactivo'
                WHERE LOWER(TRIM(estado)) = 'activo'
                    AND fecha_salida IS NOT NULL
                    AND DATE(fecha_salida) <= CURRENT_DATE()
            ");
            $stmt->execute();
            $reservacionesActualizadas = $stmt->rowCount();

            $jaulasLiberadas = 0;
            $tieneIdJaula = self::tieneColumna($conexion, 'hotel', 'id_jaula');
            $tieneHotelEnJaula = self::tieneColumna($conexion, 'jaula', 'hotel');

            if ($tieneIdJaula) {
                $jaulas = $conexion->prepare("
                    UPDATE jaula j
                    SET estado = 'disponible'
                    WHERE LOWER(TRIM(j.estado)) = 'ocupado'
                        AND EXISTS (
                            SELECT 1
                            FROM hotel h
                            WHERE h.id_jaula = j.id
                                AND LOWER(TRIM(h.estado)) = 'inactivo'
                                AND DATE(h.fecha_salida) <= CURRENT_DATE()
                        )
                        AND NOT EXISTS (
                            SELECT 1
                            FROM hotel h
                            WHERE h.id_jaula = j.id
                                AND LOWER(TRIM(h.estado)) = 'activo'
                        )
                ");
                $jaulas->execute();
                $jaulasLiberadas = $jaulas->rowCount();
            } elseif ($tieneHotelEnJaula) {
                $jaulas = $conexion->prepare("
                    UPDATE jaula j
                    SET estado = 'disponible'
                    WHERE LOWER(TRIM(j.estado)) = 'ocupado'
                        AND EXISTS (
                            SELECT 1
                            FROM hotel h
                            WHERE h.id_reservacion = j.hotel
                                AND LOWER(TRIM(h.estado)) = 'inactivo'
                                AND DATE(h.fecha_salida) <= CURRENT_DATE()
                        )
                        AND NOT EXISTS (
                            SELECT 1
                            FROM hotel h
                            WHERE h.id_reservacion = j.hotel
                                AND LOWER(TRIM(h.estado)) = 'activo'
                        )
                ");
                $jaulas->execute();
                $jaulasLiberadas = $jaulas->rowCount();
            }

            $conexion->commit();
            return [
                'actualizadas' => $reservacionesActualizadas,
                'jaulas_liberadas' => $jaulasLiberadas,
            ];
        } catch (Throwable $e) {
            if (isset($conexion) && $conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log('Error al desactivar reservaciones vencidas del hotel: ' . $e->getMessage());
            return ['error' => 'No fue posible actualizar el estado de las reservaciones vencidas.'];
        }
    }

    public static function obtenerJaulas()
    {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $stmt = $conexion->query("
                SELECT id, tamano, estado
                FROM jaula
                WHERE LOWER(estado) = 'disponible'
                ORDER BY tamano, id
            ");
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
                                AND LOWER(TRIM(h.estado)) = 'activo'
                                AND CURRENT_DATE() >= DATE(h.fecha_entrada)
                                AND CURRENT_DATE() < DATE(h.fecha_salida)
                            ORDER BY DATE(h.fecha_salida) DESC, h.id_reservacion DESC
                            LIMIT 1
                        ) AS mascota_actual,
                        (
                            SELECT MAX(DATE(h.fecha_salida))
                            FROM hotel h
                            WHERE h.id_jaula = j.id
                                AND LOWER(TRIM(h.estado)) = 'activo'
                                AND CURRENT_DATE() >= DATE(h.fecha_entrada)
                                AND CURRENT_DATE() < DATE(h.fecha_salida)
                        ) AS libre_a_partir,
                        CASE
                            WHEN LOWER(TRIM(j.estado)) = 'mantenimiento' THEN 'mantenimiento'
                            WHEN LOWER(TRIM(j.estado)) = 'ocupado'
                                OR EXISTS (
                                    SELECT 1
                                    FROM hotel h
                                    WHERE h.id_jaula = j.id
                                        AND LOWER(TRIM(h.estado)) = 'activo'
                                        AND CURRENT_DATE() >= DATE(h.fecha_entrada)
                                        AND CURRENT_DATE() < DATE(h.fecha_salida)
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
                                AND LOWER(TRIM(h.estado)) = 'activo'
                                AND CURRENT_DATE() >= DATE(h.fecha_entrada)
                                AND CURRENT_DATE() < DATE(h.fecha_salida)
                            ORDER BY DATE(h.fecha_salida) DESC, h.id_reservacion DESC
                            LIMIT 1
                        ) AS mascota_actual,
                        (
                            SELECT MAX(DATE(h.fecha_salida))
                            FROM hotel h
                            WHERE h.id_reservacion = j.hotel
                                AND LOWER(TRIM(h.estado)) = 'activo'
                                AND CURRENT_DATE() >= DATE(h.fecha_entrada)
                                AND CURRENT_DATE() < DATE(h.fecha_salida)
                        ) AS libre_a_partir,
                        CASE
                            WHEN LOWER(TRIM(j.estado)) = 'mantenimiento' THEN 'mantenimiento'
                            WHEN LOWER(TRIM(j.estado)) = 'ocupado'
                                OR EXISTS (
                                    SELECT 1
                                    FROM hotel h
                                    WHERE h.id_reservacion = j.hotel
                                        AND LOWER(TRIM(h.estado)) = 'activo'
                                        AND CURRENT_DATE() >= DATE(h.fecha_entrada)
                                        AND CURRENT_DATE() < DATE(h.fecha_salida)
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
                       vacunas.fecha_proxima, vacunas.veterinario, vacunas.lote,
                       vacunas.fabricante, vacunas.observaciones
                FROM vacunas
                INNER JOIN lista_vacunas ON vacunas.vacuna_puesta = lista_vacunas.id
                WHERE vacunas.id_mascota = ?
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

        $registrosPorTipo = [];
        foreach ($registros as $registro) {
            $tipo = strtolower(trim((string)$registro['tipo_vacunas']));
            $registrosPorTipo[$tipo][] = $registro;
        }

        $vacunas = [];
        $errores = [];

        foreach ($registros as $registro) {
            $tipo = strtolower(trim((string)$registro['tipo_vacunas']));
            $requerida = isset(self::vacunas_requeridas[$tipo]);
            $aplicacionAnteriorASalida = $registro['fecha_aplicacion'] <= $fechaSalida;
            $tieneVigencia = !empty($registro['fecha_proxima']);
            $vigenteHastaSalida = $aplicacionAnteriorASalida &&
                $tieneVigencia &&
                $registro['fecha_proxima'] >= $fechaSalida;

            if ($registro['fecha_aplicacion'] > $fechaSalida) {
                $estado = 'Aplicada después de la salida';
            } elseif (!$tieneVigencia) {
                $estado = 'Sin fecha próxima';
            } elseif ($registro['fecha_proxima'] < $fechaSalida) {
                $estado = 'Vence antes de la salida';
            } else {
                $estado = 'Vigente hasta la salida';
            }

            $vacunas[] = array_merge($registro, [
                'requerida' => $requerida,
                'valida' => $vigenteHastaSalida,
                'estado_validacion' => $estado,
            ]);

        }

        foreach (self::vacunas_requeridas as $tipo => $nombre) {
            $registrosTipo = $registrosPorTipo[$tipo] ?? [];
            $hayRegistroVigente = false;

            foreach ($registrosTipo as $registro) {
                if (
                    $registro['fecha_aplicacion'] <= $fechaSalida &&
                    !empty($registro['fecha_proxima']) &&
                    $registro['fecha_proxima'] >= $fechaSalida
                ) {
                    $hayRegistroVigente = true;
                    break;
                }
            }

            if (!$registrosTipo) {
                $vacunas[] = [
                    'tipo_vacunas' => $nombre,
                    'fecha_aplicacion' => null,
                    'fecha_proxima' => null,
                    'veterinario' => null,
                    'lote' => null,
                    'fabricante' => null,
                    'observaciones' => null,
                    'requerida' => true,
                    'valida' => false,
                    'estado_validacion' => 'Sin registro',
                ];
            }

            if (!$hayRegistroVigente) {
                $errores[] = $registrosTipo
                    ? "La vacuna {$nombre} no está vigente hasta la fecha de salida."
                    : "Falta registrar la vacuna {$nombre}.";
            }
        }

        return [
            'consulta_ok' => true,
            'valida' => count($errores) === 0,
            'vacunas' => $vacunas,
            'errores' => $errores,
        ];
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