<?php
date_default_timezone_set('America/Mexico_City');
declare(strict_types=1);

require_once __DIR__ . 'views/FichaMedica.php'; //requiere_once hace que se cargue el archivo que se le pone en este, algo como copiar y pegar sin copiar y pegar... creo (Ayuda)
require_once __DIR__ . 'models/conexion_db.php';

 
if ($_SERVER["REQUEST_METHOD"] === "POST") {

        //arreglod e errores
        $errores=[];

        //limpieza de datos
        $consulta = trim($_POST['id_consulta'] ?? '');
        $fecha = date('Y-m-d H:i');;
        $mascota = trim($_POST['id_mascota'] ?? '');
        $veterinario = trim($_POST['id_veterinario'] ?? '');
        $sintomas = htmlspecialchars(trim($_POST['sintomas'] ?? ''));
        $temperatura = htmlspecialchars(trim($_POST['temperatura'] ?? ''));
        $peso = trim($_POST['peso'] ?? '');
        $cardiaca = htmlspecialchars(trim($_POST['frecuencia_cardiaca'] ?? ''));
        $respiratoria = htmlspecialchars(trim($_POST['frecuencia_respiratoria'] ?? ''));
        $diagnostico = htmlspecialchars(trim($_POST['diagnostico'] ?? ''));
        $tratamiento = htmlspecialchars(trim($_POST['tratamiento'] ?? ''));
        $medicamentos = htmlspecialchars(trim($_POST['medicamentos'] ?? ''));
        $dosis = htmlspecialchars(trim($_POST['dosis'] ?? ''));
        $indicacion = htmlspecialchars(trim($_POST['indicaciones'] ?? ''));
        $estudios = htmlspecialchars(trim($_POST['estudios_asociados'] ?? ''));
        $proxima = htmlspecialchars(trim($_POST['proxima_cita'] ?? ''));
        $observaciones = htmlspecialchars(trim($_POST['observaciones'] ?? ''));
        $costo = trim($_POST['costo'] ?? '');
}

//Validaciones

if ($consulta=='')
    {
        $errores[]="Falta ligarlo a una consulta" ;     
    }

if ($mascota=='')
    {
        $errores{}="Debes agregar una mascota";
    }


?>