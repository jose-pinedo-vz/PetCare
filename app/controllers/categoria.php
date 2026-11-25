<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

function responderCategoria(int $codigo, array $respuesta): void
{
    http_response_code($codigo);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

function responderErrorCategoria(int $codigo, array $errores, array $datos = []): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $_SESSION['errores_categoria'] = $errores;
    $_SESSION['old_categoria'] = $datos;

    responderCategoria($codigo, [
        'exito' => false,
        'mensaje' => implode(' ', $errores),
        'errores' => $errores,
    ]);
}

function validarDatosCategoria(array $datos): array
{
    $errores = [];
    $nombre = is_string($datos['nombre'] ?? null) ? trim($datos['nombre']) : '';
    $descripcion = is_string($datos['descripcion'] ?? null) ? trim($datos['descripcion']) : '';
    $estado = is_string($datos['estado'] ?? null) ? trim($datos['estado']) : '';
    if ($nombre === '') {
        $errores[] = 'El nombre de la categoría es obligatorio.';
    } elseif (strlen($nombre) > 50) {
        $errores[] = 'El nombre de la categoría no puede superar 50 caracteres.';
    } elseif (!preg_match("~\\A[\\p{L}\\p{N}][\\p{L}\\p{N}\\s&().,'’/+_-]*\\z~u", $nombre)) {
        $errores[] = 'El nombre solo puede incluir letras, números, espacios y signos comunes.';
    } elseif (!preg_match('/\p{L}/u', $nombre)) {
        $errores[] = 'El nombre de la categoría debe incluir al menos una letra.';
    }

    if (strlen($descripcion) > 255) {
        $errores[] = 'La descripción no puede superar 255 caracteres.';
    } elseif (preg_match('/[\p{Cc}\p{Cf}]/u', str_replace(["\r", "\n", "\t"], '', $descripcion))) {
        $errores[] = 'La descripción contiene caracteres no permitidos.';
    }

    if (!in_array($estado, ['Activo', 'Inactivo'], true)) {
        $errores[] = 'Selecciona un estado válido.';
    }

    return [
        'errores' => $errores,
        'datos' => [
            'nombre'      => $nombre,
            'descripcion' => $descripcion,
            'estado'      => $estado,
        ],
    ];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderCategoria(405, ['exito' => false, 'mensaje' => 'Método no permitido.']);
}

$cuerpo = file_get_contents('php://input');
if ($cuerpo === false || $cuerpo === '') {
    responderErrorCategoria(400, ['La solicitud está vacía.']);
}
if (strlen($cuerpo) > 8192) {
    responderErrorCategoria(413, ['La solicitud supera el tamaño permitido.']);
}

try {
    $datos = json_decode($cuerpo, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    responderErrorCategoria(400, ['La solicitud no tiene un formato válido.']);
}

if (!is_array($datos)) {
    responderErrorCategoria(400, ['La solicitud no tiene un formato válido.']);
}

if (
    !is_string($datos['csrf'] ?? null)
    || !isset($_SESSION['csrf_categoria'])
    || !hash_equals($_SESSION['csrf_categoria'], $datos['csrf'])
) {
    responderCategoria(403, [
        'exito' => false,
        'mensaje' => 'La sesión expiró. Recarga la página e inténtalo nuevamente.',
    ]);
}

$validacion = validarDatosCategoria($datos);
if ($validacion['errores']) {
    responderErrorCategoria(422, $validacion['errores'], $validacion['datos']);
}

require_once __DIR__ . '/../models/Categoria.php';

try {
    if (Categoria::existeNombre($validacion['datos']['nombre'])) {
        responderErrorCategoria(
            409,
            ['Ya existe una categoría con ese nombre.'],
            $validacion['datos']
        );
    }

    $categoria = Categoria::insertarCategoria($validacion['datos']);

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    unset($_SESSION['errores_categoria'], $_SESSION['old_categoria']);

    responderCategoria(201, ['exito' => true, 'categoria' => $categoria]);
} catch (Throwable $e) {
    error_log("Error al registrar categoría: " . $e->getMessage());
    responderErrorCategoria(
        500,
        ['No fue posible guardar la categoría.'],
        $validacion['datos']
    );
}