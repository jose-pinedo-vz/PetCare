<?php
declare(strict_types=1);

if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

class Cliente
{
    public static function generarIdCliente(PDO $db): int
    {
        do {
            $aleatorio = random_int(100, 9999);
            $stmt = $db->prepare("SELECT 1 FROM clientes WHERE id_cliente = ?");
            $stmt->execute([$aleatorio]);
            $existe = (bool)$stmt->fetch();
        } while ($existe);

        return $aleatorio;
    }

    public static function obtenerTodos(): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $query = "SELECT * FROM clientes ORDER BY id_cliente DESC";
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
            $stmt = $db->prepare("DELETE FROM clientes WHERE id_cliente = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }
}
?>