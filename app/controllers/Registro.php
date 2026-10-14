<?php
session_start();
require_once '../models/conexion_db.php';
$conexion = ConexionDB::obtenerConexion();
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_usuario = trim($_POST['nuevo_usuario'] ?? '');
    $nueva_password = trim($_POST['nueva_password'] ?? '');
    $empleado_asociado = !empty($_POST['empleado_asociado']) ? $_POST['empleado_asociado'] : null;
    $rol = $_POST['rol'] ?? 'usuario';
    $permisos = $_POST['permisos'] ?? 'todos';
    $estado = $_POST['estado'] ?? 'activo';

    if (!empty($nuevo_usuario) && !empty($nueva_password)) {
        $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
        try {
            ConexionDB::insertarUsuario($conexion, $nuevo_usuario, $password_hash, $empleado_asociado, $rol, $permisos, $estado);
            $mensaje = "Usuario '$nuevo_usuario' creado con éxito.";
        } catch (PDOException $e) {
            if ($e->getCode() === '23000' || $e->getCode() === 1062) { // 1062 es MySQL para duplicados, 23000 es el código estándar SQLSTATE de integridad
                $error = "El nombre de usuario '$nuevo_usuario' ya existe.";
            }
            else
            {
               $error="Error al guardar el usuario: ".$e->getMessage();
            }
        }
    }
    else
    {
        $error="Completa los campos obligatorios";
    }
}


if (session_status()===PHP_SESSION_NONE)
{
    session_start();
}
if (!empty($mensaje))
{
    $_SESSION['mensaje']=$mensaje;
}
if (!empty($error))
{
    $_SESSION['error']=$error;
}
header("Location: ../views/registro_usuario.php");
exit();