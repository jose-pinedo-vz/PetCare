<?php
session_start();
require_once '../models/conexion_db.php';
$conexion = ConexionDB::obtenerConexion();

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmar_password = trim($_POST['confirmar_password'] ?? '');

    if (empty($usuario) || empty($password) || empty($confirmar_password))
    {
        $error = "Por favor, completa todos los campos obligatorios.";
    }
    elseif (strlen($usuario) < 4)
    {
        $error = "El nombre de usuario debe tener al menos 4 caracteres.";
    }
    elseif (strlen($password) < 6)
    {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    }
    // Validación de que las contraseñas coincidan
    elseif ($password !== $confirmar_password)
    {
        $error = "Las contraseñas no coinciden. Por favor, revísalas.";
    }
    else
    {
        // Encriptamos la contraseña para mantener la seguridad
        $password_hash = $password;
        
        // Rol predeterminado para nuevos registros y estado activo
        $rol = 'cliente'; 
        $permisos = 'lectura';
        $estado = 'activo';
        $empleado_asociado = null;

        try
        {
            ConexionDB::insertarUsuario($conexion, $usuario, $password_hash, $empleado_asociado, $rol, $permisos, $estado);
            
            $_SESSION['mensaje'] = "¡Cuenta creada con éxito! Ya puedes iniciar sesión.";
            header("Location: ../views/login.php");
            exit();
        }
        catch (PDOException $e)
        {
            if ($e->getCode() === '23000' || $e->getCode() === 1062)
            {
                $error = "El nombre de usuario '$usuario' ya se encuentra registrado.";
            }
            else
            {
                $error = "Error al registrar la cuenta: " . $e->getMessage();
            }
        }
    }
}

if (!empty($error))
{
    $_SESSION['error'] = $error;
    header("Location: ../views/registro_cliente.php");
    exit();
}