<?php

if (session_status() === PHP_SESSION_NONE)
{
    
    session_start();
    
}
require_once '../models/conexion_db.php';
require_once '../models/Cliente.php';
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
            elseif (password_verify($password, $datosUsuario['contrasena']) || $password === $datosUsuario['contrasena'])
            {
                session_regenerate_id(true);
                ConexionDB::registrarUltimoAcceso($conexion, $datosUsuario['id_usuario']);
                session_unset();
                $_SESSION['id_usuario']=$datosUsuario['id_usuario'];
                $_SESSION['empleado_asociado']=$datosUsuario['nombre_empleado'] ?? null;
                $_SESSION['horario_empleado']=$datosUsuario['horario_empleado'] ?? null;
                $_SESSION['puesto_empleado']=$datosUsuario['puesto_empleado'] ?? null;
                $_SESSION['id_empleado']=$datosUsuario['id_empleado'] ?? null;
                $_SESSION['usuario']=$usuario;
                $_SESSION['rol']=$datosUsuario['rol'];
                if ($_SESSION['rol'] === 'cliente')
                {
                    header("Location: ../views/cliente/index.php");
                    exit();
                }
                else
                {
                    header("Location: ../views/inicio.php");
                    exit();
                }
            }
            else
            {
                $error="contraseña incorrecta.";
            }
        }
        else
        {
            // no es empleado: se busca entre los clientes (su usuario y contrasena estan en la tabla clientes)
            $cliente=Cliente::obtenerPorUsuario($usuario);

            if ($cliente && password_verify($password, $cliente['contrasena']))
            {
                session_regenerate_id(true);
                session_unset();
                // solo id_cliente: sin $_SESSION['usuario'] no puede abrir las paginas de empleados
                $_SESSION['id_cliente']=$cliente['id_cliente'];
                $_SESSION['cliente_nombre']=$cliente['nombre'];
                header("Location: ../views/cliente/mis_mascotas.php");
                exit();
            }
            else
            {
                $error="usuario o contraseña incorrectos.";
            }
        }
    }
}

// si el intento vino del formulario del cliente, el error se muestra ahi y con un mensaje unico
if ($error !== '' && ($_POST['origen'] ?? '') === 'cliente')
{
    $_SESSION['errores']=['Usuario o contraseña incorrectos.'];
    header("Location: ../views/cliente/iniciar_sesion.php");
    exit();
}
require_once '../views/login.php';