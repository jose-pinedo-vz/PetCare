<?php

if (session_status() === PHP_SESSION_NONE)
{
    
    session_start();
    
}
require_once '../models/conexion_db.php';
$conexion=ConexionDB::obtenerConexion();
$action=$_GET['action'] ?? '';

if ($action==='logout')
{
    $_SESSION=array();
    if (ini_get("session.use_cookies"))
    {
        $params=session_get_cookie_params();
        setcookie(session_name(),'',time()-42000,$params["path"],$params["domain"],$params["secure"],$params["httponly"]);
    }
    session_destroy();
    header("Location: ../views/login.php");
    exit();
}

if (isset($_SESSION['usuario']) && $_SERVER['REQUEST_METHOD'] !== 'POST')
{
    header("Location: ../views/inicio.php");
    exit();
}
 
$error='';

if ($_SERVER['REQUEST_METHOD']==='POST')
{
    $usuario=trim($_POST['usuario'] ?? '');
    $password=trim($_POST['password'] ?? '');
    if (empty($usuario) || empty($password))
    {
        $error='ingrese su usuario y contraseña.';
    }
    else
    {
        $datosUsuario=ConexionDB::buscarUsuarioPorNombre($conexion, $usuario);

        if ($datosUsuario)
        {
            if ($datosUsuario['estado'] !== 'activo')
            {
                $error="La cuenta se encuentra inactiva.";
            }
            elseif ($password === $datosUsuario['contrasena'])
            {
                session_regenerate_id(true);
                ConexionDB::registrarUltimoAcceso($conexion, $datosUsuario['id_usuario']);
                session_unset();
                $_SESSION['id_usuario']=$datosUsuario['id_usuario'];
                $_SESSION['empleado_asociado']=$datosUsuario['nombre_empleado'] ?? null;
                $_SESSION['horario_empleado']=$datosUsuario['horario_empleado'] ?? null;
                $_SESSION['id_empleado']=$datosUsuario['id_empleado'] ?? null;
                $_SESSION['usuario']=$usuario;
                $_SESSION['rol']=$datosUsuario['rol'];
                header("Location: ../views/inicio.php");
                exit();
            }
            else
            {
                $error="usuario o contraseña incorrectos.";
            }
        }
        else
        {
            $error="usuario o contraseña incorrectos.";
        }
    }
}
require_once '../views/login.php';