<div class="corte-tabla-envoltura">
    <table class="corte-tabla">
        <thead><tr><th>Fecha</th><th>Corte</th><th>Venta</th><th>Cliente</th><th>Forma de pago</th><th>Referencia</th><th>Importe</th><th>Total de venta</th></tr></thead>
        <tbody>
            <?php foreach ($pagosReporte as $pagoReporte): ?>
                <?php
                $esDevolucion = $pagoReporte['tipo'] === 'devolucion';
                $importePago = (float)$pagoReporte['importe'];
                ?>
                <tr>
                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($pagoReporte['fecha']))) ?></td>
                    <td><?= htmlspecialchars($pagoReporte['folio_corte']) ?></td>
                    <td><?= htmlspecialchars($pagoReporte['folio_venta']) ?></td>
                    <td><?= htmlspecialchars($pagoReporte['cliente'] ?? 'Público general') ?></td>
                    <td><?= htmlspecialchars(ucfirst($pagoReporte['forma_pago'])) ?><?= $esDevolucion ? ' · Devolución' : '' ?></td>
                    <td><?= htmlspecialchars($pagoReporte['referencia_pago'] ?? '—') ?></td>
                    <td class="corte-monto"><?= $monedaConSigno($esDevolucion ? -abs($importePago) : $importePago) ?></td>
                    <td class="corte-monto"><?= $moneda($pagoReporte['total_venta']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$pagosReporte): ?><tr><td colspan="8">No hay pagos vinculados a cortes diarios cerrados en este periodo.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
