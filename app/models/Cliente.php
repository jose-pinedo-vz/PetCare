<?php
declare(strict_types=1);

if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

class Cliente
{

    public static function estaRegistrado(string $correo, string $telefono, ?int $id_cliente = null): bool {
        try {
            $conexion = ConexionDB::obtenerConexion();

            if ($id_cliente === null) {
                $consulta = $conexion -> prepare(
                    "
                        SELECT COUNT(*)
                        FROM clientes
                        WHERE (correo = ? OR telefono = ?) AND esta_activo = 1
                    "
                );
                $consulta -> execute([$correo, $telefono]);
            }

            else {
                $consulta = $conexion -> prepare(
                    "
                        SELECT COUNT(*)
                        FROM clientes
                        WHERE (correo = ? OR telefono = ?) AND id_cliente != ? AND esta_activo = 1
                    "
                );
                $consulta -> execute([$correo, $telefono, $id_cliente]);
            }

            $total = $consulta -> fetchColumn();

            if ($total > 0) {
                return true;
            } else {
                return false; 
            }

        } catch (Exception $error) {
            error_log("Error en la funcion de que si esta registrado " . $error -> getMessage());
            return false;
        }
    }

    public static function generarIdCliente(PDO $db): int
    {
         try {
        // Consultamos el ID máximo actual y le sumamos 1
        $stmt = $db->query("SELECT COALESCE(MAX(id_cliente), 0) + 1 FROM clientes");
        return (int) $stmt->fetchColumn();
            } 
    catch (PDOException $e) {
        return 1;
    }
    }

    public static function obtenerTodos(): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $query = "SELECT * FROM clientes where esta_activo=1 ORDER BY id_cliente DESC";
            $stmt = $db->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            return [];
        }
    }

    public static function obtenerPorId(int $id): ?array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
            $stmt->execute([$id]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            return $cliente ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function insertar(array $datos): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $id = self::generarIdCliente($db);

            $sql = "INSERT INTO clientes (
                id_cliente, nombre, apellido, fecha_nacimiento, sexo,
                telefono, telefono_alternativo, correo, calle, numero_exterior,
                numero_interior, colonia, ciudad, estado, codigo_postal,
                rfc, contacto_emergencia, telefono_emergencia, observaciones
            ) VALUES (
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?
            )";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $id,
                $datos['nombre'],
                $datos['apellido'],
                !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null,
                !empty($datos['sexo']) ? $datos['sexo'] : null,
                $datos['telefono'],
                !empty($datos['telefono_alternativo']) ? $datos['telefono_alternativo'] : null,
                $datos['correo'],
                $datos['calle'],
                $datos['numero_exterior'],
                !empty($datos['numero_interior']) ? $datos['numero_interior'] : null,
                $datos['colonia'],
                $datos['ciudad'],
                $datos['estado'],
                $datos['codigo_postal'],
                !empty($datos['rfc']) ? $datos['rfc'] : null,
                $datos['contacto_emergencia'],
                $datos['telefono_emergencia'],
                !empty($datos['observaciones']) ? $datos['observaciones'] : null
            ]);

            return ['exito' => true, 'id' => $id, 'mensaje' => 'Cliente registrado con éxito'];
        } catch (Exception $e) {
            return ['exito' => false, 'id' => null, 'mensaje' => $e->getMessage()];
        }
    }

    // cliente nuevo desde la pagina publica "Crear mi cuenta".
    // La contrasena debe llegar encriptada (password_hash)
    public static function registrarCuenta(array $datos): array
    {
        try {
            $db = ConexionDB::obtenerConexion();

            $existe = $db->prepare("SELECT COUNT(*) FROM clientes WHERE usuario = ?");
            $existe->execute([$datos['usuario']]);
            if ((int) $existe->fetchColumn() > 0) {
                return ['exito' => false, 'id' => null, 'mensaje' => 'Ese nombre de usuario ya está en uso.'];
            }

            $id = self::generarIdCliente($db);

            $sql = "INSERT INTO clientes (
                id_cliente, nombre, apellido, telefono, correo, calle, estado,
                contacto_emergencia, telefono_emergencia, usuario, contrasena
            ) VALUES (?, ?, ?, ?, ?, ?, '', ?, ?, ?, ?)";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $id,
                $datos['nombre'],
                $datos['apellido'],
                $datos['telefono'],
                $datos['correo'],
                !empty($datos['direccion']) ? $datos['direccion'] : null,
                $datos['contacto_emergencia'],
                $datos['telefono_emergencia'],
                $datos['usuario'],
                $datos['contrasena']
            ]);

            return ['exito' => true, 'id' => $id, 'mensaje' => 'Cuenta creada con éxito'];
        } catch (Exception $e) {
            error_log('Error en Cliente::registrarCuenta: ' . $e->getMessage());
            return ['exito' => false, 'id' => null, 'mensaje' => 'No se pudo crear la cuenta. Intenta de nuevo.'];
        }
    }

    // buscar un cliente activo por su nombre de usuario
    public static function obtenerPorUsuario(string $usuario): ?array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare("SELECT id_cliente, nombre, contrasena FROM clientes WHERE usuario = ? AND esta_activo = 1");
            $stmt->execute([$usuario]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            return $cliente ?: null;
        } catch (Exception $e) {
            error_log('Error en Cliente::obtenerPorUsuario: ' . $e->getMessage());
            return null;
        }
    }

    public static function actualizar(int $id, array $datos): array
    {
        try {
            $db = ConexionDB::obtenerConexion();

            $sql = "UPDATE clientes SET 
                nombre = ?, apellido = ?, fecha_nacimiento = ?, sexo = ?,
                telefono = ?, telefono_alternativo = ?, correo = ?, calle = ?,
                numero_exterior = ?, numero_interior = ?, colonia = ?, ciudad = ?,
                estado = ?, codigo_postal = ?, rfc = ?, contacto_emergencia = ?,
                telefono_emergencia = ?, observaciones = ?
                WHERE id_cliente = ?";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $datos['nombre'],
                $datos['apellido'],
                !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null,
                !empty($datos['sexo']) ? $datos['sexo'] : null,
                $datos['telefono'],
                !empty($datos['telefono_alternativo']) ? $datos['telefono_alternativo'] : null,
                $datos['correo'],
                $datos['calle'],
                $datos['numero_exterior'],
                !empty($datos['numero_interior']) ? $datos['numero_interior'] : null,
                $datos['colonia'],
                $datos['ciudad'],
                $datos['estado'],
                $datos['codigo_postal'],
                !empty($datos['rfc']) ? $datos['rfc'] : null,
                $datos['contacto_emergencia'],
                $datos['telefono_emergencia'],
                !empty($datos['observaciones']) ? $datos['observaciones'] : null,
                $id
            ]);

            return ['exito' => true, 'mensaje' => 'Cliente actualizado con éxito'];
        } catch (Exception $e) {
            return ['exito' => false, 'mensaje' => $e->getMessage()];
        }
    }

    public static function eliminar(int $id): bool
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $db->beginTransaction();
            $stmt = $db->prepare("UPDATE clientes 
                                  SET esta_activo = 0
                                  WHERE id_cliente = ?");
            $stmt->execute([$id]);

            $stmtMascotas = $db->prepare("UPDATE mascotas 
                                          SET esta_activo = 0 
                                          WHERE id_cliente = ?");
            $stmtMascotas->execute([$id]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            return false;
        }
    }

    public static function obtenerCodigoPostal($codigo):array
    {
        try
        {
            $db = ConexionDB::obtenerConexion();
            $query = ("SELECT colonia, estado, ciudad, municipio FROM codigos_postales WHERE codigo_postal = ?");
            $stml = $db->prepare($query);
            $stml->execute([$codigo]);
            return $stml->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (Exception $e)
        {
            return [];
        }
    }

    // ===== Recuperar contrasena del cliente =====
    // En BD solo se guarda el sha256 del token (token_recuperacion es varchar(255)).
    // Las fechas se calculan en PHP con America/Mexico_City y se pasan como parametro.

    private static function ahoraMexico(string $modificar = ''): string
    {
        $fecha = new DateTime('now', new DateTimeZone('America/Mexico_City'));
        if ($modificar !== '') {
            $fecha->modify($modificar);
        }
        return $fecha->format('Y-m-d H:i:s');
    }

    // Un token valido son 64 caracteres hexadecimales (bin2hex de 32 bytes)
    private static function hashTokenRecuperacion(string $token): ?string
    {
        if (!preg_match('/^[0-9a-f]{64}$/', $token)) {
            return null;
        }
        return hash('sha256', $token);
    }

    // Cliente activo por correo
    public static function buscarPorCorreoActivo(string $correo): ?array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare(
                "SELECT id_cliente, nombre, correo FROM clientes
                 WHERE correo = ? AND esta_activo = 1
                 ORDER BY id_cliente
                 LIMIT 1"
            );
            $stmt->execute([$correo]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            return $cliente ?: null;
        } catch (Exception $e) {
            error_log('Error en Cliente::buscarPorCorreoActivo: ' . $e->getMessage());
            return null;
        }
    }

    // Guarda el token (hasheado) con vigencia de 1 hora.
    // Anti-abuso: si ya hay uno generado en el ultimo minuto (expira despues de ahora + 59 min)
    // no lo reemplaza y devuelve false. La condicion va en el mismo UPDATE para que sea atomica.
    public static function guardarTokenRecuperacion(int $idCliente, string $token): bool
    {
        $hash = self::hashTokenRecuperacion($token);
        if ($hash === null) {
            return false;
        }
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare(
                "UPDATE clientes
                 SET token_recuperacion = ?, token_expiracion = ?
                 WHERE id_cliente = ? AND esta_activo = 1
                   AND (token_expiracion IS NULL OR token_expiracion <= ?)"
            );
            $stmt->execute([$hash, self::ahoraMexico('+1 hour'), $idCliente, self::ahoraMexico('+59 minutes')]);
            return $stmt->rowCount() === 1;
        } catch (Exception $e) {
            error_log('Error en Cliente::guardarTokenRecuperacion: ' . $e->getMessage());
            return false;
        }
    }

    // Cliente activo con ese token vigente, o null
    public static function obtenerPorTokenRecuperacion(string $token): ?array
    {
        $hash = self::hashTokenRecuperacion($token);
        if ($hash === null) {
            return null;
        }
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare(
                "SELECT id_cliente, nombre FROM clientes
                 WHERE token_recuperacion = ? AND token_expiracion > ? AND esta_activo = 1
                 LIMIT 1"
            );
            $stmt->execute([$hash, self::ahoraMexico()]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            return $cliente ?: null;
        } catch (Exception $e) {
            error_log('Error en Cliente::obtenerPorTokenRecuperacion: ' . $e->getMessage());
            return null;
        }
    }

    // Cambia la contrasena (ya con password_hash) y borra el token en el mismo UPDATE.
    // Solo afecta si el token sigue vigente, asi un token sirve una sola vez.
    public static function actualizarContrasenaPorToken(string $token, string $contrasenaHash): bool
    {
        $hash = self::hashTokenRecuperacion($token);
        if ($hash === null) {
            return false;
        }
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare(
                "UPDATE clientes
                 SET contrasena = ?, token_recuperacion = NULL, token_expiracion = NULL
                 WHERE token_recuperacion = ? AND token_expiracion > ? AND esta_activo = 1"
            );
            $stmt->execute([$contrasenaHash, $hash, self::ahoraMexico()]);
            return $stmt->rowCount() === 1;
        } catch (Exception $e) {
            error_log('Error en Cliente::actualizarContrasenaPorToken: ' . $e->getMessage());
            return false;
        }
    }
}
?>
