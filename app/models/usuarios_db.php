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
        $base=$conexion->prepare("SELECT id_usuario, usuario, empleado_asociado, rol, permisos, fecha_creacion, ultimo_acceso, estado
                                    FROM usuarios
                                    WHERE estado = 'activo'
                                    ORDER BY id_usuario DESC");
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

    public static function insertar(int $id, string $nuevo_usuario, string $nueva_password, ?string $empleado_asociado, string $rol, string $permisos, string $estado)
    {
        $conexion=ConexionDB::obtenerConexion();
        try
        {
            $stmt=$conexion->prepare("UPDATE usuarios SET usuario=?, contrasena=?, empleado_asociado=?, rol=?, permisos=?, estado=? WHERE id_usuario=?");
            $stmt->execute([$nuevo_usuario, $nueva_password, $empleado_asociado, $rol, $permisos, $estado, $id]);

            $_SESSION['mensaje']="Usuario actualizado con éxito.";
        }
        catch (PDOException $e)
        {
            $_SESSION['error']="Error al actualizar el usuario: " . $e->getMessage();
        }
    }
}

?>