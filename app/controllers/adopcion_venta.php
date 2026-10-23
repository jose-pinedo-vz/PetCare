<?php
require_once __DIR__ . '/../models/adopcion_venta.php';
require_once __DIR__ . '/ImagenController.php';

function validar_datos($datos) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $errores = [];

    /* Validar especie
    $especie = preg_replace('/\s+/u', ' ', trim($datos['especie'] ?? ''));
    if ($especie === '') {
        $errores[] = "Debe especificar la especie.";
    } else {
        if (mb_strlen($especie) < 2 || mb_strlen($especie) > 30) {
            $errores[] = "La especie debe tener entre 2 y 30 caracteres.";
        } else if (preg_match('/\d/', $especie)) {
            $errores[] = "La especie no puede contener números.";
        } else if (!preg_match('/^[\p{L}]{2,}(?: [\p{L}]{2,})*$/u', $especie)) {
            $errores[] = "La especie solo puede contener letras y espacios válidos (sin símbolos ni caracteres especiales).";
        } else {
            $datos['especie'] = $especie;
        }
    }

    // Validar raza
    $raza = preg_replace('/\s+/u', ' ', trim($datos['raza'] ?? ''));
    if ($raza === '') {
        $datos['raza'] = null;
    } else {
        if (mb_strlen($raza) < 2 || mb_strlen($raza) > 30) {
            $errores[] = "La raza debe tener entre 2 y 30 caracteres.";
        } else if (preg_match('/\d/', $raza)) {
            $errores[] = "La raza no puede contener números.";
        } else if (!preg_match('/^[\p{L}]{2,}(?:[\s\-][\p{L}]{2,})*$/u', $raza)) {
            $errores[] = "La raza solo puede contener letras, guiones y espacios válidos (sin símbolos ni espacios repetidos).";
        } else {
            $datos['raza'] = $raza;
        }
    }*/

    // Validar sexo
    $sexo = trim($datos['sexo'] ?? '');
    if ($sexo === '') {
        $datos['sexo'] = null;
    } else {
        if (!in_array($sexo, ['Macho', 'Hembra'], true)) {
            $errores[] = "El sexo seleccionado no es válido.";
        } else {
            $datos['sexo'] = $sexo;
        }
    }

    // Validar fecha de nacimiento
    $fechaNacRaw = trim($datos['fecha_de_nacimiento'] ?? '');
    $fechaNacObj = null;
    if ($fechaNacRaw === '') {
        $errores[] = "Debe especificar la fecha de nacimiento.";
    } else {
        try {
            $fechaActual = new DateTime('today');
            $fechaNac = new DateTime($fechaNacRaw);
            $limitePasado = new DateTime('-35 years');
            if ($fechaNac > $fechaActual) {
                $errores[] = "Fecha de nacimiento: No puede ser una fecha posterior a hoy.";
            } else if ($fechaNac < $limitePasado) {
                $errores[] = "Fecha de nacimiento: La fecha no puede tener más de 35 años de antigüedad.";
            } else {
                $fechaNacObj = $fechaNac;
                $datos['fecha_de_nacimiento'] = $fechaNac->format('Y-m-d H:i:s');
            }
        } catch (Exception $e) {
            $errores[] = "Fecha de nacimiento: Formato de fecha no válido.";
        }
    }

    /* Validar color
    $color = preg_replace('/\s+/u', ' ', trim($datos['color'] ?? ''));
    if ($color === '') {
        $errores[] = "El color es obligatorio.";
    } else {
        if (mb_strlen($color) < 3 || mb_strlen($color) > 20) {
            $errores[] = "El color debe tener entre 3 y 20 caracteres.";
        } else if (preg_match('/\d/', $color)) {
            $errores[] = "El color no puede contener números.";
        } else if (!preg_match('/^[\p{L}]{2,}(?:[\s\-][\p{L}]{2,})*$/u', $color)) {
            $errores[] = "El color solo puede contener letras y espacios válidos (sin símbolos ni caracteres especiales).";
        } else {
            $datos['color'] = $color;
        }
    }*/

    // Validar peso
    $pesoLimpio = str_replace(',', '.', trim($datos['peso'] ?? ''));
    if ($pesoLimpio === '' || !is_numeric($pesoLimpio)) {
        $errores[] = "El peso debe ser un valor numérico.";
    } else {
        $peso = (float)$pesoLimpio;
        if ($peso < 0.01) {
            $errores[] = "El peso debe ser de al menos 0.01 kg.";
        } else if ($peso > 150.00) {
            $errores[] = "El peso no puede exceder los 150 kg para animales domésticos.";
        } else {
            $datos['peso'] = $peso;
        }
    }

    // Validar fecha de ingreso
    $fechaIngRaw = trim($datos['fecha_de_ingreso'] ?? '');
    if ($fechaIngRaw === '') {
        $errores[] = "Debe especificar la fecha de ingreso.";
    } else {
        try {
            $fechaIng = new DateTime($fechaIngRaw);
            $ahoraMasMargen = (new DateTime())->modify('+10 minutes');
            if ($fechaIng > $ahoraMasMargen) {
                $errores[] = "Fecha de ingreso: No puede ser una fecha futura.";
            } else if ($fechaNacObj && $fechaIng < $fechaNacObj) {
                $errores[] = "Fecha de ingreso: No puede ser anterior a la fecha de nacimiento del animal.";
            } else {
                $datos['fecha_de_ingreso'] = $fechaIng->format('Y-m-d H:i:s');
            }
        } catch (Exception $e) {
            $errores[] = "Fecha de ingreso: Formato de fecha no válido.";
        }
    }

    // Validar costo de adquisición
    $costoLimpio = str_replace(',', '.', trim($datos['costo_de_adquisicion'] ?? ''));
    if ($costoLimpio === '') {
        $datos['costo_de_adquisicion'] = null;
    } else if (!is_numeric($costoLimpio)) {
        $errores[] = "El costo de adquisición debe ser un valor numérico.";
    } else {
        $costo = (float)$costoLimpio;
        if ($costo < 0) {
            $errores[] = "El costo de adquisición no puede ser negativo.";
        } else if ($costo > 500000.00) {
            $errores[] = "El costo de adquisición excede el límite permitido ($500,000.00).";
        } else {
            $datos['costo_de_adquisicion'] = $costo;
        }
    }

    // Validar precio de venta
    $precioLimpio = str_replace(',', '.', trim($datos['precio_de_venta'] ?? ''));
    if ($precioLimpio === '' || !is_numeric($precioLimpio)) {
        $errores[] = "El precio de venta debe ser un valor numérico.";
    } else {
        $precio = (float)$precioLimpio;
        if ($precio < 0) {
            $errores[] = "El precio de venta no puede ser negativo.";
        } else if ($precio > 500000.00) {
            $errores[] = "El precio de venta excede el límite permitido ($500,000.00).";
        } else {
            $datos['precio_de_venta'] = $precio;
        }
    }

    // Validar estado
    $estadosValidos = ['Disponible', 'Venta legal', 'Adopcion', 'Vendido', 'Adoptado', 'Reservado'];
    $estado = trim($datos['estado'] ?? '');
    if ($estado === '') {
        $datos['estado'] = 'Disponible';
    } else if (!in_array($estado, $estadosValidos, true)) {
        $errores[] = "El estado seleccionado no es válido.";
    } else {
        $datos['estado'] = $estado;
    }

    $validarTextoLibre = function(string $campo, string $nombreCampo, int $min, int $max) use (&$errores): ?string {
        $limpio = preg_replace('/\s+/u', ' ', trim($campo));
        if ($limpio === '') {
            $errores[] = "El campo {$nombreCampo} es obligatorio.";
            return null;
        }
        if (mb_strlen($limpio) < $min || mb_strlen($limpio) > $max) {
            $errores[] = "El campo {$nombreCampo} debe tener entre {$min} y {$max} caracteres.";
            return null;
        }
        if (!preg_match('/[\p{L}]{2,}/u', $limpio)) {
            $errores[] = "El campo {$nombreCampo} debe incluir texto explicativo con palabras reales.";
            return null;
        }
        return $limpio;
    };

    $caract = $validarTextoLibre($datos['caracteristicas'] ?? '', 'Características', 5, 500);
    if ($caract !== null) $datos['caracteristicas'] = $caract;

    $proc = $validarTextoLibre($datos['procedencia'] ?? '', 'Procedencia', 5, 500);
    if ($proc !== null) $datos['procedencia'] = $proc;

    $vac = $validarTextoLibre($datos['vacunas'] ?? '', 'Vacunas', 3, 500);
    if ($vac !== null) $datos['vacunas'] = $vac;

    // Desparasitación (fecha)
    $despRaw = trim($datos['desparasitacion'] ?? '');
    if ($despRaw === '') {
        $errores[] = "Debe especificar la fecha de desparasitación.";
    } else {
        try {
            $fechaDesp = new DateTime($despRaw);
            $datos['desparasitacion'] = $fechaDesp->format('Y-m-d 00:00:00');
        } catch (Exception $e) {
            $errores[] = "Desparasitación: Formato de fecha no válido.";
        }
    }

    $obs = $validarTextoLibre($datos['observaciones'] ?? '', 'Observaciones', 5, 500);
    if ($obs !== null) $datos['observaciones'] = $obs;

    $foto = $_FILES['fotografia'] ?? null;
    $datos['fotografia'] = null;
    if (is_array($foto) && !empty($foto['name']) && $foto['error'] !== UPLOAD_ERR_NO_FILE) {
        $resultadoImagen = ImagenController::subir($foto);
        if ($resultadoImagen['exito']) {
            $datos['fotografia'] = $resultadoImagen['ruta'];
        } else {
            $errores[] = "Error en la foto: " . $resultadoImagen['mensaje'];
        }
    }

    if (count($errores) > 0) {
        $_SESSION['errores'] = $errores;
        $_SESSION['old']     = $_POST;
        header("Location: ../views/agregar_adopcion_venta.php");
        exit;
    } else {
        $advert = insertar($datos);
        if (is_array($advert) && count($advert) > 0) {
            if ($advert[0] === "Error BD") {
                $_SESSION['errores'] = ["No se pudo conectar a la base de datos: " . $advert[1]];
            } else if ($advert[0] === "Error al Insertar") {
                $_SESSION['errores'] = ["Error al guardar en la base de datos: " . $advert[1]];
            }
            $_SESSION['old'] = $_POST;
            header("Location: ../views/agregar_adopcion_venta.php");
            exit;
        } else {
            header('Location: ../views/adopcion_venta.php');
            exit;
        }
    }
    
}

function listar_adopciones_ventas(): array {
    require_once __DIR__ . '/../models/adopcion_venta.php';
    return obtenerTodos();
}
