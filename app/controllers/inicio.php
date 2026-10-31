<?php 
    require_once '../models/inicio.php';

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    };
    $accion = $_GET['inicio'] ?? 'inicio';
    if ($accion === 'inicio')
    {
        retornarInicio();
    }

    // datos nesesarios para la vista de inicio
    // citas de hoy 
    // citas por cobrar 
    // mascotas registrads 
    // productos con stock bajo 
    // mostrar las citas 
    function retornarInicio()
    {
        $json = file_get_contents('php://input');
        $codigoPostal = json_decode($json, true);

        // citas de hoy
        $hoy = new DateTime();
        $fechaHoy = $hoy->format('Y-m-d');
        $numCitas = inicio::cantidadCitas($fechaHoy);
        $citasCobrar = inicio::cantidadCitasPorCobrar();
        $numMascotas = inicio::cantidaDeMascotas();
        $stockBajo = inicio::cantidadProductosStockBajo();
        $citasHoy = inicio::citasDeHoy($fechaHoy);


        $respuesta = [
            'citas' => $numCitas,
            'numCitasPagos' => $citasCobrar,
            'numMascotas' => $numMascotas,
            'numProductosStockBajo' => $stockBajo,
            'citasHoy' => $citasHoy
        ];

        echo json_encode($respuesta);
        exit;
    }
?>