<?php
// hola
// hola 2
declare(strict_types=1);
require_once __DIR__ . '/../models/citas.php';
// session_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function errorSession($mensaje)
{
    $_SESSION['error'] = $mensaje;

    $_SESSION['oldTipo'] = trim($_POST['Tipo'] ?? '');
    $_SESSION['oldProductoUtilizado'] = trim($_POST['ProductoUtilizado'] ?? '');
    $_SESSION['oldFecha'] = trim($_POST['fecha'] ?? '');
    $_SESSION['oldDicis'] = trim($_POST['Dicis'] ?? '');
    $_SESSION['oldObservaciones'] = trim($_POST['observaciones'] ?? '');


    $id_cita = (int)($_POST['id_cita'] ?? 0);  
    header("Location: ../views/desparasitacion.php?id_consulta=" . $id_cita . "&error=" . urlencode($mensaje));
    exit();
}



if ($_SERVER["REQUEST_METHOD"] === "POST")
{

    $id_cita = $_POST['id_cita'] ?? '';

    // datos 
    $TipoDesparacitacion = trim($_POST['Tipo'] ?? '');
    $ProductoUtilizado = trim($_POST['ProductoUtilizado'] ?? '');
    $ProximaCita = trim($_POST['fecha'] ?? '');
    $Docies = trim($_POST['Dicis'] ?? '');
    $Observaciones = trim($_POST['observaciones'] ?? '');

    if ($TipoDesparacitacion === '' || $ProductoUtilizado === '' || $ProximaCita === '' || $Docies === '') 
    {
        errorSession("Todos los campos son obligatorios.");
        header("Location: ../views/desparasitacion.php");
        exit();
    }


    // validaciones
    $d = DateTime::createFromFormat('Y-m-d', $ProximaCita);
    if (!$d || $d->format('Y-m-d') !== $ProximaCita)
    {
        errorSession("El formato de la fecha no es válido.");
        header("Location: ../views/desparasitacion.php");
        exit();
    }

    $fechaActual = date('Y-m-d');
    if ($ProximaCita < $fechaActual)
    {
        errorSession("Verifique la fecha por favor. Debe ingresar una fecha de hoy en adelante.");
        header("Location: ../views/desparasitacion.php");
        exit();
    }

    if (filter_var($Docies, FILTER_VALIDATE_FLOAT) !== false) 
    {
        $Docies = (float)$Docies;
    } else {
        errorSession("El valor ingresado para Dosis debe ser un número válido.");
        header("Location: ../views/desparasitacion.php");
        exit();
    }

    if (!empty($Observaciones) && !preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,]+$/u', $Observaciones)) 
    {
        errorSession("Las observaciones contienen caracteres no válidos.");
        header("Location: ../views/desparasitacion.php");
        exit();
    }


    // validados
    $idEmpleado =  ConexionesClientes::consultarResponsablePorConsulta($id_cita);
    
    $insercion = ConexionesClientes::insertarDesparacitacion($id_cita, $TipoDesparacitacion, $ProductoUtilizado, $ProximaCita, $Docies, $idEmpleado, $Observaciones);
    if (!$insercion) {
        errorSession("Error al guardar la información. Por favor, inténtelo de nuevo.");
        header("Location: ../views/desparasitacion.php");
        exit();
    }

    ConexionesClientes::marcarCitaAtendida((int)$id_cita);

    header("Location: ../views/citas.php");
    exit();
}
?>