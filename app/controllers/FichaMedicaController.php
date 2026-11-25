<?php
declare(strict_types=1);
date_default_timezone_set('America/Mexico_City');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once __DIR__ . '/../models/fichaMedicaModelo.php';
require_once __DIR__ . '/../models/citas.php';
require_once __DIR__ . '/../controllers/ImagenController.php';

$ficha = new FichaMedica();
$idUsuario = (int)($_SESSION['id_usuario'] ?? 0);
$idVeterinario = $ficha->obtenerEmpleadoDeUsuario($idUsuario);
$idConsulta = (int)($_GET['id_consulta'] ?? $_POST['id_consulta'] ?? $_SESSION['id_consulta'] ?? 0);

function errorSession(string $error, int $idConsulta = 0) : void
{
    guardarErroresYSesion([$error], $idConsulta);
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
    $_SESSION['oldVacunas'] = $_POST['vacunas'] ?? [];
    $_SESSION['oldVacunasProxima'] = $_POST['vacunas_proxima'] ?? [];
    $_SESSION['oldVacunasLote'] = $_POST['vacunas_lote'] ?? [];
    $_SESSION['oldVacunasFabricante'] = $_POST['vacunas_fabricante'] ?? [];

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
$idCliente=(int)$Datosconsulta['cliente'];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $mascotas = $ficha->agarraMascotas($idCliente);
    $veterinarios = $ficha->obtenerVeterinarios();
    $radiografiasPrevias = $ficha->obtenerRadiografias($idConsulta);
    $catalogoVacunas = $ficha->obtenerCatalogoVacunas();
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
    } elseif (!$ficha->existeVeterinario((int)$veterinario)) {
        $errores[] = "El veterinario seleccionado no es válido o no existe en el sistema.";
    }

    $mascotaSeleccionada = null;
    $idEspecieMascota = 0;
    if ($mascota === '' || !ctype_digit($mascota) || (int)$mascota <= 0) {
        $errores[] = "Debe seleccionar una mascota válida.";
    } else {
        $mascotasCliente = $ficha->agarraMascotas($idCliente);
        foreach ($mascotasCliente as $m) {
            if ((int)$m['id'] === (int)$mascota) {
                $mascotaSeleccionada = $m;
                $idEspecieMascota = (int)($m['id_especie'] ?? 0);
                break;
            }
        }
        if ($mascotaSeleccionada === null) {
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

    // Validar imágenes de radiografías (opcionales)
    $archivosRadiografias = [];
    if (isset($_FILES['radiografias'])) {
        $archivosRadiografias = ImagenController::normalizarArchivosMultiples($_FILES['radiografias']);
    }

    $tiposPermitidosImg = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $maxSizeImg = 10485760 / 2; // 5 MB

    foreach ($archivosRadiografias as $archivoRx) {
        if ($archivoRx['error'] !== UPLOAD_ERR_OK) {
            $errores[] = "Error al transferir la radiografía '{$archivoRx['name']}' (código: {$archivoRx['error']}).";
            continue;
        }
        if ($archivoRx['size'] > $maxSizeImg) {
            $errores[] = "La radiografía '{$archivoRx['name']}' excede el límite de 5 MB.";
            continue;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipoMime = finfo_file($finfo, $archivoRx['tmp_name']);
        finfo_close($finfo);
        if (!in_array($tipoMime, $tiposPermitidosImg, true)) {
            $errores[] = "El archivo '{$archivoRx['name']}' no es un formato de imagen permitido (solo JPG, PNG, WEBP o GIF).";
        }
    }

    $vacunasAplicadas = [];
    $vacunasRaw = $_POST['vacunas'] ?? [];
    $vacunasProximaRaw = is_array($_POST['vacunas_proxima'] ?? null) ? $_POST['vacunas_proxima'] : [];
    $vacunasLoteRaw = is_array($_POST['vacunas_lote'] ?? null) ? $_POST['vacunas_lote'] : [];
    $vacunasFabricanteRaw = is_array($_POST['vacunas_fabricante'] ?? null) ? $_POST['vacunas_fabricante'] : [];

    if (!is_array($vacunasRaw)) {
        $errores[] = "El formato de las vacunas seleccionadas no es válido.";
    } elseif (!empty($vacunasRaw)) {
        $mapaVacunas = [];
        foreach ($ficha->obtenerCatalogoVacunas() as $vacCat) {
            $mapaVacunas[(int)$vacCat['id']] = $vacCat;
        }

        $hoyVacuna = date('Y-m-d');
        $maxFechaVacuna = (new DateTime('+5 years'))->format('Y-m-d');
        $idsVacunasVistos = [];

        foreach ($vacunasRaw as $rawIdVacuna) {
            $idVacunaStr = is_int($rawIdVacuna) || is_string($rawIdVacuna) ? trim((string)$rawIdVacuna) : '';
            if ($idVacunaStr === '' || !ctype_digit($idVacunaStr) || (int)$idVacunaStr <= 0) {
                $errores[] = "El identificador de una de las vacunas seleccionadas no es válido.";
                continue;
            }

            $idVacuna = (int)$idVacunaStr;
            if (isset($idsVacunasVistos[$idVacuna])) {
                continue;
            }
            $idsVacunasVistos[$idVacuna] = true;

            if (!isset($mapaVacunas[$idVacuna])) {
                $errores[] = "Una de las vacunas seleccionadas no existe o no está activa.";
                continue;
            }

            if ($mascotaSeleccionada !== null && (int)$mapaVacunas[$idVacuna]['especie'] !== $idEspecieMascota) {
                $errores[] = "La vacuna '{$mapaVacunas[$idVacuna]['nombre']}' no corresponde a la especie de la mascota seleccionada.";
                continue;
            }

            $fechaProximaVal = null;
            $fechaProximaStr = is_string($vacunasProximaRaw[$idVacuna] ?? null) ? trim($vacunasProximaRaw[$idVacuna]) : '';
            if ($fechaProximaStr !== '') {
                $fProx = DateTime::createFromFormat('Y-m-d', $fechaProximaStr);
                if (!$fProx || $fProx->format('Y-m-d') !== $fechaProximaStr) {
                    $errores[] = "La fecha de próxima aplicación de '{$mapaVacunas[$idVacuna]['nombre']}' no es válida.";
                } elseif ($fechaProximaStr < $hoyVacuna) {
                    $errores[] = "La próxima aplicación de '{$mapaVacunas[$idVacuna]['nombre']}' no puede ser anterior a hoy.";
                } elseif ($fechaProximaStr > $maxFechaVacuna) {
                    $errores[] = "La próxima aplicación de '{$mapaVacunas[$idVacuna]['nombre']}' no puede ser mayor a 5 años en el futuro.";
                } else {
                    $fechaProximaVal = $fechaProximaStr;
                }
            }

            $loteVal = null;
            $loteStr = is_string($vacunasLoteRaw[$idVacuna] ?? null) ? preg_replace('/\s+/u', ' ', trim($vacunasLoteRaw[$idVacuna])) : '';
            if ($loteStr !== '') {
                if (mb_strlen($loteStr) > 20) {
                    $errores[] = "El lote de '{$mapaVacunas[$idVacuna]['nombre']}' no puede exceder los 20 caracteres.";
                } elseif (!preg_match('/^[\p{L}\p{N}\s\-\.\/]+$/u', $loteStr)) {
                    $errores[] = "El lote de '{$mapaVacunas[$idVacuna]['nombre']}' contiene caracteres no permitidos.";
                } else {
                    $loteVal = $loteStr;
                }
            }

            $fabricanteVal = null;
            $fabricanteStr = is_string($vacunasFabricanteRaw[$idVacuna] ?? null) ? preg_replace('/\s+/u', ' ', trim($vacunasFabricanteRaw[$idVacuna])) : '';
            if ($fabricanteStr !== '') {
                if (mb_strlen($fabricanteStr) > 20) {
                    $errores[] = "El fabricante de '{$mapaVacunas[$idVacuna]['nombre']}' no puede exceder los 20 caracteres.";
                } elseif (!preg_match('/\p{L}/u', $fabricanteStr) || !preg_match('/^[\p{L}\p{N}\s\-\.\/\(\)]+$/u', $fabricanteStr)) {
                    $errores[] = "El fabricante de '{$mapaVacunas[$idVacuna]['nombre']}' debe contener letras válidas.";
                } else {
                    $fabricanteVal = $fabricanteStr;
                }
            }

            $vacunasAplicadas[] = [
                'vacuna_puesta'    => $idVacuna,
                'fecha_aplicacion' => $hoyVacuna,
                'fecha_proxima'    => $fechaProximaVal,
                'lote'             => $loteVal,
                'fabricante'       => $fabricanteVal,
                'observaciones'    => null,
            ];
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

    $resultado = $ficha->insertar($datos, $vacunasAplicadas);

    if ($resultado['exito'] === false) {
        guardarErroresYSesion(["No se logró guardar la ficha médica en la base de datos: " . ($resultado['mensaje'] ?? '')], $idConsulta);
    }

    $idFicha = $resultado['id_ficha'] ?? null;

    // Subir y registrar las imágenes de radiografías
    foreach ($archivosRadiografias as $archivoRx) {
        $subida = ImagenController::subir($archivoRx, 'radiografias', 'rx_');
        if ($subida['exito']) {
            $ficha->guardarRadiografia((int)$consulta, $idFicha, (int)$mascota, $subida['ruta']);
        } else {
            error_log("Error al subir radiografía: " . $subida['mensaje']);
        }
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
