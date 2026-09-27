<?php

session_start();

require_once '../models/conexion_db.php';
require_once '../views/bienvenida.php';

$conexion=ConexionDB::obtenerConexion();
$action=$_GET['action']??'home';

if($action==='logout')
{
    session_destroy();
    header("Location: Login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD']==='POST')
{
    $post_action=$_POST['action']??'';

    if($post_action==='login')
    {
        $usuario=trim($_POST['usuario']??'');
        $password=trim($_POST['password']??'');

        $datosUsuario=ConexionDB::buscarUsuarioPorNombre($conexion, $usuario);

        if($datosUsuario)
        {
            if($datosUsuario['estado'] !== 'activo')
            {
                vistaLogin("La cuenta se encuentra inactiva.");
                exit();
            }
            elseif(password_verify($password, $datosUsuario['contrasena']))
            {
                ConexionDB::registrarUltimoAcceso($conexion, $datosUsuario['id_usuario']);

                $_SESSION['id_usuario']=$datosUsuario['id_usuario'];
                $_SESSION['usuario']=$usuario;
                $_SESSION['rol']=$datosUsuario['rol'];

                header("Location: Login.php?action=bienvenido");
                exit();
            }
        }
        vistaLogin("Usuario o contraseña incorrectos.");
        exit();
    }

    if($post_action==='registro')
    {
        if (!isset($_SESSION['usuario'])||$_SESSION['rol'] !== 'admin')
        {
            header("Location: Login.php?action=bienvenido");
            exit();
        }

        $nuevo_usuario=trim($_POST['nuevo_usuario']??'');
        $nueva_password=trim($_POST['nueva_password']??'');
        $empleado_asociado=!empty($_POST['empleado_asociado'])?$_POST['empleado_asociado']:null;
        $rol=$_POST['rol']??'usuario';
        $permisos=$_POST['permisos']??'todos';
        $estado=$_POST['estado']??'activo';

        if(!empty($nuevo_usuario)&&!empty($nueva_password))
        {
            $password_hash=password_hash($nueva_password, PASSWORD_DEFAULT);
            try
            {
                ConexionDB::insertarUsuario($conexion, $nuevo_usuario, $password_hash, $empleado_asociado, $rol, $permisos, $estado);
                vistaRegistro("Usuario '$nuevo_usuario' creado con éxito", "");
            } 
            catch(PDOException $e)
            {
                if($e->getCode()===1062)
                {
                    vistaRegistro("", "El nombre de usuario '$nuevo_usuario' ya existe.");
                } else {
                    vistaRegistro("", "Error al guardar el usuario: ".$e->getMessage());
                }
            }
        }
        else
        {
            vistaRegistro("", "Por favor completa los campos obligatorios.");
        }
        exit();
    }
}

if($action==='home'||$action==='login')
{
    if(isset($_SESSION['usuario']))
    {
        header("Location: Login.php?action=bienvenido");
        exit();
    }
    vistaLogin();
}
elseif($action==='bienvenido')
{
    if(!isset($_SESSION['usuario']))
    {
        header("Location: Login.php");
        exit();
    }
    vistaBienvenido($_SESSION['usuario'], $_SESSION['rol']);
}
elseif ($action==='registro')
{
    if(!isset($_SESSION['usuario'])||$_SESSION['rol']!=='admin')
    {
        header("Location: Login.php?action=bienvenido");
        exit();
    }
    vistaRegistro();
}

elseif ($action==='modificar')
{
    if(!isset($_SESSION['usuario'])||$_SESSION['rol']!=='admin')
    {
        header("Location: Login.php?action=bienvenido");
        exit();
    }
    vistaModificar();
}
?>