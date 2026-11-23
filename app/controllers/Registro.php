<?php
session_start();
if (strtolower((string)($_SESSION['rol'] ?? '')) !== 'admin') {
    header("Location: ../views/inicio.php");
    exit();
}

require_once '../models/conexion_db.php';
require_once '../models/usuarios_db.php';

$conexion = ConexionDB::obtenerConexion();
$mensaje = '';
$error = '';

$rolesValidos = ['admin', 'veterinario', 'recepcionista', 'vendedor', 'estilista', 'hotelero', 'cajero'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_usuario = trim($_POST['nuevo_usuario'] ?? '');
    $nueva_password = trim($_POST['nueva_password'] ?? '');
    $empleado_asociado = !empty($_POST['empleado_asociado']) ? (int)$_POST['empleado_asociado'] : null;
    $rol = strtolower(trim($_POST['rol'] ?? ''));
    $estado = strtolower(trim($_POST['estado'] ?? ''));

    if (empty($nuevo_usuario) || empty($nueva_password) || empty($rol) || empty($estado)) {
        $error = "Por favor, completa todos los campos obligatorios.";
    } elseif (strlen($nuevo_usuario) < 4) {
        $error = "El nombre de usuario debe tener al menos 4 caracteres.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $nuevo_usuario)) {
        $error = "El nombre de usuario solo puede contener letras, números y guiones bajos.";
    } elseif (strlen($nueva_password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } elseif (!in_array($rol, $rolesValidos, true)) {
        $error = "El rol seleccionado no es válido.";
    } elseif (!in_array($estado, ['activo', 'inactivo'], true)) {
        $error = "El estado seleccionado no es válido.";
    } elseif (!UsuariosDB::validarCombinacionRolPuesto($conexion, $empleado_asociado, $rol)) {
        $error = "El rol no coincide con el puesto del empleado asociado.";
    } else {
        $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
        try {
            ConexionDB::insertarUsuario($conexion, $nuevo_usuario, $password_hash, $empleado_asociado, $rol, $estado);
            $_SESSION['mensaje'] = "Usuario '$nuevo_usuario' creado con éxito.";
            header("Location: ../views/usuarios.php");
            exit();
        } catch (PDOException $e) {
            if ((string)$e->getCode() === '23000' && isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1062) {
                $error = "El nombre de usuario '$nuevo_usuario' ya existe.";
            } else {
                $error = "Error al guardar el usuario: " . $e->getMessage();
            }
        }
    }
}

if (!empty($error)) {
    $_SESSION['error'] = $error;
    header("Location: ../views/registro_usuario.php");
    exit();
}

header("Location: ../views/usuarios.php");
exit();