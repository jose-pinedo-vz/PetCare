<?php 

declare(strict_types=1);

require_once __DIR__ . '/../models/Veterinaria.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function errorSessionMascota(string $error) {
    $_SESSION['error'] = $error;
    $_SESSION['oldMascota'] = $_POST['id_mascota'] ?? '';

    header("Location: ../views/veterinaria_mascota.php");
    exit();
}

function extraerDatosMascota(): array {
    $idMascota = filter_input(INPUT_GET, 'id_mascota', FILTER_VALIDATE_INT);

    if (!$idMascota) {
        $idMascota = (int) ($_SESSION['mascotaSeleccionada'] ?? 0);
    }
    if (!$idMascota) {
        $idMascota = (int) ($_SESSION['oldMascota'] ?? 0);
    }

    unset($_SESSION['oldMascota']);

    $listaMascotas = Veterinaria::listaMascotas();
    $detMascotas = [];
    $detProp = [];
    $edad = '';

    if ($idMascota > 0) {
        $detMascotas = Veterinaria::obtenerNombreMascota($idMascota);

        if (!$detMascotas) {
            unset($_SESSION['mascotaSeleccionada']);
            $idMascota = 0;
        } else {
            $_SESSION['mascotaSeleccionada'] = $idMascota;
            $detProp = Veterinaria::obtenerDatosPropietario($idMascota);
            $edad = calcularEdad((string) ($detMascotas['edad'] ?? ''));
        }
    } 

    return [
        'idMascota' => $idMascota,
        'listaMascotas' => $listaMascotas,
        'detMascotas' => $detMascotas, 
        'detProp' => $detProp,
        'edad' => $edad
    ];
}

function calcularEdad(string $fechaNacimiento): string {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}/', $fechaNacimiento)) {
        return $fechaNacimiento;
    }
    try {
        $diferencia = (new DateTime($fechaNacimiento)) -> diff(new DateTime());
        $fechaEsp = formatearFechaEsp($fechaNacimiento);

        $difYear = $diferencia -> y;
        $difMes = $diferencia -> m;
        $difDia = $diferencia -> d;

        if ($difYear >= 1) {
            return $difYear . ' año(s)  /  ' . $fechaEsp;
        } else if ($difMes >= 1) {
            return $difMes . ' mes(es)  /  ' . $fechaEsp;
        } else if ($difDia >= 1) {
            return $difDia . ' dia(s)  /  ' . $fechaEsp;
        } else {
            return 'Recien nacido / ' . $fechaEsp;
        }
    } catch (Exception $error) {
        error_log("Error en calcular la edad de la mascota " . $error -> getMessage());
        return $fechaNacimiento;
    }
}

// combierte la fecha en formato en español (dia, mes, año)
function formatearFechaEsp(string $fecha) {
    $fechaDateTime = DateTime::createFromFormat('Y-m-d', substr($fecha, 0, 10));

    if (!$fechaDateTime) {
        return $fecha;
    }

    return $fechaDateTime -> format('d/m/Y');
}


function extraerCitasHoy(): array {
    return Veterinaria::citasHoyVeterinaria(date('Y-m-d'));
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idMascota = trim($_POST['id_mascota'] ?? '');

    if ($idMascota === "") {
        unset($_SESSION["mascotaSeleccionada"]);
        header("Location: ../views/veterinaria_mascota.php");
        exit();
    }

    if (!ctype_digit($idMascota) || (int)$idMascota <= 0) {
        errorSessionMascota("El id de la mascota debe de ser un numero valido mayor a 0");
    }

    if (!Veterinaria::obtenerNombreMascota((int) $idMascota)) {
        errorSessionMascota("La mascota con el id #$idMascota, no se encuentra registrada en el sistema");
    }

    $_SESSION['mascotaSeleccionada'] = (int) $idMascota;
    header("Location: ../views/veterinaria_mascota.php");
    exit();
}

?>
