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

        if ($nombre === '' || mb_strlen($nombre) > 50 || !$especie || $especie <= 0) {
            $_SESSION['error'] = 'Datos inválidos para agregar la vacuna.';
        } 

    } elseif ($accion === 'editarVacuna') {

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nombre = trim($_POST['nombre'] ?? '');
        $especie = filter_input(INPUT_POST, 'especie', FILTER_VALIDATE_INT);

        if (!$id || $id <= 0 || $nombre === '' || mb_strlen($nombre) > 50 || !$especie || $especie <= 0) {
            $_SESSION['error'] = 'Datos inválidos para editar la vacuna.';
        } 

    } elseif ($accion === 'eliminarVacuna') {

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id <= 0) {
            $_SESSION['error'] = 'Vacuna inválida.';
        }
    }

    header('Location: ../views/veterinaria_vacunas.php');
    exit();
}
?>