<?php
declare(strict_types=1);
class ConexionDB 
{
    private static ?PDO $conexion = null;

    private static function cargarEntorno(string $ruta):void
    {
        if(!file_exists($ruta))
        {
            return;
        }

        $lineas=file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lineas === false)
        {
            return;
        }
        foreach ($lineas as $linea) 
        {
            $lineaTrime=trim($linea);
            if($lineaTrime=='' || str_starts_with($lineaTrime, '#'))
            {
                continue;
            }
            $partes=explode('=',$linea,2);
            if(count($partes)===2)
            {
                $clave=trim($partes[0]);
                $valor=trim($partes[1]);

                $valor=trim($valor,"\"'");
                if (function_exists('putenv'))
                {
                    @putenv("{$clave}={$valor}");
                }
                $_ENV[$clave]=$valor;
                $_SERVER[$clave]=$valor;
            }
        }
    }

    public static function obtenerConexion(): PDO
    {
        if (self::$conexion !== null)
        {
            return self::$conexion;
        }

        try {
            $rutaEnv = __DIR__ . '/../../.env';
            if (!file_exists($rutaEnv))
            {
                $rutaEnv = dirname(__DIR__, 2) . '/.env';
            }
            if (file_exists($rutaEnv))
            {
                self::cargarEntorno($rutaEnv);
            }

            $host = $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? (function_exists('getenv') ? getenv('DB_HOST') : null) ?: 'localhost';
            $db   = $_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? (function_exists('getenv') ? getenv('DB_NAME') : null) ?: '';
            $user = $_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? (function_exists('getenv') ? getenv('DB_USER') : null) ?: 'root';
            $pass = $_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? (function_exists('getenv') ? getenv('DB_PASS') : null) ?: '';
            $port = $_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? (function_exists('getenv') ? getenv('DB_PORT') : null) ?: '3306';

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
            
            self::$conexion = new PDO($dsn, $user, $pass);
            self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return self::$conexion;

        } catch (PDOException $e) {
            throw new Exception("Error de conexión a la Base de Datos: " . $e->getMessage());
        }
    }
    public static function buscarUsuarioPorNombre(PDO $conexion, string $usuario)
    {
        $sql = "SELECT u.id_usuario, u.contrasena, u.rol, u.estado, u.empleado_asociado AS id_empleado,e.nombre AS nombre_empleado, e.apellido AS apellido_empleado, e.horario AS horario_empleado
                FROM usuarios u
                LEFT JOIN empleados e ON u.empleado_asociado = e.id_empleado
                WHERE u.usuario = ?";
        $base = $conexion->prepare($sql);
        $base->execute([$usuario]);
        return $base->fetch(PDO::FETCH_ASSOC);
    }

    public static function registrarUltimoAcceso(PDO $conexion, int $id_usuario)
    {
        $base=$conexion->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id_usuario = ?");
        $base->execute([$id_usuario]);
    }

    public static function insertarUsuario(PDO $conexion, string $usuario, string $password_hash, ?int $empleado, string $rol, string $estado)
    {
        $empleado=trim($_POST['empleado_asociado']??'');
        $empleado_asociado=($empleado !== '' && $empleado !== '0')?(int)$empleado : null;
        $base=$conexion->prepare("INSERT INTO usuarios (id_usuario, usuario, contrasena, empleado_asociado, rol, estado) 
                                    VALUES ((SELECT COALESCE(MAX(id_usuario), 0) + 1 FROM usuarios AS u), ?, ?, ?, ?, ?, ?)");
        $base->execute([$usuario, $password_hash, $empleado_asociado, $rol, $estado]);
        return $base->rowCount();
    }
}
?>