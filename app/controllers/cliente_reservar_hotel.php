<?php
// Desactivado: las reservaciones de hotel se hacen en sucursal y las registra un empleado.
// El codigo de abajo se conserva por si se vuelve a habilitar; para eso, quitar estas 2 lineas.
header('Location: ../views/cliente/servicios.php');
exit;

session_start();

// Solo clientes con sesion
if (empty($_SESSION['id_cliente'])) {
    header('Location: ../views/cliente/iniciar_sesion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/cliente/solicitar_reservacion.php');
    exit;
}

$idCliente = (int) $_SESSION['id_cliente'];

// Lee un texto del POST (si llega como arreglo se toma vacio)
function textoPost(string $clave): string
{
    $v = $_POST[$clave] ?? '';
    return is_string($v) ? trim($v) : '';
}

$idMascota = textoPost('id_mascota');
$fechaEntrada = textoPost('fecha_entrada');
$fechaSalida = textoPost('fecha_salida');
$personaRecoge = textoPost('persona_recoge');
$telefonoEmergencia = textoPost('telefono_emergencia');

// Opcionales: clave => limite de caracteres segun la columna
$opcionales = [
    'servicios_adicionales' => 1000,
    'alimentacion_habitual' => 100,
    'cantidad_alimento' => 50,
    'horario_alimentacion' => 50,
    'medicamentos' => 1000,
    'horario_medicamentos' => 100,
    'necesidades_especiales' => 1000,
    'observaciones' => 1000,
];
$nombresCampo = [
    'servicios_adicionales' => 'Servicios adicionales',
    'alimentacion_habitual' => 'Alimentación habitual',
    'cantidad_alimento' => 'Cantidad de alimento',
    'horario_alimentacion' => 'Horario de alimentación',
    'medicamentos' => 'Medicamentos',
    'horario_medicamentos' => 'Horario de medicamentos',
    'necesidades_especiales' => 'Necesidades especiales',
    'observaciones' => 'Observaciones',
];

$errores = [];

if (!ctype_digit($idMascota) || (int) $idMascota <= 0) {
    $errores[] = 'Elige una mascota.';
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaEntrada)) {
    $errores[] = 'La fecha de entrada es obligatoria.';
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSalida)) {
    $errores[] = 'La fecha de salida es obligatoria.';
}

$largoRecoge = mb_strlen($personaRecoge, 'UTF-8');
if ($largoRecoge < 3 || $largoRecoge > 100) {
    $errores[] = 'El nombre de quien recoge debe tener de 3 a 100 caracteres.';
}

if (!preg_match('/^[0-9]{10}$/', $telefonoEmergencia)) {
    $errores[] = 'El teléfono de emergencia debe tener 10 dígitos.';
}

$datosOpcionales = [];
foreach ($opcionales as $clave => $limite) {
    $valor = textoPost($clave);
    if (mb_strlen($valor, 'UTF-8') > $limite) {
        $errores[] = $nombresCampo[$clave] . ': máximo ' . $limite . ' caracteres.';
    }
    $datosOpcionales[$clave] = ($valor === '') ? null : $valor;
}

if (count($errores) > 0) {
    $_SESSION['errores'] = $errores;
    $_SESSION['old'] = $_POST;
    header('Location: ../views/cliente/solicitar_reservacion.php');
    exit;
}

require_once __DIR__ . '/../models/hotel_cliente.php';

$resultado = HotelCliente::reservar($idCliente, array_merge($datosOpcionales, [
    'id_mascota' => (int) $idMascota,
    'fecha_entrada' => $fechaEntrada,
    'fecha_salida' => $fechaSalida,
    'persona_recoge' => $personaRecoge,
    'telefono_emergencia' => $telefonoEmergencia,
]));

if (!$resultado['exito']) {
    $_SESSION['errores'] = [$resultado['mensaje']];
    $_SESSION['old'] = $_POST;
    header('Location: ../views/cliente/solicitar_reservacion.php');
    exit;
}

$mensaje = 'Reservación registrada: ' . $resultado['noches'] . ' noche(s), total $'
    . number_format((float) $resultado['total'], 2) . '. Paga en recepción.';
if ($resultado['estado'] === HotelCliente::ESTADO_PENDIENTE_CARTILLA) {
    $mensaje .= ' Lleva la cartilla de vacunación de tu mascota para confirmarla.';
}
$_SESSION['exito'] = $mensaje;
header('Location: ../views/cliente/mis_reservaciones.php');
exit;
