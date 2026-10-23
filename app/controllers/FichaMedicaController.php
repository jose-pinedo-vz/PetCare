<?php
declare(strict_types=1);
date_default_timezone_set('America/Mexico_City');
//requiere_once hace que se cargue el archivo que se le pone en este
require_once __DIR__ . '/../models/fichaMedicaModelo.php';

require_once __DIR__ . '/../models/citas.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
};

$idVeterinario = (int)($_SESSION['id_usuario'] ?? 1);
$idConsulta = (int)($_GET['id_consulta'] ?? $_POST['id_consulta'] ?? $_SESSION['id_consulta'] ?? 0);

$ficha=new FichaMedica();

function errorSession(string $error, int $idConsulta = 0) : void
{
    $_SESSION['error'] = $error;
    $_SESSION['errores'] = [$error];
    $_SESSION['oldid_consulta'] = $_POST['id_consulta'] ?? '';
    $_SESSION['oldMascota'] = $_POST['mascota'] ?? '';
    $_SESSION['oldid_veterinario'] = $_POST['id_veterinario'] ?? '';
    $_SESSION['oldSintomas'] = $_POST['sintomas'] ?? '';
    $_SESSION['oldTemperatura'] = $_POST['temperatura'] ?? '';
    $_SESSION['oldPeso'] = $_POST['peso'] ?? '';
    $_SESSION['oldFrecuenciaC'] = $_POST['frecuenciaC'] ?? '';
    $_SESSION['oldFrecuenciaR'] = $_POST['frecuenciaR'] ?? '';
    $_SESSION['oldTratamiento'] = $_POST['tratamiento'] ?? '';
    $_SESSION['oldMedicamento'] = $_POST['Medicamento'] ?? '';
    $_SESSION['oldIndicacion'] = $_POST["indicaciones"] ?? '';
    $_SESSION['oldDosis'] = $_POST['Dosis'] ?? '';
    $_SESSION['oldEstudiosaA'] = $_POST['estudiosA'] ?? '';
    $_SESSION['oldDiagnostico'] = $_POST['diagnostico'] ?? '';
    $_SESSION['oldFecha'] = $_POST['fecha'] ?? '';
    $_SESSION['oldPrecio'] = $_POST['precio'] ?? '';
    $_SESSION['oldObservaciones'] = $_POST['observaciones'] ?? '';

    $id = $idConsulta > 0 ? $idConsulta : (int)($_POST['id_consulta'] ?? 0);
    if ($id > 0) {
        header("Location: ../controllers/FichaMedicaController.php?id_consulta=" . $id);
    } else {
        header("Location: ../views/citas.php");
    }
    exit();
}

function guardarErroresYSesion(array $errores, int $idConsulta) : void
{
    $_SESSION['errores'] = $errores;
    $_SESSION['error'] = implode("\n", $errores);
    $_SESSION['oldid_consulta'] = $_POST['id_consulta'] ?? '';
    $_SESSION['oldMascota'] = $_POST['mascota'] ?? '';
    $_SESSION['oldid_veterinario'] = $_POST['id_veterinario'] ?? '';
    $_SESSION['oldSintomas'] = $_POST['sintomas'] ?? '';
    $_SESSION['oldTemperatura'] = $_POST['temperatura'] ?? '';
    $_SESSION['oldPeso'] = $_POST['peso'] ?? '';
    $_SESSION['oldFrecuenciaC'] = $_POST['frecuenciaC'] ?? '';
    $_SESSION['oldFrecuenciaR'] = $_POST['frecuenciaR'] ?? '';
    $_SESSION['oldTratamiento'] = $_POST['tratamiento'] ?? '';
    $_SESSION['oldMedicamento'] = $_POST['Medicamento'] ?? '';
    $_SESSION['oldIndicacion'] = $_POST["indicaciones"] ?? '';
    $_SESSION['oldDosis'] = $_POST['Dosis'] ?? '';
    $_SESSION['oldEstudiosaA'] = $_POST['estudiosA'] ?? '';
    $_SESSION['oldDiagnostico'] = $_POST['diagnostico'] ?? '';
    $_SESSION['oldFecha'] = $_POST['fecha'] ?? '';
    $_SESSION['oldPrecio'] = $_POST['precio'] ?? '';
    $_SESSION['oldObservaciones'] = $_POST['observaciones'] ?? '';

    $id = $idConsulta > 0 ? $idConsulta : (int)($_POST['id_consulta'] ?? 0);
    if ($id > 0) {
        header("Location: ../controllers/FichaMedicaController.php?id_consulta=" . $id);
    } else {
        header("Location: ../views/citas.php");
    }
    exit();
}

$Datosconsulta=$ficha->datosConsulta($idConsulta);
if ($Datosconsulta===null){
    $_SESSION['error']="La consulta no existe o ya fue atendida";
    header("Location: ../views/citas.php");
    exit();
}
$idCliente=(int)$Datosconsulta['id_cliente'];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $mascotas = $ficha->agarraMascotas($idCliente);
    require __DIR__ . '/../views/FichaMedica.php';   // la vista usa $mascotas e $idConsulta
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") 
{
    $errores = [];

    // Consulta y Veterinario
    $consulta = trim($_POST['id_consulta'] ?? '');
    $fecha = date("Y-m-d H:i:s");
    $mascota = trim($_POST['mascota'] ?? '');
    $veterinario = trim($_POST['id_veterinario'] ?? '');

    $temperaturaRaw = trim($_POST['temperatura'] ?? '');
    $pesoRaw = trim($_POST['peso'] ?? '');
    $FrecCarRaw = trim($_POST['frecuenciaC'] ?? '');
    $FrecRespRaw = trim($_POST['frecuenciaR'] ?? '');
    $costoRaw = trim($_POST['precio'] ?? '');

    $sintomas = trim($_POST['sintomas'] ?? '');
    $diagnostico = trim($_POST['diagnostico'] ?? '');
    $tratamiento = trim($_POST['tratamiento'] ?? '');
    $medicamento = trim($_POST['Medicamento'] ?? '');
    $indicaciones = trim($_POST['indicaciones'] ?? '');
    $dosis = trim($_POST['Dosis'] ?? '');
    $estudioAs = trim($_POST['estudiosA'] ?? '');
    $proximaCita = trim($_POST['fecha'] ?? '');
    $observacion = trim($_POST['observaciones'] ?? '');

    // Validar consulta
    if ($consulta === '' || !ctype_digit($consulta) || (int)$consulta <= 0) {
        $errores[] = "Identificador de consulta no válido.";
    }

    // Validar veterinario
    if ($veterinario === '' || !ctype_digit($veterinario) || (int)$veterinario <= 0) {
        $errores[] = "Debe estar asignado a un veterinario válido.";
    }

    // Validar mascota perteneciente al cliente
    if ($mascota === '' || !ctype_digit($mascota) || (int)$mascota <= 0) {
        $errores[] = "Debe seleccionar una mascota válida.";
    } else {
        $mascotasCliente = $ficha->agarraMascotas($idCliente);
        $idsMascotas = array_map(fn($m) => (int)$m['id'], $mascotasCliente);
        if (!in_array((int)$mascota, $idsMascotas, true)) {
            $errores[] = "La mascota seleccionada no pertenece al cliente de esta consulta.";
        }
    }

    // Validar costo
    $costoLimpio = str_replace(',', '.', $costoRaw);
    $costoVal = null;
    if ($costoLimpio === '' || !is_numeric($costoLimpio)) {
        $errores[] = "El costo de la consulta debe ser numérico.";
    } else {
        $costoNum = (float)$costoLimpio;
        if ($costoNum < 1.00 || $costoNum > 100000.00) {
            $errores[] = "El costo de la consulta debe estar entre $1.00 y $100,000.00.";
        } else {
            $costoVal = $costoNum;
        }
    }

    // Validar temperatura clínica
    $tempLimpia = str_replace(',', '.', $temperaturaRaw);
    $tempVal = null;
    if ($tempLimpia === '' || !is_numeric($tempLimpia)) {
        $errores[] = "La temperatura debe ser un valor numérico.";
    } else {
        $tempNum = (float)$tempLimpia;
        if ($tempNum < 30.0 || $tempNum > 45.0) {
            $errores[] = "La temperatura corporal debe estar en el rango clínico de 30.0 °C a 45.0 °C.";
        } else {
            $tempVal = $tempNum;
        }
    }

    // Validar peso
    $pesoLimpio = str_replace(',', '.', $pesoRaw);
    $pesoVal = null;
    if ($pesoLimpio === '' || !is_numeric($pesoLimpio)) {
        $errores[] = "El peso debe ser un valor numérico.";
    } else {
        $pesoNum = (float)$pesoLimpio;
        if ($pesoNum < 0.10 || $pesoNum > 150.00) {
            $errores[] = "El peso del paciente debe estar entre 0.10 kg y 150.00 kg.";
        } else {
            $pesoVal = $pesoNum;
        }
    }

    // Validar frecuencia cardiaca
    $frecCarVal = null;
    if ($FrecCarRaw === '' || !ctype_digit($FrecCarRaw)) {
        $errores[] = "La frecuencia cardíaca debe ser un número entero.";
    } else {
        $fcNum = (int)$FrecCarRaw;
        if ($fcNum < 30 || $fcNum > 350) {
            $errores[] = "La frecuencia cardíaca debe estar en el rango de 30 a 350 lpm.";
        } else {
            $frecCarVal = $fcNum;
        }
    }

    // Validar frecuencia respiratoria
    $frecRespVal = null;
    if ($FrecRespRaw === '' || !ctype_digit($FrecRespRaw)) {
        $errores[] = "La frecuencia respiratoria debe ser un número entero.";
    } else {
        $frNum = (int)$FrecRespRaw;
        if ($frNum < 10 || $frNum > 150) {
            $errores[] = "La frecuencia respiratoria debe estar en el rango de 10 a 150 rpm.";
        } else {
            $frecRespVal = $frNum;
        }
    }

    $validarTextoClinico = function(string $valor, string $nombreCampo, int $min, int $max, bool $obligatorio) use (&$errores): ?string {
        $limpio = preg_replace('/\s+/u', ' ', trim($valor));
        if ($limpio === '') {
            if ($obligatorio) {
                $errores[] = "El campo '{$nombreCampo}' es obligatorio.";
            }
            return null;
        }
        if (mb_strlen($limpio) < $min || mb_strlen($limpio) > $max) {
            $errores[] = "El campo '{$nombreCampo}' debe tener entre {$min} y {$max} caracteres.";
            return null;
        }
        if (!preg_match('/[\p{L}]{2,}/u', $limpio)) {
            $errores[] = "El campo '{$nombreCampo}' debe contener texto descriptivo con palabras reales (no solo números o símbolos).";
            return null;
        }
        return $limpio;
    };

    $sintomasVal = $validarTextoClinico($sintomas, 'Síntomas presentados', 5, 1000, true);
    $diagVal = $validarTextoClinico($diagnostico, 'Diagnóstico clínico', 5, 1000, true);
    $tratVal = $validarTextoClinico($tratamiento, 'Tratamiento', 5, 1000, true);
    $medVal = $validarTextoClinico($medicamento, 'Medicamentos', 3, 500, true);
    $indVal = $validarTextoClinico($indicaciones, 'Indicaciones para el dueño', 5, 1000, true);
    $dosisVal = $validarTextoClinico($dosis, 'Dosis', 2, 500, false);
    $estudiosVal = $validarTextoClinico($estudioAs, 'Estudios asociados', 3, 500, false);
    $obsVal = $validarTextoClinico($observacion, 'Observaciones', 3, 1000, false);

    // Validar próxima cita
    $proximaCitaVal = null;
    if ($proximaCita !== '') {
        $f = DateTime::createFromFormat('Y-m-d', $proximaCita);
        if (!$f || $f->format('Y-m-d') !== $proximaCita) {
            $errores[] = "El formato de la fecha de la próxima cita no es válido.";
        } else {
            $hoy = date('Y-m-d');
            $maxFecha = (new DateTime('+2 years'))->format('Y-m-d');
            if ($proximaCita < $hoy) {
                $errores[] = "La próxima cita no puede ser en una fecha anterior a hoy.";
            } else if ($proximaCita > $maxFecha) {
                $errores[] = "La fecha de la próxima cita no puede ser mayor a 2 años en el futuro.";
            } else {
                $proximaCitaVal = $proximaCita . ' 00:00:00';
            }
        }
    }

    if (count($errores) > 0) {
        guardarErroresYSesion($errores, $idConsulta);
    }

    $datos = [
        'id_consulta' => (int)$consulta,
        'fecha' => $fecha,
        'id_mascota' => (int)$mascota,
        'id_veterinario' => (int)$veterinario,
        'sintomas' => $sintomasVal,
        'temperatura' => $tempVal,
        'peso' => $pesoVal,
        'frecuencia_cardiaca' => $frecCarVal,
        'frecuencia_respiratoria' => $frecRespVal,
        'diagnostico' => $diagVal,
        'tratamiento' => $tratVal,
        'medicamentos' => $medVal,
        'dosis' => $dosisVal,
        'indicaciones' => $indVal,
        'estudios_asociados' => $estudiosVal,
        'proxima_cita' => $proximaCitaVal,
        'observaciones' => $obsVal,
        'costo' => $costoVal,
    ];

    $resultado = $ficha->insertar($datos);

    if ($resultado['exito'] === false) {
        guardarErroresYSesion(["No se logró guardar la ficha médica en la base de datos: " . ($resultado['mensaje'] ?? '')], $idConsulta);
    }

    actualzarCita($idConsulta);

    header("Location: ../views/citas.php?exito=1");
    exit();
}

function actualzarCita(int $idCita)
{
    $idConsulta = $idCita;
    $contulta = new ConexionesClientes();
    $res = $contulta->pendinteDepago($idConsulta);
}
?>

<!-- Session ayuda a crear sesiones en lo que el cliente permanece en una pagina web

Lo que mas me intereso es el uso de $_SESSION que es una variable global que se usa
siempre que se este dentro de la sesion, funciona como un array $_SESSION[Usuario]="Tal"
Esto hace que si lo defino en la pagina de login y lo necesito en la pagina de mascotas 
pueda usarlo sin problema porque esta guardado en la misma sesion, una vez no lo necesite se
puede borrar todo con session_destroy(); o se podria borrar solo una variable con
unset($_SESSION["valor1"]);-->

<!-- _POST() Sirve para enviar variables unicamente entre 2 paginas, solo eso -->