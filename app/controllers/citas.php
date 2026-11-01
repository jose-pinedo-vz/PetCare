<?php
// hola
// hola 2
declare(strict_types=1);

require_once __DIR__ . '/../models/citas.php';
session_start();


// intermediario entre front y base de datos
function estraerDatosConsultas():array
{
    $resultados = [];
    $consultasCitas = new ConexionesClientes();
    $datos = $consultasCitas->datosCitas();

    foreach ($datos as $cita)
    {
        $resultados[] =
        [
            'id_consulta' => (int)$cita['id_consulta'],
            'nombre' => $cita['nombre'].' '.$cita['apellido'],
            'fecha' => $cita['fecha'],
            'motivo' => $cita['motivo_de_consulta'],
            'atendido' => $cita['atendido']
        ];
    }
    return $resultados;
}



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



function errorSession(string $error)
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

    $orararioConsulta = trim($_POST['horarioConsulta'] ?? $_POST['orararioConsulta'] ?? ''); 

    // validar campos obligatorios
    if ($claveCliente === "" || $fecha === "" || $motivoConsulta === "" || $orararioConsulta === "")
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
        errorSession("El cliente con clave #$claveCliente no se encuentra registrado en el sistema.");
    }

    // Longitud minima y maxima
    $longitud = mb_strlen($motivoConsulta);
    if ($longitud < 5 || $longitud > 250)
    {
        errorSession("El motivo de la consulta debe tener entre 5 y 250 caracteres.");
    }


    // validacion de fecha
    $objetoHora = DateTime::createFromFormat('g:i A', $orararioConsulta);
    $horaLimpia = $objetoHora->format('H:i:s');
    $fechaHoraFinal = $fecha . ' ' . $horaLimpia;

    // echo "hola " . $fecha . "<br>". $horaLimpia . "<br>";

    // insertar la cita
    $seInserto = $consultasCitas->insertarCita((int)$claveCliente, $fechaHoraFinal, $motivoConsulta);
    if (!$seInserto)
    {
        errorSession("Hubo un error al registrar la cita en la base de datos.");
    }

    // insercin de la cita
    // $seInserto = $consultasCitas->insertarCita((int) $claveCliente,(string) $fecha,(string) $motivoConsulta);
    // if (!$seInserto)
    // {
    //     errorSession("Hubo un error en la insercino de los datos.");
    // }

    //$_SESSION['exito'] = "Cita agendada correctamente para el cliente #$claveCliente.";
    header("Location: ../views/citas.php?exito=1");
    exit();

}

$accion = $_GET['accion'] ?? '';

if ($accion === 'horario' || $accion === 'orario')
{
    horarios();
}

function horarios()
{
    $fechaSeleccionada = $_GET['fecha'] ?? '';

    $Horarios = [];

    $entradaMatutino = 9;
    $salidaMatutino = 15;
    $entradaVespertino = 15;
    $salidaVespertino = 20;
    $horasPorConsulta = 1;

    // echo "la fecha es" . $fechaSeleccionada;

    $Veterinarios = ConexionesClientes::rescatarVeterinarios();
    //$Veterinarios = ConexionesClientes::rescatarVeterinarios();

    if ($Veterinarios)
    {
        // cantidad de veterinarios por turno
        $cantMatutino = ConexionesClientes::cantidadHorarios("Matutino");
        $cantVespertino = ConexionesClientes::cantidadHorarios("Vespertino");

        if ($cantMatutino > 0)
        {
            for($i = $entradaMatutino; $i<= $salidaMatutino; $i++)
            {
                    $entrada = $i . ":00:00";
                    $dateTime = DateTime::createFromFormat('H:i:s', $entrada);

                    //distincion por fehca
                    $cantidadCitasPorFecha =  ConexionesClientes::cantidadCitasFecha($fechaSeleccionada . " " . $dateTime->format('H:i:s'));

                    //echo $cantidadCitasPorFecha . "<br>";

                    if ($cantidadCitasPorFecha < $cantMatutino) $Horarios[] = $dateTime->format('g:i A');
            }
        }

        if ($cantVespertino > 0)
        {
            for($i = $entradaVespertino; $i<= $salidaVespertino; $i++)
            {
                $entrada = $i . ":00:00";
                $dateTime = DateTime::createFromFormat('H:i:s', $entrada);
                

                $cantidadCitasPorFecha =  ConexionesClientes::cantidadCitasFecha($fechaSeleccionada . " " . $dateTime->format('H:i:s'));
                if ($cantidadCitasPorFecha < $cantVespertino) $Horarios[] = $dateTime->format('g:i A');
            }
        }
    }

    echo json_encode($Horarios);   
    exit();
}
?>
