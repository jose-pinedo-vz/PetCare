<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Empleados.php';
require_once __DIR__ . '/../models/Mascota.php';

function errorSession(string $error, int $id): void
{
    $_SESSION['error'] = $error;
    header('Location: ../views/eliminar_mascota.php?id=' . $id);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ../views/mascotas.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id_mascota', FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id <= 0) {
    header('Location: ../views/mascotas.php');
    exit;
}

$nombreEmpleado = trim((string)($_POST['nombre_empleado'] ?? ''));
// La contraseña (password_empleado) no se valida todavía( aun no existe en la BD)

if ($nombreEmpleado === '') {
    errorSession('Debes escribir el nombre del empleado.', $id);
}

$mascota = Mascota::obtenerPorId($id);
if ($mascota === null || (int)($mascota['esta_activo'] ?? 1) !== 1) {
    errorSession('La mascota no existe o ya fue eliminada.', $id);
}

$empleadoValido = false;
try {
    $buscado = mb_strtolower($nombreEmpleado);
    foreach (Empleados::Listar_empleados_activos() as $fila) {
        $soloNombre = mb_strtolower(trim((string)$fila['nombre']));
        $completo   = mb_strtolower(trim($fila['nombre'] . ' ' . ($fila['apellido'] ?? '')));
        if ($buscado === $soloNombre || $buscado === $completo) {
            $empleadoValido = true;
            break;
        }
    }
} catch (Throwable $error) {
    error_log('Error al validar empleado: ' . $error->getMessage());
    errorSession('No se pudo validar al empleado. Intenta de nuevo.', $id);
}

if (!$empleadoValido) {
    errorSession('El empleado no existe o no está activo.', $id);
}

if (!Mascota::eliminar($id)) {
    errorSession('No se pudo eliminar la mascota. Intenta de nuevo.', $id);
}

header('Location: ../views/mascotas.php');
exit;