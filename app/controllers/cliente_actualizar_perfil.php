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

/* ---------- 0. Buscar código postal (lo llama el JS de Mi perfil) ---------- */
if ($accion === 'codigo_postal') {
    header('Content-Type: application/json; charset=utf-8');
    $cp = trim($_POST['codigo_postal'] ?? '');
    echo json_encode(preg_match('/^[0-9]{5}$/', $cp) ? Cliente::obtenerCodigoPostal($cp) : []);
    exit;
}

/* ---------- 1. Datos personales ---------- */
if ($accion === 'datos') {
    $campos = [
        'usuario', 'nombre', 'apellido', 'fecha_nacimiento', 'sexo', 'rfc',
        'telefono', 'telefono_alternativo', 'correo',
        'calle', 'numero_exterior', 'numero_interior', 'codigo_postal', 'colonia', 'ciudad', 'estado',
        'contacto_emergencia', 'telefono_emergencia',
    ];
    $datos = [];
    foreach ($campos as $campo) {
        $datos[$campo] = trim($_POST[$campo] ?? '');
    }
    $datos['rfc'] = strtoupper($datos['rfc']);

    $errores = [];
    $soloLetras = '/^[\p{L} ]+$/u';

    // Usuario: obligatorio, sin espacios y que no lo tenga otro cliente
    if ($datos['usuario'] === '') {
        $errores[] = 'El nombre de usuario es obligatorio.';
    } elseif (strlen($datos['usuario']) > 30) {
        $errores[] = 'El nombre de usuario no puede tener más de 30 letras.';
    } elseif (str_contains($datos['usuario'], ' ')) {
        $errores[] = 'El nombre de usuario no debe contener espacios.';
    } else {
        $otro = Cliente::obtenerPorUsuario($datos['usuario']);
        if ($otro && (int) $otro['id_cliente'] !== $idCliente) {
            $errores[] = 'Ese nombre de usuario ya está en uso.';
        }
    }

    // Nombre y apellidos: obligatorios, solo letras
    if ($datos['nombre'] === '') {
        $errores[] = 'Los nombres son obligatorios.';
    } elseif (mb_strlen($datos['nombre']) > 50) {
        $errores[] = 'Los nombres no pueden tener más de 50 letras.';
    } elseif (!preg_match($soloLetras, $datos['nombre'])) {
        $errores[] = 'Los nombres solo pueden tener letras y espacios.';
    }
    if ($datos['apellido'] === '') {
        $errores[] = 'Los apellidos son obligatorios.';
    } elseif (mb_strlen($datos['apellido']) > 100) {
        $errores[] = 'Los apellidos no pueden tener más de 100 letras.';
    } elseif (!preg_match($soloLetras, $datos['apellido'])) {
        $errores[] = 'Los apellidos solo pueden tener letras y espacios.';
    }

    // Fecha de nacimiento: opcional; si viene, válida y mayor de edad (igual que el empleado)
    if ($datos['fecha_nacimiento'] !== '') {
        $fecha = DateTime::createFromFormat('!Y-m-d', $datos['fecha_nacimiento']);
        if (!$fecha || $fecha->format('Y-m-d') !== $datos['fecha_nacimiento']) {
            $errores[] = 'La fecha de nacimiento no es válida.';
        } elseif ($fecha > new DateTime('today')) {
            $errores[] = 'La fecha de nacimiento no puede ser posterior a hoy.';
        } elseif ($fecha->diff(new DateTime('today'))->y < 18) {
            $errores[] = 'Tienes que ser mayor de edad.';
        } elseif ($fecha->diff(new DateTime('today'))->y > 120) {
            $errores[] = 'La fecha de nacimiento no es válida.';
        }
    }

    // Sexo: opcional, solo las opciones del formulario
    if ($datos['sexo'] !== '' && !in_array($datos['sexo'], ['Femenino', 'Masculino', 'Otro'], true)) {
        $errores[] = 'Elige una opción válida en Sexo.';
    }

    // RFC: opcional; si viene, mismas reglas que proveedores (validar_rfc.php)
    if ($datos['rfc'] !== '') {
        require_once __DIR__ . '/validar_rfc.php';
        foreach (validarRfc($datos['rfc']) as $errorRfc) {
            $errores[] = 'RFC: ' . $errorRfc . '.';
        }
    }

    // Teléfonos: 10 dígitos (el alternativo es opcional)
    if (!preg_match('/^[0-9]{10}$/', $datos['telefono'])) {
        $errores[] = 'El teléfono debe tener 10 dígitos.';
    }
    if ($datos['telefono_alternativo'] !== '' && !preg_match('/^[0-9]{10}$/', $datos['telefono_alternativo'])) {
        $errores[] = 'El teléfono alternativo debe tener 10 dígitos.';
    }

    // Correo
    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Formato de correo inválido.';
    } elseif (strlen($datos['correo']) > 100) {
        $errores[] = 'El correo no puede tener más de 100 caracteres.';
    }

    if (!$errores && Cliente::estaRegistrado($datos['correo'], $datos['telefono'], $idCliente)) {
        $errores[] = 'Ya existe otro cliente con ese correo o teléfono.';
    }

    $direccion = ['calle', 'numero_exterior', 'numero_interior', 'codigo_postal', 'colonia', 'ciudad', 'estado'];
    $hayDireccion = false;
    foreach ($direccion as $campo) {
        if ($datos[$campo] !== '') {
            $hayDireccion = true;
        }
    }
    if ($hayDireccion) {
        $obligatorios = [
            'calle' => 'la calle', 'numero_exterior' => 'el número exterior', 'codigo_postal' => 'el código postal',
            'colonia' => 'la colonia', 'ciudad' => 'la ciudad', 'estado' => 'el estado',
        ];
        foreach ($obligatorios as $campo => $texto) {
            if ($datos[$campo] === '') {
                $errores[] = 'Para completar tu dirección falta ' . $texto . '.';
            }
        }
        if (mb_strlen($datos['calle']) > 100) {
            $errores[] = 'La calle no puede tener más de 100 caracteres.';
        }
    
        foreach (['numero_exterior' => 'El número exterior', 'numero_interior' => 'El número interior'] as $campo => $texto) {
            if ($datos[$campo] !== '' && !preg_match('/^[a-zA-Z0-9.\-\/ ]{1,15}$/', $datos[$campo])) {
                $errores[] = $texto . ' solo puede tener números, letras, guiones y diagonal (máximo 15).';
            } elseif ($datos[$campo] !== '' && $datos[$campo][0] === '-') {
                $errores[] = $texto . ' no puede ser negativo.';
            }
        }
        if ($datos['codigo_postal'] !== '' && !preg_match('/^[0-9]{5}$/', $datos['codigo_postal'])) {
            $errores[] = 'El código postal debe tener 5 dígitos.';
        }
        foreach (['colonia' => 'La colonia', 'ciudad' => 'La ciudad', 'estado' => 'El estado'] as $campo => $texto) {
            if (mb_strlen($datos[$campo]) > 60) {
                $errores[] = $texto . ' no puede tener más de 60 caracteres.';
            }
        }
    }

    if ($datos['contacto_emergencia'] === '') {
        $errores[] = 'El contacto de emergencia es obligatorio.';
    } elseif (mb_strlen($datos['contacto_emergencia']) > 100) {
        $errores[] = 'El contacto de emergencia no puede tener más de 100 letras.';
    } elseif (!preg_match($soloLetras, $datos['contacto_emergencia'])) {
        $errores[] = 'El contacto de emergencia solo puede tener letras y espacios.';
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

    $_SESSION['cliente_nombre'] = $datos['nombre'];
    $_SESSION['perfil_exito'] = $resultado['mensaje'];
    header('Location: ../views/cliente/mi_perfil.php');
    exit;
}

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
