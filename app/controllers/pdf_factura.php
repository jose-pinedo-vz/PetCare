<?php
declare(strict_types=1);

// Factura de una venta en PDF (solo si la venta tiene datos fiscales guardados).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    http_response_code(403);
    exit('Inicia sesión para descargar la factura.');
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/ventas.php';

use Dompdf\Dompdf;
use Dompdf\Options;

function hf($t): string { return htmlspecialchars((string)$t, ENT_QUOTES, 'UTF-8'); }
function mf($n): string { return '$' . number_format((float)$n, 2); }

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id < 1) {
    http_response_code(400);
    exit('La venta no es válida.');
}

try {
    $d = Ventas::detalleVenta((int)$id);
} catch (Throwable $e) {
    error_log('pdf_factura: ' . $e->getMessage());
    http_response_code(500);
    exit('No se pudo generar la factura.');
}
if (!$d) {
    http_response_code(404);
    exit('La venta no existe.');
}
if (empty($d['factura']) || !is_array($d['factura'])) {
    http_response_code(409);
    exit('Esta venta no tiene factura solicitada.');
}
$f = $d['factura'];

$filas = '';
foreach ($d['productos'] as $x) {
    $filas .= '<tr><td>' . hf($x['nombre']) . '</td><td class="r">' . (int)$x['cantidad'] . '</td><td class="r">' . mf($x['subtotal']) . '</td></tr>';
}
foreach ($d['servicios'] as $x) {
    $filas .= '<tr><td>' . hf($x['nombre']) . '</td><td class="r">1</td><td class="r">' . mf($x['subtotal']) . '</td></tr>';
}

$html = '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><style>
body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#222}
h1{font-size:20px;margin:0;color:#0b6b6b} .m{color:#666}
table{width:100%;border-collapse:collapse;margin-top:12px}
th{background:#0b6b6b;color:#fff;text-align:left;padding:6px} td{padding:6px;border-bottom:1px solid #ddd}
.r{text-align:right} .caja{border:1px solid #ccc;padding:8px;margin-top:12px}
.tot td{border:0;padding:3px 6px} .fuerte{font-weight:bold;font-size:13px}
</style></head><body>
<h1>PetCare</h1><div class="m">Factura de venta ' . hf($d['folio']) . ' · ' . hf(substr((string)$d['fecha'], 0, 16)) . '</div>
<div class="caja"><strong>Datos fiscales del cliente</strong><br>
RFC: ' . hf($f['rfc'] ?? '') . '<br>Razón social: ' . hf($f['razon_social'] ?? '') . '<br>
C.P.: ' . hf($f['codigo_postal'] ?? '') . '<br>Correo: ' . hf($f['correo'] ?? '') . '</div>
<table><thead><tr><th>Concepto</th><th class="r">Cant.</th><th class="r">Importe</th></tr></thead><tbody>' . $filas . '</tbody></table>
<table class="tot" style="width:45%;margin-left:55%">
<tr><td>Subtotal</td><td class="r">' . mf($d['subtotal']) . '</td></tr>
<tr><td>IVA 16%</td><td class="r">' . mf($d['iva']) . '</td></tr>
<tr class="fuerte"><td>Total</td><td class="r">' . mf($d['total']) . ' MXN</td></tr></table>
<p class="m">Forma de pago: ' . hf(ucfirst((string)$d['forma_pago'])) . '. Documento informativo generado por el sistema PetCare.</p>
</body></html>';

$op = new Options();
$op->set('isRemoteEnabled', false);
$dompdf = new Dompdf($op);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('Factura_' . $d['folio'] . '.pdf', ['Attachment' => false]);
