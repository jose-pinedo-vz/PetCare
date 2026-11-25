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
    header('Location: ../views/cliente/editar_mascota.php?id_mascota=' . $idMascota);
    exit;
}

//convertir los años a fecha de nacimiento
$fechaNacimiento = (new DateTime('today'))
    ->modify('-' . round($edad * 12) . ' months')
    ->format('Y-m-d');

$sets = 'nombre = ?, especie = ?, raza = ?, sexo = ?, edad = ?, color = ?, peso = ?, tamanio = ?, temperamento = ?, alergias = ?, condiciones_especiales = ?';
$valores = [$nombre, $especie, $raza, $sexo, $fechaNacimiento, $color, $peso, $tamanio, $temperamento, $alergias, $condiciones_especiales];

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