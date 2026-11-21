<?php
// Citas desde la vista de clientes (usa la misma tabla citas que el panel administrativo)
require_once __DIR__ . '/conexion_db.php';

class CitasCliente
{
    public const HORAS = ['09:00', '10:00', '11:00', '12:00', '13:00', '15:00', '16:00', '17:00'];

    private static ?bool $conHora = null;

    // true si la tabla citas guarda la hora en una columna aparte (fecha DATE + hora TIME)
    private static function conHora(PDO $db): bool
    {
        if (self::$conHora === null) {
            self::$conHora = (bool)$db->query("SHOW COLUMNS FROM citas LIKE 'hora'")->fetch();
        }
        return self::$conHora;
    }

    // Expresion SQL con fecha y hora juntas
    private static function momento(PDO $db, string $alias = 'c'): string
    {
        return self::conHora($db) ? "TIMESTAMP($alias.fecha, $alias.hora)" : "$alias.fecha";
    }

    public static function anticipo(float $total): float
    {
        return round($total / 3, 2);
    }

    public static function servicio(int $id): ?array
    {
        $db = ConexionDB::obtenerConexion();
        $s = $db->prepare("SELECT * FROM servicios WHERE id_servicio = ? AND estado + 0 = 1");
        $s->execute([$id]);
        return $s->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Empleado que atiende: el asignado al servicio o el primer empleado activo
    private static function empleadoDe(PDO $db, array $servicio): int
    {
        if (!empty($servicio['id_empleados'])) {
            return (int)$servicio['id_empleados'];
        }
        $r = $db->query("SELECT id_empleado FROM empleados WHERE esta_activo + 0 = 1 ORDER BY id_empleado LIMIT 1");
        return (int)$r->fetchColumn();
    }

    public static function horasOcupadas(int $idServicio, string $fecha, int $excluirCita = 0): array
    {
        $db = ConexionDB::obtenerConexion();
        $srv = self::servicio($idServicio);
        if (!$srv) {
            return [];
        }
        $emp = self::empleadoDe($db, $srv);
        $s = $db->prepare(
            "SELECT DATE_FORMAT(" . self::momento($db, 'c') . ", '%H:%i') FROM citas c
             WHERE c.empleado = ? AND DATE(c.fecha) = ? AND c.id_cita <> ?
               AND LOWER(c.estado_cita) NOT IN ('cancelada', 'cancelado')"
        );
        $s->execute([$emp, $fecha, $excluirCita]);
        return $s->fetchAll(PDO::FETCH_COLUMN);
    }

    // Valida fecha/hora: futura, dentro de horario y libre. Devuelve mensaje de error o ''
    public static function validarHorario(int $idServicio, string $fecha, string $hora, int $excluirCita = 0): string
    {
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$d || $d->format('Y-m-d') !== $fecha) {
            return 'La fecha no es válida.';
        }
        if (!in_array($hora, self::HORAS, true)) {
            return 'Elige una hora disponible.';
        }
        $zona = new DateTimeZone('America/Mexico_City');
        $cuando = new DateTime("$fecha $hora:00", $zona);
        if ($cuando <= new DateTime('now', $zona)) {
            return 'La cita debe ser en una fecha y hora futuras.';
        }
        if ($cuando > (new DateTime('now', $zona))->modify('+6 months')) {
            return 'Solo se agenda con máximo 6 meses de anticipación.';
        }
        if (in_array($hora, self::horasOcupadas($idServicio, $fecha, $excluirCita), true)) {
            return 'Esa hora ya fue tomada, elige otra.';
        }
        return '';
    }

    public static function agendar(int $idCliente, int $idMascota, int $idServicio, string $fecha, string $hora, string $motivo): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $srv = self::servicio($idServicio);
            if (!$srv) {
                return ['ok' => false, 'error' => 'El servicio no existe.'];
            }
            $m = $db->prepare("SELECT COUNT(*) FROM mascotas WHERE id_mascota = ? AND id_cliente = ?");
            $m->execute([$idMascota, $idCliente]);
            if (!$m->fetchColumn()) {
                return ['ok' => false, 'error' => 'La mascota no es tuya.'];
            }
            $err = self::validarHorario($idServicio, $fecha, $hora);
            if ($err !== '') {
                return ['ok' => false, 'error' => $err];
            }
            $motivo = mb_substr(trim($motivo), 0, 250);
            $datos = [
                $idCliente, $idMascota, $idServicio, self::empleadoDe($db, $srv),
                (int)$srv['duracion'], $motivo
            ];
            if (self::conHora($db)) {
                $s = $db->prepare(
                    "INSERT INTO citas (cliente, mascota, servicio, empleado, estado_cita, duracion_estimada,
                                        observacion, estado, fecha, hora, motivo_consulta, atendido)
                     VALUES (?, ?, ?, ?, 'pendiente', ?, ?, 'sin atender', ?, ?, ?, 0)"
                );
                array_push($datos, $fecha, "$hora:00", $motivo);
            } else {
                $s = $db->prepare(
                    "INSERT INTO citas (cliente, mascota, servicio, empleado, estado_cita, duracion_estimada,
                                        observacion, estado, fecha, motivo_consulta, atendido)
                     VALUES (?, ?, ?, ?, 'pendiente', ?, ?, 'sin atender', ?, ?, 0)"
                );
                array_push($datos, "$fecha $hora:00", $motivo);
            }
            $s->execute($datos);
            return ['ok' => true, 'id' => (int)$db->lastInsertId()];
        } catch (Exception $e) {
            error_log('CitasCliente::agendar ' . $e->getMessage());
            return ['ok' => false, 'error' => 'No se pudo guardar la cita.'];
        }
    }

    private static function sel(PDO $db): string
    {
        return "SELECT c.id_cita, " . self::momento($db) . " AS fecha, c.duracion_estimada, c.estado_cita, c.observacion,
                   s.id_servicio, s.nombre AS servicio, s.precio, m.nombre AS mascota,
                   CONCAT(e.nombre, ' ', e.apellido) AS empleado
            FROM citas c
            LEFT JOIN servicios s ON s.id_servicio = c.servicio
            LEFT JOIN mascotas m ON m.id_mascota = c.mascota
            LEFT JOIN empleados e ON e.id_empleado = c.empleado";
    }

    public static function listar(int $idCliente, string $ver): array
    {
        $db = ConexionDB::obtenerConexion();
        $proximas = $ver !== 'anteriores';
        $sql = self::sel($db) . " WHERE c.cliente = ? AND " .
            ($proximas
                ? self::momento($db) . " >= NOW() AND LOWER(c.estado_cita) NOT IN ('cancelada','atendida') ORDER BY fecha ASC"
                : "(" . self::momento($db) . " < NOW() OR LOWER(c.estado_cita) IN ('cancelada','atendida')) ORDER BY fecha DESC");
        $s = $db->prepare($sql);
        $s->execute([$idCliente]);
        return $s->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtener(int $idCita, int $idCliente): ?array
    {
        $db = ConexionDB::obtenerConexion();
        $s = $db->prepare(self::sel($db) . " WHERE c.id_cita = ? AND c.cliente = ?");
        $s->execute([$idCita, $idCliente]);
        return $s->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Solo se cancela o reprograma una cita pendiente con al menos 2 horas de margen
    public static function modificable(array $cita): bool
    {
        if (strtolower((string)$cita['estado_cita']) !== 'pendiente') {
            return false;
        }
        $zona = new DateTimeZone('America/Mexico_City');
        return new DateTime($cita['fecha'], $zona) > (new DateTime('now', $zona))->modify('+2 hours');
    }

    public static function cancelar(int $idCita, int $idCliente): bool
    {
        $c = self::obtener($idCita, $idCliente);
        if (!$c || !self::modificable($c)) {
            return false;
        }
        $db = ConexionDB::obtenerConexion();
        $s = $db->prepare("UPDATE citas SET estado_cita = 'cancelada' WHERE id_cita = ? AND cliente = ?");
        return $s->execute([$idCita, $idCliente]);
    }

    public static function reprogramar(int $idCita, int $idCliente, string $fecha, string $hora): string
    {
        $c = self::obtener($idCita, $idCliente);
        if (!$c || !self::modificable($c)) {
            return 'Esta cita ya no se puede reprogramar.';
        }
        $err = self::validarHorario((int)$c['id_servicio'], $fecha, $hora, $idCita);
        if ($err !== '') {
            return $err;
        }
        $db = ConexionDB::obtenerConexion();
        if (self::conHora($db)) {
            $s = $db->prepare("UPDATE citas SET fecha = ?, hora = ? WHERE id_cita = ? AND cliente = ?");
            $s->execute([$fecha, "$hora:00", $idCita, $idCliente]);
        } else {
            $s = $db->prepare("UPDATE citas SET fecha = ? WHERE id_cita = ? AND cliente = ?");
            $s->execute(["$fecha $hora:00", $idCita, $idCliente]);
        }
        return '';
    }
}
