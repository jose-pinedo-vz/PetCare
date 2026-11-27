<?php
// Validaciones de nombre, especie, raza y color para registrar y editar mascota (lado cliente).
// Las usan cliente_guardar_mascota.php y cliente_editar_mascota.php
require_once __DIR__ . '/../models/Mascota.php';

// Revisa los campos y agrega los errores a $errores.
// Devuelve el color final que se guarda (si eligieron "Otro", el que escribieron).
function validarCamposMascotaCliente(array &$errores, string $nombre, string $especie, string $raza, string $color, string $colorOtro): string
{
    // Nombre: solo letras (con acentos y ñ) y espacios; sin números ni símbolos
    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio.';
    } elseif (mb_strlen($nombre) > 20) {
        $errores[] = 'El nombre no puede tener más de 20 letras.';
    } elseif (!preg_match('/^[\p{L} ]+$/u', $nombre)) {
        $errores[] = 'El nombre solo puede tener letras y espacios (sin números ni símbolos).';
    }

    // Especie y raza: deben existir en la BD y la raza debe ser de esa especie
    if ($especie === '') {
        $errores[] = 'La especie es obligatoria.';
    } elseif ($raza === '') {
        $errores[] = 'La raza es obligatoria.';
    } elseif (!Mascota::especieRazaExiste($especie, $raza)) {
        $errores[] = 'La raza elegida no corresponde a la especie.';
    }

    // Color: de la lista, o "Otro" con el color escrito
    if ($color === '') {
        $errores[] = 'El color es obligatorio.';
        return '';
    }
    if ($color === 'Otro') {
        if ($colorOtro === '') {
            $errores[] = 'Escribe el color si eliges "Otro".';
        } elseif (mb_strlen($colorOtro) > 50) {
            $errores[] = 'El color no puede tener más de 50 letras.';
        } elseif (!preg_match('/^[\p{L} ,]+$/u', $colorOtro)) {
            $errores[] = 'El color solo puede tener letras, espacios y comas.';
        }
        return $colorOtro;
    }
    if (!in_array($color, Mascota::coloresDisponibles(), true)) {
        $errores[] = 'El color elegido no es válido.';
    }
    return $color;
}

// Campos de salud y manejo (los mismos del formulario del empleado).
// Para el cliente son opcionales; si escribe algo debe incluir texto, no solo números.
function validarSaludMascotaCliente(array &$errores, array $datos, string $fechaNacimiento): void
{
    $textos = [
        'alergias'                  => 'Las alergias',
        'enfermedades'              => 'Las enfermedades',
        'medicamentos'              => 'Los medicamentos',
        'condiciones_especiales'    => 'Las condiciones especiales',
        'restricciones_para_manejo' => 'Las restricciones para manejo',
        'observaciones'             => 'Las observaciones',
    ];
    foreach ($textos as $campo => $texto) {
        $valor = $datos[$campo] ?? '';
        if ($valor === '') {
            continue;
        }
        if (!preg_match('/\p{L}/u', $valor)) {
            $errores[] = $texto . ' deben incluir texto, no solo números o símbolos.';
        } elseif (mb_strlen($valor) > 500) {
            $errores[] = $texto . ' no pueden tener más de 500 caracteres.';
        }
    }

    // Última desparasitación: opcional; no futura ni antes de que naciera
    $desparasitacion = $datos['ultima_desparasitacion'] ?? '';
    if ($desparasitacion !== '') {
        $fecha = DateTime::createFromFormat('!Y-m-d', $desparasitacion);
        if (!$fecha || $fecha->format('Y-m-d') !== $desparasitacion) {
            $errores[] = 'La fecha de la última desparasitación no es válida.';
        } elseif ($fecha > new DateTime('today')) {
            $errores[] = 'La última desparasitación no puede ser posterior a hoy.';
        } elseif ($fechaNacimiento !== '' && $desparasitacion < $fechaNacimiento) {
            $errores[] = 'La última desparasitación no puede ser antes de la fecha de nacimiento.';
        }
    }
}
