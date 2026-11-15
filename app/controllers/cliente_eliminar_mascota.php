<?php
session_start();

if (empty($_SESSION['id_cliente'])) {
    header('Location: ../views/cliente/iniciar_sesion.php');
    exit;
}

$idCliente = (int) $_SESSION['id_cliente'];
$idMascota = (int) ($_POST['id_mascota'] ?? 0);

require_once __DIR__ . '/../models/Mascota.php';
$mascota = Mascota::obtenerPorId($idMascota);

if (!$mascota || (int) $mascota['id_cliente'] !== $idCliente) {
    $_SESSION['errores'] = ['No se encontró la mascota.'];
    header('Location: ../views/cliente/mis_mascotas.php');
    exit;
}

Mascota::eliminar($idMascota);
header('Location: ../views/cliente/mis_mascotas.php');
exit;