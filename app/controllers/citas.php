<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/citas.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") 
{
    $claveCliente = trim($_POST['claveCliente'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');
    $motivoConsulta = trim($_POST['motivoConsulta'] ?? '');

    // validar campos completos
    if ($claveCliente === "" || $fecha === "" || $motivoConsulta === "")
    {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        $_SESSION['oldClave'] = $_POST['claveCliente'];
        $_SESSION['oldFecha'] = $_POST['fecha'];
        $_SESSION['oldMotivo'] = $_POST['motivoConsulta'];

        header("Location: ../views/agendarCita.php");
        exit();
    }

    // validar fecha
    $fechaActual = date('Y-m-d');

    if ($fecha < $fechaActual)
    {
        $_SESSION['error'] = "Verifique la fecha por favor. Tiene que ingresar una fecha valida.";
        $_SESSION['oldClave'] = $_POST['claveCliente'];
        $_SESSION['oldFecha'] = $_POST['fecha'];
        $_SESSION['oldMotivo'] = $_POST['motivoConsulta'];

        header("Location: ../views/agendarCita.php");
        exit();
    }

    // validar existencia del cliente
    $consultasCitas = new ConexionesClientes();
    $claveCliente = (int) $claveCliente;
    $clienteExiste = $consultasCitas->consultarIdCliente($claveCliente);

    if (!$clienteExiste)
    {
        $_SESSION['error'] = "Verifique el usuario. Tiene que ingresar un usuario existente.";
        $_SESSION['oldClave'] = $_POST['claveCliente'];
        $_SESSION['oldFecha'] = $_POST['fecha'];
        $_SESSION['oldMotivo'] = $_POST['motivoConsulta'];

        header("Location: ../views/agendarCita.php");
        exit();
    }
    


    //session_destroy();
    // Si pasa la validación del backend, continúa a la BD...
    header("Location: ../views/citas.html");
    exit();
}
?>