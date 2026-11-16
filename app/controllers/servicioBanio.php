<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/citas.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function errorSession($mensaje, $id_cita)
{
    $_SESSION['error'] = $mensaje;
    $url = "../views/servicioBanio.php?error=" . urlencode($mensaje) . "&id_consulta=" . $id_cita;
    header("Location: " . $url);
    exit();
}


if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    $id_cita = trim($_POST['id_cita'] ?? '');
    $tipoBanio = trim($_POST['Tipo'] ?? '');
    // $tipoBanio = trim($_POST['Tipo'] ?? '');
    $Shampoo = trim($_POST['Shampoo'] ?? '');
    $TratamientoAdicional = trim($_POST['TratamientoAdicional'] ?? '');
    $Cepillado = trim($_POST['Cepillado'] ?? ''); 
    $Observaciones = trim($_POST['observaciones'] ?? ''); 
    $Condiciones_recibidas = trim($_POST['Condiciones_recibidas'] ?? ''); 
    $Condiciones_entrega = trim($_POST['Condiciones_entrega'] ?? ''); 
    $Secado = trim($_POST['Secado'] ?? ''); 
    // $Cepillado = trim($_POST['Cepillado'] ?? '');

    //$Deslanado = trim($_POST['Deslanado'] ?? ''); 
    if ($id_cita === '') {
        errorSession("ID de cita no proporcionado.", $id_cita);
        exit();
    }

    if ($id_cita <= 0) {
        errorSession("Error: No se recibió un ID de cita válido.", $id_cita);
        exit();
    }

    if ($tipoBanio === '' || $Shampoo === '' || $TratamientoAdicional === '' || $Cepillado === '' || $Observaciones === '' || $Condiciones_recibidas === '' || $Condiciones_entrega === '' || $Secado === '') {
        errorSession("Todos los campos son obligatorios.", $id_cita);
        exit();
    }

    if (!preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,]+$/u', $Observaciones)) 
    {
        errorSession("Los campos contienen caracteres no válidos.", $id_cita);
        exit();
    }

    if (!preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,]+$/u', $Condiciones_entrega)) 
    {
        errorSession("Los campos contienen caracteres no válidos.", $id_cita);
        exit();
    }

    if (!preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,]+$/u', $Condiciones_recibidas))
    {
        errorSession("Los campos contienen caracteres no válidos.", $id_cita);
        exit();
    }

    $estado = "En proceso"; 
    $precio = 150.00;       

    $insercion = ConexionesClientes::insertarBano(
        (int)$id_cita, 
        $tipoBanio, 
        $Shampoo, 
        $TratamientoAdicional, 
        $Cepillado, 
        $Secado, 
        (float)$precio, 
        $estado, 
        $Observaciones, 
        $Condiciones_recibidas, 
        $Condiciones_entrega
    );

    if (!$insercion) {
        errorSession("Error al guardar la información del baño.", (int)$id_cita);
        exit();
    }
    

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