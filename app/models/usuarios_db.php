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
        $base=$conexion->prepare("SELECT id_usuario, usuario, empleado_asociado, rol, permisos, fecha_creacion, ultimo_acceso, estado FROM usuarios");
        $base->execute();
        return $base->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>