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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';

    if ($accion === 'agregarVacuna') {

        $nombre = trim($_POST['nombre'] ?? '');
        $especie = filter_input(INPUT_POST, 'especie', FILTER_VALIDATE_INT);

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
        } else {
            $resultado = Veterinaria::agregarVacuna($nombre, $especie);
        }

    } elseif ($accion === 'editarVacuna') {

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nombre = trim($_POST['nombre'] ?? '');
        $especie = filter_input(INPUT_POST, 'especie', FILTER_VALIDATE_INT);

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
        }else {
            $resultado = Veterinaria::editarVacuna($id, $nombre, $especie);
        }

    } elseif ($accion === 'eliminarVacuna') {

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id <= 0) {
            $_SESSION['error'] = 'La vacuna no puede ser menor a 0';
        } else {
            $resultado = Veterinaria::eliminarVacuna($id);
        }
    }

    header('Location: ../views/veterinaria_vacunas.php');
    exit();
}
?>