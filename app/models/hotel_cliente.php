<?php
declare(strict_types=1);

if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

class HotelCliente
{
    // Precio por noche segun el tamano de la jaula
    const PRECIO_CHICO = 150.00;
    const PRECIO_MEDIANO = 200.00;
    const PRECIO_GRANDE = 250.00;

    const MAX_NOCHES = 30;

    // Hora con la que se guardan entrada y salida (el formulario solo manda la fecha).
    // Con fin de dia, una reservacion para hoy no se marca Vencida en cuanto se crea.
    const HORA_HOTEL = '23:59:59';

    const ESTADO_PENDIENTE_PAGO = 'Pendiente de pago';
    const ESTADO_PENDIENTE_CARTILLA = 'Pendiente cartilla';
    const ESTADO_CONFIRMADA = 'Confirmada';
    const ESTADO_VENCIDA = 'Vencida';
    const ESTADO_ABANDONADA = 'Abandonada';
    const ESTADO_CANCELADA = 'Cancelada';
    // Estado que usan las reservaciones creadas por el empleado (modelo Hotel.php)
    const ESTADO_ACTIVO_EMPLEADO = 'activo';

    // Mascotas activas del cliente para el select del formulario
    public static function mascotasDelCliente(int $idCliente): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare(
                "SELECT id_mascota, nombre, especie
                 FROM mascotas
                 WHERE id_cliente = ? AND esta_activo = 1
                 ORDER BY nombre"
            );
            $stmt->execute([$idCliente]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log('Error en HotelCliente::mascotasDelCliente: ' . $e->getMessage());
            return [];
        }
    }

    // Crea la reservacion. $datos ya viene validado en formato por el controlador.
    public static function reservar(int $idCliente, array $datos): array
    {
        $zona = new DateTimeZone('America/Mexico_City');

        // Fechas: entrada no anterior a hoy, salida posterior, maximo 30 noches
        $entrada = DateTime::createFromFormat('!Y-m-d', $datos['fecha_entrada'], $zona);
        $salida = DateTime::createFromFormat('!Y-m-d', $datos['fecha_salida'], $zona);
        if (!$entrada || $entrada->format('Y-m-d') !== $datos['fecha_entrada']
            || !$salida || $salida->format('Y-m-d') !== $datos['fecha_salida']) {
            return ['exito' => false, 'mensaje' => 'Las fechas no son validas.'];
        }
        $hoy = new DateTime('today', $zona);
        if ($entrada < $hoy) {
            return ['exito' => false, 'mensaje' => 'La fecha de entrada no puede ser anterior a hoy.'];
        }
        if ($salida <= $entrada) {
            return ['exito' => false, 'mensaje' => 'La fecha de salida debe ser posterior a la de entrada.'];
        }
        $noches = (int)$entrada->diff($salida)->days;
        if ($noches > self::MAX_NOCHES) {
            return ['exito' => false, 'mensaje' => 'La estancia maxima es de ' . self::MAX_NOCHES . ' noches.'];
        }
        $fechaEntrada = $entrada->format('Y-m-d') . ' ' . self::HORA_HOTEL;
        $fechaSalida = $salida->format('Y-m-d') . ' ' . self::HORA_HOTEL;

        try {
            $db = ConexionDB::obtenerConexion();

            // La mascota debe ser del cliente y estar activa
            $stmt = $db->prepare(
                "SELECT id_mascota, especie, tamanio, vacunas
                 FROM mascotas
                 WHERE id_mascota = ? AND id_cliente = ? AND esta_activo = 1"
            );
            $stmt->execute([$datos['id_mascota'], $idCliente]);
            $mascota = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$mascota) {
                return ['exito' => false, 'mensaje' => 'La mascota no existe o no pertenece a tu cuenta.'];
            }

            if (!self::especiePermitida((string)($mascota['especie'] ?? ''))) {
                return ['exito' => false, 'mensaje' => 'El hotel solo recibe perros, gatos y mamíferos pequeños (conejos, roedores)'];
            }

            // Con vacunas registradas paga en recepcion; sin vacunas debe llevar cartilla
            $vacunas = trim((string)($mascota['vacunas'] ?? ''));
            $estado = ($vacunas !== '') ? self::ESTADO_PENDIENTE_PAGO : self::ESTADO_PENDIENTE_CARTILLA;

            $tamano = self::tamanoJaula($mascota['tamanio'] ?? null);
            $precioNoche = self::precioPorTamano($tamano);
            $anticipo = round($noches * $precioNoche, 2);
            $saldo = 0.00;

            $db->beginTransaction();

            // La misma mascota no puede tener dos reservaciones que se crucen
            $stmt = $db->prepare(
                "SELECT 1 FROM hotel h
                 WHERE h.mascota = ?
                   AND h.estado IN (?, ?, ?, ?)
                   AND ? < h.fecha_salida
                   AND ? > h.fecha_entrada
                 LIMIT 1"
            );
            $stmt->execute([
                (int)$mascota['id_mascota'],
                self::ESTADO_PENDIENTE_PAGO, self::ESTADO_PENDIENTE_CARTILLA, self::ESTADO_CONFIRMADA, self::ESTADO_ACTIVO_EMPLEADO,
                $fechaEntrada, $fechaSalida
            ]);
            if ($stmt->fetchColumn() !== false) {
                $db->rollBack();
                return ['exito' => false, 'mensaje' => 'Esa mascota ya tiene una reservación en esas fechas.'];
            }

            // Bloquea las jaulas de ese tamano para que dos reservaciones no tomen la misma
            $stmt = $db->prepare(
                "SELECT id FROM jaula
                 WHERE tamano = ? AND estado = 'disponible'
                 ORDER BY id
                 FOR UPDATE"
            );
            $stmt->execute([$tamano]);
            $stmt->fetchAll();

            // Primera jaula sin reservacion activa que se empalme con las fechas
            $stmt = $db->prepare(
                "SELECT j.id FROM jaula j
                 WHERE j.tamano = ? AND j.estado = 'disponible'
                   AND NOT EXISTS (
                       SELECT 1 FROM hotel h
                       WHERE h.id_jaula = j.id
                         AND h.estado IN (?, ?, ?, ?)
                         AND ? < h.fecha_salida
                         AND ? > h.fecha_entrada
                   )
                 ORDER BY j.id
                 LIMIT 1
                 FOR UPDATE"
            );
            $stmt->execute([
                $tamano,
                self::ESTADO_PENDIENTE_PAGO, self::ESTADO_PENDIENTE_CARTILLA, self::ESTADO_CONFIRMADA, self::ESTADO_ACTIVO_EMPLEADO,
                $fechaEntrada, $fechaSalida
            ]);
            $idJaula = $stmt->fetchColumn();

            if ($idJaula === false) {
                $db->rollBack();
                return ['exito' => false, 'mensaje' => 'No hay jaulas disponibles para esas fechas'];
            }

            $stmt = $db->prepare(
                "INSERT INTO hotel (
                    cliente, mascota, fecha_entrada, fecha_salida, num_noches,
                    servicios_adicionales, precio_noche, anticipo, saldo, estado, id_jaula,
                    alimentacion_habitual, cantidad_alimento, horario_alimentacion,
                    medicamentos, horario_medicamentos, persona_recoge, telefono_emergencia,
                    necesidades_especiales, observaciones
                 ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $idCliente,
                (int)$mascota['id_mascota'],
                $fechaEntrada,
                $fechaSalida,
                $noches,
                $datos['servicios_adicionales'],
                $precioNoche,
                $anticipo,
                $saldo,
                $estado,
                (int)$idJaula,
                $datos['alimentacion_habitual'],
                $datos['cantidad_alimento'],
                $datos['horario_alimentacion'],
                $datos['medicamentos'],
                $datos['horario_medicamentos'],
                $datos['persona_recoge'],
                $datos['telefono_emergencia'],
                $datos['necesidades_especiales'],
                $datos['observaciones'],
            ]);
            $idReservacion = (int)$db->lastInsertId();

            $db->commit();

            return [
                'exito' => true,
                'id' => $idReservacion,
                'estado' => $estado,
                'noches' => $noches,
                'total' => $anticipo,
                'mensaje' => 'Reservación registrada.'
            ];
        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            error_log('Error en HotelCliente::reservar: ' . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'No se pudo registrar la reservación. Intenta de nuevo.'];
        }
    }

    // Marca Vencida / Abandonada solo las reservaciones de ese cliente (sin cron)
    public static function actualizarVencidas(int $idCliente): void
    {
        try {
            $db = ConexionDB::obtenerConexion();

            $stmt = $db->prepare(
                "UPDATE hotel SET estado = ?
                 WHERE cliente = ? AND estado IN (?, ?) AND fecha_entrada < NOW()"
            );
            $stmt->execute([self::ESTADO_VENCIDA, $idCliente, self::ESTADO_PENDIENTE_PAGO, self::ESTADO_PENDIENTE_CARTILLA]);

            $stmt = $db->prepare(
                "UPDATE hotel SET estado = ?
                 WHERE cliente = ? AND estado = ? AND fecha_entrada < NOW() - INTERVAL 3 DAY"
            );
            $stmt->execute([self::ESTADO_ABANDONADA, $idCliente, self::ESTADO_VENCIDA]);
        } catch (Exception $e) {
            error_log('Error en HotelCliente::actualizarVencidas: ' . $e->getMessage());
        }
    }

    // Reservaciones del cliente con el nombre de la mascota, la mas reciente primero
    public static function reservacionesDelCliente(int $idCliente): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare(
                "SELECT h.id_reservacion, h.fecha_entrada, h.fecha_salida, h.num_noches,
                        h.anticipo, h.estado, m.nombre AS mascota
                 FROM hotel h
                 INNER JOIN mascotas m ON m.id_mascota = h.mascota
                 WHERE h.cliente = ?
                 ORDER BY h.fecha_entrada DESC"
            );
            $stmt->execute([$idCliente]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log('Error en HotelCliente::reservacionesDelCliente: ' . $e->getMessage());
            return [];
        }
    }

    // Quita acentos y pasa a minusculas para comparar
    private static function normalizar(string $texto): string
    {
        $texto = mb_strtolower(trim($texto), 'UTF-8');
        return strtr($texto, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
    }

    // Solo perros, gatos y mamiferos pequenos
    private static function especiePermitida(string $especie): bool
    {
        $e = self::normalizar($especie);
        if ($e === '') {
            return false;
        }

        // Especies que el hotel no recibe
        $rechazadas = '/(pez|peces|reptil|tortuga|iguana|serpiente|culebra|lagart|gecko|camaleon|dragon|'
            . 'pajaro|loro|perico|canario|cotorra|anfibio|rana|sapo|ajolote|axolote|insecto|arana|tarantula|escorpion|alacran)'
            . '|\baves?\b/u';
        if (preg_match($rechazadas, $e)) {
            return false;
        }

        // "otro" es la opcion del formulario del cliente; en recepcion se confirma la especie
        $permitidas = ['perro', 'gato', 'otro', 'conejo', 'hamster', 'huron', 'erizo', 'cuyo', 'cobaya',
            'conejillo', 'raton', 'rata', 'roedor', 'chinchilla', 'jerbo', 'gerbo', 'ardilla', 'degu'];
        foreach ($permitidas as $p) {
            if (preg_match('/\b' . $p . '/u', $e)) {
                return true;
            }
        }
        return false;
    }

    // mascotas.tamanio (Pequeño/Mediano/Grande/Gigante) -> jaula.tamano
    private static function tamanoJaula(?string $tamanio): string
    {
        $t = self::normalizar((string)$tamanio);
        if ($t === 'pequeno' || $t === 'chico') {
            return 'chico';
        }
        if ($t === 'grande' || $t === 'gigante') {
            return 'grande';
        }
        return 'mediano';
    }

    private static function precioPorTamano(string $tamano): float
    {
        if ($tamano === 'chico') {
            return self::PRECIO_CHICO;
        }
        if ($tamano === 'grande') {
            return self::PRECIO_GRANDE;
        }
        return self::PRECIO_MEDIANO;
    }
}
