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

    $id = !empty($_POST['id_empleado']) ? (int)$_POST['id_empleado'] : 0;

    if ($id > 0) {
        header("Location: ../views/editar_empleado.php?id_empleado=" . $id);
    } else {
        header("Location: ../views/agregar_empleado.php");
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $errores = [];

    $id_empleado = !empty($_POST['id_empleado']) ? (int)$_POST['id_empleado'] : null;

    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $calle = trim($_POST['calle'] ?? '');
    $numero_exterior = trim($_POST['numero_exterior'] ?? '');
    $numero_interior = trim($_POST['numero_interior'] ?? '');
    $colonia = trim($_POST['colonia'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    $codigo_postal = trim($_POST['codigo_postal'] ?? '');
    $puesto = trim($_POST['puesto'] ?? '');
    $especialidad = trim($_POST['especialidad'] ?? '');
    $num_cedula_profesional = trim($_POST['num_cedula_profesional'] ?? '');
    $Fecha_de_contratacion = trim($_POST['Fecha_de_contratacion'] ?? '');
    $horario = trim($_POST['horario'] ?? '');

    // validacion regex
    $soloLetras = '/^[\p{L}\s.\'-]+$/u';

    // validacion telefono
    $soloTelefono = '/^\+?[0-9]{7,15}$/';

    // validacion codigo postal
    $soloCodigo_postal = '/^[0-9]{5}$/';

    // puestos validos
    $puestosValidos = ['Gerente', 'Veterinario/a', 'Veterinario', 'Vendedor', 'Estilista'];

    // horarios validos
    $horariosValidos = ['Matutino', 'Vespertino'];

    // nombre obligatorio (20)
    if ($nombre === '') {
        $errores[] = "El nombre es obligatorio";
    } else if (mb_strlen($nombre) > 20) {
        $errores[] = "El nombre no debe de tener mas de 20 letras";
    } else if (!preg_match($soloLetras, $nombre)) {
        $errores[] = "El nombre no puede tener numeros";
    }

    // apellido es obligatorio (40)
    if ($apellido === '') {
        $errores[] = "El apellido es obligatorio";
    } else if (mb_strlen($apellido) > 40) {
        $errores[] = "El apellido no debe de tener mas de 40 letras";
    } else if (!preg_match($soloLetras, $apellido)) {
        $errores[] = "El apellido no debe de tener numeros";
    }

    // telefono es obligatorio (15)
    if ($telefono === '') {
        $errores[] = "El telefono es obligatorio";
    } else if (!preg_match($soloTelefono, $telefono) || mb_strlen($telefono) > 15) {
        $errores[] = "El telefono debe de tener menos de 15 numeros (no debe de tener letras ni espacios)";
    }

    // correo es obligatorio (100)
    if ($correo === '') {
        $errores[] = "El correo es obligatorio";
    } else if (mb_strlen($correo) > 100) {
        $errores[] = "El correo no debe de tener mas de 100 caracteres";
    } else if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Debe de proporcionar un correo electronico valido";
    }

    // calle es obligatrio (100)
    if ($calle === '') {
        $errores[] = "La calle es obligatoria";
    } else if (mb_strlen($calle) > 100) {
        $errores[] = "La calle no debe de tener mas de 100 letras";
    }

    // numero exterior es obligatorio (15)
    if ($numero_exterior === '') {
        $errores[] = "El numero exterior es obligatorio";
    } else if (mb_strlen($numero_exterior) > 15) {
        $errores[] = "El numero exterior no debe de tener mas de 15 numeros";
    }

    // numero interior no es obligatorio (15)
    if ($numero_interior === '') {
        $numero_interior = null;
    } else if (mb_strlen($numero_interior) > 15) {
        $errores[] = "El numero interior no debe de tener mas de 15 letras";
    }

    // colonia es obligatorio (60)
    if ($colonia === '') {
        $errores[] = "La colonia es obligatoria";
    } else if (mb_strlen($colonia) > 60) {
        $errores[] = "La colonia no debe de tener mas de 60 letras";
    }

    // ciudad es obligatorio (60)
    if ($ciudad === '') {
        $errores[] = "La ciudad es obligatoria";
    } else if (mb_strlen($ciudad) > 60) {
        $errores[] = "La ciudad no debe de tener mas de 60 letras";
    } else if (!preg_match($soloLetras, $ciudad)) {
        $errores[] = "La ciudad solo puede tener letras";
    }

    // estado es obligatorio (60)
    if ($estado === '') {
        $errores[] = "El estado es obligatorio";
    } else if (mb_strlen($estado) > 60) {
        $errores[] = "El estado no debe de tener mas de 60 letras";
    } else if (!preg_match($soloLetras, $estado)) {
        $errores[] = "El estado solo puede tener letras";
    }

    // codigo postal es obligatorio (10)
    if ($codigo_postal === '') {
        $errores[] = "El codigo postal es obligatorio";
    } else if (!preg_match($soloCodigo_postal, $codigo_postal)) {
        $errores[] = "El codigo postal debe de tener 5 numeros exactamente";
    }

    // puesto es obligatorio (35)
    if ($puesto === '') {
        $errores[] = "El puesto es obligatorio";
    } else if (mb_strlen($puesto) > 35) {
        $errores[] = "El puesto no debe de tener mas de 35 letras";
    } else if (!in_array($puesto, $puestosValidos, true)) {
        $errores[] = "El puesto seleccionado no es valido";
    }

    // especialidad no es obligatorio (50)
    if ($especialidad === '') {
        $especialidad = null;
    } else if (mb_strlen($especialidad) > 50) {
        $errores[] = "La especialidad no debe de tener mas de 50 letras";
    }

    // num medula profesional no es obligatorio
    if ($num_cedula_profesional === '') {
        $num_cedula_profesional = null;
    } else if (!ctype_digit($num_cedula_profesional)) {
        $errores[] = "El numero de medula profesional solo debe de contener numeros";
    } else if ((int) $num_cedula_profesional <= 0 || (int) $num_cedula_profesional > 2147483647) {
        $errores[] = "El numero debe de ser mayor a 1 pero menor a 2147483647";
    } else {
        $num_cedula_profesional = (int) $num_cedula_profesional;
    }

    // Fecha de contratacion no es obligatoria
    if ($Fecha_de_contratacion === '') {
        $Fecha_de_contratacion = null;
    } else {
        $f = DateTime::createFromFormat('Y-m-d', $Fecha_de_contratacion);
        if (!$f || $f -> format('Y-m-d') !== $Fecha_de_contratacion) {
            $errores[] = "La fecha no es valida";
        }
    }

    // horario es obligatorio (20)
    if ($horario === '') {
        $errores[] = "El horario es obligatorio";
    } else if (!in_array($horario, $horariosValidos, true)) {
        $errores[] = "El horario no es valido";
    }

    if (count($errores) > 0) {
        $msg = implode(" | ", $errores);
        errorSession($msg);
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
    } else {
        $res = Empleados::insertar($datos);
    }

    if ($res['exito']) {
        header("Location: ../views/empleados.php");
        exit();
    } else {
        errorSession("Error en la base de datos: " . $res['mensaje']);
    }

} else {
    header("Location: ../views/empleados.php");
    exit();
}

?>
