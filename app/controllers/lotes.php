<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Lotes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/lotes.php#lotes');
    exit;
}

$obtenerCampo = static function (string $campo): string {
    $valor = $_POST[$campo] ?? '';
    return is_string($valor) ? trim($valor) : '';
};

$datos = [
    'lote'            => $obtenerCampo('lote'),
    'id_producto'     => $obtenerCampo('id_producto'),
    'fecha_caducidad' => $obtenerCampo('fecha_caducidad'),
    'precio_compra'   => $obtenerCampo('precio_compra'),
    'ubicacion'       => $obtenerCampo('ubicacion'),
    'cantidad'        => $obtenerCampo('cantidad'),
];

$errores = [];

if ($datos['lote'] === '') {
    $errores[] = 'El código de lote es obligatorio.';
} elseif (strlen($datos['lote']) > 20) {
    $errores[] = 'El código de lote no puede tener más de 20 caracteres.';
} elseif (!preg_match('/^(?=.*[a-zA-ZñÑ])(?=.*\d)[a-zA-Z0-9ñÑ\-_]+$/', $datos['lote'])) {
    $errores[] = 'El lote debe contener al menos una letra, un número y sin caracteres especiales.';
}

$idProducto = filter_var($datos['id_producto'], FILTER_VALIDATE_INT);
if ($idProducto === false || $idProducto < 1) {
    $errores[] = 'Selecciona un producto válido.';
}

$fecha = DateTimeImmutable::createFromFormat(
    '!Y-m-d',
    $datos['fecha_caducidad'],
    new DateTimeZone('America/Mexico_City')
);
$erroresFecha = DateTimeImmutable::getLastErrors();
if (
    $fecha === false
    || ($erroresFecha !== false && ($erroresFecha['warning_count'] > 0 || $erroresFecha['error_count'] > 0))
    || $fecha->format('Y-m-d') !== $datos['fecha_caducidad']
) {
    $errores[] = 'La fecha de caducidad no tiene un formato válido.';
} elseif ($fecha < new DateTimeImmutable('today', new DateTimeZone('America/Mexico_City'))) {
    $errores[] = 'La fecha de caducidad no puede ser anterior a hoy.';
}

if ($datos['precio_compra'] === '') {
    $errores[] = 'El precio de compra es obligatorio.';
} elseif (!preg_match('/^\d+(?:\.\d{1,2})?$/D', $datos['precio_compra'])) {
    $errores[] = 'El precio de compra debe ser numérico y no puede tener más de dos decimales.';
} elseif ((float) $datos['precio_compra'] <= 0) {
    $errores[] = 'El precio de compra debe ser un valor mayor a 0.';
}

if ($datos['ubicacion'] === '') {
    $errores[] = 'La ubicación es obligatoria.';
} elseif (strlen($datos['ubicacion']) > 20) {
    $errores[] = 'La ubicación no puede tener más de 20 caracteres.';
} elseif (!preg_match('/^(?=.*[a-zA-ZñÑ])(?=.*\d)[a-zA-Z0-9ñÑ\-_]+$/', $datos['ubicacion'])) {
    $errores[] = 'La ubicación debe contener al menos una letra, un número y sin caracteres especiales.';
}

if ($datos['cantidad'] === '') {
    $errores[] = 'La cantidad es obligatoria.';
}
$cantidad = filter_var($datos['cantidad'], FILTER_VALIDATE_INT);
if ($datos['cantidad'] !== '' && ($cantidad === false || $cantidad < 0)) {
    $errores[] = 'La cantidad debe ser un número entero igual o mayor que cero.';
}

if (
    $datos['lote'] !== ''
    && strlen($datos['lote']) <= 20
    && preg_match('/^(?=.*[a-zA-ZñÑ])(?=.*\d)[a-zA-Z0-9ñÑ\-_]+$/', $datos['lote'])
) {
    try {
        if (Lotes::existeLote($datos['lote'])) {
            $errores[] = 'Ya existe un lote con ese código.';
        }
    } catch (Exception $e) {
        error_log('Error al verificar si el código de lote existe: ' . $e->getMessage());
        $_SESSION['errores'] = ['No se pudo validar el código del lote. Inténtalo nuevamente.'];
        $_SESSION['old'] = $datos;
        $_SESSION['abrir_formulario_lote'] = true;
        header('Location: ../views/lotes.php#lotes');
        exit;
    }
}

if ($errores !== []) {
    $_SESSION['errores'] = $errores;
    $_SESSION['old'] = $datos;
    $_SESSION['abrir_formulario_lote'] = true;
    header('Location: ../views/lotes.php#lotes');
    exit;
}

try {
    $registrado = Lotes::registrarLote([
        'id_producto' => $idProducto,
        'lote' => $datos['lote'],
        'fecha_caducidad' => $datos['fecha_caducidad'],
        'precio_compra' => $datos['precio_compra'],
        'ubicacion' => $datos['ubicacion'],
        'cantidad' => $cantidad,
    ]);

    if (!$registrado) {
        $_SESSION['errores'] = ['El producto seleccionado ya no está activo o no existe.'];
        $_SESSION['old'] = $datos;
        $_SESSION['abrir_formulario_lote'] = true;
        header('Location: ../views/lotes.php#lotes');
        exit;
    }
} catch (Exception $e) {
    error_log('Error al registrar lote: ' . $e->getMessage());
    $_SESSION['errores'] = ['No se pudo registrar el lote. Revisa los datos e inténtalo nuevamente.'];
    $_SESSION['old'] = $datos;
    $_SESSION['abrir_formulario_lote'] = true;
    header('Location: ../views/lotes.php#lotes');
    exit;
}

unset($_SESSION['old'], $_SESSION['abrir_formulario_lote']);
header('Location: ../views/lotes.php#lotes');
exit;