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

    public static function buscarCorreo(string $correo)
    {
        $conexion=ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare("SELECT id_cliente AS id, nombre, 'cliente' AS tipo FROM clientes WHERE correo = ?
                                UNION
                                SELECT id_empleado AS id, nombre, 'empleado' AS tipo FROM empleados WHERE correo = ?");
        $stmt->execute([$correo,$correo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function tipoUsuario(string $tipo, string $token, string $expiracion, string $correo)
    {
        $conexion=ConexionDB::obtenerConexion();
        if ($tipo === 'cliente')
        {
            $update = $conexion->prepare("UPDATE clientes SET token_recuperacion = ?, token_expiracion = ? WHERE correo = ?");
        }
        else
        {
            $update = $conexion->prepare("UPDATE empleados SET token_recuperacion = ?, token_expiracion = ? WHERE correo = ?");
        }

        $update->execute([$token, $expiracion, $correo]);
        return $update;
    }

    public static function tipoContraseña(string $tipo,string $token,string $nuevoPassword,string $nombre_usuario)
    {
        $conexion=ConexionDB::obtenerConexion();
        if ($tipo === 'cliente')
        {
            $stmt = $conexion->prepare("SELECT id_cliente FROM clientes WHERE token_recuperacion = ? AND token_expiracion > NOW()");
            $stmt->execute([$token]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario)
            {
                $update = $conexion->prepare("UPDATE clientes SET token_recuperacion = NULL, token_expiracion = NULL WHERE id_cliente = ?");
                $update->execute([$usuario['id_cliente']]);
                $update = $conexion->prepare("UPDATE usuarios SET contrasena = ? WHERE usuario = ?");
                $update->execute([$nuevoPassword, $nombre_usuario]);
            }
        }
        else
        {
            $stmt = $conexion->prepare("SELECT id_empleado FROM empleados WHERE token_recuperacion = ? AND token_expiracion > NOW()");
            $stmt->execute([$token]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario)
            {
                $update = $conexion->prepare("UPDATE empleados SET token_recuperacion = NULL, token_expiracion = NULL WHERE id_empleado = ?");
                $update->execute([$usuario['id_empleado']]);
                $update = $conexion->prepare("UPDATE usuarios SET contrasena = ? WHERE usuario = ?");
                $update->execute([$nuevoPassword, $nombre_usuario]);
            }
        }
        return $usuario;
    }

    public static function buscarToken(string $token)
    {
        $conexion=ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare("SELECT id_cliente AS id, 'cliente' AS tipo FROM clientes WHERE token_recuperacion = ? AND token_expiracion > NOW()
                                    UNION
                                    SELECT id_empleado AS id, 'empleado' AS tipo FROM empleados WHERE token_recuperacion = ? AND token_expiracion > NOW()");
        $stmt->execute([$token, $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>