<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/Servicios.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Guarda el error y lo escrito, y regresa al formulario (agregar o editar)
function errorSession(string $error)
{
    $_SESSION['error'] = $error;
    $_SESSION['oldNombre_servicios'] = $_POST['nombre'] ?? '';
    $_SESSION['oldCategoria_servicios'] = $_POST['categoria'] ?? '';
    $_SESSION['oldDuracion_servicios'] = $_POST['duracion'] ?? '';
    $_SESSION['oldPrecio_servicios'] = $_POST['precio'] ?? '';
    $_SESSION['oldEmpleado_servicios'] = $_POST['id_empleados'] ?? '';
    $_SESSION['oldEstado_servicios'] = $_POST['estado'] ?? '';
    $_SESSION['oldDescripcion_servicios'] = $_POST['descripcion'] ?? '';

    $idServicio = (int)($_POST['id_servicio'] ?? 0);

    if ($idServicio > 0) {
        header("Location: ../views/editar_servicio.php?id_servicio=" . $idServicio);
        exit();
    }
    header("Location: ../views/agregar_servicio.php");
    exit();
}

// Si no viene por POST, regresa a la lista
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/servicios.php");
    exit();
}

// Activar o desactivar (viene de la lista de servicios)
$accion = $_POST['accion'] ?? '';
if ($accion !== '') {
    $idEstado = (int)($_POST['id_servicio'] ?? 0);

    if ($idEstado <= 0 || Servicios::obtenerPorId($idEstado) === null) {
        $_SESSION['error'] = "El servicio no existe.";
    } elseif (!in_array($accion, ['activar', 'desactivar'], true)) {
        $_SESSION['error'] = "La accion no es valida.";
    } elseif (!Servicios::cambiarEstado($idEstado, $accion === 'activar' ? 1 : 0)) {
        $_SESSION['error'] = "No se pudo cambiar el estado del servicio.";
    }

    header("Location: ../views/servicios.php");
    exit();
}

// Si trae id es editar, si no es agregar
$id_servicio  = (int)($_POST['id_servicio'] ?? 0);
$nombre       = trim($_POST['nombre'] ?? '');
$categoria    = trim($_POST['categoria'] ?? '');
$duracion     = trim($_POST['duracion'] ?? '');
$precio       = trim($_POST['precio'] ?? '');
$id_empleados = trim($_POST['id_empleados'] ?? '');
$estado       = trim($_POST['estado'] ?? '');
$descripcion  = trim($_POST['descripcion'] ?? '');

// Si es editar, el servicio debe existir
if ($id_servicio > 0 && Servicios::obtenerPorId($id_servicio) === null) {
    header("Location: ../views/servicios.php");
    exit();
}

// Validaciones
if ($nombre === '') {
    errorSession("El nombre del servicio es obligatorio.");
} elseif (mb_strlen($nombre) > 50) {
    errorSession("El nombre no puede pasar de 50 caracteres.");
} elseif (Servicios::existeNombre($nombre, $id_servicio)) {
    errorSession("Ya existe un servicio con ese nombre.");
}

if (!in_array($categoria, ['Estética', 'Salud'], true)) {
    errorSession("Selecciona una categoria valida.");
}

if (!preg_match('/^[0-9]+$/', $duracion) || (int)$duracion < 1 || (int)$duracion > 1440) {
    errorSession("La duracion debe ser un numero de minutos entre 1 y 1440.");
}

if (!preg_match('/^[0-9]{1,8}(\.[0-9]{1,2})?$/', $precio)) {
    errorSession("El precio debe ser un numero valido, con maximo 2 decimales.");
}

if ($id_empleados !== '' && !preg_match('/^[0-9]+$/', $id_empleados)) {
    errorSession("El empleado seleccionado no es valido.");
}

if (!in_array($estado, ['0', '1'], true)) {
    errorSession("El estado no es valido.");
}

$datos = [
    'nombre'       => $nombre,
    'categoria'    => $categoria,
    'descripcion'  => $descripcion === '' ? null : $descripcion,
    'duracion'     => (int)$duracion,
    'precio'       => $precio,
    'id_empleados' => $id_empleados === '' ? null : (int)$id_empleados,
    'estado'       => (int)$estado
];

if ($id_servicio > 0) {
    $res = Servicios::actualizar($id_servicio, $datos);
} else {
    $res = Servicios::insertar($datos);
}

if ($res['exito']) {
    // Limpia lo guardado en sesion
    unset(
        $_SESSION['error'],
        $_SESSION['oldNombre_servicios'],
        $_SESSION['oldCategoria_servicios'],
        $_SESSION['oldDuracion_servicios'],
        $_SESSION['oldPrecio_servicios'],
        $_SESSION['oldEmpleado_servicios'],
        $_SESSION['oldEstado_servicios'],
        $_SESSION['oldDescripcion_servicios']
    );
    header("Location: ../views/servicios.php");
    exit();
}

errorSession($res['mensaje']);
?>