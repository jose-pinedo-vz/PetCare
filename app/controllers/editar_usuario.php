<?php
session_start();
if (strtolower((string)($_SESSION['rol'] ?? '')) !== 'admin') {
    header("Location: ../views/inicio.php");
    exit();
}

require_once '../models/usuarios_db.php';
require_once '../models/conexion_db.php';

$rolesValidos = ['admin', 'gerente', 'veterinario', 'recepcionista', 'vendedor', 'estilista', 'hotelero', 'cajero'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
    $nuevo_usuario = trim($_POST['nuevo_usuario'] ?? '');
    $nueva_password = trim($_POST['nueva_password'] ?? '');
    $empleado_asociado = !empty($_POST['empleado_asociado']) ? (int)$_POST['empleado_asociado'] : null;
    $rol = strtolower(trim($_POST['rol'] ?? ''));
    $estado = strtolower(trim($_POST['estado'] ?? ''));

    $error = '';

    if ($id < 1) {
        $error = "ID de usuario no válido.";
    } elseif (empty($nuevo_usuario) || empty($rol) || empty($estado)) {
        $error = "Por favor, completa todos los campos obligatorios.";
    } elseif (strlen($nuevo_usuario) < 4) {
        $error = "El nombre de usuario debe tener al menos 4 caracteres.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $nuevo_usuario)) {
        $error = "El nombre de usuario solo puede contener letras, números y guiones bajos.";
    } elseif ($nueva_password !== '' && strlen($nueva_password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } elseif (!in_array($rol, $rolesValidos, true)) {
        $error = "El rol seleccionado no es válido.";
    } elseif (!in_array($estado, ['activo', 'inactivo'], true)) {
        $error = "El estado seleccionado no es válido.";
    } elseif (!UsuariosDB::validarCombinacionRolPuesto(ConexionDB::obtenerConexion(), $empleado_asociado, $rol)) {
        $error = "El rol no coincide con el puesto del empleado asociado.";
    } else {
        UsuariosDB::insertar($id, $nuevo_usuario, $nueva_password, $empleado_asociado, $rol, $estado);
        header("Location: ../views/usuarios.php");
        exit();
    }

    $_SESSION['error'] = $error;
    if ($id > 0) {
        header("Location: ../views/editar_usuarios.php?id_usuario=" . $id);
        exit();
    }
}

header("Location: ../views/usuarios.php");
exit();