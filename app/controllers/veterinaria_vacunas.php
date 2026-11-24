<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Veterinaria.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function listado_Vacunas(): array {
    $vacunas = Veterinaria::obtenerVacunas();

    return ['listaVacunas' => $vacunas];
}

function listado_Especies(): array {
    return Veterinaria::obtenerEspecies();
}

function listado_HistorialVacunas(): array {
    return Veterinaria::historialVacunas();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nombre = trim($_POST['nombre'] ?? '');
    $especie = filter_input(INPUT_POST, 'especie', FILTER_VALIDATE_INT);

    if ($accion === 'agregarVacuna') {

        if ($nombre === '') {
            $_SESSION['error'] = 'El nombre de la vacuna no puede estar vacio';
        } elseif(mb_strlen($nombre)<3){
            $_SESSION['error'] = 'El nombre debe tener al menos 3 caracteres';
        } elseif(mb_strlen($nombre)>50){
            $_SESSION['error'] = 'El nombre no debe ser mayor a 50 caracteres';
        } elseif(!$especie || $especie<=0){
            $_SESSION['error'] = 'El formato de la especie no es valido';
        } elseif (!preg_match('/\p{L}/u', $nombre)) {
            $_SESSION['error'] = 'El nombre debe contener al menos una letra.';
        } else if (!preg_match('/^[\p{L}\p{N}\s\-\.\/\(\)\+,]+$/u', $nombre)) {
            $_SESSION['error'] = 'El nombre solo puede contener letras, numeros, espacios y algunos signos como - . / () + , ';
        } else if (Veterinaria::estaRepetida($nombre, $especie)) {
            $_SESSION['error'] = "Ya existe una vacuna con ese nombre para la especie";
        } else {
            $resultado = Veterinaria::agregarVacuna($nombre, $especie);
        }

    } elseif ($accion === 'editarVacuna') {

        if (!$id || $id <= 0) {
            $_SESSION['error'] = 'La vacuna seleccionada no es valida';
        } else if ($nombre === '') {
            $_SESSION['error'] = 'El nombre de la vacuna no puede estar vacio';
        } elseif(mb_strlen($nombre)<3){
            $_SESSION['error'] = 'El nombre debe tener al menos 3 caracteres';
        } elseif(mb_strlen($nombre)>50){
            $_SESSION['error'] = 'El nombre no debe ser mayor a 50 caracteres';
        } elseif(!$especie || $especie<=0){
            $_SESSION['error'] = 'El formato de la especie no es valido';
        } elseif (!preg_match('/\p{L}/u', $nombre)) {
            $_SESSION['error'] = 'El nombre debe contener al menos una letra.';
        } else if (!preg_match('/^[\p{L}\p{N}\s\-\.\/\(\)\+,]+$/u', $nombre)) {
            $_SESSION['error'] = 'El nombre solo puede contener letras, numeros, espacios y algunos signos como - . / () + , ';
        } else if (Veterinaria::estaRepetida($nombre, $especie, $id)) {
            $_SESSION['error'] = "Ya existe una vacuna con ese nombre para la especie";
        } else {
            $resultado = Veterinaria::editarVacuna($id, $nombre, $especie);
        }

    } elseif ($accion === 'eliminarVacuna') {

        if (!$id || $id <= 0) {
            $_SESSION['error'] = 'La vacuna no puede ser menor a 0';
        } else {
            $resultado = Veterinaria::eliminarVacuna($id);
        }
    }

    if (isset($_SESSION['error'])) {
        $modales = [ 
            'agregarVacuna' => 'agregar',
            'editarVacuna' => 'editar',
            'eliminarVacuna' => 'eliminar'
        ];

        $_SESSION['modalError'] = $modales[$accion] ?? '';
        $_SESSION['oldNombre_vacunas'] = $_POST['nombre'] ?? '';
        $_SESSION['oldEspecie_vacunas'] = $_POST['especie'] ?? '';
        $_SESSION['oldId_vacunas'] = $_POST['id'] ?? '';

    }

    header('Location: ../views/veterinaria_vacunas.php');
    exit();
}
?>
