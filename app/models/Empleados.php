<?php
    declare(strict_types=1);
    require_once 'conexion_db.php';

    function Cambiar_estado_empleado(int $id_empleado): bool
    {
        $conexion = ConexionDB::obtenerConexion();

        $sql = "UPDATE empleados SET esta_activo = 0 WHERE id_empleado = ?";
        $stmt = $conexion->prepare($sql);

        return $stmt->execute([$id_empleado]);
    }
        function Listar_empleados_activos(): array
    {
        $conexion = ConexionDB::obtenerConexion();

        $sql = "SELECT * FROM empleados WHERE esta_activo = 1 ORDER BY id_empleado";
        $stmt = $conexion->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>