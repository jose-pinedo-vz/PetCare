<?php
    declare(strict_types=1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require_once __DIR__ . '/../models/citas.php';
    // session_start();

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    
    $id_consulta = $_GET['id_consulta'] ?? null;

    
    if ($id_consulta === null) 
    {
        header("Location: ../views/citas.php");
        exit();
    }
        redireccion($id_consulta);
    

    function redireccion($id_consulta)
    {
        $id_servicio = ConexionesClientes::consultarServicioPorConsulta($id_consulta);

        $esrvicio = ConexionesClientes::consultarServicio((int)$id_servicio);

        if ($esrvicio['nombre'] === 'Desparasitación') {
            header("Location: ../views/desparasitacion.php?id_consulta=" . urlencode((string)$id_consulta));
            exit();
        } else if ($esrvicio['nombre'] === 'Corte de uñas') {
            header("Location: ../views/corte_unias.php?id_consulta=" . urlencode((string)$id_consulta));
            exit();
        } else if ($esrvicio['nombre'] === 'Baño') {
            header("Location: ../views/servicioBanio.php?id_consulta=" . urlencode((string)$id_consulta));
            exit();
        } else {
            echo "ID de consulta no proporcionado.";
        }

        $clasificacion = ConexionesClientes::consultarServicioPorCategoria((string)$id_servicio);
        if ($clasificacion['categoria'] === 'Salud') {
            header("Location: FichaMedicaController.php?id_consulta=" . urlencode((string)$id_consulta));
            exit();
        } else {
            echo "ID de consulta no proporcionado.";
        }
    }
?>