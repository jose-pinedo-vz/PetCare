<?php
session_start();
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
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['usuario']))
{
    header("Location: ../views/clientes.php");
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
            elseif (password_verify($password, $datosUsuario['contrasena']))
            {
                session_regenerate_id(true);
                ConexionDB::registrarUltimoAcceso($conexion, $datosUsuario['id_usuario']);
                $_SESSION['id_usuario']=$datosUsuario['id_usuario'];
                $_SESSION['usuario']=$usuario;
                $_SESSION['rol']=$datosUsuario['rol'];
                header("Location: ../views/clientes.php");
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