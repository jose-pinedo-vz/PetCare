<?php
session_start();
if (strtolower((string)($_SESSION['rol'] ?? '')) !== 'admin') {
    header("Location: ../views/inicio.php");
    exit();
}
require_once '../models/usuarios_db.php';
require_once '../models/conexion_db.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id=$_POST['id']??null;
    $nuevo_usuario=trim($_POST['nuevo_usuario']??'');
    $nueva_password=trim($_POST['nueva_password']??'');
    $password_hash=password_hash($nueva_password, PASSWORD_DEFAULT);
    $empleado_asociado=!empty($_POST['empleado_asociado'])?$_POST['empleado_asociado']:null;
    $rol=$_POST['rol']??'usuario';
    $estado=$_POST['estado']??'activo';

    if (empty($nuevo_usuario) || empty($nueva_password) || empty($rol) || empty($estado))
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
    elseif (!in_array($rol, ['admin', 'gerente', 'veterinario', 'recepcionista', 'vendedor', 'estilista', 'hotelero']))
    {
        $error = "El rol seleccionado no es válido.";
    }
    elseif (!in_array($estado, ['activo', 'inactivo']))
    {
        $error = "El estado seleccionado no es válido.";
    }
    elseif (!UsuariosDB::validarCombinacionRolPuesto(
        ConexionDB::obtenerConexion(),
        $empleado_asociado !== null ? (int)$empleado_asociado : null,
        $rol
    ))
    {
        $error = "El rol no coincide con el puesto del empleado asociado.";
    }
    else
    {
        UsuariosDB::insertar((int)$id, $nuevo_usuario, $nueva_password, $empleado_asociado !== null ? (int)$empleado_asociado : null, $rol, $estado);
    }

    if (!empty($error)) {
        $_SESSION['error'] = $error;
        if (!empty($id)) {
            header("Location: ../views/editar_usuarios.php?id_usuario=" . (int)$id);
            exit();
        }
    }
}

header("Location: ../views/usuarios.php");
exit();