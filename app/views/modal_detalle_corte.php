<dialog class="corte-dialog" id="modalDetalleCorte" aria-labelledby="tituloDetalleModal" data-auto-open>
    <div class="corte-dialog-encabezado">
        <div><span class="corte-kicker">Detalle del registro</span><h2 id="tituloDetalleModal">Corte <?= htmlspecialchars($detalleCorte['folio']) ?></h2></div>
        <button class="corte-dialog-cerrar" type="button" data-cerrar-dialog aria-label="Cerrar ventana">×</button>
    </div>
    <?php
    $gastosDetalle = 0.0;
    $nominasDetalle = 0.0;
    $otrosIngresosDetalle = 0.0;
    $resguardosManualesDetalle = 0.0;
    $resguardosAutomaticosDetalle = 0.0;
    foreach ($movimientosDetalle as $movimientoDetalle) {
        $importeDetalle = (float)$movimientoDetalle['importe'];
        if ($movimientoDetalle['tipo'] === 'retiro') {
            if (stripos((string)$movimientoDetalle['concepto'], 'Entrega de excedente a resguardo') !== false) {
                $resguardosAutomaticosDetalle += $importeDetalle;
            } else {
                $resguardosManualesDetalle += $importeDetalle;
            }
        } elseif ($movimientoDetalle['tipo'] === 'gasto') {
            $gastosDetalle += $importeDetalle;
        } elseif ($movimientoDetalle['tipo'] === 'nomina') {
            $nominasDetalle += $importeDetalle;
        } elseif ($movimientoDetalle['tipo'] === 'ingreso manual') {
            $otrosIngresosDetalle += $importeDetalle;
        }
    }
    $fondoInicialDetalle = (float)($detalleCorte['fondo_inicial'] ?? 0);
    $efectivoEsperadoAntesResguardo = (float)$detalleCorte['efectivo_esperado'] + $resguardosManualesDetalle;
    $efectivoRestanteDetalle = (float)$detalleCorte['efectivo_contado'];
    if ($resguardosAutomaticosDetalle > 0) {
        $efectivoRestanteDetalle = $fondoInicialDetalle;
    }
    ?>
    <div class="corte-detalle-modal">
        <?php if ($detalleCorte['tipo'] === 'mensual'): ?>
            <div><span>Periodo</span><strong><?= htmlspecialchars(date('d/m/Y', strtotime($detalleCorte['periodo_inicio']))) ?> – <?= htmlspecialchars(date('d/m/Y', strtotime($detalleCorte['periodo_fin']))) ?></strong></div>
            <div><span>Administrador responsable</span><strong><?= htmlspecialchars($detalleCorte['cajero_cierre'] ?? $detalleCorte['cajero_apertura']) ?></strong></div>
        <?php else: ?>
            <div><span>Turno</span><strong><?= htmlspecialchars(ucfirst($detalleCorte['turno'])) ?></strong></div>
            <div><span>Apertura</span><strong><?= htmlspecialchars(date('d/m/Y H:i', strtotime($detalleCorte['apertura']))) ?></strong></div>
            <div><span>Cajero de apertura</span><strong><?= htmlspecialchars($detalleCorte['cajero_apertura']) ?></strong></div>
            <div><span>Empleado de cierre</span><strong><?= htmlspecialchars($detalleCorte['cajero_cierre'] ?? '—') ?></strong></div>
        <?php endif; ?>
        <div><span>Estado</span><strong><?= htmlspecialchars(ucfirst($detalleCorte['estado'])) ?></strong></div>
        <?php if ($detalleCorte['tipo'] === 'mensual'): ?>
            <div><span>Fondo inicial</span><strong>No aplica al corte mensual</strong></div>
            <div><span>Total general</span><strong><?= $moneda($detalleCorte['total_general']) ?></strong></div>
        <?php elseif ($detalleCorte['estado'] === 'cerrado'): ?>
            <div class="corte-detalle-seccion">Arqueo físico</div>
            <div><span>Fondo inicial de caja</span><strong><?= $moneda($fondoInicialDetalle) ?></strong></div>
            <div><span>Cobros en efectivo del corte</span><strong><?= $moneda($detalleCorte['total_efectivo']) ?></strong></div>
            <div><span>Otros ingresos en efectivo</span><strong><?= $moneda($otrosIngresosDetalle) ?></strong></div>
            <div><span>Egresos en efectivo (gastos y nómina)</span><strong><?= $monedaConSigno(-($gastosDetalle + $nominasDetalle)) ?></strong></div>
            <div><span>Efectivo esperado antes de resguardo</span><strong><?= $moneda($efectivoEsperadoAntesResguardo) ?></strong></div>
            <div><span>Efectivo contado en arqueo</span><strong><?= $moneda($detalleCorte['efectivo_contado']) ?></strong></div>
            <div><span>Diferencia en arqueo</span><strong><?= $monedaConSigno($detalleCorte['diferencia']) ?></strong></div>
            <div><span>Retiro a resguardo (post-arqueo)</span><strong><?= $monedaConSigno(-$resguardosAutomaticosDetalle) ?></strong></div>
            <div><span>Efectivo restante en caja (nuevo fondo)</span><strong><?= $moneda($efectivoRestanteDetalle) ?></strong></div>

            <div class="corte-detalle-seccion">Cobros del corte</div>
            <div><span>Pagos en efectivo</span><strong><?= $moneda($detalleCorte['total_efectivo']) ?></strong></div>
            <div><span>Pagos con tarjeta</span><strong><?= $moneda($detalleCorte['total_tarjeta']) ?></strong></div>
            <div><span>Pagos por transferencia</span><strong><?= $moneda($detalleCorte['total_transferencia']) ?></strong></div>
            <div><span>Total cobrado</span><strong><?= $moneda($detalleCorte['total_general']) ?></strong></div>

            <div class="corte-detalle-seccion">Movimientos y resguardos</div>
            <div><span>Otros ingresos de caja</span><strong><?= $moneda($otrosIngresosDetalle) ?></strong></div>
            <div><span>Gastos</span><strong><?= $moneda($gastosDetalle) ?></strong></div>
            <div><span>Nómina</span><strong><?= $moneda($nominasDetalle) ?></strong></div>
            <div><span>Retiro manual a resguardo</span><strong><?= $moneda($resguardosManualesDetalle) ?></strong></div>
            <div><span>Retiro post-arqueo a resguardo</span><strong><?= $moneda($resguardosAutomaticosDetalle) ?></strong></div>
        <?php else: ?>
            <div><span>Arqueo</span><strong>Pendiente de declaración</strong></div>
        <?php endif; ?>
    </div>
    <?php if ($detalleCorte['estado'] === 'cerrado'): ?>
        <p class="corte-dialog-nota">El resguardo separa únicamente el excedente tras el arqueo y reduce el efectivo físico que queda en caja; no es un gasto ni una pérdida del negocio, y el fondo inicial no cuenta como ganancia del corte.</p>
    <?php endif; ?>
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
