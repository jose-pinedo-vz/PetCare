<?php
declare(strict_types=1);
date_default_timezone_set('America/Mexico_City');
//requiere_once hace que se cargue el archivo que se le pone en este
require_once __DIR__ . '/../models/fichaMedicaModelo.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
};

$idVeterinario=(int)$_SESSION['id_usuario'];
$idConsulta=(int)$_SESSION['id_consulta'];

$ficha=new FichaMedica();

function errorSession(string $error) : null
{
    $_SESSION['error']=$error;
    $_SESSION['oldid_consulta']=$_POST['id_consulta'] ?? '';
    $_SESSION['oldid_mascota']=$_POST['id_mascota'] ?? '';
    $_SESSION['oldid_veterinario']=$_POST['id_veterinario'] ?? '';
    $_SESSION['oldSintomas']=$_POST['sintomas'] ?? '';
    $_SESSION['oldTemperatura']=$_POST['temperatura'] ?? '';
    $_SESSION['oldPeso']=$_POST['peso'] ?? '';
    $_SESSION['oldFrecuenciaC']=$_POST['frecuenciaC'] ?? '';
    $_SESSION['oldFrecuenciaR']=$_POST['frecuenciaR'] ?? '';
    $_SESSION['oldTratamiento']=$_POST['tratamiento'] ?? '';
    $_SESSION['oldMedicamento']=$_POST['Medicamento'] ?? '';
    $_SESSION['oldIndicacion']=$_POST["indicaciones"] ?? '';
    $_SESSION['oldDosis']=$_POST['Dosis'] ?? '';
    $_SESSION['oldEstudiosaA']=$_POST['estudiosA'] ?? '';
    $_SESSION['oldDiagnostico']=$_POST['diagnostico'] ?? '';
    $_SESSION['oldFecha']=$_POST['fecha'] ?? '';
    $_SESSION['oldPrecio']=$_POST['precio'] ?? '';
    $_SESSION['oldObservaciones']=$_POST['observaciones'] ?? '';

    header("Location: ../views/FichaMedica.php");
    exit();
}

$Datosconsulta=$ficha->datosConsulta($idConsulta);
if ($Datosconsulta===null){
    $_SESSION['error']="La consulta no existe o ya fue atendida";
    header("Location: ../views/citas.php");
    exit();
}
$idCliente=(int)$Datosconsulta['id_cliente'];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $mascotas = $ficha->agarraMascotas($idCliente);
    require __DIR__ . '/../views/FichaMedica.php';   // la vista usa $mascotas e $idConsulta
    exit();
    }

if ($_SERVER["REQUEST_METHOD"] === "POST") 
{

    //Datos
    $consulta=trim($_POST['id_consulta'] ?? '');
    $fecha=date("Y-m-d H:i");
    $mascota=trim($_POST['mascota'] ?? '');
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

    if ($consulta==='')
        {
            errorSession("No esta ligado a un veterinario");
        }

    if ($mascota === '' || !ctype_digit($mascota))
        {
            errorSession("No esta ligado a una mascota");
        }

    if ($veterinario==='')
        {
            errorSession("No esta ligado a un veterinario");
        }

    if (!is_numeric($temperatura) || !is_numeric($peso) || !is_numeric($costo))
        {
            errorSession("Temperatura, peso y precio deben ser numéricos");
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
        'estudios_asociados'=>$estudioAs !=='' ? $estudioAs:null, //Si la variable tiene contenido la guardas, sino pos pone null
        'proxima_cita'=>$proximaCita !=='' ? $estudioAs:null,
        'observaciones'=>$observacion,
        'costo'=>$costo,
    ];

    $resultado=$ficha->insertar($datos);

    if($resultado['exito']===false){
        $_SESSION['errores']=["no se logro guardar la ficha"];
        header("Location: ../views/FichaMedica.php?id_consulta=" . (int)$consulta);
        exit();
    }



    header("Location: ../views/citas.php?exito=1");
    exit();
}
?>

<!-- Session ayuda a crear sesiones en lo que el cliente permanece en una pagina web

Lo que mas me intereso es el uso de $_SESSION que es una variable global que se usa
siempre que se este dentro de la sesion, funciona como un array $_SESSION[Usuario]="Tal"
Esto hace que si lo defino en la pagina de login y lo necesito en la pagina de mascotas 
pueda usarlo sin problema porque esta guardado en la misma sesion, una vez no lo necesite se
puede borrar todo con session_destroy(); o se podria borrar solo una variable con
unset($_SESSION["valor1"]);-->

<!-- _POST() Sirve para enviar variables unicamente entre 2 paginas, solo eso -->