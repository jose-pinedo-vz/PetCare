<?php
declare(strict_types=1);
date_default_timezone_set('America/Mexico_City');
session_start(); //Que es session start?
//requiere_once hace que se cargue el archivo que se le pone en este
require_once __DIR__ . '/../models/fichaMedicaModelo.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") { //Si no es POST lo manda a citas de nuevo
    header("Location: ../views/citas.php");
    exit();
} 


//Datos
$consulta=trim($_POST['id_consulta'] ?? '');
$fecha=date("Y-m-d H:i");
$mascota=trim($_POST['id_mascota'] ?? '');
$veterinario=trim($_POST['id_veterinario'] ?? '');

//Datos
$sintomas=trim($_POST['sintomas'] ?? '');
$temperatura=trim($_POST['temperatura'] ?? '');
$peso=trim($_POST['peso'] ?? '');
$FrecCar=trim($_POST['frecuenciaC'] ?? '');
$FrecResp=trim($_POST['frecuenciaR'] ?? '');

//Tratamiento
$tratamiento=trim($_POST['tratamiento'] ?? '');
$medicamento=trim($_POST['Medicamento'] ?? '');
$indicaciones=trim($_POST["indicaciones"] ?? '');
$dosis=trim($_POST['Dosis'] ?? '');
$estudioAs=trim($_POST['estudiosA'] ?? '');

$diagnostico=trim($_POST['diagnostico'] ?? '');
$proximaCita=trim($_POST['fecha'] ?? '');
$costo=trim($_POST['precio'] ?? '');
$observacion=trim($_POST['observaciones'] ?? '');

$errores=[];

if ($consulta==='')
    {
        $errores[]="No esta ligado a una consulta";
    }

if ($veterinario==='')
    {
        $errores[]="No esta ligado a un veterinario";
    }

if ($sintomas==='' || $temperatura==='' || $peso==='' || $FrecCar==='' || $FrecResp==='')
    {
        $errores[]="Porfavor, llene los campos obligatorios del apartado de Datos del paciente";
    }

if ($tratamiento==='' || $medicamento==='' || $indicaciones==='')
    {
        $errores[]="Porfavor, llene los campos obligatorios del apartado Detalles de tratamiento";
    }



//Datos a insertar
$datos=[
    'id_consulta'=>(int)$consulta,
    'fecha'=>$fecha,
    'id_mascota'=>(int)$mascota,
    'id_veterinario'=>(int)$veterinario,
    'sintomas'=>$sintomas,
    'temperatura'=>$temperatura,
    'peso'=>$peso,
    'frecuencia_cardiaca'=>$FrecCar,
    'frecuencia_respiratoria'=>$FrecResp,
    'diagnostico'=>$diagnostico,
    'tratamiento'=>$tratamiento,
    'medicamentos'=>$medicamento,
    'dosis'=>$dosis,
    'indicaciones'=>$indicaciones,
    'estudios_asociados'=>$estudioAs,
    'proxima_cita'=>$proximaCita,   
    'observaciones'=>$observacion,
    'costo'=>$costo,
];

$ficha=new FichaMedica();
$resultado=$ficha->insertar($datos);

if($resultado===false){
    $_SESSION['errores']="no se logro guardar la ficha";
    header("Location: ../views/FichaMedica.php?id_consulta=" . (int)$consulta);
    exit();
}

header("Location: ../views/citas.php?exito=1");
?>




<!-- Session ayuda a crear sesiones en lo que el cliente permanece en una pagina web

Lo que mas me intereso es el uso de $_SESSION que es una variable global que se usa
siempre que se este dentro de la sesion, funciona como un array $_SESSION[Usuario]="Tal"
Esto hace que si lo defino en la pagina de login y lo necesito en la pagina de mascotas 
pueda usarlo sin problema porque esta guardado en la misma sesion, una vez no lo necesite se
puede borrar todo con session_destroy(); o se podria borrar solo una variable con
unset($_SESSION["valor1"]);-->

<!-- _POST() Sirve para enviar variables unicamente entre 2 paginas, solo eso -->