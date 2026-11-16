<dialog class="corte-dialog" id="modalDetalleCorte" aria-labelledby="tituloDetalleModal" data-auto-open>
    <div class="corte-dialog-encabezado">
        <div><span class="corte-kicker">Detalle del registro</span><h2 id="tituloDetalleModal">Corte <?= htmlspecialchars($detalleCorte['folio']) ?></h2></div>
        <button class="corte-dialog-cerrar" type="button" data-cerrar-dialog aria-label="Cerrar ventana">×</button>
    </div>
    <div class="corte-detalle-modal">
        <div><span>Turno</span><strong><?= htmlspecialchars(ucfirst($detalleCorte['turno'])) ?></strong></div>
        <div><span>Apertura</span><strong><?= htmlspecialchars(date('d/m/Y H:i', strtotime($detalleCorte['apertura']))) ?></strong></div>
        <div><span>Cajero de apertura</span><strong><?= htmlspecialchars($detalleCorte['cajero_apertura']) ?></strong></div>
        <div><span>Empleado de cierre</span><strong><?= htmlspecialchars($detalleCorte['cajero_cierre'] ?? '—') ?></strong></div>
        <div><span>Estado</span><strong><?= htmlspecialchars(ucfirst($detalleCorte['estado'])) ?></strong></div>
        <?php if ($detalleCorte['estado'] === 'cerrado'): ?>
            <div><span>Efectivo esperado</span><strong><?= $moneda($detalleCorte['efectivo_esperado']) ?></strong></div>
            <div><span>Efectivo contado</span><strong><?= $moneda($detalleCorte['efectivo_contado']) ?></strong></div>
            <div><span>Diferencia</span><strong><?= $monedaConSigno($detalleCorte['diferencia']) ?></strong></div>
            <div><span>Total en efectivo</span><strong><?= $moneda($detalleCorte['total_efectivo']) ?></strong></div>
            <div><span>Total con tarjeta</span><strong><?= $moneda($detalleCorte['total_tarjeta']) ?></strong></div>
            <div><span>Total por transferencia</span><strong><?= $moneda($detalleCorte['total_transferencia']) ?></strong></div>
            <div><span>Total general</span><strong><?= $moneda($detalleCorte['total_general']) ?></strong></div>
        <?php else: ?>
            <div><span>Arqueo</span><strong>Pendiente de declaración</strong></div>
        <?php endif; ?>
    </div>
    <?php if ($detalleCorte['observaciones']): ?>
        <p class="corte-dialog-nota"><strong>Observaciones:</strong> <?= nl2br(htmlspecialchars($detalleCorte['observaciones'])) ?></p>
    <?php endif; ?>
    <h3>Movimientos registrados</h3>
    <div class="corte-tabla-envoltura">
        <table class="corte-tabla">
            <thead><tr><th>Tipo</th><th>Concepto</th><th>Fecha</th><th>Responsable</th><th class="corte-monto">Importe</th></tr></thead>
            <tbody>
                <?php foreach ($movimientosDetalle as $movimiento): ?>
                    <?php $esIngreso = $movimiento['tipo'] === 'ingreso manual'; ?>
                    <tr>
                        <td><?= htmlspecialchars($movimientosTipos[$movimiento['tipo']] ?? $movimiento['tipo']) ?></td>
                        <td><?= htmlspecialchars((string)$movimiento['concepto']) ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($movimiento['fecha']))) ?></td>
                        <td><?= htmlspecialchars($movimiento['responsable']) ?></td>
                        <td class="corte-monto"><?= $esIngreso ? '' : '−' ?><?= $moneda($movimiento['importe']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$movimientosDetalle): ?><tr><td colspan="5">Sin movimientos manuales.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="corte-dialog-acciones">
        <a class="btn-secundario" href="corte_caja.php?panel=historial&periodo=<?= rawurlencode($periodo) ?>">Cerrar</a>
        <button class="btn" type="button" data-imprimir>Imprimir</button>
    </div>
</dialog>
