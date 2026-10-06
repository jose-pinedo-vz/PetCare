<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/Empleados.php';

session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function errorSession(string $error) : void {
    $_SESSION['error'] = $error;

    $_SESSION['oldnombre_empleado'] = $_POST['nombre'] ?? '';
    $_SESSION['oldapellido'] = $_POST['apellido'] ?? '';
    $_SESSION['oldtelefono'] = $_POST['telefono'] ?? '';
    $_SESSION['oldcorreo'] = $_POST['correo'] ?? '';
    $_SESSION['oldcalle'] = $_POST['calle'] ?? '';
    $_SESSION['oldnumero_exterior'] = $_POST['numero_exterior'] ?? '';
    $_SESSION['oldnumero_interior'] = $_POST['numero_interior'] ?? '';
    $_SESSION['oldcolonia'] = $_POST['colonia'] ?? '';
    $_SESSION['oldciudad'] = $_POST['ciudad'] ?? '';
    $_SESSION['oldestado'] = $_POST['estado'] ?? '';
    $_SESSION['oldcodigo_postal'] = $_POST['codigo_postal'] ?? '';
    $_SESSION['oldpuesto'] = $_POST['puesto'] ?? '';
    $_SESSION['oldespecialidad'] = $_POST['especialidad'] ?? '';
    $_SESSION['oldnum_cedula_profesional'] = $_POST['num_cedula_profesional'] ?? '';
    $_SESSION['oldFecha_de_contratacion'] = $_POST['Fecha_de_contratacion'] ?? '';
    $_SESSION['oldhorario'] = $_POST['horario'] ?? '';

    header("Location: ../views/agregar_empleado.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $errores = [];

    $id_empleado = !empty($_POST['id_empleado']) ? (int) $_POST['id_empleado'] : null;

    $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $apellido = htmlspecialchars(trim($_POST['apellido'] ?? ''));
    $telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''));
    $correo = htmlspecialchars(trim($_POST['correo'] ?? ''));
    $calle = htmlspecialchars(trim($_POST['calle'] ?? ''));
    $numero_exterior = htmlspecialchars(trim($_POST['numero_exterior'] ?? ''));
    $numero_interior = htmlspecialchars(trim($_POST['numero_interior'] ?? ''));
    $colonia = htmlspecialchars(trim($_POST['colonia'] ?? ''));
    $ciudad = htmlspecialchars(trim($_POST['ciudad'] ?? ''));
    $estado = htmlspecialchars(trim($_POST['estado'] ?? ''));
    $codigo_postal = htmlspecialchars(trim($_POST['codigo_postal'] ?? ''));
    $puesto = htmlspecialchars(trim($_POST['puesto'] ?? ''));
    $especialidad = htmlspecialchars(trim($_POST['especialidad'] ?? ''));
    $num_cedula_profesional = htmlspecialchars(trim($_POST['num_cedula_profesional'] ?? ''));
    $Fecha_de_contratacion = htmlspecialchars(trim($_POST['Fecha_de_contratacion'] ?? ''));
    $horario = htmlspecialchars(trim($_POST['horario'] ?? ''));

    // nombre obligatorio (20)
    if (empty($datos['nombre'])) {
        $errores[] = "El nombre del empleado es obligatorio";
    } else if (strlen($datos['nombre']) > 20) {
        $errores[] = "El nombre no debe de tener mas de 20 letras";
    }

    // apellido es obligatorio (40)
    if (empty($datos['apellido'])) {
        $errores[] = "El apellido es obligatorio";
    } else if (strlen($datos['apellido']) > 40) {
        $errores[] = "El apellido no debe de tener mas de 40 letras";
    }

    // telefono es obligatorio (15)
    if (empty($datos['telefono'])) {
        $errores[] = "El telefono es obligatorio";
    } else if (strlen($datos['telefono']) > 15) {
        $errores[] = "El telefono no debe de tener mas de 15 numeros";
    }

    // correo es obligatorio (100)
    if (empty($datos['correo'])) {
        $errores[] = "El correo es obligatorio";
    } else if (strlen($datos['correo']) > 100) {
        $errores[] = "El correo no debe de tener mas de 100 caracteres";
    }

    // calle es obligatrio (100)
    if (empty($datos['calle'])) {
        $errores[] = "La calle es obligatoria";
    } else if (strlen($datos['calle']) > 100) {
        $errores[] = "La calle no debe de tener mas de 100 letras";
    }

    // numero exterior es obligatorio (15)
    if (empty($datos['numero_exterior'])) {
        $errores[] = "El numero exterior es obligatorio";
    } else if (strlen($datos['numero_exterior']) > 15) {
        $errores[] = "El numero exterior no debe de tener mas de 15 numeros";
    }

    // numero interior no es obligatorio (15)
    if (empty($datos['numero_interior'])) {
        $errores[] = "El numero interior no es obligatorio";
    } else if (strlen($datos['numero_interior']) > 15) {
        $errores[] = "El numero interior no debe de tener mas de 15 letras";
    }

    // colonia es obligatorio (60)
    if (empty($datos['colonia'])) {
        $errores[] = "La colonia es obligatoria";
    } else if (strlen($datos['colonia']) > 60) {
        $errores[] = "La colonia no debe de tener mas de 60 letras";
    }

    // ciudad es obligatorio (60)
    if (empty($datos['ciudad'])) {
        $errores[] = "La ciudad es obligatoria";
    } else if (strlen($datos['ciudad']) > 60) {
        $errores[] = "La ciudad no debe de tener mas de 60 letras";
    }

    // estado es obligatorio (60)
    if (empty($datos['estado'])) {
        $errores[] = "El estado es obligatorio";
    } else if (strlen($datos['estado']) > 60) {
        $errores[] = "El estado no debe de tener mas de 60 letras";
    }

    // codigo postal es obligatorio (10)
    if (empty($datos['codigo_postal'])) {
        $errores[] = "El codigo postal no debe de tener mas de 10 caracteres";
    }

    // puesto es obligatorio (35)
    if (empty($datos['puesto'])) {
        $errores[] = "El puesto es obligatorio";
    } else if (strlen($datos['puesto']) > 35) {
        $errores[] = "El puesto no debe de tener mas de 35 letras";
    }

    // especialidad no es obligatorio (50)
    if (empty($datos['especialidad'])) {
        $errores[] = "La especialidad no es obligatorio";
    } else if (strlen($datos['especialidad']) > 50) {
        $errores[] = "La especialidad no debe de tener mas de 50 letras";
    }

    // num medula profesional es obligatorio
    if (empty($datos['num_cedula_profesional'])) {
        $errores[] = "El numero de medula profesional es obligatorio";
    } else if (strlen($datos['num_cedula_profesional']) > 20) {
        $errores[] = "El numero de medula profesional no debe de tener mas de 20 letras";
    }

    // horario es obligatorio (20)
    if (empty($datos['horario'])) {
        $errores[] = "El horario es obligatorio";
    } else if (strlen($datos['horario']) > 20) {
        $errores[] = "El horario no debe de tener mas de 20 letras";
    }

    if (count($errores) > 0) {
        $msg = implode("\n", $errores);
        errorSession($msg);
        // header("Location: /../views/empleados.php");
        exit();
    }

    $datos = [
        'nombre' => $nombre,
        'apellido' => $apellido,
        'telefono' => $telefono,
        'correo' => $correo,
        'calle' => $calle,
        'numero_exterior' => $numero_exterior,
        'numero_interior' => $numero_interior,
        'colonia' => $colonia,
        'ciudad' => $ciudad,
        'estado' => $estado,
        'codigo_postal' => $codigo_postal,
        'puesto' => $puesto,
        'especialidad' => $especialidad,
        'num_cedula_profesional' => $num_cedula_profesional,
        'Fecha_de_contratacion' => $Fecha_de_contratacion,
        'horario' => $horario,
    ];


    if ($id_empleado) {
        $res = Empleados::actualizar($id_empleado, $datos);
        // $idEmpleadoFinal = $id_empleado;
    } else {
        $res = Empleados::insertar($datos);
        // $idEmpleadoFinal = $res['id'];
    }

    if ($res['exito']) {
        header("Location: /../views/empleados.php");
        exit();
    } else {
        errorSession("Error en la base de datos: " . $res['mensaje']);
    }

} else {
    header("Location: /../views/empleados.php");
    exit();
}

?>
