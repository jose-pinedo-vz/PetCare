<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


session_start();

require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/Productos.php';
require_once __DIR__ . '/../models/Pedidos.php';
require_once __DIR__ . '/../controllers/validar_orden_compra.php';
$configuracion = require __DIR__ . '/../../config_correo.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Metodo no permitido');
}

// datos

$id_proveedor = $_SESSION['orden_proveedor'] ?? null;
$productosOrden = $_SESSION['orden_productos'] ?? [];

if ($id_proveedor == null || count($productosOrden) === 0) {
    exit("No hay una orden activa");
}

$errores = validarCantidadesOrdenCompra($productosOrden, $_SESSION['orden_cantidades'] ?? []);

if(count($errores) > 0) {
    $_SESSION['error_orden'] = implode("\n\n", $errores);
    header('Location: ../views/orden_compra.php?proveedor=' . (int) $id_proveedor);
    exit;
}

$proveedorNombre = trim(Productos::obtenerNombreProveedor($id_proveedor) ?? '');
$proveedorEmail = trim(Productos::obtenerCorreoProveedor($id_proveedor) ?? '');

if ($proveedorNombre === '' || $proveedorEmail === '') {
    exit("El proveedor no tiene nombre o el correo no esta registrado");
}

if (!filter_var($proveedorEmail, FILTER_VALIDATE_EMAIL)) {
    exit("El correo del proveedor no es valido");
}

try {
    $id_pedido = Pedidos::crearPedido((int) $id_proveedor, $productosOrden, $_SESSION['orden_cantidades'] ?? []);
} catch (Throwable $e) {
    error_log("Error al guardar la orden " . $e -> getMessage());
    exit('No se puedo guardar la orden <br><a href="../views/orden_compra.php?proveedor=' . (int) $id_proveedor . '">Regresar</a>');
}

$proveedorNombreSeguro = htmlspecialchars($proveedorNombre, ENT_QUOTES, "UTF-8");

$listaHTML = "";
$listaTexto = "";

foreach ($productosOrden as $id_prod => $p) {
    $cantidad = (int) ($_SESSION['orden_cantidades'][$id_prod] ?? 1);
    $productoNombreSeguro = htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8');

    $listaHTML .= "<li> <strong> {$productoNombreSeguro} </strong> - Cantidad: {$cantidad} </li>"; 
    $listaTexto .= "- {$p['nombre']}: {$cantidad}\n";
}
$ubicacion = 'Tlaltenango de Sanchez Roman';

$mail = new PHPMailer(true);

$enviado = false;

try {
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Host = $configuracion['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $configuracion['username'];
    $mail->Password = $configuracion['password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $configuracion['port'];
    
    // remitente
    $mail->setFrom($configuracion['username'], $configuracion['from_name']);

    // destinatario 
    $mail->addAddress($proveedorEmail, $proveedorNombre);

    // Si el proveedor responde, le llega a PetCare
    $mail->addReplyTo($configuracion['username'], $configuracion['from_name']);

    // mensaje
    $mail->isHTML(true);
    $mail->Subject = 'Pedido OC-' . $id_pedido . ' - PetCare';

    $mail->Body = "
        <h3> Estimado/a {$proveedorNombreSeguro}: </h3>
        <p> Nos ponemos en contacto con ustedes desde PetCare, ya que estamos interesados en ampliar nuestro stock debido a la alta demanda que hemos tenido.</p>
        <p> Actualmente requerimos solicitar informacion sobre la disponibilidad y cotizacion para el reabastecimiento de los siguientes productos: </p>
        <ul> 
            {$listaHTML}
        </ul>
        <p> Agradecemos de antemano si nos pueden compartir su catalogo actualizado, los precios por volumen, las condiciones de pago y los tiempos estimados de envio hacia nuestra ubicacion {$ubicacion}.</p>
        <p> Quedamos antentos a sus comentarios para poder proceder con el pedido a la brevedad posible.</p>
        <p> <strong>Atentemente</strong> <br> PetCare </p>
    ";

    $mail->AltBody = "Estimado/a {$proveedorNombreSeguro}: \n\n" 
        . "Desde PetCare solicitamos disponibilidad y cotizacion de: \n\n"
        . $listaTexto
        . "\nUbicacion: {$ubicacion}\n\n"
        . "Atentamente, \nPetCare";

    $mail->send();

    $enviado = true;

} catch (Exception $error) {
    error_log("Error al mandar el correo electronico " . $mail -> ErrorInfo);
    Pedidos::cancelarPedido($id_pedido);
} 

if ($enviado) {
    $_SESSION['orden_productos'] = [];
    $_SESSION['orden_cantidades'] = [];
}

?>

<?php if ($enviado) { ?>
    <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Pedido enviado - PetCare</title>
            <style>
                body { margin: 0; height: 100vh; display: flex; flex-direction: column; font-family: Arial, sans-serif; background: #eef2f5; }
                .aviso { background: #176b58; color: #fff; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; gap: 12px; font-weight: 600; }
                .aviso a { color: #fff; background: #0f4d3f; padding: 9px 14px; border-radius: 5px; text-decoration: none; }
                iframe { flex: 1; border: 0; width: 100%; }
            </style>
        </head>
        <body>
            <div class="aviso">
                <span>Pedido enviado correctamente a <?php echo $proveedorNombreSeguro ?></span>
                <a href="../views/inventario.php">Volver al inventario</a>
            </div>
            <iframe src="pdf_ordencompra.php?id=<?php echo (int) $id_pedido ?>" title="Orden de compra"></iframe>
        </body>
        </html>
<?php } else { ?>
    <div style="max-width:600px;margin:50px auto;text-align:center;font-family:sans-serif;">
        <h3>No se pudo enviar el pedido</h3>
        <p><?php echo htmlspecialchars($mail->ErrorInfo, ENT_QUOTES, 'UTF-8') ?></p>
        <a href="../views/orden_compra.php?proveedor=<?php echo (int) $id_proveedor ?>">Regresar a la orden</a>
    </div>
<?php } ?>
