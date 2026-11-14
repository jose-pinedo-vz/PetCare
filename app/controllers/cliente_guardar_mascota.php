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
if ($nombre === '') {
    $errores[] = 'El nombre es obligatorio.';
} elseif (strlen($nombre) > 20) {
    $errores[] = 'El nombre no puede tener más de 20 letras.';
} 

if ($especie === '') {
    $errores[] = 'La especie es obligatoria';
} 

if (!in_array($sexo, ['Hembra', 'Macho'])) {
    $errores[] = '¿Es macho o hembra?';
} 

if (!is_numeric($peso) || $peso <= 0 || $peso > 100) {
    $errores[] = 'El peso debe ser mayor a 0 y no mas de 100 kg';
}

if (!is_numeric($edad) || $edad < 0 || $edad > 40) {
    $errores[] = 'La edad debe ser un número entre 0 y 40 años.';
} /*else {
    $fechaNacimiento = (new DateTime('today'))
         ->modify('-' . round($edad * 12) . 'months')  
         ->format('Y-m-d');
}*/

if ($raza === '') {
    $raza = 'Sin especificar';
} elseif (strlen($raza) > 20) {
    $errores[] = 'La raza no puede tener más de 20 letras.';
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

//convertir los años a fecha de nacimiento
$fechaNacimiento = (new DateTime('today'))
    ->modify('-' . round($edad * 12) . ' months')
    ->format('Y-m-d');

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