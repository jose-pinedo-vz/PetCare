<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/citas.php';
session_start();

function errorSession(string $error):null 
{
    $_SESSION['error'] = $error;
    $_SESSION['oldClave'] = $_POST['claveCliente'];
    $_SESSION['oldFecha'] = $_POST['fecha'];
    $_SESSION['oldMotivo'] = $_POST['motivoConsulta'];

    header("Location: ../views/agendarCita.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") 
{
    $claveCliente = trim($_POST['claveCliente'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');
    $motivoConsulta = trim($_POST['motivoConsulta'] ?? '');

    // validar campos completos
    if ($claveCliente === "" || $fecha === "" || $motivoConsulta === "") 
    {
        errorSession("Todos los campos son obligatorios.");
    }

    // formato correcto de la fecha
    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    if (!$d || $d->format('Y-m-d') != $fecha) 
    {
        errorSession("El formato de la fecha no es válido.");
    }

    // validar fecha
    $fechaActual = date('Y-m-d');
    if ($fecha < $fechaActual) 
    {
        errorSession("Verifique la fecha por favor. Tiene que ingresar una fecha valida.");   
    }

    // validar existencia del cliente
    $consultasCitas = new ConexionesClientes();
    $clienteExiste = $consultasCitas->consultarIdCliente((int) $claveCliente);
    if (!$clienteExiste) 
    {
        errorSession("Verifique el usuario. Tiene que ingresar un usuario existente.");   
    }

    // longitud del motivo de la  consulta
    $longitud = mb_strlen($motivoConsulta);
    if ($longitud < 5 || $longitud > 150) 
    {
        errorSession("El motivo de la consulta debe tener entre 5 y 250 caracteres.");   
    }


    // insercin de la cita 
    $seInserto = $consultasCitas->insertarCita((int) $claveCliente,(string) $fecha,(string) $motivoConsulta);
    if (!$seInserto)
    {
        errorSession("Hubo un error en la insercino de los datos.");
    }


    //session_destroy();
    // Si pasa la validación del backend, continúa a la BD...
    header("Location: ../views/citas.html");
    exit();
}
?>