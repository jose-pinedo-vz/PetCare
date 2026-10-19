<?php
require_once __DIR__ . '/../models/adopcion_venta.php';
require_once __DIR__ . '/ImagenController.php';

function validar_datos($datos) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $errores = [];

    if (empty(trim($datos['especie'] ?? ''))) {
        $errores[] = "Debe especificar la especie.";
    } else {
        $datos['especie'] = trim($datos['especie']);
        if (strlen($datos['especie']) > 30) {
            $errores[] = "La especie no puede tener más de 30 caracteres.";
        } else if (preg_match('/\d/', $datos['especie'])) {
            $errores[] = "La especie no puede contener números.";
        }
    }

    if (empty(trim($datos['raza'] ?? ''))) {
        $datos['raza'] = null;
    } else {
        $datos['raza'] = trim($datos['raza']);
        if (strlen($datos['raza']) > 30) {
            $errores[] = "La raza no puede tener más de 30 caracteres.";
        } else if (preg_match('/\d/', $datos['raza'])) {
            $errores[] = "La raza no puede contener números.";
        }
    }

    if (empty(trim($datos['sexo'] ?? ''))) {
        $datos['sexo'] = null;
    } else {
        $datos['sexo'] = trim($datos['sexo']);
        if (!in_array($datos['sexo'], ['Macho', 'Hembra'])) {
            $errores[] = "El sexo seleccionado no es válido.";
        }
    }

    if (empty(trim($datos['fecha_de_nacimiento'] ?? ''))) {
        $errores[] = "Debe especificar la fecha de nacimiento.";
    } else {
        try {
            $fechaActual = new DateTime('today');
            $fechaNac = new DateTime($datos['fecha_de_nacimiento']);
            if ($fechaNac > $fechaActual) {
                $errores[] = "Fecha de nacimiento: No puede ser una fecha posterior a hoy.";
            } else {
                $datos['fecha_de_nacimiento'] = $fechaNac->format('Y-m-d H:i:s');
            }
        } catch (Exception $e) {
            $errores[] = "Fecha de nacimiento: Formato de fecha no válido.";
        }
    }

    if (empty(trim($datos['color'] ?? ''))) {
        $errores[] = "El color es obligatorio.";
    } else {
        $datos['color'] = trim($datos['color']);
        if (strlen($datos['color']) > 20) {
            $errores[] = "El color no puede tener más de 20 caracteres.";
        } else if (preg_match('/\d/', $datos['color'])) {
            $errores[] = "El color no puede contener números.";
        }
    }

    $pesoLimpio = str_replace(',', '.', trim($datos['peso'] ?? ''));
    if ($pesoLimpio === '' || !is_numeric($pesoLimpio)) {
        $errores[] = "El peso debe ser un valor numérico.";
    } else {
        $peso = (float)$pesoLimpio;
        if ($peso <= 0) {
            $errores[] = "El peso debe ser un número mayor a 0.";
        } else if ($peso > 99999999.99) {
            $errores[] = "El peso excede el límite permitido.";
        } else {
            $datos['peso'] = $peso;
        }
    }

    if (empty(trim($datos['fecha_de_ingreso'] ?? ''))) {
        $errores[] = "Debe especificar la fecha de ingreso.";
    } else {
        try {
            $fechaIng = new DateTime($datos['fecha_de_ingreso']);
            $datos['fecha_de_ingreso'] = $fechaIng->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            $errores[] = "Fecha de ingreso: Formato de fecha no válido.";
        }
    }

    $costoLimpio = str_replace(',', '.', trim($datos['costo_de_adquisicion'] ?? ''));
    if ($costoLimpio === '') {
        $datos['costo_de_adquisicion'] = null;
    } else if (!is_numeric($costoLimpio)) {
        $errores[] = "El costo de adquisición debe ser numérico.";
    } else {
        $costo = (float)$costoLimpio;
        if ($costo < 0) {
            $errores[] = "El costo de adquisición no puede ser negativo.";
        } else if ($costo > 99999999.99) {
            $errores[] = "El costo de adquisición excede el límite permitido.";
        } else {
            $datos['costo_de_adquisicion'] = $costo;
        }
    }

    $precioLimpio = str_replace(',', '.', trim($datos['precio_de_venta'] ?? ''));
    if ($precioLimpio === '' || !is_numeric($precioLimpio)) {
        $errores[] = "El precio de venta debe ser numérico.";
    } else {
        $precio = (float)$precioLimpio;
        if ($precio < 0) {
            $errores[] = "El precio de venta no puede ser negativo.";
        } else if ($precio > 99999999.99) {
            $errores[] = "El precio de venta excede el límite permitido.";
        } else {
            $datos['precio_de_venta'] = $precio;
        }
    }

    if (empty(trim($datos['estado'] ?? ''))) {
        $datos['estado'] = 'Disponible';
    } else {
        $datos['estado'] = trim($datos['estado']);
    }

    if (empty(trim($datos['caracteristicas'] ?? ''))) {
        $errores[] = "Las características son obligatorias.";
    } else {
        $datos['caracteristicas'] = trim($datos['caracteristicas']);
        if (!preg_match('/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/u', $datos['caracteristicas'])) {
            $errores[] = "Las características deben incluir texto explicativo, no solo números o espacios.";
        }
    }

    if (empty(trim($datos['procedencia'] ?? ''))) {
        $errores[] = "La procedencia es obligatoria.";
    } else {
        $datos['procedencia'] = trim($datos['procedencia']);
        if (!preg_match('/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/u', $datos['procedencia'])) {
            $errores[] = "La procedencia debe incluir texto explicativo, no solo números o espacios.";
        }
    }

    if (empty(trim($datos['vacunas'] ?? ''))) {
        $errores[] = "Debe especificar las vacunas.";
    } else {
        $datos['vacunas'] = trim($datos['vacunas']);
        if (!preg_match('/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/u', $datos['vacunas'])) {
            $errores[] = "Las vacunas deben incluir texto explicativo, no solo números o espacios.";
        }
    }

    if (empty(trim($datos['desparasitacion'] ?? ''))) {
        $errores[] = "Debe especificar la desparasitación.";
    } else {
        try {
            $fechaDesp = new DateTime($datos['desparasitacion']);
            $datos['desparasitacion'] = $fechaDesp->format('Y-m-d 00:00:00');
        } catch (Exception $e) {
            $errores[] = "Desparasitación: Formato de fecha no válido.";
        }
    }

    if (empty(trim($datos['observaciones'] ?? ''))) {
        $errores[] = "Las observaciones son obligatorias.";
    } else {
        $datos['observaciones'] = trim($datos['observaciones']);
        if (!preg_match('/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/u', $datos['observaciones'])) {
            $errores[] = "Las observaciones deben incluir texto explicativo, no solo números o espacios.";
        }
    }

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
