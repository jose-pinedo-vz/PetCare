<?php
session_start();

require_once __DIR__ . '/../models/Cliente.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/cliente/recuperar_contrasena.php');
    exit;
}

$token = is_string($_POST['token'] ?? null) ? trim($_POST['token']) : '';
$contrasena = is_string($_POST['contrasena'] ?? null) ? $_POST['contrasena'] : '';
$confirmar = is_string($_POST['confirmar_contrasena'] ?? null) ? $_POST['confirmar_contrasena'] : '';

$volver = '../views/cliente/restablecer_contrasena.php?token=' . urlencode($token);

// El token se valida otra vez (pudo expirar o ya haberse usado)
if (Cliente::obtenerPorTokenRecuperacion($token) === null) {
    header('Location: ' . $volver);
    exit;
}

$errores = [];
if (strlen($contrasena) < 8) {
    $errores[] = 'La contraseña debe contener mínimo 8 caracteres.';
} elseif ($contrasena !== $confirmar) {
    $errores[] = 'Las contraseñas no coinciden.';
}

if (count($errores) > 0) {
    $_SESSION['errores'] = $errores;
    header('Location: ' . $volver);
    exit;
}

// Mismo hash que el registro. El UPDATE borra el token: solo sirve una vez
if (!Cliente::actualizarContrasenaPorToken($token, password_hash($contrasena, PASSWORD_DEFAULT))) {
    // Otro envio lo uso o expiro justo ahora: la vista mostrara "no es valido o ya expiro"
    header('Location: ' . $volver);
    exit;
}

$_SESSION['exito'] = 'Contraseña actualizada, ya puedes iniciar sesión';
header('Location: ../views/cliente/iniciar_sesion.php');
exit;
