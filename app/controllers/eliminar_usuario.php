<?php
session_start();
require_once '../models/usuarios_db.php';

if (isset($_GET['id'])) {
    $id_usuario = $_GET['id'];
    $conexion = ConexionDB::obtenerConexion();

    try {
        UsuariosDB::eliminar($id_usuario);
        $_SESSION['mensaje']="Usuario eliminado con éxito.";
    }
    catch (PDOException $e)
    {
        $_SESSION['error'] = "Error al eliminar el usuario: " . $e->getMessage();
    }
}

header("Location: ../views/usuarios.php");
exit();