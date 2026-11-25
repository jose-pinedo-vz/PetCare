<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/citas.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function errorSession($mensaje, $id_cita = '')
{
    $_SESSION['error'] = $mensaje;
    $id_cita = (int)($_POST['id_cita'] ?? 0);  
    header("Location: ../views/servicioGeneral.php?id_consulta=" . $id_cita . "&error=" . urlencode($mensaje));
    exit();
}


if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    $id_cita = trim($_POST['id_cita'] ?? '');
    $observaciones = trim($_POST['observaciones'] ?? '');

    if ($id_cita === '') {
        errorSession("ID de cita no proporcionado.");
        exit();
    }

    if ($id_cita <= 0) {
        errorSession("Error: No se recibió un ID de cita válido.");
    }

    if ($observaciones === '') {
        errorSession("Todos los campos son obligatorios.");
        exit();
    }



    if (!preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,]+$/u', $observaciones)) 
    {
        errorSession("Los campos contienen caracteres no válidos.");
        exit();
    }


    $insercion = ConexionesClientes::actualizarCitaObservacino((int)$id_cita, $observaciones);


    if (!$insercion) {
        errorSession("Error al guardar la información. Por favor, inténtelo de nuevo.");
        exit();
    }

    ConexionesClientes::marcarCitaAtendida((int)$id_cita);

    // actualizar consulta

    header("Location: ../views/citas.php");
    exit();
}
?>