<?php
session_start();

$usuario  = trim($_POST['usuario'] ?? '');
$correo   = trim($_POST['correo'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';
$confirmar  = $_POST['confirmar_contrasena'] ?? '';
$nombres  = trim($_POST['nombres'] ?? '');
$apellidos  = trim($_POST['apellidos'] ?? '');
$telefono  = trim($_POST['telefono'] ?? '');
$direccion  = trim($_POST['direccion'] ?? '');
$emergencia_nombre  = trim($_POST['emergencia_nombre'] ?? '');
$emergencia_telefono  = trim($_POST['emergencia_telefono'] ?? '');

$errores = [];
if ($usuario === '') {
    $errores[] = 'El nombre de usuario es obligatorio.';
} elseif (strlen($usuario) > 30) {
    $errores[] = 'El nombre de usuario no puede tener más de 30 letras.';
} elseif (str_contains($usuario, ' ')) {
    $errores[] = 'El nombre de usuario no debe contener espacios.';
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) { 
    $errores[] = 'Formato de correo inválido.';
} elseif (strlen($correo) > 100) {
    $errores[] = 'El correo no puede tener más de 100 carácteres.';
}

if (strlen($contrasena) < 8) {
    $errores[] = 'La contraseña debe contener mínimo 8 caracteres.';
} elseif ($contrasena != $confirmar) {
    $errores[] = 'Las contraseñas no coinciden.';
}

if ($nombres === '') {
    $errores[] = 'Los nombres son obligatorios.';
} elseif (strlen($nombres) > 50) {
    $errores[] = 'Los nombres no pueden tener más de 50 letras.';
}

if ($apellidos === '') {
    $errores[] = 'Los apellidos son obligatorios.';
} elseif (strlen($apellidos) > 100) {
    $errores[] = 'Los apellidos no pueden tener más de 100 letras.';
}

if (!preg_match('/^[0-9]{10}$/', $telefono)) {
    $errores[] = 'El teléfono debe tener 10 dígitos.';
}

if (!preg_match('/^[0-9]{10}$/', $emergencia_telefono)) {
    $errores[] = 'El teléfono de emergencia debe tener 10 dígitos';
}

if ($emergencia_nombre === '') {
    $errores[] = 'El nombre de emergencia es obligatorio.';
} elseif (strlen($emergencia_nombre) > 100) {
    $errores[] = 'El nombre de emergencia no puede tener más de 100 letras.';
}

if (count($errores) > 0) {
    $old = $_POST;
    unset($old['contrasena'], $old['confirmar_contrasena']);
    $_SESSION['errores'] = $errores;
    $_SESSION['old'] = $old;
    header('Location: ../views/cliente/crear_cuenta.php');
    exit;
}

require_once __DIR__ . '/../models/Cliente.php';

$resultado = Cliente::registrarCuenta([
    'usuario' => $usuario,
    'nombre' => $nombres,
    'apellido' => $apellidos,
    'telefono' => $telefono,
    'correo' => $correo,
    'direccion' => $direccion,
    'contacto_emergencia' => $emergencia_nombre,
    'telefono_emergencia' => $emergencia_telefono,
    'contrasena' => password_hash($contrasena, PASSWORD_DEFAULT),
]);

if (!$resultado['exito']) {
    $old = $_POST;
    unset($old['contrasena'], $old['confirmar_contrasena']);
    $_SESSION['errores'] = [$resultado['mensaje']];
    $_SESSION['old'] = $old;
    header('Location: ../views/cliente/crear_cuenta.php');
    exit;
}

$_SESSION['id_cliente'] = $resultado['id'];
$_SESSION['cliente_nombre'] = $nombres;
header('Location: ../views/cliente/mis_mascotas.php');
exit;
