<?php
session_start();

require_once '../models/conexion_db.php';

$conexion = ConexionDB::obtenerConexion();
$action = $_GET['action'] ?? '';

// Cierre de sesión
if ($action === 'logout') {
    session_destroy();
    header("Location: Login.php");
    exit();
}

// Si ya hay sesión iniciada, redirige al inicio
if (isset($_SESSION['usuario'])) {
    header("Location: Bienvenido.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $datosUsuario = ConexionDB::buscarUsuarioPorNombre($conexion, $usuario);

    if ($datosUsuario) {
        if ($datosUsuario['estado'] !== 'activo') {
            $error = "La cuenta se encuentra inactiva.";
        } elseif (password_verify($password, $datosUsuario['contrasena'])) {
            ConexionDB::registrarUltimoAcceso($conexion, $datosUsuario['id_usuario']);

            $_SESSION['id_usuario'] = $datosUsuario['id_usuario'];
            $_SESSION['usuario'] = $usuario;
            $_SESSION['rol'] = $datosUsuario['rol'];

            header("Location: Bienvenido.php");
            exit();
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}

// Carga la vista de Login
require_once '../views/login.php';