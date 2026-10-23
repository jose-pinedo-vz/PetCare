<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/Empleados.php';
require_once __DIR__ . '/../models/Cliente.php';


session_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$accion = $_GET['accion'] ?? '';

if ($accion === 'codigo_postal')
{
    resivirCodigoPostal();
}

function resivirCodigoPostal()
{
    $json = file_get_contents('php://input');
    $codigoPostal = json_decode($json, true);

    $res = Cliente::obtenerCodigoPostal($codigoPostal['codigo_postal']);

    if ($res === [])
    {
        echo json_encode(false);
        exit;
    }
    else
    {
        echo json_encode($res);
        exit;
    }
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

    
    $nombre = preg_replace('/\s+/u', ' ', trim($_POST['nombre'] ?? ''));
    $apellido = preg_replace('/\s+/u', ' ', trim($_POST['apellido'] ?? ''));
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $calle = preg_replace('/\s+/u', ' ', trim($_POST['calle'] ?? ''));
    $numero_exterior = trim($_POST['numero_exterior'] ?? '');
    $numero_interior = trim($_POST['numero_interior'] ?? '');
    $colonia = preg_replace('/\s+/u', ' ', trim($_POST['colonia'] ?? ''));
    $ciudad = preg_replace('/\s+/u', ' ', trim($_POST['ciudad'] ?? ''));
    $estado = preg_replace('/\s+/u', ' ', trim($_POST['estado'] ?? ''));
    $codigo_postal = trim($_POST['codigo_postal'] ?? '');
    $puesto = trim($_POST['puesto'] ?? '');
    $especialidad = preg_replace('/\s+/u', ' ', trim($_POST['especialidad'] ?? ''));
    $num_cedula_profesional = trim($_POST['num_cedula_profesional'] ?? '');
    $Fecha_de_contratacion = trim($_POST['Fecha_de_contratacion'] ?? '');
    $horario = trim($_POST['horario'] ?? '');

    // VALIDACION REGEX
    $patronNombre = '/^[\p{L}]{2,}(?: [\p{L}]{2,})*$/u';
    
    $patronCiudadEstado = '/^[\p{L}]{2,}(?:[\s.\'-][\p{L}]{2,})*$/u';

    $soloCodigo_postal = '/^[0-9]{5}$/';

    $puestosValidos = ['Gerente', 'Veterinario/a', 'Veterinario', 'Vendedor', 'Estilista'];
    $horariosValidos = ['Matutino', 'Vespertino'];

    $patronNumero = '/^[a-zA-Z0-9\-\/]{1,15}$/';

    if ($nombre === '') {
        $errores[] = "El nombre es obligatorio.";
    } else if (preg_match('/\d/', $nombre)) {
        $errores[] = "El nombre no puede contener números.";
    } else if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 20) {
        $errores[] = "El nombre debe tener entre 2 y 20 letras.";
    } else if (!preg_match($patronNombre, $nombre)) {
        $errores[] = "El nombre solo puede contener letras y un espacio simple entre palabras (mínimo 2 letras por palabra, sin caracteres especiales ni espacios repetidos).";
    }


    if ($apellido === '') {
        $errores[] = "El apellido es obligatorio.";
    } else if (preg_match('/\d/', $apellido)) {
        $errores[] = "El apellido no puede contener números.";
    } else if (mb_strlen($apellido) < 2 || mb_strlen($apellido) > 40) {
        $errores[] = "El apellido debe tener entre 2 y 40 letras.";
    } else if (!preg_match($patronNombre, $apellido)) {
        $errores[] = "El apellido solo puede contener letras y un espacio simple entre palabras (mínimo 2 letras por palabra, sin caracteres especiales ni espacios repetidos).";
    }

    
    if ($telefono === '') {
        $errores[] = "El teléfono es obligatorio.";
    } else if (preg_match('/[^0-9\s\-()]/', $telefono)) {
        $errores[] = "El teléfono solo puede contener números, espacios y guiones (sin letras ni caracteres especiales).";
    } else {
        $soloNumerosTel = preg_replace('/\D/', '', $telefono);
        if (strlen($soloNumerosTel) !== 10) {
            $errores[] = "El teléfono debe contener exactamente 10 dígitos (ej: 3312345678 o 345-103-2345).";
        } else {
            $telefono = $soloNumerosTel;
        }
    }

    
    if ($correo === '') {
        $errores[] = "El correo es obligatorio.";
    } else if (mb_strlen($correo) > 100) {
        $errores[] = "El correo no debe tener más de 100 caracteres.";
    } else if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $correo)) {
        $errores[] = "Debe proporcionar un correo electrónico válido (ej: usuario@dominio.com).";
    }

    
    if ($calle === '') {
        $errores[] = "La calle es obligatoria.";
    } else if (mb_strlen($calle) < 3 || mb_strlen($calle) > 100) {
        $errores[] = "La calle debe tener entre 3 y 100 caracteres.";
    } else if (!preg_match('/[\p{L}]/u', $calle)) {
        $errores[] = "La calle debe incluir el nombre de la vía en letras.";
    } else if (!preg_match('/^[\p{L}0-9\s.,#\-\/]+$/u', $calle)) {
        $errores[] = "La calle contiene caracteres no permitidos.";
    }

    
    if ($numero_exterior === '') {
        $errores[] = "El número exterior es obligatorio.";
    } else if (mb_strlen($numero_exterior) > 15) {
        $errores[] = "El número exterior no debe tener más de 15 caracteres.";
    } else if (!preg_match($patronNumero, $numero_exterior)) {
        $errores[] = "El número exterior solo puede contener números, letras y guiones (sin espacios ni símbolos raros).";
    }

    
    if ($numero_interior === '') {
        $numero_interior = null;
    } else if (mb_strlen($numero_interior) > 15) {
        $errores[] = "El número interior no debe tener más de 15 caracteres.";
    } else if (!preg_match('/^[a-zA-Z0-9\s.\-\/]{1,15}$/', $numero_interior)) {
        $errores[] = "El número interior contiene caracteres no permitidos.";
    } else if (!preg_match('/\d/', $numero_interior) && !in_array(strtoupper(trim($numero_interior)), ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','PB','PH','SN','S/N'], true)) {
        $errores[] = "El número interior debe contener al menos un dígito o ser una designación válida (ej: 4, 10-A, S/N).";
    }

    $datosCP = [];
    if ($codigo_postal === '') {
        $errores[] = "El código postal es obligatorio.";
    } else if (!preg_match($soloCodigo_postal, $codigo_postal)) {
        $errores[] = "El código postal debe contener exactamente 5 dígitos numéricos.";
    } else {
        $datosCP = Cliente::obtenerCodigoPostal($codigo_postal);
        if (empty($datosCP)) {
            $errores[] = "El código postal ingresado no existe en el catálogo postal.";
        }
    }

    if ($colonia === '') {
        $errores[] = "La colonia es obligatoria.";
    } else if (mb_strlen($colonia) < 2 || mb_strlen($colonia) > 60) {
        $errores[] = "La colonia debe tener entre 2 y 60 caracteres.";
    } else if (!empty($datosCP)) {
        $coloniasCatalogo = array_map(function($c) { return mb_strtolower(trim($c['colonia'])); }, $datosCP);
        if (!in_array(mb_strtolower($colonia), $coloniasCatalogo, true)) {
            $errores[] = "La colonia seleccionada no corresponde al código postal $codigo_postal.";
        }
    } else if (!preg_match('/[\p{L}]/u', $colonia) || !preg_match('/^[\p{L}0-9\s.,\-\/\(\)]+$/u', $colonia)) {
        $errores[] = "La colonia contiene caracteres no permitidos.";
    }

    if ($ciudad === '') {
        $errores[] = "La ciudad es obligatoria.";
    } else if (mb_strlen($ciudad) < 2 || mb_strlen($ciudad) > 60) {
        $errores[] = "La ciudad debe tener entre 2 y 60 letras.";
    } else if (!empty($datosCP)) {
        $ciudadesValidas = [];
        foreach ($datosCP as $c) {
            if (!empty($c['ciudad'])) $ciudadesValidas[] = mb_strtolower(trim($c['ciudad']));
            if (!empty($c['municipio'])) $ciudadesValidas[] = mb_strtolower(trim($c['municipio']));
        }
        if (!empty($ciudadesValidas) && !in_array(mb_strtolower($ciudad), $ciudadesValidas, true)) {
            $errores[] = "La ciudad o municipio no coincide con el código postal ingresado.";
        }
    } else if (!preg_match($patronCiudadEstado, $ciudad)) {
        $errores[] = "La ciudad solo puede contener letras y espacios válidos.";
    }

    if ($estado === '') {
        $errores[] = "El estado es obligatorio.";
    } else if (mb_strlen($estado) < 2 || mb_strlen($estado) > 60) {
        $errores[] = "El estado debe tener entre 2 y 60 letras.";
    } else if (!empty($datosCP)) {
        $estadosValidos = array_unique(array_map(function($c) { return mb_strtolower(trim($c['estado'])); }, $datosCP));
        if (!empty($estadosValidos) && !in_array(mb_strtolower($estado), $estadosValidos, true)) {
            $errores[] = "El estado no coincide con el código postal ingresado.";
        }
    } else if (!preg_match($patronCiudadEstado, $estado)) {
        $errores[] = "El estado solo puede contener letras y espacios válidos.";
    }

    if ($puesto === '') {
        $errores[] = "El puesto es obligatorio.";
    } else if (!in_array($puesto, $puestosValidos, true)) {
        $errores[] = "El puesto seleccionado no es válido.";
    }

    $esVeterinario = in_array($puesto, ['Veterinario', 'Veterinario/a'], true);

    if ($esVeterinario) {
        if ($num_cedula_profesional === '') {
            $errores[] = "El número de cédula profesional es obligatorio para el puesto de Veterinario/a.";
        }
    } else {
        if ($num_cedula_profesional !== '') {
            $errores[] = "El número de cédula profesional solo aplica para el puesto de Veterinario/a.";
            $num_cedula_profesional = '';
        }
        if ($especialidad !== '') {
            $errores[] = "La especialidad solo aplica para el puesto de Veterinario/a.";
            $especialidad = '';
        }
    }

    if ($especialidad === '') {
        $especialidad = null;
    } else if (mb_strlen($especialidad) < 3 || mb_strlen($especialidad) > 50) {
        $errores[] = "La especialidad debe tener entre 3 y 50 letras.";
    } else if (!preg_match('/^[\p{L}]{2,}(?:[\s\-][\p{L}]{2,})*$/u', $especialidad)) {
        $errores[] = "La especialidad solo puede contener letras, guiones y espacios válidos (sin números ni símbolos).";
    }

    if ($num_cedula_profesional === '' || $num_cedula_profesional === null) {
        $num_cedula_profesional = null;
    } else if (!ctype_digit($num_cedula_profesional)) {
        $errores[] = "El número de cédula profesional solo debe contener dígitos numéricos.";
    } else if (strlen($num_cedula_profesional) < 6 || strlen($num_cedula_profesional) > 8) {
        $errores[] = "La cédula profesional debe tener entre 6 y 8 dígitos numéricos.";
    } else if (preg_match('/^(\d)\1+$/', $num_cedula_profesional)) {
        $errores[] = "El número de cédula profesional no puede tener todos los dígitos repetidos.";
    } else if (in_array($num_cedula_profesional, ['123456', '1234567', '12345678', '87654321', '7654321'], true)) {
        $errores[] = "El número de cédula profesional no puede ser una secuencia numérica consecutiva.";
    } else {
        $num_cedula_profesional = (int)$num_cedula_profesional;
        if (Empleados::cedulaRegistrada($num_cedula_profesional, $id_empleado)) {
            $errores[] = "Ya existe un empleado registrado con el mismo número de cédula profesional.";
        }
    }

    
    if ($Fecha_de_contratacion === '') {
        $Fecha_de_contratacion = null;
    } else {
        $f = DateTime::createFromFormat('Y-m-d', $Fecha_de_contratacion);
        if (!$f || $f->format('Y-m-d') !== $Fecha_de_contratacion) {
            $errores[] = "El formato de la fecha de contratación no es válido.";
        } else {
            $fecha_actual = date('Y-m-d');
            if ($Fecha_de_contratacion > $fecha_actual) {
                $errores[] = "La fecha de contratación no puede ser una fecha posterior a hoy.";
            } else if ($Fecha_de_contratacion < '1970-01-01') {
                $errores[] = "La fecha de contratación no puede ser anterior a 1970.";
            }
        }
    }

    
    if ($horario === '') {
        $errores[] = "El horario es obligatorio.";
    } else if (!in_array($horario, $horariosValidos, true)) {
        $errores[] = "El horario no es válido.";
    }

    // validar que no este en la base de datos
    $estaReg = Empleados::estaRegistrado($correo, $telefono, $id_empleado);
    if ($estaReg) {
        $errores[] = "Ya existe un empleado registrado con el mismo correo electronico o numero de telefono";
    }

    if (count($errores) > 0) {
        $msg = implode("\n\n", $errores);
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
