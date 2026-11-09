<?php
require_once '../models/conexion_db.php';
$conexion = ConexionDB::obtenerConexion();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ventas.php");
    exit;
}

$cliente_id = !empty($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : null;
$productos_json = $_POST['productos_json'] ?? '[]';
$servicios_json = $_POST['servicios_json'] ?? '[]';
$productos = json_decode($productos_json, true);
$servicios = json_decode($servicios_json, true);

if (!is_array($productos)) {
    $productos = [];
}

if (!is_array($servicios)) {
    $servicios = [];
}

if (empty($productos) && empty($servicios)) {

    header("Location: ventas.php?error=carrito_vacio");
    exit;
}

$subtotal_general = 0;


//Productos
foreach ($productos as $p) {
    $precio = (float)($p['precio'] ?? 0);
    $cantidad = (int)($p['cantidad'] ?? 1);
    $descuento = (float)($p['descuento'] ?? 0);

    $subtotal_general +=
        ($precio * $cantidad) - $descuento;
}

//Servicios
foreach ($servicios as $s) {
    $precio = (float)($s['precio'] ?? 0);
    $cantidad = (int)($s['cantidad'] ?? 1);
    $descuento = (float)($s['descuento'] ?? 0);

    $subtotal_general +=
        ($precio * $cantidad) - $descuento;
}

$iva_general = $subtotal_general * 0.16;
$total_general = $subtotal_general + $iva_general;

if ($total_general <= 0) {
    header("Location: ventas.php?error=total_invalido");
    exit;
}
$ruta_pago = "../pago_tarjeta/index.php";

?>
<!--Enviamos los datos a pago mediante Post--->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Redirigiendo al pago...</title>
</head>

<body>
    <form id="formPagoTarjeta" action="<?= htmlspecialchars($ruta_pago) ?>" method="POST">
        <input
            type="hidden"
            name="id_cliente"
            value="<?= htmlspecialchars((string)$cliente_id) ?>"
        >
        <!---Productos--->
        <input
            type="hidden"
            name="productos_json"
            value="<?= htmlspecialchars($productos_json,ENT_QUOTES,'UTF-8') ?>"
        >
        <!---Servicios--->
        <input
            type="hidden"
            name="servicios_json"
            value="<?= htmlspecialchars($servicios_json,ENT_QUOTES,'UTF-8') ?>"
        >
        <!---Subtotal--->
        <input
            type="hidden"
            name="subtotal"
            value="<?= htmlspecialchars(number_format($subtotal_general, 2, '.', '')) ?>"
        >
        <!---IVA--->
        <input
            type="hidden"
            name="iva"
            value="<?= htmlspecialchars(number_format($iva_general, 2, '.', '')) ?>"
        >
        <!---Total--->
        <input
            type="hidden"
            name="total"
            value="<?= htmlspecialchars(number_format($total_general, 2, '.', '')) ?>"
        >
    </form>

    <script>
        //Enviar automáticamente al modulo de pago
        document.getElementById('formPagoTarjeta').submit();
    </script>

</body>
</html>