<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/citas.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function errorSession($mensaje, $id_cita)
{
    $_SESSION['error'] = $mensaje;

    $_SESSION['oldTipo'] = trim($_POST['Tipo'] ?? '');
    $_SESSION['oldLongitud'] = trim($_POST['Longitud'] ?? '');
    $_SESSION['oldCorte_higienico'] = trim($_POST['corte_higienico'] ?? '');
    $_SESSION['oldAccesorios'] = trim($_POST['accesorios'] ?? ''); 
    $_SESSION['oldCorteCara'] = trim($_POST['corteCara'] ?? ''); 
    $_SESSION['oldCortePatas'] = trim($_POST['cortePatas'] ?? ''); 
    $_SESSION['oldDeslanado'] = trim($_POST['Deslanado'] ?? ''); 
    $_SESSION['oldObservaciones'] = trim($_POST['Observaciones'] ?? ''); 
    $_SESSION['oldCepillado'] = trim($_POST['Cepillado'] ?? '');

    $url = "../views/servicioCortepelo.php?error=" . urlencode($mensaje) . "&id_consulta=" . $id_cita;
    header("Location: " . $url);
    exit();
}


if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    $id_cita = trim($_POST['id_cita'] ?? '');
    $tipoCorte = trim($_POST['Tipo'] ?? '');
    $Longitud = trim($_POST['Longitud'] ?? '');
    $corte_higienico = trim($_POST['corte_higienico'] ?? '');
    $accesorios = trim($_POST['accesorios'] ?? ''); 
    //$Observaciones = trim($_POST['Observaciones'] ?? ''); 
    $corteCara = trim($_POST['corteCara'] ?? ''); 
    $cortePatas = trim($_POST['cortePatas'] ?? ''); 
    $Deslanado = trim($_POST['Deslanado'] ?? ''); 
    $Observaciones = trim($_POST['Observaciones'] ?? ''); 
    $Cepillado = trim($_POST['Cepillado'] ?? '');

    //$Deslanado = trim($_POST['Deslanado'] ?? ''); 
    if ($id_cita === '') {
        errorSession("ID de cita no proporcionado.", $id_cita);
        exit();
    }

    if ($id_cita <= 0) {
        errorSession("Error: No se recibió un ID de cita válido.", $id_cita);
        exit();
    }

    if ($tipoCorte === '' || $Longitud === '' || $corte_higienico === '' || $accesorios === '' || $Observaciones === '' || $corteCara === '' || $cortePatas === '' || $Deslanado === '' || $Observaciones === '' || $Cepillado === '') {
        errorSession("Todos los campos son obligatorios.", $id_cita);
        exit();
    }

    if (!preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,]+$/u', $Observaciones)) 
    {
        errorSession("Los campos contienen caracteres no válidos.", $id_cita);
        exit();
    }

    $tipo_servicio = "Corte de uñas";
    $precio = 0.00; 
    $estado = "1";    

    $insercion = ConexionesClientes::insertarCortePelo(
        (int)$id_cita, 
        $tipoCorte, 
        $Longitud, 
        $corte_higienico, 
        $corteCara, 
        $cortePatas, 
        $Cepillado,
        $Deslanado, 
        $accesorios, 
        (float)0.00,
        $Observaciones, 
    );

    

    if (!$insercion) {
        errorSession("Error al guardar la información. Por favor, inténtelo de nuevo.", $id_cita);
        exit();
    }

    ConexionesClientes::marcarCitaAtendida((int)$id_cita);

    // actualizar consulta

    header("Location: ../views/citas.php");
    exit();
}
?>