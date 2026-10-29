<?php
require_once __DIR__ . '/../models/usuarios_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = trim($_POST['token'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $nombre_usuario=trim($_POST['nombre_usuario']??'');
    $nuevoPassword = trim($_POST['nuevo_password'] ?? '');

    if (empty($token) || empty($nuevoPassword) || empty($tipo) || empty($nombre_usuario))
    {
        echo "Faltan datos obligatorios.";
        exit();
    }

    $usuario= UsuariosDB::tipoContraseña( $tipo, $token, $nuevoPassword, $nombre_usuario);

    if (isset($usuario) && $usuario)
    {
        header("Location: ../views/login.php?exito=password_actualizado");
        exit();
    }
    else
    {
        echo "El enlace es inválido o ha expirado.";
    }
}