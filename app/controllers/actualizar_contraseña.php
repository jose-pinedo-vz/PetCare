<?php
if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}
require_once __DIR__ . '/../models/usuarios_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = trim($_POST['token'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $nombre_usuario=trim($_POST['nombre_usuario']??'');
    $nuevoPassword = trim($_POST['nuevo_password'] ?? '');

    if (empty($token) || empty($nuevoPassword) || empty($tipo) || empty($nombre_usuario))
    {
        $_SESSION['error']= "Faltan datos obligatorios.";
        header("Location: ../views/restablecer_contraseña.php?token=" . urlencode($token));
        exit();
    }

    $usuario = UsuariosDB::obtenerUsuario($nombre_usuario);
    if (!$usuario)
    {
        $_SESSION['error'] = "Usuario no encontrado.";
        header("Location: ../views/restablecer_contraseña.php?token=" . urlencode($token));
        exit();
    }
    
    $val_usuario = UsuariosDB::tipoContraseña($tipo, $token, $nuevoPassword, $nombre_usuario);

    if ($val_usuario)
    {
        header("Location: ../views/login.php?exito=password_actualizado");
        exit();
    }
    else
    {
        $_SESSION['error'] = "El enlace es inválido o ha expirado.";
        header("Location: ../views/restablecer_contraseña.php?token=" . urlencode($token));
        exit();
    }
}