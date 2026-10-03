<?php
require_once __DIR__ . '/../controllers/validar_empleados.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $datos = [
        $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
        $apellido = htmlspecialchars(trim($_POST['apellido'] ?? ''));
        $telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''));
        $correo = htmlspecialchars(trim($_POST['correo'] ?? ''));
        $calle = htmlspecialchars(trim($_POST['calle'] ?? ''));
        $numero_exterior = htmlspecialchars(trim($_POST['numero_exterior'] ?? ''));
        $numero_interior = htmlspecialchars(trim($_POST['numero_interior'] ?? ''));
        $colonia = htmlspecialchars(trim($_POST['colonia'] ?? ''));
        $ciudad = htmlspecialchars(trim($_POST['ciudad'] ?? ''));
        $estado = htmlspecialchars(trim($_POST['estado'] ?? ''));
        $codigo_postal = htmlspecialchars(trim($_POST['codigo_postal'] ?? ''));
        $puesto = htmlspecialchars(trim($_POST['puesto'] ?? ''));
        $especialidad = htmlspecialchars(trim($_POST['especialidad'] ?? ''));
        $num_cedula_profesional = htmlspecialchars(trim($_POST['num_cedula_profesional'] ?? ''));
        $Fecha_de_contratacion = htmlspecialchars(trim($_POST['Fecha_de_contratacion'] ?? ''));
        $horario = htmlspecialchars(trim($_POST['horario'] ?? ''));
    ];

    validacion($datos);

    if ($errores) {
            header("Location: agregar_empleado.php");
            exit;
    }

}

?>
