<?php
// hola
// hola 2
declare(strict_types=1);
date_default_timezone_set('America/Mexico_City');

require_once __DIR__ . '/../models/citas.php';
// session_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    exit();
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
            'nombre' => $cita['nombre'] . ' ' . $cita['apellido'],
            'fecha' => $cita['fecha'],
            'motivo' => $cita['observacion'],           
            'estado_cita' => $cita['estado_cita'],
            'servicio' => $cita['servicio'],
            'empleado' => $cita['empleado'],
            'estado' => $cita['estado'],     

        ];
    }
    return $resultados;
}


function errorSession(string $error)
{
    $_SESSION['error'] = $error;

    $_SESSION['oldClave'] = $_POST['cliente'] ?? '';
    $_SESSION['oldFecha'] = $_POST['fecha'] ?? '';
    $_SESSION['oldMotivo'] = $_POST['motivoConsulta'] ?? '';
    $_SESSION['oldHorario'] = $_POST['horarioConsulta'] ?? '';
    $_SESSION['oldMascota'] = $_POST['mascota'] ?? '';
    $_SESSION['oldDuracion'] = $_POST['duracion'] ?? '';
    $_SESSION['oldServicio'] = $_POST['servicio'] ?? '';
    $_SESSION['oldResponsable'] = $_POST['responsable'] ?? '';

    header("Location: ../views/agendarCita.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && (($_GET['accion'] ?? '') === 'agendar_cita'))
{
    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // exit(); // Detiene la ejecución aquí para ver qué datos llegaron realmente del formulario

    
    // datos 
    $claveCliente = trim($_POST['cliente'] ?? '');
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

    $fechaMaxima = date('Y-m-d', strtotime('+1 year'));
    if ($fecha > $fechaMaxima)
    {
        errorSession("La fecha no puede ser mayor a un año en adelante.");
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
    try
    {
        $objetoHora = DateTime::createFromFormat('g:i A', $horarioConsulta);
        $horaLimpia = $objetoHora->format('H:i:s');
        $fechaHoraFinal = $fecha . ' ' . $horaLimpia;
    }
    catch (Exception $e)
    {
        errorSession("La fecha intregada se enceunta mal. Revise formato.");
    }
    

    // echo "hola " . $fecha . "<br>". $horaLimpia . "<br>";

    $HayMasHoras = $consultasCitas->hayMasCitas((int)$responsable, $fechaHoraFinal, (int)$mascota, (int)$claveCliente);
    if ($HayMasHoras === true)
    {
        errorSession("Ya hay una consulta registrada para este horario (el responsable, el cliente o la mascota ya tienen cita a esta hora).");
        exit();
    }

    try
    {
        $seInserto = $consultasCitas->insertarCita((int)$claveCliente,(int)$mascota,(int)$servicio, (int) $responsable, "pendiente",(int) $duracion, $motivoConsulta, "sin atender", $fechaHoraFinal,  $motivoConsulta,(string) 0);
    }
    catch (Throwable $e)
    {
        errorSession("Se encontro un error en la insercion de los datos, revise sus datos, intente de nuevo o consulte a un profecional.");
    }

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
    // primero, tengo que encontrar la fomra de contjltar la cita, responsable y horario
    // despues saco el horario
    // lo agrego a una lista y eso ya esta, despues tengo que asegurarme de si ese dia ahay citas
    // ocultar al ahora a la que hay citas

    // una ves logrado eso, tengo que agregar una opcion para sumarle el tiempo
     // de demora a esa hora empesada, y el proximo horario a mostrar
    // sera cuando el termine, por ejempllo, cita a las 11 duracion de 40 minutos, proximo horario
    // a las 11:40, y el proximo de ese que saa a las 12, pero si yo agarro 11:40
    // con media hora entonces seria 12:30. sera complicado, espero terminarlo en esta tarde

    $fechaSeleccionada = $_GET['fecha'] ?? '';
    $empleado = (int)($_GET['responsable'] ?? 0);
    $Horarios = [];
    $entradaMatutino = 9;
    $salidaMatutino = 13; // aqui al mostrarse hasta la una como ultima hora, es a proposito, no hay error, la una es la utlima hora que se muestra, par que no reserves a las 2
    $entradaVespertino = 16;
    $salidaVespertino = 18;
    $horasPorConsulta = 1;

    // echo "la fecha es" . $fechaSeleccionada;

    // $Veterinarios = ConexionesClientes::rescatarVeterinarios();
    //$Veterinarios = ConexionesClientes::rescatarVeterinarios();
    $Veterinarios = true;

    $arrayConFechas = ConexionesClientes::citasDelDia($fechaSeleccionada, $empleado);

    if ($Veterinarios)
    {
        $currentMatutino = DateTime::createFromFormat('H:i:s', '09:00:00');
        $limiteMatutino = DateTime::createFromFormat('H:i:s', '13:00:00');
        while ($currentMatutino < $limiteMatutino)
        {
            $fechaHoraActual = $fechaSeleccionada . " " . $currentMatutino->format('H:i:s');
            $hora = $currentMatutino->format('H:i:s'); 
            //$cantidadCitasPorFecha = ConexionesClientes::cantidadCitasFecha($fechaHoraActual, $empleado);

            if (isset($arrayConFechas[$hora]))
            {
                //$duracion = (int) ConexionesClientes::duracionCita($fechaHoraActual, $empleado);
                //$duracionMinutos = ($duracion > 0) ? $duracion : 60;\
                //$duracion = ($arrayConFechas[$hora] > 0) ? $arrayConFechas[$hora] : 60;
                $duracionMinutos = ($arrayConFechas[$hora] > 0) ? $arrayConFechas[$hora] : 60;
                $currentMatutino->modify("+{$duracionMinutos} minutes");
            }
            else
            {
                $Horarios[] = $currentMatutino->format('g:i A');
                if ((int)$currentMatutino->format('i') > 0) {
                    $currentMatutino->setTime((int)$currentMatutino->format('H'), 0, 0);
                    $currentMatutino->modify('+1 hour');
                } else {
                    $currentMatutino->modify('+1 hour');
                }
            }
        }

        $currentVespertino = DateTime::createFromFormat('H:i:s', '16:00:00');
        $limiteVespertino = DateTime::createFromFormat('H:i:s', '19:00:00');
        while ($currentVespertino < $limiteVespertino)
        {
            $fechaHoraActual = $fechaSeleccionada . " " . $currentVespertino->format('H:i:s');
            $hora = $currentVespertino->format('H:i:s');
            //$cantidadCitasPorFecha = ConexionesClientes::cantidadCitasFecha($fechaHoraActual, $empleado);

            if (isset($arrayConFechas[$hora]))
            {
                //$duracion = (int)ConexionesClientes::duracionCita($fechaHoraActual, $empleado);
                //$hora = $currentVespertino->format('H:i:s');
                $duracionMinutos = ($arrayConFechas[$hora] > 0) ? $arrayConFechas[$hora] : 60;
                
                $currentVespertino->modify("+{$duracionMinutos} minutes");
            }
            else
            {
                $Horarios[] = $currentVespertino->format('g:i A');
                
                if ((int)$currentVespertino->format('i') > 0) {
                    $currentVespertino->setTime((int)$currentVespertino->format('H'), 0, 0);
                    $currentVespertino->modify('+1 hour');
                } else {
                    $currentVespertino->modify('+1 hour');
                }
            }
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
    $id_cliente = (int)($_GET['id_cliente'] ?? 0);
    $consultasCitas = new ConexionesClientes();
    $clienteExiste = $consultasCitas->consultarIdCliente($id_cliente);
    if (!$clienteExiste)
    {
        echo json_encode(['error' => 'cliente_no_existe']);
        exit();
    }
    $mascotas = ConexionesClientes::consultaMascota($id_cliente);

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
// editar para mostrar servicio
function mostrarResponsable()
{
    $servicio = $_GET['servicio'];

    $servicio = ConexionesClientes::consultarServicioPorCategoria($servicio);
    $equivalente = '';

    $servicio = $servicio['categoria'] ?? '';

    if ($servicio === 'Estética') $equivalente = 'Estilista';
    if ($servicio === 'Salud') $equivalente = 'Veterinario';

    $responsables = ConexionesClientes::consultarEmpleadoPorPuesto($equivalente);

    echo json_encode($responsables);
    exit();
}



// a tratar
$accion = $_GET['accion'] ?? '';
if ($accion === "cancelarCita")
{
    cancelarCita();
}
function cancelarCita()
{
    $idCita = $_GET['id_cita'] ?? '';
    $res = ConexionesClientes::cancelarCita($idCita);

    echo json_encode($res);
    exit();
}

$accion = $_GET['accion'] ?? '';
if ($accion === "noAsistio")
{
    noAsistio();
}
function noAsistio()
{
    $idCita = (int)($_GET['id_cita'] ?? 0);              
    $res = ConexionesClientes::noAsistioCita($idCita);

    if (!$res) { http_response_code(500); }            
    echo json_encode($res);
    exit();
}



function consultarServicio(int $idServicio)
{
    $servicio = ConexionesClientes::consultarServicio($idServicio);
    return $servicio;
}

function consultarResponsable(int $idResponsable)
{
    $responsable = ConexionesClientes::consultarResponsable($idResponsable);
    return $responsable;
}

function consultarMascota(int $idMascota)
{
    $mascota = ConexionesClientes::consultarMascota($idMascota);
    return $mascota;
}


function obtenerNombreResponsable($idCita)
{
    $datosCita = ConexionesClientes::obtenerResponsable((int)$idCita);
    if (!$datosCita || empty($datosCita['empleado'])) {
        return null;
    }
    $idEmpleado = (int)$datosCita['empleado'];
    $persona = ConexionesClientes::consultarResponsable($idEmpleado);

    if (!$persona || !is_array($persona)) {
        return null;
    }

    return $persona['nombre'] . " " . $persona["apellido"];
}


function erroresDeReprogramarCitas(string $error, $nuevaFecha, $responsable, $nuevoHorario, $idConsulta)
{
    $_SESSION['error'] = $error;

    $_SESSION['oldFecha'] = $nuevaFecha;
    $_SESSION['oldResponsable'] = $responsable;
    $_SESSION['oldHorario'] = $nuevoHorario;

    

    header("Location: ../views/reprogramar_cita.php?id_consulta=" . $idConsulta);
    exit();
}

function actualizarConsulta($idConsulta, $nuevaFecha, $responsable, $nuevoHorario)
{
    // validaciones 
    if ($idConsulta === '' || $nuevaFecha === '' || $responsable === '' || $nuevoHorario === '')
    {
        erroresDeReprogramarCitas("Todos los campos son obligatorios.", $nuevaFecha, $responsable, $nuevoHorario, $idConsulta);
    }

    $objetoHora = DateTime::createFromFormat('g:i A', $nuevoHorario);
    if (!$objetoHora) 
    {
        erroresDeReprogramarCitas("Formato de hora inválido.", $nuevaFecha, $responsable, $nuevoHorario, $idConsulta);
    }

    $fechaActual = date('Y-m-d');
    if ($nuevaFecha < $fechaActual)
    {
        erroresDeReprogramarCitas("Verifique la fecha por favor. Debe ingresar una fecha de hoy en adelante.", $nuevaFecha, $responsable, $nuevoHorario, $idConsulta);
    }

    $fechaMaxima = date('Y-m-d', strtotime('+1 year'));
    if ($nuevaFecha > $fechaMaxima)
    {
        erroresDeReprogramarCitas("La fecha no puede ser mayor a un año en adelante.", $nuevaFecha, $responsable, $nuevoHorario, $idConsulta);
    }

    // juntar fecha con horas 
    $horaLimpia = $objetoHora->format('H:i:s');
    $fechaHoraFinal = $nuevaFecha . ' ' . $horaLimpia;  

    
    $consultasCitas = new ConexionesClientes();
    $citaDetalle = ConexionesClientes::obtenerDetalleCita((int)$idConsulta);
    $idMascota = $citaDetalle ? (int)$citaDetalle['mascota'] : null;
    $idCliente = $citaDetalle ? (int)$citaDetalle['cliente'] : null;
    $HayMasHoras = $consultasCitas->hayMasCitas((int)$responsable, $fechaHoraFinal, $idMascota, $idCliente, (int)$idConsulta);
    if ($HayMasHoras)
    {
        erroresDeReprogramarCitas("Ya hay una consulta registrada para este horario (el responsable, el cliente o la mascota ya tienen cita a esta hora).", $nuevaFecha, $responsable, $nuevoHorario, $idConsulta);
        exit();
    }

    // actualisar en base  datos
    $resultado = ConexionesClientes::actualizarCita((int)$idConsulta, (string)$fechaHoraFinal);

    if (!$resultado) 
    {
        erroresDeReprogramarCitas("Error al actualizar la cita en la base de datos.", $nuevaFecha, $responsable, $nuevoHorario, $idConsulta);
    }
    header("Location: ../views/citas.php");
    exit();
}


$accion = $_GET['accion'] ?? '';
if ($accion === "mostrarDuracion")
{
    duracionServicio();
}

function duracionServicio()
{
    $idServicio = $_GET['servicio' ?? ''];
    $res = ConexionesClientes::consutltarDuracinoServicio($idServicio);
    header('Content-Type: application/json');
    echo json_encode(['duracion' => $res]);
    exit();
}


function serviciosIdYNombre()
{
    return ConexionesClientes::serviciosIdYNombreModel();
}

function responsablesNombreEId()
{
    return ConexionesClientes::ReponsableIdNombre();
}

function nombreDelUsuario(string $usuario)
{
    return ConexionesClientes::empleadoDelUsuario($usuario);
}

function MostrarnombreCliente(int $id_cliente)
{
    return ConexionesClientes::nombreCliente($id_cliente);
}


$accion = $_GET['accion'] ?? '';

if ($accion === "datos_Servicio")
{
    $id = (int)$_GET['id_Servicio'] ?? '';
    echo json_encode(ConexionesClientes::datosServicio($id));
    exit();
}


$accion = $_GET['accion'] ?? '';

if ($accion === "mostrarCliente")
{
    //$id = (int)$_GET['id_Servicio'] ?? '';
    echo json_encode(ConexionesClientes::idConCliente());
    exit();
}

?>