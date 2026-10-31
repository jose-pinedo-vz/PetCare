<?php
declare(strict_types=1);
if (file_exists(__DIR__ . '/conexion_db.php'))
{
    require_once __DIR__ . '/conexion_db.php';
}
elseif (file_exists(__DIR__ . '/../../conexion_db.php'))
{
    require_once __DIR__ . '/../../conexion_db.php';
}
class UsuariosDB
{
    
    public static function obtenerTodosUsuarios(PDO $conexion): array
    {
        $base=$conexion->prepare("SELECT u.id_usuario, u.usuario, e.nombre AS empleado_asociado, u.rol, u.permisos, u.fecha_creacion, u.ultimo_acceso, u.estado
                                    FROM usuarios u
                                    LEFT JOIN empleados e ON u.empleado_asociado = e.id_empleado
                                    WHERE u.estado='activo'
                                    ORDER BY u.id_usuario DESC");
        $base->execute();
        return $base->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function eliminar (int $id) : bool
    {
        try
        {
            $conexion = ConexionDB::obtenerConexion();
            $consulta = $conexion -> prepare(
            "
            UPDATE usuarios
            SET estado = 'inactivo'
            WHERE id_usuario = ?
            AND estado = 'activo'
            "
            );
            return $consulta -> execute([$id]);
        }
        catch (Exception $error) {
            return false;
        }
    }

    public static function insertar(int $id, string $nuevo_usuario, string $nueva_password, ?int $empleado, string $rol, string $permisos, string $estado)
    {
        $conexion=ConexionDB::obtenerConexion();
        try
        {
            $empleado=trim($_POST['empleado_asociado']??'');
            $empleado_asociado=($empleado !== '' && $empleado !== '0')?(int)$empleado : null;
            $stmt=$conexion->prepare("UPDATE usuarios SET usuario=?, contrasena=?, empleado_asociado=?, rol=?, permisos=?, estado=? WHERE id_usuario=?");
            $stmt->execute([$nuevo_usuario, $nueva_password, $empleado_asociado, $rol, $permisos, $estado, $id]);

            $_SESSION['mensaje']="Usuario actualizado con éxito.";
        }
        catch (PDOException $e)
        {
            $_SESSION['error']="Error al actualizar el usuario: " . $e->getMessage();
        }
    }

    public static function empleados_disponibles()
    {
        $conexion=ConexionDB::obtenerConexion();
        try {
            $query="SELECT id_empleado,nombre FROM empleados WHERE nombre NOT IN (SELECT empleado_asociado FROM usuarios WHERE empleado_asociado=nombre) and esta_activo=1";
            $stmt = $conexion->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function insertar_clientes(string $nuevo_usuario, string $nueva_password, ?int $empleado, string $rol, string $permisos, string $estado)
    {
        $conexion=ConexionDB::obtenerConexion();
        try
        {
            $empleado=trim($_POST['empleado_asociado']??'');
            $empleado_asociado=($empleado !== '' && $empleado !== '0')?(int)$empleado : null;
            $base=$conexion->prepare("INSERT INTO usuarios (id_usuario, usuario, contrasena, empleado_asociado, rol, permisos, estado) 
                                    VALUES ((SELECT COALESCE(MAX(id_usuario), 0) + 1 FROM usuarios AS u), ?, ?, ?, ?, ?, ?)");
            $base->execute([$nuevo_usuario, $nueva_password, $empleado_asociado, $rol, $permisos, $estado]);

            $_SESSION['mensaje']="Usuario actualizado con éxito.";
        }
        catch (PDOException $e)
        {
            $_SESSION['error']="Error al actualizar el usuario: " . $e->getMessage();
        }
    }
}

?>