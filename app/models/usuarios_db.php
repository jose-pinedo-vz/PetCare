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
    public static function validarCombinacionRolPuesto(PDO $conexion, ?int $empleado, string $rol): bool
    {
        $rolNormalizado = strtolower(trim($rol));

        if ($rolNormalizado === 'cliente') {
            return $empleado === null;
        }
        if ($empleado === null || $empleado < 1) {
            return false;
        }

        $stmt = $conexion->prepare(
            'SELECT puesto FROM empleados WHERE id_empleado = :empleado AND esta_activo = 1 LIMIT 1'
        );
        $stmt->execute([':empleado' => $empleado]);
        $puesto = $stmt->fetchColumn();
        if (!is_string($puesto)) {
            return false;
        }

        $combinaciones = [
            'admin' => ['Administrador'],
            'gerente' => ['Gerente'],
            'veterinario' => ['Veterinario', 'Veterinario/a'],
            'vendedor' => ['Vendedor'],
            'estilista' => ['Estilista'],
            'recepcionista' => ['Recepcionista'],
            'hotelero' => ['Hotelero'],
            'cajero' => ['Cajero']
        ];

        return isset($combinaciones[$rolNormalizado])
            && in_array($puesto, $combinaciones[$rolNormalizado], true);
    }
    
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

    public static function insertar(int $id, string $nuevo_usuario, string $nueva_password, ?int $empleado, string $rol, string $estado)
    {
        $conexion=ConexionDB::obtenerConexion();
        try
        {
            $empleado=trim($_POST['empleado_asociado']??'');
            $empleado_asociado=($empleado !== '' && $empleado !== '0')?(int)$empleado : null;
            $stmt = $conexion->prepare("UPDATE usuarios SET usuario=?, empleado_asociado=?, rol=?, estado=? WHERE id_usuario=?");
            $stmt->execute([$nuevo_usuario, $empleado_asociado, $rol, $estado, $id]);
            if (!empty($nueva_password))
            {
                $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
                $stmtPass = $conexion->prepare("UPDATE usuarios SET contrasena=? WHERE id_usuario=?");
                $stmtPass->execute([$password_hash, $id]);
            }

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
            $query="SELECT e.id_empleado, e.nombre, e.puesto
                    FROM empleados e 
                    LEFT JOIN usuarios u ON e.id_empleado = u.empleado_asociado 
                    WHERE u.empleado_asociado IS NULL AND e.esta_activo = 1";
            $stmt = $conexion->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function empleados_disponibles_editar(int $id_usuario)
    {
        $conexion = ConexionDB::obtenerConexion();
        try {
            // Trae los empleados activos que no tienen usuario, 
            // OR (o bien) el empleado que ya tiene asignado este usuario en específic.
            $query =   "SELECT id_empleado, nombre, puesto FROM empleados 
                        WHERE (id_empleado NOT IN (SELECT empleado_asociado FROM usuarios WHERE empleado_asociado IS NOT NULL)
                        OR id_empleado = (SELECT empleado_asociado FROM usuarios WHERE id_usuario = :id_usuario))
                        AND esta_activo = 1";
            
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            
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

    public static function obtenerUsuario(string $usuario)
    {
        $conexion=ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare("SELECT usuario FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function usuario_ID(int $id)
    {
        $conexion=ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>