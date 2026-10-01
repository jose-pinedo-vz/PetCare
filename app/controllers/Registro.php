<?php
session_start();

require_once '../models/conexion_db.php';

// Control de acceso: solo administradores
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: Bienvenido.php");
    exit();
}

$conexion = ConexionDB::obtenerConexion();
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_usuario = trim($_POST['nuevo_usuario'] ?? '');
    $nueva_password = trim($_POST['nueva_password'] ?? '');
    $empleado_asociado = !empty($_POST['empleado_asociado']) ? $_POST['empleado_asociado'] : null;
    $rol = $_POST['rol'] ?? 'usuario';
    $permisos = $_POST['permisos'] ?? 'todos';
    $estado = $_POST['estado'] ?? 'activo';

    if (!empty($nuevo_usuario) && !empty($nueva_password)) {
        $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
        try {
            ConexionDB::insertarUsuario($conexion, $nuevo_usuario, $password_hash, $empleado_asociado, $rol, $permisos, $estado);
            $mensaje = "Usuario '$nuevo_usuario' creado con éxito.";
        } catch (PDOException $e) {
            if ($e->getCode() === 1062) {
                $error = "El nombre de usuario '$nuevo_usuario' ya existe.";
            } else {
                $error = "Error al guardar el usuario: " . $e->getMessage();
            }
        }
    } else {
        $error = "Por favor completa los campos obligatorios.";
    }
}

// Carga la vista de Registro
require_once '../views/registro.php';
