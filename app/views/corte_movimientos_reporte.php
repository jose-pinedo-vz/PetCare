<div class="corte-tabla-envoltura">
    <table class="corte-tabla">
        <thead><tr><th>Fecha</th><th>Tipo</th><th>Folio de caja</th><th>Responsable</th><th>Concepto</th><th>Beneficiario</th><th>Importe</th><th>Observaciones</th></tr></thead>
        <tbody>
            <?php foreach ($movimientosReporte as $movimiento): ?>
                <?php
                $esIngreso = $movimiento['tipo'] === 'ingreso manual';
                $esRetiro = $movimiento['tipo'] === 'retiro';
                $importeMovimiento = (float)$movimiento['importe'];
                $etiquetaMovimiento = $esIngreso
                    ? 'Otro ingreso de caja'
                    : ($esRetiro
                        ? 'Dinero separado para resguardo'
                        : 'Gasto operativo: ' . ($movimientosTipos[$movimiento['tipo']] ?? $movimiento['tipo']));
                ?>
                <tr>
                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($movimiento['fecha']))) ?></td>
                    <td><span class="corte-tipo-movimiento <?= $esIngreso ? 'entrada' : 'salida' ?>"><?= htmlspecialchars($etiquetaMovimiento) ?></span></td>
                    <td><?= htmlspecialchars($movimiento['folio_corte']) ?></td>
                    <td><?= htmlspecialchars($movimiento['responsable']) ?></td>
                    <td><?= htmlspecialchars($movimiento['concepto']) ?></td>
                    <td><?= htmlspecialchars($movimiento['beneficiario'] ?? '—') ?></td>
                    <td class="corte-monto"><?= $monedaConSigno($esIngreso ? $importeMovimiento : -$importeMovimiento) ?></td>
                    <td><?= htmlspecialchars($movimiento['observaciones'] ?? '—') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$movimientosReporte): ?><tr><td colspan="8">No hay movimientos registrados para este periodo.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
