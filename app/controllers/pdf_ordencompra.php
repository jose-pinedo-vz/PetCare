<?php
declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/Pedidos.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id < 1) {
    http_response_code(400);
    exit('El identificador del pedido no es válido.');
}

try {
    $pedido = Pedidos::obtenerPedidoCompleto((int) $id);
} catch (Throwable $e) {
    error_log('Error al generar PDF de pedido: ' . $e->getMessage());
    http_response_code(500);
    exit('No se pudo generar el PDF del pedido.');
}

if ($pedido === null) {
    http_response_code(404);
    exit('No se encontró el pedido solicitado.');
}

$escapar = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
$formatearDinero = static fn ($valor): string => '$' . number_format((float) $valor, 2, '.', ',');
$filasProductos = '';
$totalCalculado = 0.0;

foreach ($pedido['productos'] as $producto) {
    $cantidad = (int) $producto['cantidad'];
    $precio = (float) $producto['precio_unitario'];
    $subtotal = $cantidad * $precio;
    $totalCalculado += $subtotal;
    $unidad = trim(implode(' ', array_filter([
        $producto['contenido'] ?? '',
        $producto['unidad_medida'] ?? '',
    ])));

    $filasProductos .= sprintf(
        '<tr><td>%s</td><td>%s</td><td>%s</td><td class="numero">%d</td><td class="numero">%s</td><td class="numero">%s</td></tr>',
        $escapar($producto['codigo'] ?? ''),
        $escapar($producto['nombre']),
        $escapar($unidad),
        $cantidad,
        $formatearDinero($precio),
        $formatearDinero($subtotal)
    );
}

$nombreProveedor = $pedido['nombre_comercial'] ?: $pedido['razon_social'];
$domicilioProveedor = implode(', ', array_filter([
    $pedido['domicilio'] ?? '',
    $pedido['ciudad'] ?? '',
    $pedido['estado_proveedor'] ?? '',
    $pedido['codigo_postal'] ?? '',
]));
$fechaSolicitud = $pedido['fecha_solicitud'] ? date('d/m/Y', strtotime($pedido['fecha_solicitud'])) : 'No disponible';
$fechaEsperada = $pedido['fecha_esperada'] ? date('d/m/Y', strtotime($pedido['fecha_esperada'])) : 'No definida';
$estadoPedido = ucfirst((string) $pedido['estado']);
$folioPdf = $escapar($pedido['clave_compra']);
$fechaSolicitudPdf = $escapar($fechaSolicitud);
$fechaEsperadaPdf = $escapar($fechaEsperada);
$estadoPdf = $escapar($estadoPedido);
$nombreProveedorPdf = $escapar($nombreProveedor);
$rfcPdf = $escapar($pedido['rfc'] ?? 'No registrado');
$domicilioPdf = $escapar($domicilioProveedor ?: 'Domicilio no registrado');
$correoPdf = $escapar($pedido['correo'] ?? 'No registrado');
$telefonoPdf = $escapar($pedido['telefono'] ?? 'No registrado');
$clavePdf = $escapar($pedido['clave_compra']);
$totalPdf = $formatearDinero($pedido['total_estimado'] ?? $totalCalculado);

$htmlPdf = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 36px; }
        body { font-family: DejaVu Sans, sans-serif; color: #243044; font-size: 11px; }
        .encabezado { width: 100%; margin-bottom: 28px; }
        .marca { width: 55%; vertical-align: top; }
        .orden { width: 45%; text-align: right; vertical-align: top; }
        .logo { border: 1px dashed #8b98a8; color: #65758b; display: inline-block; padding: 18px 24px; }
        h1 { color: #176b58; font-size: 23px; margin: 0 0 8px; }
        h2 { color: #176b58; font-size: 13px; margin: 0 0 8px; }
        p { margin: 4px 0; }
        .bloque { border: 1px solid #dce3e9; padding: 12px; margin-bottom: 18px; }
        table.productos { border-collapse: collapse; width: 100%; margin-top: 12px; }
        .productos th { background: #6b4817; color: #fff; text-align: left; }
        .productos th, .productos td { border: 1px solid #dce3e9; padding: 9px 7px; }
        .numero { text-align: right; white-space: nowrap; }
        .total { margin-top: 14px; text-align: right; font-size: 15px; font-weight: bold; }
        .nota { color: #65758b; font-size: 9px; margin-top: 30px; }
    </style>
</head>
<body>
    <table class="encabezado">
        <tr>
            <td class="marca">
                <div class="logo">PetCare</div>
                <p><strong>Veterinaria PetCare</strong></p>
            </td>
            <td class="orden">
                <h1>ORDEN DE COMPRA</h1>
                <p><strong>Folio:</strong> OC-{$folioPdf}</p>
                <p><strong>Fecha de solicitud:</strong> {$fechaSolicitudPdf}</p>
                <p><strong>Entrega esperada:</strong> {$fechaEsperadaPdf}</p>
                <p><strong>Estado:</strong> {$estadoPdf}</p>
            </td>
        </tr>
    </table>

    <div class="bloque">
        <h2>Proveedor</h2>
        <p><strong>{$nombreProveedorPdf}</strong></p>
        <p>RFC: {$rfcPdf}</p>
        <p>{$domicilioPdf}</p>
        <p>Correo: {$correoPdf} · Teléfono: {$telefonoPdf}</p>
    </div>

    <h2>Productos solicitados</h2>
    <table class="productos">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Unidad / presentación</th>
                <th class="numero">Cantidad</th>
                <th class="numero">Precio unitario</th>
                <th class="numero">Importe</th>
            </tr>
        </thead>
        <tbody>
            {$filasProductos}
        </tbody>
    </table>

    <p class="total">Total estimado: {$totalPdf}</p>
    <p class="nota">Documento generado desde el registro del pedido {$clavePdf}.</p>
</body>
</html>
HTML;

if (isset($_GET['pdf']) || isset($_GET['download'])) {
    $opciones = new Options();
    $opciones->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($opciones);
    $dompdf->loadHtml($htmlPdf, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream(
        'orden-compra-OC-' . (int) $pedido['clave_compra'] . '.pdf',
        ['Attachment' => isset($_GET['download'])]
    );
    exit;
}

$idConsulta = (int) $pedido['clave_compra'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de compra OC-<?= $idConsulta ?> - PetCare</title>
    <style>
        * { box-sizing: border-box; }
        body { background: #eef2f5; color: #243044; font-family: Arial, sans-serif; margin: 0; }
        .barra { align-items: center; background: #fff; box-shadow: 0 2px 8px #2430441a; display: flex; gap: 12px; justify-content: space-between; padding: 14px 24px; }
        .titulo { font-size: 18px; font-weight: 700; }
        .acciones { display: flex; gap: 10px; }
        .boton { border: 0; border-radius: 5px; color: #fff; cursor: pointer; font-size: 14px; font-weight: 600; padding: 11px 16px; }
        .descargar { background: #176b58; }
        .marco { height: calc(100vh - 68px); padding: 18px; }
        iframe { background: #fff; border: 0; box-shadow: 0 3px 14px #24304426; height: 100%; width: 100%; }
        @media (max-width: 600px) {
            .barra { align-items: flex-start; flex-direction: column; padding: 12px; }
            .acciones { width: 100%; }
            .boton { flex: 1; padding: 10px 8px; }
            .marco { height: calc(100vh - 112px); padding: 10px; }
        }
    </style>
</head>
<body>
    <header class="barra">
        <div class="titulo">Vista previa de la orden de compra OC-<?= $idConsulta ?></div>
        <div class="acciones">
            <a href="?id=<?= $idConsulta ?>&amp;download=1" style="text-decoration: none;">
                <button class="boton descargar" type="button">Descargar PDF</button>
            </a>
        </div>
    </header>
    <main class="marco">
        <iframe src="?id=<?= $idConsulta ?>&amp;pdf=1" title="Vista previa del PDF de la orden de compra"></iframe>
    </main>
</body>
</html>
