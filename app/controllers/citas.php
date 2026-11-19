<?php
// hola
// hola 2
declare(strict_types=1);

require_once __DIR__ . '/../models/citas.php';
// session_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
            'nombre_servicio' => $cita['nombre_servicio'] ?? '',     
        ];
    }
    return $resultados;
}


function errorSession(string $error)
{
    $_SESSION['error'] = $error;

    $_SESSION['oldClave'] = $_POST['claveCliente'] ?? '';
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

    // validar que no sea fecha pasada ni excesivamente futura (maximo 1 año)
    $fechaActual = date('Y-m-d');
    $fechaMaxima = date('Y-m-d', strtotime('+1 year'));
    if ($fecha < $fechaActual)
    {
        errorSession("Verifique la fecha por favor. Debe ingresar una fecha de hoy en adelante.");
    }
    if ($fecha > $fechaMaxima)
    {
        errorSession("La fecha de la cita no puede exceder el límite permitido (máximo un año en adelante: $fechaMaxima).");
    }

    // verificar que el cliente exista en la base de datos
    $consultasCitas = new ConexionesClientes();
    $clienteExiste = $consultasCitas->consultarIdCliente((int)$claveCliente);
    if (!$clienteExiste)
    {
        errorSession("El cliente con clave #$claveCliente no se encuentra registrado en el sistema.");
    }

    // validar que la mascota sea numérica, exista y pertenezca al cliente especificado
    if (!ctype_digit($mascota) || (int)$mascota <= 0)
    {
        errorSession("Debe seleccionar una mascota válida.");
    }
    if (!ConexionesClientes::validarMascotaCliente((int)$mascota, (int)$claveCliente))
    {
        errorSession("La mascota seleccionada no pertenece al cliente especificado o no se encuentra activa.");
    }

    // validar servicio y responsable
    if (!ctype_digit($servicio) || (int)$servicio <= 0)
    {
        errorSession("Debe seleccionar un servicio válido.");
    }
    if (!ctype_digit($responsable) || (int)$responsable <= 0)
    {
        errorSession("Debe seleccionar un responsable válido.");
    }

    // validar duración estimada (positiva, entre 1 y 480 minutos)
    if (!ctype_digit($duracion) || (int)$duracion <= 0 || (int)$duracion > 480)
    {
        errorSession("La duración estimada debe ser un número entero válido en minutos (entre 1 y 480).");
    }
    $duracionServicio = ConexionesClientes::consutltarDuracinoServicio((int)$servicio);
    if ($duracionServicio !== null && (int)$duracion !== (int)$duracionServicio)
    {
        errorSession("La duración especificada no coincide con la duración oficial del servicio.");
    }

    // Longitud minima y maxima
    $longitud = mb_strlen($motivoConsulta);
    if ($longitud < 5 || $longitud > 250)
    {
        errorSession("El motivo de la consulta debe tener entre 5 y 250 caracteres.");
    }

    // validacion de formato y disponibilidad de horario
    $objetoHora = DateTime::createFromFormat('g:i A', $horarioConsulta);
    if (!$objetoHora)
    {
        errorSession("El horario de consulta no tiene un formato válido.");
    }

    $horariosDisponibles = calcularHorariosDisponibles($fecha, (string)$responsable);
    if (!in_array($horarioConsulta, $horariosDisponibles, true))
    {
        errorSession("El horario seleccionado ($horarioConsulta) no está disponible o se encuentra fuera del horario de atención.");
    }

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
function calcularHorariosDisponibles(string $fechaSeleccionada, string $empleado = ''): array
{
    $Horarios = [];
    if (empty($fechaSeleccionada)) {
        return $Horarios;
    }

    $currentMatutino = DateTime::createFromFormat('H:i:s', '09:00:00');
    $limiteMatutino = DateTime::createFromFormat('H:i:s', '13:00:00');
    while ($currentMatutino < $limiteMatutino)
    {
        $fechaHoraActual = $fechaSeleccionada . " " . $currentMatutino->format('H:i:s');
        $cantidadCitasPorFecha = ConexionesClientes::cantidadCitasFecha($fechaHoraActual, $empleado);

        if ($cantidadCitasPorFecha > 0)
        {
            $duracion = (int) ConexionesClientes::duracionCita($fechaHoraActual, $empleado);
            $duracionMinutos = ($duracion > 0) ? $duracion : 60;
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
        $cantidadCitasPorFecha = ConexionesClientes::cantidadCitasFecha($fechaHoraActual, $empleado);

        if ($cantidadCitasPorFecha > 0)
        {
            $duracion = (int)ConexionesClientes::duracionCita($fechaHoraActual, $empleado);
            $duracionMinutos = ($duracion > 0) ? $duracion : 60;
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

    return $Horarios;
}

function horarios()
{
    $fechaSeleccionada = $_GET['fecha'] ?? '';
    $empleado = $_GET['responsable'] ?? $_GET['empleado'] ?? '';
    $Horarios = calcularHorariosDisponibles((string)$fechaSeleccionada, (string)$empleado);

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

    $d = DateTime::createFromFormat('Y-m-d', $nuevaFecha);
    if (!$d || $d->format('Y-m-d') !== $nuevaFecha)
    {
        erroresDeReprogramarCitas("El formato de la fecha no es válido.", $nuevaFecha, $responsable, $nuevoHorario, $idConsulta);
    }

    $fechaActual = date('Y-m-d');
    $fechaMaxima = date('Y-m-d', strtotime('+1 year'));
    if ($nuevaFecha < $fechaActual)
    {
        erroresDeReprogramarCitas("Verifique la fecha por favor. Debe ingresar una fecha de hoy en adelante.", $nuevaFecha, $responsable, $nuevoHorario, $idConsulta);
    }
    if ($nuevaFecha > $fechaMaxima)
    {
        erroresDeReprogramarCitas("La fecha de la cita no puede exceder el límite permitido (máximo un año en adelante: $fechaMaxima).", $nuevaFecha, $responsable, $nuevoHorario, $idConsulta);
    }

    $objetoHora = DateTime::createFromFormat('g:i A', $nuevoHorario);
    if (!$objetoHora) 
    {
        erroresDeReprogramarCitas("Formato de hora inválido.", $nuevaFecha, $responsable, $nuevoHorario, $idConsulta);
    }

    $horariosDisponibles = calcularHorariosDisponibles($nuevaFecha, (string)$responsable);
    if (!in_array($nuevoHorario, $horariosDisponibles, true))
    {
        erroresDeReprogramarCitas("El horario seleccionado ($nuevoHorario) no está disponible o se encuentra fuera del horario de atención.", $nuevaFecha, $responsable, $nuevoHorario, $idConsulta);
    }

    // juntar fecha con horas 
    $horaLimpia = $objetoHora->format('H:i:s');
    $fechaHoraFinal = $nuevaFecha . ' ' . $horaLimpia;
    $fecha = $horaLimpia . " " . $nuevaFecha;

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
    $idServicio = $_GET['servicio'];
    $res = ConexionesClientes::consutltarDuracinoServicio($idServicio);
    header('Content-Type: application/json');
    echo json_encode(['duracion' => $res]);
    exit();
}






