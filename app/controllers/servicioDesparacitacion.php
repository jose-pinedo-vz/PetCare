<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/citas.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function errorSession($mensaje, $id_cita = 0)
{
    $_SESSION['error'] = $mensaje;
    $url = "../views/desparasitacion.php?error=" . urlencode($mensaje);
    if ((int)$id_cita > 0) {
        $url .= "&id_consulta=" . (int)$id_cita;
    }
    header("Location: " . $url);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    $id_cita = (int)($_POST['id_cita'] ?? 0);

    // datos 
    $TipoDesparacitacion = trim($_POST['Tipo'] ?? '');
    $ProductoUtilizado = trim($_POST['ProductoUtilizado'] ?? '');
    $ProximaCita = trim($_POST['fecha'] ?? '');
    $Docies = trim($_POST['dosis'] ?? $_POST['Dosis'] ?? $_POST['Dicis'] ?? '');
    $Observaciones = trim($_POST['observaciones'] ?? '');


    $_SESSION['oldDosis'] = $Docies;
    $_SESSION['oldDicis'] = $Docies;
    $_SESSION['oldFecha'] = $ProximaCita;
    $_SESSION['oldObservaciones'] = $Observaciones;
    $_SESSION['oldTipo'] = $TipoDesparacitacion;
    $_SESSION['oldProducto'] = $ProductoUtilizado;

    if ($id_cita <= 0) {
        errorSession("ID de cita no proporcionado o inválido.", $id_cita);
        exit();
    }

    if ($TipoDesparacitacion === '' || $ProductoUtilizado === '' || $Docies === '') 
    {
        errorSession("Todos los campos obligatorios deben ser completados.", $id_cita);
        exit();
    }


    if ($ProximaCita !== '') {
        $d = DateTime::createFromFormat('Y-m-d', $ProximaCita);
        if (!$d || $d->format('Y-m-d') !== $ProximaCita)
        {
            errorSession("El formato de la fecha no es válido.", $id_cita);
            exit();
        }

        $fechaActual = date('Y-m-d');
        if ($ProximaCita < $fechaActual)
        {
            errorSession("Verifique la fecha por favor. Debe ingresar una fecha de hoy en adelante.", $id_cita);
            exit();
        }
    } else {
        $ProximaCita = null;
    }

    if (filter_var($Docies, FILTER_VALIDATE_FLOAT) !== false) 
    {
        $Docies = (float)$Docies;
        if ($Docies <= 0) {
            errorSession("El valor ingresado para Dosis debe ser mayor a 0.", $id_cita);
            exit();
        }
    } else {
        errorSession("El valor ingresado para Dosis debe ser un número válido.", $id_cita);
        exit();
    }

    if (!empty($Observaciones) && !preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,\-_]+$/u', $Observaciones)) 
    {
        errorSession("Las observaciones contienen caracteres no válidos.", $id_cita);
        exit();
    }

    
    $idEmpleado = ConexionesClientes::consultarResponsablePorConsulta($id_cita);
    if (empty($idEmpleado) || (int)$idEmpleado <= 0) {
        $idEmpleado = $_SESSION['id_empleado'] ?? $_SESSION['empleado_asociado'] ?? null;
    }
    if (empty($idEmpleado) || (int)$idEmpleado <= 0) {
        $veterinarios = ConexionesClientes::consultarEmpleadoPorPuesto('Veterinario');
        if (!empty($veterinarios)) {
            $idEmpleado = (int)$veterinarios[0]['id_empleado'];
        }
    }

    
    if (!empty($idEmpleado) && (int)$idEmpleado > 0) {
        try {
            $db = ConexionDB::obtenerConexion();
            $upd = $db->prepare("UPDATE citas SET empleado = ? WHERE id_cita = ? AND (empleado IS NULL OR empleado = 0)");
            $upd->execute([(int)$idEmpleado, $id_cita]);
        } catch (Throwable $ignore) {
        }
    }
    
    $insercion = ConexionesClientes::insertarDesparacitacion(
        $id_cita,
        $TipoDesparacitacion,
        $ProductoUtilizado,
        $ProximaCita,
        $Docies,
        (!empty($idEmpleado) && (int)$idEmpleado > 0) ? (int)$idEmpleado : null,
        $Observaciones
    );

    if (!$insercion) {
        errorSession("Error al guardar la información. Por favor, inténtelo de nuevo.", $id_cita);
        exit();
    }

    ConexionesClientes::marcarCitaAtendida($id_cita);
    
    unset($_SESSION['oldDosis'], $_SESSION['oldDicis'], $_SESSION['oldFecha'], $_SESSION['oldObservaciones'], $_SESSION['oldTipo'], $_SESSION['oldProducto'], $_SESSION['error']);

    header("Location: ../views/citas.php");
    exit();
}
?>