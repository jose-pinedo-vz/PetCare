<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/citas.php';

session_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    // validar campos obligatorios
    if ($claveCliente === "" || $fecha === "" || $motivoConsulta === "") 
    {
        errorSession("Todos los campos marcados con asterisco son obligatorios.");
    }

    // validar que la clave de cliente sea numerica
    if (!ctype_digit($claveCliente) || (int)$claveCliente <= 0)
    {
        errorSession("La clave del cliente debe ser un número válido mayor a 0.");
    }

    // validar formato correcto de la fecha (YYYY-MM-DD)
    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    if (!$d || $d->format('Y-m-d') !== $fecha) 
    {
        errorSession("El formato de la fecha no es válido.");
    }

    // validar que no sea fecha pasada
    $fechaActual = date('Y-m-d');
    if ($fecha < $fechaActual) 
    {
        errorSession("Verifique la fecha por favor. Debe ingresar una fecha de hoy en adelante.");   
    }

    // verificar que el cliente exista en la base de datos
    $consultasCitas = new ConexionesClientes();
    $clienteExiste = $consultasCitas->consultarIdCliente((int)$claveCliente);
    if (!$clienteExiste) 
    {
        errorSession("El cliente con clave #$claveCliente no se encuentra registrado en el sistema. Registre al cliente primero.");   
    }

    // Longitud minima y maxima
    $longitud = mb_strlen($motivoConsulta);
    if ($longitud < 5 || $longitud > 250) 
    {
        errorSession("El motivo de la consulta debe tener entre 5 y 250 caracteres.");   
    }



    // insercin de la cita 
    $seInserto = $consultasCitas->insertarCita((int) $claveCliente,(string) $fecha,(string) $motivoConsulta);
    if (!$seInserto)
    {
        errorSession("Hubo un error en la insercino de los datos.");
    }


    // insertar la cita
    $seInserto = $consultasCitas->insertarCita((int)$claveCliente, $fecha, $motivoConsulta);
    if (!$seInserto)
    {
        errorSession("Hubo un error al registrar la cita en la base de datos.");
    }
    

    //$_SESSION['exito'] = "Cita agendada correctamente para el cliente #$claveCliente.";
    header("Location: ../views/agendarCita.php?exito=1");
    exit();

}
?>