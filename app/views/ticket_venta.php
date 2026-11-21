<?php
// Ticket de una venta (80 mm). Si se pago con tarjeta, incluye el comprobante del banco.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../models/ventas.php';

function h($texto): string
{
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}
function din($n): string
{
    return '$' . number_format((float)$n, 2);
}
function fechaCorta($texto): string
{
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/', (string)$texto, $m)) {
        return $m[3] . '/' . $m[2] . '/' . $m[1] . ' ' . $m[4] . ':' . $m[5];
    }
    return (string)$texto;
}
function linea(string $izq, string $der, string $clase = ''): void
{
    echo '<div class="linea ' . h($clase) . '"><span>' . h($izq) . '</span><span>' . h($der) . '</span></div>';
}

$id = (int)($_GET['id'] ?? 0);
$d = $id > 0 ? Ventas::detalleVenta($id) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket <?php echo h($d['folio'] ?? ''); ?></title>
    <style>
        @page { size: 80mm auto; margin: 4mm; }
        body { width: 72mm; margin: 0 auto; font-family: "Courier New", monospace; font-size: 12px; color: #000; }
        h1 { font-size: 16px; text-align: center; margin: 4px 0; }
        h2 { font-size: 12px; text-align: center; margin: 4px 0; }
        p { margin: 2px 0; }
        .linea { display: flex; justify-content: space-between; gap: 6px; }
        .sep { border-top: 1px dashed #000; margin: 6px 0; }
        .centro { text-align: center; }
        .fuerte { font-weight: bold; }
    </style>
</head>
<body>
<?php if (!$d): ?>
    <p class="centro">No se pudo cargar el ticket.</p>
<?php else: ?>
    <h1>PetCare</h1>
    <div class="sep"></div>
    <p>Folio: <?php echo h($d['folio']); ?></p>
    <p>Fecha: <?php echo h(fechaCorta($d['fecha'])); ?></p>
    <p>Cajero: <?php echo h($d['empleado_nombre']); ?></p>
    <p>Cliente: <?php echo h($d['cliente_nombre']); ?></p>
    <div class="sep"></div>

    <?php foreach ($d['productos'] as $x) { linea($x['cantidad'] . ' x ' . $x['nombre'], din($x['subtotal'])); } ?>
    <?php foreach ($d['servicios'] as $x) { linea('1 x ' . $x['nombre'], din($x['subtotal'])); } ?>
    <div class="sep"></div>

    <?php
    linea('Subtotal', din($d['subtotal']));
    linea('IVA', din($d['iva']));
    linea('Total', din($d['total']), 'fuerte');
    ?>
    <div class="sep"></div>
    <?php
    linea('Forma de pago', ucfirst((string)$d['forma_pago']));
    if ($d['forma_pago'] === 'efectivo') {
        linea('Recibido', din($d['monto_recibido']));
        linea('Cambio', din($d['cambio']));
    } elseif ($d['forma_pago'] === 'transferencia' && trim((string)($d['referencia_pago'] ?? '')) !== '') {
        linea('Referencia', (string)$d['referencia_pago']);
    }
    ?>

    <?php if (!empty($d['banco'])): $b = $d['banco']; ?>
        <div class="sep"></div>
        <h2>BANCO EMISOR - COMPROBANTE</h2>
        <?php
        linea('Operacion', $b['folio']);
        linea('Estado', strtoupper($b['estado']), 'fuerte');
        linea('Comercio', $b['comercio']);
        linea('Tarjeta', $b['tarjeta']);
        linea('Monto', din($b['monto']) . ' MXN');
        linea('Fecha y hora', fechaCorta($b['fecha']));
        linea('Codigo resp.', $b['codigo']);
        ?>
        <p>Mensaje: <?php echo h($b['codigo'] === '00' ? 'Transaccion autorizada con exito' : $b['estado']); ?></p>
    <?php endif; ?>

    <div class="sep"></div>
    <p class="centro">Gracias por su compra</p>
    <script>
        window.addEventListener('load', function () { window.focus(); window.print(); });
    </script>
<?php endif; ?>
</body>
</html>
