<?php
declare(strict_types=1);

if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

class ConexionesClientes 
{
    public function consultarIdCliente(int $clave): bool
    {
        try
        {
            $db = ConexionDB::obtenerConexion();
            $query = "SELECT id_cliente FROM clientes WHERE id_cliente = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$clave]);

            $datos = $stmt->fetch(PDO::FETCH_ASSOC);

            return ($datos !== false && $datos !== null);
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    public function insertarCita(int $clave_cliente, string $fecha, string $motivo): bool
    {
        try
        {
            $db = ConexionDB::obtenerConexion();
            $idConsulta = random_int(100, 9999);

            $query = "INSERT INTO consulta_veterinaria (id_consulta, id_cliente, fecha, motivo_de_consulta, estado_de_pago) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->execute([$idConsulta, $clave_cliente, $fecha, $motivo, "Pendiente"]);
            return true;
        }
        catch (Exception $e)
        {
            return false; 
        }
    }

    public function obtenerTodasLasCitas(): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $query = "SELECT cv.*, c.nombre as nombre_cliente, c.apellido as apellido_cliente 
                      FROM consulta_veterinaria cv 
                      LEFT JOIN clientes c ON cv.id_cliente = c.id_cliente 
                      ORDER BY cv.fecha DESC";
            $stmt = $db->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            return [];
        }
    }
}
?>