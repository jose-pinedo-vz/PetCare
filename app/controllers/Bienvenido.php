<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: Login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$rol = $_SESSION['rol'];

// Carga la vista del inicio
require_once '../views/bienvenido.php';
