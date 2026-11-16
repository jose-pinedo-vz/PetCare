<div class="corte-tabla-envoltura">
    <table class="corte-tabla corte-tabla-historial">
        <thead><tr><th>Folio</th><th>Fecha</th><th>Turno</th><th>Cajero apertura</th><th>Empleado cierre</th><th>Efectivo</th><th>Tarjeta</th><th>Transferencia</th><th>Total</th><th>Esperado</th><th>Contado</th><th>Diferencia</th><th>Estado</th><th>Detalle</th></tr></thead>
        <tbody id="<?= htmlspecialchars($tablaHistorialId) ?>">
            <?php foreach ($historialCortes as $corte): ?>
                <?php $cerrado = $corte['estado'] === 'cerrado'; ?>
                <tr data-search="<?= htmlspecialchars(strtolower($corte['folio'] . ' ' . $corte['cajero_apertura'])) ?>">
                    <td><strong><?= htmlspecialchars($corte['folio']) ?></strong></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($corte['apertura']))) ?></td>
                    <td><?= htmlspecialchars(ucfirst($corte['turno'])) ?></td>
                    <td><?= htmlspecialchars($corte['cajero_apertura']) ?></td>
                    <td><?= htmlspecialchars($corte['cajero_cierre'] ?? '—') ?></td>
                    <td><?= $cerrado ? $moneda($corte['total_efectivo']) : '—' ?></td>
                    <td><?= $cerrado ? $moneda($corte['total_tarjeta']) : '—' ?></td>
                    <td><?= $cerrado ? $moneda($corte['total_transferencia']) : '—' ?></td>
                    <td><?= $cerrado ? $moneda($corte['total_general']) : '—' ?></td>
                    <td><?= $cerrado ? $moneda($corte['efectivo_esperado']) : '—' ?></td>
                    <td><?= $cerrado ? $moneda($corte['efectivo_contado']) : '—' ?></td>
                    <td><?= $cerrado ? $monedaConSigno($corte['diferencia']) : '—' ?></td>
                    <td><span class="corte-estado <?= $cerrado ? 'cerrado' : 'abierto' ?>"><?= htmlspecialchars(ucfirst($corte['estado'])) ?></span></td>
                    <td><a class="btn-secundario btn-chico" href="corte_caja.php?panel=historial&periodo=<?= rawurlencode(date('Y-m', strtotime($corte['apertura']))) ?>&detalle=<?= (int)$corte['id'] ?>">Ver</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$historialCortes): ?>
                <tr><td colspan="14">No hay cortes diarios en este periodo.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
