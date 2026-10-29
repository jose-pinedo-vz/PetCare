<?php
session_start();
require_once '../models/conexion_db.php';
$conexion = ConexionDB::obtenerConexion();
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $nuevo_usuario = trim($_POST['nuevo_usuario'] ?? '');
    $nueva_password = trim($_POST['nueva_password'] ?? '');
    $empleado_asociado = !empty($_POST['empleado_asociado']) ? $_POST['empleado_asociado'] : null;
    
    $rol = $_POST['rol'] ?? '';
    $permisos = $_POST['permisos'] ?? '';
    $estado = $_POST['estado'] ?? '';

    if (empty($nuevo_usuario) || empty($nueva_password) || empty($rol) || empty($permisos) || empty($estado))
    {
        $error = "Por favor, completa todos los campos obligatorios.";
    }
    elseif (strlen($nuevo_usuario) < 4)
    {
        $error = "El nombre de usuario debe tener al menos 4 caracteres.";
    }
    elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $nuevo_usuario))
    {
        $error = "El nombre de usuario solo puede contener letras, números y guiones bajos.";
    }
    elseif (strlen($nueva_password) < 6)
    {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    }
    elseif (!in_array($rol, ['admin', 'veterinario', 'recepcionista',"vendedor","estilista","hotelero"]))
    {
        $error = "El rol seleccionado no es válido.";
    }
    elseif (!in_array($estado, ['activo', 'inactivo']))
    {
        $error = "El estado seleccionado no es válido.";
    }
    else
    {
        $password_hash=$nueva_password;
        try
        {
            ConexionDB::insertarUsuario($conexion, $nuevo_usuario, $password_hash, $empleado_asociado, $rol, $permisos, $estado);
            $mensaje = "Usuario '$nuevo_usuario' creado con éxito.";
        }
        catch (PDOException $e)
        {
            if ($e->getCode() === '23000' || $e->getCode() === 1062) 
            {
                if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1062)
                {
                    $error = "El nombre de usuario '$nuevo_usuario' ya existe.";
                }
                else
                {
                    $error = "Error al guardar el usuario: " . $e->getMessage();
                }
            }
            else
            {
                $error = "Error de base de datos: " . $e->getMessage();
            }
        }
    }
}

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}
if (!empty($mensaje))
{
    $_SESSION['mensaje'] = $mensaje;
}
if (!empty($error))
{
    $_SESSION['error'] = $error;
}
header("Location: ../views/usuarios.php");
exit();