<?php
session_start();
require_once '../models/usuarios_db.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id=$_POST['id']??null;
    $nuevo_usuario=trim($_POST['nuevo_usuario']??'');
    $nueva_password=trim($_POST['nueva_password']??'');
    $empleado_asociado=!empty($_POST['empleado_asociado'])?$_POST['empleado_asociado']:null;
    $rol=$_POST['rol']??'usuario';
    $permisos=$_POST['permisos']??'todos';
    $estado=$_POST['estado']??'activo';

    UsuariosDB::insertar($id, $nuevo_usuario, $nueva_password, $empleado_asociado, $rol, $permisos, $estado);
}

header("Location: ../views/usuarios.php");
exit();