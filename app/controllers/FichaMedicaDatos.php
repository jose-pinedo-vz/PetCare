<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/fichaMedicaModelo.php';

function escogeMascota(FichaMedica $ficha, int $idConsulta): array
{ 
    $datosconsulta=$ficha->datosConsulta($idConsulta);
    if ($datosconsulta===null){
        $_SESSION['error']="La consulta no existe o ya fue atendida";
        header("Location: ../controllers/FichaMedicaController.php");
        exit();
    }
    
    return $ficha->agarraMascotas((int)$datosconsulta['id_cliente']);
}

?>