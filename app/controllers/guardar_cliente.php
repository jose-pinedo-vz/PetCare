<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Cliente.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $errores = [];

    $id_cliente            = !empty($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : null;
    $nombre                = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $apellido              = htmlspecialchars(trim($_POST['apellido'] ?? ''));
    $fecha_nacimiento      = trim($_POST['fecha_nacimiento'] ?? '');
    $sexo                  = htmlspecialchars(trim($_POST['sexo'] ?? ''));
    $rfc                   = htmlspecialchars(trim($_POST['rfc'] ?? ''));
    $telefono              = htmlspecialchars(trim($_POST['telefono'] ?? ''));
    $telefono_alternativo  = htmlspecialchars(trim($_POST['telefono_alternativo'] ?? ''));
    $correo                = filter_var(trim($_POST['correo'] ?? ''), FILTER_SANITIZE_EMAIL);
    $calle                 = htmlspecialchars(trim($_POST['calle'] ?? ''));
    $numero_exterior       = htmlspecialchars(trim($_POST['numero_exterior'] ?? ''));
    $numero_interior       = htmlspecialchars(trim($_POST['numero_interior'] ?? ''));
    $colonia               = htmlspecialchars(trim($_POST['colonia'] ?? ''));
    $ciudad                = htmlspecialchars(trim($_POST['ciudad'] ?? ''));
    $estado                = htmlspecialchars(trim($_POST['estado'] ?? ''));
    $codigo_postal         = htmlspecialchars(trim($_POST['codigo_postal'] ?? ''));
    $contacto_emergencia   = htmlspecialchars(trim($_POST['contacto_emergencia'] ?? ''));
    $telefono_emergencia   = htmlspecialchars(trim($_POST['telefono_emergencia'] ?? ''));
    $observaciones         = htmlspecialchars(trim($_POST['observaciones'] ?? ''));

    // Validaciones
    if (empty($nombre)) {
        $errores[] = "El nombre del cliente es obligatorio.";
    } elseif (mb_strlen($nombre) > 50) {
        $errores[] = "El nombre no puede exceder los 50 caracteres.";
    }

    if (empty($apellido)) {
        $errores[] = "El apellido del cliente es obligatorio.";
    } elseif (mb_strlen($apellido) > 100) {
        $errores[] = "El apellido no puede exceder los 100 caracteres.";
    }

    if (empty($telefono)) {
        $errores[] = "El teléfono principal es obligatorio.";
    } elseif (mb_strlen($telefono) > 15) {
        $errores[] = "El teléfono no puede exceder los 15 caracteres.";
    }

    if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Debe proporcionar un correo electrónico válido.";
    }

    if (empty($calle)) {
        $errores[] = "La calle es obligatoria.";
    }

    if (empty($numero_exterior)) {
        $errores[] = "El número exterior es obligatorio.";
    }

    if (empty($colonia)) {
        $errores[] = "La colonia es obligatoria.";
    }

    if (empty($ciudad)) {
        $errores[] = "La ciudad es obligatoria.";
    }

    if (empty($estado)) {
        $errores[] = "El estado es obligatorio.";
    }

    if (empty($codigo_postal)) {
        $errores[] = "El código postal es obligatorio.";
    }

    if (empty($contacto_emergencia)) {
        $errores[] = "El nombre del contacto de emergencia es obligatorio.";
    }

    if (empty($telefono_emergencia)) {
        $errores[] = "El teléfono del contacto de emergencia es obligatorio.";
    }

    $datos = [
        'nombre'                => $nombre,
        'apellido'              => $apellido,
        'fecha_nacimiento'      => $fecha_nacimiento,
        'sexo'                  => $sexo,
        'rfc'                   => $rfc,
        'telefono'              => $telefono,
        'telefono_alternativo'  => $telefono_alternativo,
        'correo'                => $correo,
        'calle'                 => $calle,
        'numero_exterior'       => $numero_exterior,
        'numero_interior'       => $numero_interior,
        'colonia'               => $colonia,
        'ciudad'                => $ciudad,
        'estado'                => $estado,
        'codigo_postal'         => $codigo_postal,
        'contacto_emergencia'   => $contacto_emergencia,
        'telefono_emergencia'   => $telefono_emergencia,
        'observaciones'         => $observaciones
    ];

    if (count($errores) > 0) {
        echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Error de validación</title><link rel='stylesheet' href='../views/css/estilos_base.css'></head><body>";
        echo "<div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #f44336; background: #ffebee; max-width: 500px; border-radius: 8px; margin: 40px auto;'>";
        echo "<h3 style='color: #c62828; margin-top: 0;'>Errores en el formulario:</h3><ul>";
        foreach ($errores as $error) {
            echo "<li style='color: #b71c1c; margin-bottom: 5px;'>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
        echo "<a href='javascript:history.back()' class='btn' style='background: #c62828;'>← Corregir datos</a>";
        echo "</div></body></html>";
        exit();
    }

    if ($id_cliente) {
        $res = Cliente::actualizar($id_cliente, $datos);
        $idClienteFinal = $id_cliente;
    } else {
        $res = Cliente::insertar($datos);
        $idClienteFinal = $res['id'];
    }

    if ($res['exito']) {
        echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Cliente guardado</title><link rel='stylesheet' href='../views/css/estilos_base.css'></head><body>";
        echo "<div style='font-family: Arial, sans-serif; padding: 25px; border: 1px solid #4CAF50; background: #e8f5e9; max-width: 550px; border-radius: 8px; margin: 40px auto; text-align: center;'>";
        echo "<h2 style='color: #2e7d32; margin-top: 0;'>✔ " . htmlspecialchars($res['mensaje']) . "</h2>";
        echo "<p style='font-size: 16px;'><strong>ID Asignado al cliente:</strong> <code>#" . htmlspecialchars((string)$idClienteFinal) . "</code></p>";
        echo "<p style='color: #555;'>Cliente: <strong>" . htmlspecialchars($nombre . ' ' . $apellido) . "</strong> (" . htmlspecialchars($telefono) . ")</p>";
        echo "<div style='margin-top: 25px;'>";
        echo "<a href='../views/clientes.php' class='btn' style='margin-right: 10px;'>Ver Lista de Clientes</a> ";
        echo "<a href='../views/agregar_mascota.php?id_cliente=" . urlencode((string)$idClienteFinal) . "' class='btn' style='background: #2196F3;'>+ Registrar Mascota para este Cliente</a>";
        echo "</div>";
        echo "</div></body></html>";
    } else {
        echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Error al guardar</title><link rel='stylesheet' href='../views/css/estilos_base.css'></head><body>";
        echo "<div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ff9800; background: #fff3e0; max-width: 500px; border-radius: 8px; margin: 40px auto;'>";
        echo "<h3 style='color: #e65100; margin-top: 0;'>Aviso al guardar en BD:</h3>";
        echo "<p>" . htmlspecialchars($res['mensaje']) . "</p>";
        echo "<a href='javascript:history.back()' class='btn'>Volver al formulario</a>";
        echo "</div></body></html>";
    }
} else {
    header("Location: ../views/clientes.php");
    exit();
}
?>