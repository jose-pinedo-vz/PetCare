<?php
session_start();
if (strtolower((string)($_SESSION['rol'] ?? '')) !== 'admin') {
    header("Location: ../views/inicio.php");
    exit();
}
require_once '../models/usuarios_db.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id=$_POST['id']??null;
    $nuevo_usuario=trim($_POST['nuevo_usuario']??'');
    $nueva_password=trim($_POST['nueva_password']??'');
    $password_hash=password_hash($nueva_password, PASSWORD_DEFAULT);
    $empleado_asociado=!empty($_POST['empleado_asociado'])?$_POST['empleado_asociado']:null;
    $rol=$_POST['rol']??'usuario';
    $estado=$_POST['estado']??'activo';

    UsuariosDB::insertar($id, $nuevo_usuario, $password_hash, $empleado_asociado, $rol, $estado);
}

header("Location: ../views/usuarios.php");
exit();