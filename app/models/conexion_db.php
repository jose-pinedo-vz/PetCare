<?php
declare(strict_types=1);
class ConexionDB 
{
    private static function cargarEntorno(string $ruta):void
    {
        if(!file_exists($ruta))
        {
            throw new Exception("El archivo de configuracion (.env) no se ha encontrado"); 
        }

        $lineas=file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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
                putenv("{$clave}={$valor}");
                $_ENV[$clave]=$valor;
                $_SERVER[$clave]=$valor;
            }
        }
    }

    public static function obtenerConexion(): PDO
    {
        try {
            // Cargamos el .env ubicado en la misma carpeta raíz
            self::cargarEntorno(__DIR__ . '/../../.env');

            $host = getenv('DB_HOST');
            $db   = getenv('DB_NAME');
            $user = getenv('DB_USER');
            $pass = getenv('DB_PASS');

            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
            
            $conexion = new PDO($dsn, $user, $pass);
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $conexion;

        } catch (PDOException $e) {
            // Si falla la base de datos, relanzamos la excepción con un mensaje más claro
            throw new Exception("Error de conexión a la Base de Datos: " . $e->getMessage());
        }
    }
}
?>