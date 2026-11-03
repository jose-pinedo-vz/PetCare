<?php
// hola
// hola 2
declare(strict_types=1);

require_once __DIR__ . '/../models/citas.php';
session_start();


function consultarFechas(string $fecha):array
{
    $datos = [];

    echo "llego " . $fecha;
    return [];

    if ("hoy" === $fecha)
    {

    }
    else if ("semana" === $fecha)
    {

    }
    else if ("mes" === $fecha)
    {

    }
    else if ("todos" === $fecha)
    {
        $datos = estraerDatosConsultas();
    }
    else if ("SemanaPasada" === $fecha)
    {

    }
    else if ("MesPasado" === $fecha)
    {

    }
    else
    {
        return [];
    }

    foreach ($datos as $citas)
    {
        $resultados[] =
        [
            'id' => $citas['id_cita'],
            'nombre' => $citas['nombre'],
            'fecha' => $citas['fecha'],
            'motivo' => $citas['motivo'],
            'atendido' => $citas['atendido']
        ];
    }

    return $resultados ?? [];
        
}

// intermediario entre front y base de datos
function estraerDatosConsultas(): array
{
    $resultados = [];
    $consultasCitas = new ConexionesClientes();
    $datos = $consultasCitas->datosCitas();

    foreach ($datos as $cita)
    {
        $resultados[] = [
            'id_consulta' => (int)$cita['id_cita'],         
            'nombre'      => $cita['nombre'] . ' ' . $cita['apellido'],
            'fecha'       => $cita['fecha'],
            'motivo'      => $cita['observacion'],           
            'atendido'    => (int)$cita['atendido']         
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
    // LÍNEA DE DEPURACIÓN TEMPORAL:
    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // exit(); // Detiene la ejecución aquí para ver qué datos llegaron realmente del formulario

    
    // datos 
    $claveCliente = trim($_POST['claveCliente'] ?? '');
    $mascota = trim($_POST['mascota'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');
    $duracion = trim($_POST['duracion'] ?? '');
    $servicio = trim($_POST['servicio'] ?? '');
    $motivoConsulta = trim($_POST['motivoConsulta'] ?? '');
    $responsable = trim($_POST['responsable'] ?? '');
    $horarioConsulta = trim($_POST['horarioConsulta'] ?? '');
    //$orararioConsulta = trim($_POST['horarioConsulta'] ?? $_POST['orararioConsulta'] ?? ''); 

    // validar campos obligatorios
    if ($claveCliente === "" || $fecha === "" || $motivoConsulta === "" || $horarioConsulta === "")
    {
        errorSession("Todos los campos marcados con asterisco son obligatorios.");
    }

    if ($mascota === "" || $duracion === "" || $servicio === "" || $responsable === "" || $horarioConsulta === "")
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
    $objetoHora = DateTime::createFromFormat('g:i A', $horarioConsulta);
    $horaLimpia = $objetoHora->format('H:i:s');
    $fechaHoraFinal = $fecha . ' ' . $horaLimpia;

    // echo "hola " . $fecha . "<br>". $horaLimpia . "<br>";

    // insertar la cita
    $seInserto = $consultasCitas->insertarCita((int)$claveCliente,(int)$mascota,(int)$servicio, (int) $responsable, "pendiente",(int) $duracion, $motivoConsulta, "sin atender", $fechaHoraFinal,  $motivoConsulta,(string) 0);
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
    $empleado = $_GET['empleado'] ?? '';
    $Horarios = [];
    $entradaMatutino = 9;
    $salidaMatutino = 13;
    $entradaVespertino = 16;
    $salidaVespertino = 18;
    $horasPorConsulta = 1;

    // echo "la fecha es" . $fechaSeleccionada;

    // $Veterinarios = ConexionesClientes::rescatarVeterinarios();
    //$Veterinarios = ConexionesClientes::rescatarVeterinarios();
    $Veterinarios = true;

    if ($Veterinarios)
    {
        // cantidad de veterinarios por turno
        // $cantMatutino = ConexionesClientes::cantidadHorarios("Matutino");
        // $cantVespertino = ConexionesClientes::cantidadHorarios("Vespertino");

        
        for($i = $entradaMatutino; $i<= $salidaMatutino; $i++)
        {
            $entrada = $i . ":00:00";
            $dateTime = DateTime::createFromFormat('H:i:s', $entrada);

            //distincion por fecha
            $cantidadCitasPorFecha =  ConexionesClientes::cantidadCitasFecha($fechaSeleccionada . " " . $dateTime->format('H:i:s'), $empleado);
            if ($cantidadCitasPorFecha === 0) $Horarios[] = $dateTime->format('g:i A');
        }
        

        for($i = $entradaVespertino; $i<= $salidaVespertino; $i++)
        {
            $entrada = $i . ":00:00";
            $dateTime = DateTime::createFromFormat('H:i:s', $entrada);
            
            $cantidadCitasPorFecha =  ConexionesClientes::cantidadCitasFecha($fechaSeleccionada . " " . $dateTime->format('H:i:s'), $empleado);
            if ($cantidadCitasPorFecha === 0) $Horarios[] = $dateTime->format('g:i A');
        }
    }

    echo json_encode($Horarios);   
    exit();
}



// mostrar mascotas segun el cliente que se inserto

$accion = $_GET['accion'] ?? '';
if ($accion === "mostrarMascota")
{
    mostrarMascotas();
}
function mostrarMascotas()
{
    $id_cliente = $_GET['id_cliente'];
    $mascotas =  ConexionesClientes::consultaMascota($id_cliente);

    echo json_encode($mascotas);
    exit();
}


// mostrar servicio existente
$accion = $_GET['accion'] ?? '';
if ($accion === "mostrarServicio")
{
    mostrarServicios();
}

function mostrarServicios()
{
    $servicios = ConexionesClientes::mostrarServicios();
    echo json_encode($servicios);
    exit();
}


$accion = $_GET['accion'] ?? '';
if ($accion === "mostrarResponsable")
{
    mostrarResponsable();
}
function mostrarResponsable()
{
    $servicio = $_GET['servicio'];
    $responsables = ConexionesClientes::mostrarServiciosPorServicio($servicio);

    echo json_encode($responsables);
    exit();
    }

$accion = $_GET['accion'] ?? '';
if ($accion === "cancelarCita")
{
    cancelarCita();
}

function cancelarCita()
{
    $idCita = $_GET['id_cita'];
    $res = ConexionesClientes::cancelarCita($idCita);

    echo json_encode($res);
    exit();
}




?>





