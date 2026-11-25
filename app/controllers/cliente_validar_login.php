<?php
session_start();

$usuario = trim($_POST['usuario'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';

require_once __DIR__ . '/../models/Cliente.php';

$cliente = Cliente::obtenerPorUsuario($usuario);

$errores = [];

if ($cliente && password_verify($contrasena, $cliente['contrasena'])) {
    $_SESSION['id_cliente'] = $cliente['id_cliente'];
    $_SESSION['cliente_nombre'] = $cliente['nombre'];
    header('Location: ../views/cliente/mis_mascotas.php');
    exit;
} else {
    $errores[] = 'Usuario o contraseña incorrectos.';
    $_SESSION['errores'] = $errores;
    header('Location: ../views/cliente/iniciar_sesion.php');
    exit;
}