<?php
session_start();


// sin cliente en la sesiom no se puede registrar una mascota
$idCliente = (int) ($_SESSION['id_cliente'] ?? 0);
if ($idCliente <= 0) {
    header('Location: ../views/cliente/inicio.php');
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

if (count($errores) > 0) {
    $_SESSION['errores'] = $errores;
    $_SESSION['old'] = $_POST;
    header('Location: ../views/cliente/registrar_mascota.php');
    exit;
}

//foto
$rutaFoto = null;
if (!empty($_FILES['fotografia']['name'])) {
    require_once __DIR__ . '/ImagenController.php';
    $resultado = ImagenController::subir($_FILES['fotografia']);
    if (!$resultado['exito']) {
        $_SESSION['errores'] = [$resultado['mensaje']];
        $_SESSION['old'] = $_POST;
        header('Location: ../views/cliente/registrar_mascota.php');
        exit;
    }
    $rutaFoto = $resultado['ruta'];
}

//la BD guarda la fecha de nacimiento tal cual
$fechaNacimiento = $edad;

//guardar
require_once __DIR__ . '/../models/Mascota.php';
$resultado = Mascota::insertar([
    'nombre' => $nombre,
    'especie' => $especie,
    'raza' => $raza,
    'sexo' => $sexo,
    'edad' => $fechaNacimiento,
    'color' => $color,
    'peso' => $peso,
    'tamanio' => $tamanio,
    'fotografia' => $rutaFoto, 
    'id_cliente' => $idCliente,
    'alergias' => $alergias,
    'condiciones_especiales' => $condiciones_especiales,
    'temperamento' => $temperamento
]);

if (is_array($resultado)) {
    // falló: insertar solo devuelve un arreglo cuando hay error
    $_SESSION['errores'] =  ['No se pudo guardar la mascota. Intenta de nuevo.'];   
    $_SESSION['old'] = $_POST;
    header('Location: ../views/cliente/registrar_mascota.php');
    exit;
}

header('Location: ../views/cliente/mis_mascotas.php');
exit;