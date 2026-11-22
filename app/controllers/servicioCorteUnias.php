<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/citas.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function errorSession($mensaje, $id_cita = '')
{
    $_SESSION['error'] = $mensaje;
    $url = "../views/corte_unias.php?error=" . urlencode($mensaje) . "&id_consulta=" . $id_cita;
    header("Location: " . $url);
    exit();
}


if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    //$id_cita = trim($_POST['id_consulta'] ?? '');
    $id_cita = trim($_POST['id_cita'] ?? '');
    $comportamiento = trim($_POST['comportamiento'] ?? '');
    $observaciones = trim($_POST['observaciones'] ?? '');

    if ($id_cita === '') {
        errorSession("ID de cita no proporcionado.", $id_cita);
        exit();
    }

    if ($id_cita <= 0) {
        errorSession("Error: No se recibió un ID de cita válido.", $id_cita);
        exit();
    }

    if ($comportamiento === '' || $observaciones === '') {
        errorSession("Todos los campos son obligatorios.", $id_cita);
        exit();
    }

    if (!preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,]+$/u', $comportamiento) || 
        !preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,]+$/u', $observaciones)) 
    {
        errorSession("Los campos contienen caracteres no válidos.", $id_cita);
        exit();
    }

    $tipo_servicio = "Corte de uñas";
    $precio = 0.00; 
    $estado = "1";    

    $insercion = ConexionesClientes::insertarCorteUnias(
        (int)$id_cita, 
        $tipo_servicio, 
        (float)$precio, 
        $observaciones, 
        $comportamiento, 
        (int)$estado
    );

    

    if (!$insercion) {
        errorSession("Error al guardar la información. Por favor, inténtelo de nuevo.", $id_cita);
        exit();
    }

    ConexionesClientes::marcarCitaAtendida((int)$id_cita);

    // actualizar consulta

    header("Location: ../views/citas.php");
    exit();
}
?>