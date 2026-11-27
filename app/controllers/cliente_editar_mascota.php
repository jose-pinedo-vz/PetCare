<?php
session_start();



// sin cliente en la sesiom no se puede editar una mascota
$idCliente = (int) ($_SESSION['id_cliente'] ?? 0);
if ($idCliente <= 0) {
    header('Location: ../views/cliente/inicio.php');
    exit;
}

$idMascota = (int) ($_POST['id_mascota'] ?? 0);

require_once __DIR__ . '/../models/Mascota.php';
$mascota = Mascota::obtenerPorId($idMascota);
if (!$mascota || !$mascota['esta_activo'] || (int) $mascota['id_cliente'] !== $idCliente) {
    $_SESSION['errores'] = ['No se encontró la mascota.'];
    header('Location: ../views/cliente/mis_mascotas.php');
    exit;
}

//leer lo que mando el formulario
$nombre = trim($_POST['nombre'] ?? '');
$especie = trim($_POST['especie'] ?? '');
$raza = trim($_POST['raza'] ?? '');
$sexo = trim($_POST['sexo'] ?? '');
$edad = trim($_POST['edad'] ?? '');
$peso = trim($_POST['peso'] ?? '');
$color = trim($_POST['color'] ?? '');
$temperamento = trim($_POST['temperamento'] ?? '');
$tamanio = trim($_POST['tamanio'] ?? '');
$alergias = trim($_POST['alergias'] ?? '');
$condiciones_especiales = trim($_POST['condiciones_especiales'] ?? '');
$enfermedades = trim($_POST['enfermedades'] ?? '');
$medicamentos = trim($_POST['medicamentos'] ?? '');
$ultima_desparasitacion = trim($_POST['ultima_desparasitacion'] ?? '');
$restricciones_para_manejo = trim($_POST['restricciones_para_manejo'] ?? '');
$observaciones = trim($_POST['observaciones'] ?? '');

$errores = [];
// nombre, especie, raza y color (mismas reglas en registrar y editar)
require_once __DIR__ . '/cliente_validar_mascota.php';
$color = validarCamposMascotaCliente($errores, $nombre, $especie, $raza, $color, trim($_POST['color_otro'] ?? ''));

if (!in_array($sexo, ['Hembra', 'Macho'])) {
    $errores[] = '¿Es macho o hembra?';
} 

if (!is_numeric($peso) || $peso <= 0 || $peso > 100) {
    $errores[] = 'El peso debe ser mayor a 0 y no mas de 100 kg';
}

// fecha de nacimiento: obligatoria, no futura y maximo 40 años
$fechaNac = DateTime::createFromFormat('!Y-m-d', $edad);
if ($edad === '') {
    $errores[] = 'La fecha de nacimiento es obligatoria.';
} elseif (!$fechaNac || $fechaNac->format('Y-m-d') !== $edad) {
    $errores[] = 'La fecha de nacimiento no es válida.';
} elseif ($fechaNac > new DateTime('today')) {
    $errores[] = 'La fecha de nacimiento no puede ser posterior a hoy.';
} elseif ($fechaNac->diff(new DateTime('today'))->y > 40) {
    $errores[] = 'La mascota no puede tener más de 40 años.';
}

// temperamento: opcional, sin numeros (igual que el empleado)
if ($temperamento !== '' && preg_match('/\d/', $temperamento)) {
    $errores[] = 'El temperamento no puede contener números.';
} elseif (mb_strlen($temperamento) > 100) {
    $errores[] = 'El temperamento no puede tener más de 100 caracteres.';
}

// alergias, enfermedades, medicamentos, desparasitacion, etc. (mismos campos que el empleado)
validarSaludMascotaCliente($errores, [
    'alergias' => $alergias,
    'enfermedades' => $enfermedades,
    'medicamentos' => $medicamentos,
    'condiciones_especiales' => $condiciones_especiales,
    'ultima_desparasitacion' => $ultima_desparasitacion,
    'restricciones_para_manejo' => $restricciones_para_manejo,
    'observaciones' => $observaciones,
], $edad);

if (count($errores) > 0) {
    $_SESSION['errores'] = $errores;
    $_SESSION['old'] = $_POST;
    header('Location: ../views/cliente/editar_mascota.php?id_mascota=' . $idMascota);
    exit;
}

//la BD guarda la fecha de nacimiento tal cual
$fechaNacimiento = $edad;

$sets = 'nombre = ?, especie = ?, raza = ?, sexo = ?, edad = ?, color = ?, peso = ?, tamanio = ?, temperamento = ?, alergias = ?, condiciones_especiales = ?, enfermedades = ?, medicamentos = ?, ultima_desparasitacion = ?, restricciones_para_manejo = ?, observaciones = ?';
$valores = [$nombre, $especie, $raza, $sexo, $fechaNacimiento, $color, $peso, $tamanio, $temperamento, $alergias, $condiciones_especiales, $enfermedades, $medicamentos, $ultima_desparasitacion !== '' ? $ultima_desparasitacion : null, $restricciones_para_manejo, $observaciones];

if (!empty($_FILES['fotografia']['name'])) {
    require_once __DIR__ . '/ImagenController.php';
    $resultado = ImagenController::subir($_FILES['fotografia']);
    if (!$resultado['exito']) {
        $_SESSION['errores'] = [$resultado['mensaje']];
        $_SESSION['old'] = $_POST;
        header('Location: ../views/cliente/editar_mascota.php?id_mascota=' . $idMascota);
        exit;
    }
    $sets .= ', fotografia = ?';
    $valores[] = $resultado['ruta'];
}

if (!Mascota::actualizar($idMascota, $sets, $valores)) {
    $_SESSION['errores'] = ['No se pudo guardar. Intenta de nuevo.'];
    $_SESSION['old'] = $_POST;
    header('Location: ../views/cliente/editar_mascota.php?id_mascota=' . $idMascota);
    exit;
}
header('Location: ../views/cliente/mis_mascotas.php');
exit;