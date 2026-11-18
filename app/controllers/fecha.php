<?php 

declare(strict_types=1);

// $ponerFecha = true ( "3 año(s)  /  11/11/2020" ) (veterinaria)
// $ponerFecha = false ( "3 año(s)" ) (mascotas)

function calcularEdad(string $fechaNacimiento, bool $ponerFecha = true): string {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}/', $fechaNacimiento)) {
        return $fechaNacimiento;
    }

    try {
        $diferencia = (new DateTime($fechaNacimiento)) -> diff(new DateTime());
        $fechaEsp = formatearFechaEsp($fechaNacimiento);
        $edad_fecha = $ponerFecha ? ' / ' . $fechaEsp : '';

        $difYear = $diferencia -> y;
        $difMes = $diferencia -> m;
        $difDia = $diferencia -> d;

        if ($difYear >= 1) {
            return $difYear . ' año(s)' . $edad_fecha;
        } else if ($difMes >= 1) {
            return $difMes . ' mes(es)' . $edad_fecha;
        } else if ($difDia >= 1) {
            return $difDia . ' dia(s)' . $edad_fecha;
        } else {
            return 'Recien nacido' . $edad_fecha;
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
