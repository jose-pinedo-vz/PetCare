<?php
session_start();


if (empty($_SESSION['id_cliente'])) {
    header('Location: ../views/cliente/iniciar_sesion.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/cliente/mi_perfil.php');
    exit;
}

require_once __DIR__ . '/../models/Cliente.php';

$idCliente = (int) $_SESSION['id_cliente'];
$accion = $_POST['accion'] ?? '';


function regresarConErrores(array $errores, string $destino, array $old = []): void
{
    $_SESSION['perfil_errores'] = $errores;
    $_SESSION['perfil_old'] = $old;
    header('Location: ../views/cliente/mi_perfil.php' . $destino);
    exit;
}

/* ---------- 1. Datos de contacto ---------- */
if ($accion === 'datos') {
    $datos = [
        'nombre'              => trim($_POST['nombres'] ?? ''),
        'apellido'            => trim($_POST['apellidos'] ?? ''),
        'telefono'            => trim($_POST['telefono'] ?? ''),
        'correo'              => trim($_POST['correo'] ?? ''),
        'direccion'           => trim($_POST['direccion'] ?? ''),
        'contacto_emergencia' => trim($_POST['emergencia_nombre'] ?? ''),
        'telefono_emergencia' => trim($_POST['emergencia_telefono'] ?? ''),
    ];

    // Mismas reglas que al crear la cuenta
    $errores = [];
    if ($datos['nombre'] === '') {
        $errores[] = 'Los nombres son obligatorios.';
    } elseif (mb_strlen($datos['nombre']) > 50) {
        $errores[] = 'Los nombres no pueden tener más de 50 letras.';
    }
    if ($datos['apellido'] === '') {
        $errores[] = 'Los apellidos son obligatorios.';
    } elseif (mb_strlen($datos['apellido']) > 100) {
        $errores[] = 'Los apellidos no pueden tener más de 100 letras.';
    }
    if (!preg_match('/^[0-9]{10}$/', $datos['telefono'])) {
        $errores[] = 'El teléfono debe tener 10 dígitos.';
    }
    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Formato de correo inválido.';
    } elseif (strlen($datos['correo']) > 100) {
        $errores[] = 'El correo no puede tener más de 100 caracteres.';
    }
    if (mb_strlen($datos['direccion']) > 100) {
        $errores[] = 'La dirección no puede tener más de 100 caracteres.';
    }
    if ($datos['contacto_emergencia'] === '') {
        $errores[] = 'El contacto de emergencia es obligatorio.';
    } elseif (mb_strlen($datos['contacto_emergencia']) > 100) {
        $errores[] = 'El contacto de emergencia no puede tener más de 100 letras.';
    } elseif (!preg_match('/^[\p{L} ]+$/u', $datos['contacto_emergencia'])) {
        $errores[] = 'El contacto de emergencia solo puede tener letras.';
    }
    if (!preg_match('/^[0-9]{10}$/', $datos['telefono_emergencia'])) {
        $errores[] = 'El teléfono de emergencia debe tener 10 dígitos.';
    }

    if ($errores) {
        regresarConErrores($errores, '?editar=1', $_POST);
    }

    $resultado = Cliente::actualizarPerfil($idCliente, $datos);
    if (!$resultado['exito']) {
        regresarConErrores([$resultado['mensaje']], '?editar=1', $_POST);
    }

    // El menú muestra el nombre, así que lo actualizamos en la sesión
    $_SESSION['cliente_nombre'] = $datos['nombre'];
    $_SESSION['perfil_exito'] = $resultado['mensaje'];
    header('Location: ../views/cliente/mi_perfil.php');
    exit;
}

/* ---------- 2. Cambiar contraseña ---------- */
if ($accion === 'contrasena') {
    $actual    = $_POST['contrasena_actual'] ?? '';
    $nueva     = $_POST['contrasena_nueva'] ?? '';
    $confirmar = $_POST['confirmar_contrasena'] ?? '';

    $cliente = Cliente::obtenerPorId($idCliente);

    $errores = [];
    if (!$cliente || empty($cliente['contrasena']) || !password_verify($actual, $cliente['contrasena'])) {
        $errores[] = 'La contraseña actual no es correcta.';
    }
    if (strlen($nueva) < 8) {
        $errores[] = 'La nueva contraseña debe tener mínimo 8 caracteres.';
    } elseif ($nueva !== $confirmar) {
        $errores[] = 'Las contraseñas nuevas no coinciden.';
    }

    // Nunca regresamos las contraseñas escritas
    if ($errores) {
        regresarConErrores($errores, '#seguridad');
    }

    if (!Cliente::actualizarContrasena($idCliente, password_hash($nueva, PASSWORD_DEFAULT))) {
        regresarConErrores(['No se pudo cambiar la contraseña. Intenta de nuevo.'], '#seguridad');
    }

    $_SESSION['perfil_exito'] = 'Tu contraseña se cambió correctamente.';
    header('Location: ../views/cliente/mi_perfil.php');
    exit;
}

header('Location: ../views/cliente/mi_perfil.php');
exit;
