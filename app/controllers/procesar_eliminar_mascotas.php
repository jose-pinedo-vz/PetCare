<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

$mascota = Mascota::obtenerPorId($id);
if ($mascota === null || (int)($mascota['esta_activo'] ?? 1) !== 1) {
    errorSession('La mascota no existe o ya fue eliminada.', $id);
}

if (!Mascota::eliminar($id)) {
    errorSession('No se pudo eliminar la mascota. Intenta de nuevo.', $id);
}

header('Location: ../views/mascotas.php');
exit; 