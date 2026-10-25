<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$productos = [
    [
        'codigo' => 'ALI-001',
        'descripcion' => 'Alimento para perro adulto',
        'unidad' => 'Saco de 20 kg',
        'cantidad' => 5,
        'precio' => 680.00,
    ],
    [
        'codigo' => 'MED-014',
        'descripcion' => 'Guantes de exploración',
        'unidad' => 'Caja de 100 piezas',
        'cantidad' => 10,
        'precio' => 95.50,
    ],
];

$total = array_reduce(
    $productos,
    static fn (float $acumulado, array $producto): float =>
        $acumulado + ($producto['cantidad'] * $producto['precio']),
    0.0
);

$formatearDinero = static fn (float $cantidad): string =>
    '$' . number_format($cantidad, 2, '.', ',');

$filasProductos = '';
foreach ($productos as $producto) {
    $subtotal = $producto['cantidad'] * $producto['precio'];
    $filasProductos .= sprintf(
        '<tr><td>%s</td><td>%s</td><td>%s</td><td class="numero">%d</td><td class="numero">%s</td><td class="numero">%s</td></tr>',
        htmlspecialchars($producto['codigo'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($producto['descripcion'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($producto['unidad'], ENT_QUOTES, 'UTF-8'),
        $producto['cantidad'],
        $formatearDinero($producto['precio']),
        $formatearDinero($subtotal)
    );
}

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
                <!-- Aquí iría el logotipo o la imagen de la empresa. -->
                <div class="logo">LOGOTIPO DE LA EMPRESA</div>
                <p><strong>PetCare</strong></p>
                <!-- Aquí irían el domicilio, teléfono y datos fiscales de la empresa. -->
                <p>Domicilio y datos de contacto de la empresa</p>
            </td>
            <td class="orden">
                <h1>ORDEN DE COMPRA</h1>
                <p><strong>Folio:</strong> OC-2026-001</p>
                <p><strong>Fecha:</strong> 24/10/2026</p>
                <p><strong>Entrega solicitada:</strong> 31/10/2026</p>
            </td>
        </tr>
    </table>

    <div class="bloque">
        <h2>Proveedor</h2>
        <!-- Aquí irían el proveedor seleccionado y sus datos fiscales/de contacto. -->
        <p><strong>Comercializadora Patitas, S.A. de C.V.</strong></p>
        <p>RFC: CPA010203AB1</p>
        <p>Av. Ejemplo 123, Col. Centro, Guadalajara, Jalisco, C.P. 44100</p>
        <p>Correo: ventas@proveedor-ejemplo.com · Teléfono: 33 1234 5678</p>
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

    <p class="total">Total: {$formatearDinero($total)}</p>
    <div class="bloque">
        <h2>Observaciones</h2>
        <p>Datos de muestra para visualizar el formato de la orden de compra.</p>
        <!-- Aquí irían las condiciones de pago y notas reales de la orden. -->
    </div>
    <p class="nota">Documento de demostración con información estática. No representa una orden confirmada.</p>
</body>
</html>
HTML;

if (isset($_GET['pdf'])) {
    $opciones = new Options();
    $opciones->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($opciones);
    $dompdf->loadHtml($htmlPdf, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream('orden-compra-demo.pdf', ['Attachment' => false]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista previa de orden de compra - PetCare</title>
    <style>
        * { box-sizing: border-box; }
        body { background: #eef2f5; color: #243044; font-family: Arial, sans-serif; margin: 0; }
        .barra { align-items: center; background: #fff; box-shadow: 0 2px 8px #2430441a; display: flex; gap: 12px; justify-content: space-between; padding: 14px 24px; }
        .titulo { font-size: 18px; font-weight: 700; }
        .acciones { display: flex; gap: 10px; }
        .boton { border: 0; border-radius: 5px; color: #fff; cursor: default; font-size: 14px; font-weight: 600; padding: 11px 16px; }
        .descargar { background: #176b58; }
        .enviar { background: #2675b8; }
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
        <div class="titulo">Vista previa de la orden de compra</div>
        <div class="acciones">
            <button class="boton descargar" type="button">Descargar PDF</button>
            <button class="boton enviar" type="button">Enviar orden de compra</button>
        </div>
    </header>
    <main class="marco">
        <iframe src="?pdf=1" title="Vista previa del PDF de la orden de compra"></iframe>
    </main>
</body>
</html>