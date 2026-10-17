<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: Login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$rol = $_SESSION['rol'];

// Carga la vista a mascotas
require_once '../views/mascotas.php';
