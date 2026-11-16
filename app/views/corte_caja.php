<?php
require_once __DIR__ . '/../controllers/corte_caja.php';

$moneda = static fn($importe): string => '$' . number_format((float)$importe, 2);
$monedaConSigno = static function ($importe) use ($moneda): string {
    $importe = (float)$importe;
    return ($importe < 0 ? '−' : '') . $moneda(abs($importe));
};
$nombreEmpleado = $corteAbierto['cajero_apertura']
    ?? ($_SESSION['empleado_asociado'] ?? $_SESSION['usuario']);
$hayCorteAbierto = $corteAbierto !== null;
$movimientosTipos = [
    'ingreso manual' => 'Ingreso manual',
    'nomina' => 'Nómina',
    'retiro' => 'Retiro',
    'gasto' => 'Gasto',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Corte de caja</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
    <link rel="stylesheet" href="css/corte_caja.css">
</head>
<body>
    <div class="layout">
        <?php $paginaActiva = 'corte_caja'; include __DIR__ . '/layout/sidebar.php'; ?>

        <main class="corte-pagina">
            <div class="migaja"><a href="Ventas.php">Punto de Venta</a> / Corte de caja</div>

            <header class="encabezado-pagina corte-encabezado">
                <div>
                    <span class="corte-kicker">Administración de caja</span>
                    <h1>Corte de caja</h1>
                </div>
                <div class="corte-encabezado-derecha">
                    <div class="corte-sesion" aria-label="Sesión de caja">
                        <span class="corte-avatar" aria-hidden="true"><?= htmlspecialchars(strtoupper(substr((string)$nombreEmpleado, 0, 1))) ?></span>
                        <span><strong><?= htmlspecialchars((string)$nombreEmpleado) ?></strong><small><?= htmlspecialchars($_SESSION['usuario']) ?> · <time id="fechaActual"><?= date('d/m/Y') ?></time></small></span>
                    </div>
                    <a class="btn btn-secundario btn-chico" href="Ventas.php">Regresar a venta</a>
                </div>
            </header>

            <?php if ($avisoFlash): ?>
                <p class="corte-aviso" role="status"><?= htmlspecialchars($avisoFlash['mensaje']) ?></p>
            <?php endif; ?>

            <?php if (!$idEmpleado): ?>
                <p class="corte-form-error" role="alert">Tu usuario no está asociado a un empleado. No puedes abrir ni operar una caja.</p>
            <?php endif; ?>

            <nav class="corte-tabs" role="tablist" aria-label="Secciones de corte de caja">
                <button class="corte-tab <?= $activePanel === 'diario' ? 'activa' : '' ?>" id="tab-diario" type="button" role="tab" aria-selected="<?= $activePanel === 'diario' ? 'true' : 'false' ?>" aria-controls="panel-diario" tabindex="<?= $activePanel === 'diario' ? '0' : '-1' ?>" data-tab="diario">1. Corte diario</button>
                <button class="corte-tab <?= $activePanel === 'historial' ? 'activa' : '' ?>" id="tab-historial" type="button" role="tab" aria-selected="<?= $activePanel === 'historial' ? 'true' : 'false' ?>" aria-controls="panel-historial" tabindex="<?= $activePanel === 'historial' ? '0' : '-1' ?>" data-tab="historial">2. Historial de Cortes Z</button>
                <?php if ($esAdministrador): ?>
                    <button class="corte-tab <?= $activePanel === 'estadisticas' ? 'activa' : '' ?>" id="tab-estadisticas" type="button" role="tab" aria-selected="<?= $activePanel === 'estadisticas' ? 'true' : 'false' ?>" aria-controls="panel-estadisticas" tabindex="<?= $activePanel === 'estadisticas' ? '0' : '-1' ?>" data-tab="estadisticas">3. Resumen mensual</button>
                <?php endif; ?>
            </nav>

            <section class="corte-panel" id="panel-diario" role="tabpanel" aria-labelledby="tab-diario" data-panel="diario" <?= $activePanel === 'diario' ? '' : 'hidden' ?>>
                <div class="corte-panel-encabezado">
                    <div>
                        <h2>Corte diario y turno en curso</h2>
                        <p>La información del arqueo esperado se revela únicamente después de declarar el efectivo contado.</p>
                    </div>
                    <span class="corte-estado <?= $hayCorteAbierto ? 'abierto' : 'cerrado' ?>"><?= $hayCorteAbierto ? 'Abierto' : 'Sin corte abierto' ?></span>
                </div>

                <?php if (!$hayCorteAbierto && $idEmpleado): ?>
                    <section class="corte-apertura tarjeta">
                        <div class="corte-apertura-campos">
                            <form method="post" action="../controllers/corte_caja.php" class="corte-dialog-campos">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="periodo" value="<?= htmlspecialchars($periodo) ?>">
                                <input type="hidden" name="accion" value="abrir">
                                <div class="corte-campo">
                                    <label class="label-campo" for="turnoCorte">Turno</label>
                                    <select class="campo" id="turnoCorte" name="turno" required>
                                        <option value="matutino">Matutino</option>
                                        <option value="vespertino">Vespertino</option>
                                        <option value="ambos">Ambos</option>
                                    </select>
                                </div>
                                <div class="corte-campo">
                                    <span class="label-campo">Fondo inicial</span>
                                    <strong><?= $moneda(CorteCaja::FONDO_INICIAL) ?></strong>
                                    <small>Importe fijo; configuración administrativa pendiente.</small>
                                </div>
                                <button class="btn btn-chico" type="submit">Abrir caja</button>
                            </form>
                        </div>
                        <div class="corte-apertura-estado">
                            <span class="corte-estado cerrado">Cerrado</span>
                            <span class="corte-apertura-detalle"><strong>Sin corte abierto</strong><small>El corte quedará asociado a tu usuario.</small></span>
                        </div>
                    </section>
                <?php elseif ($hayCorteAbierto): ?>
                    <section class="corte-apertura tarjeta">
                        <div class="corte-apertura-campos">
                            <div class="corte-campo"><span class="label-campo">Folio</span><strong><?= htmlspecialchars($corteAbierto['folio']) ?></strong></div>
                            <div class="corte-campo"><span class="label-campo">Turno</span><strong><?= htmlspecialchars(ucfirst($corteAbierto['turno'])) ?></strong></div>
                            <div class="corte-campo"><span class="label-campo">Fondo inicial</span><strong><?= $moneda($corteAbierto['fondo_inicial']) ?></strong></div>
                        </div>
                        <div class="corte-apertura-estado">
                            <span class="corte-estado abierto">Abierto</span>
                            <span class="corte-apertura-detalle"><strong><?= htmlspecialchars($corteAbierto['cajero_apertura']) ?></strong><small><?= htmlspecialchars(date('d/m/Y H:i', strtotime($corteAbierto['apertura']))) ?></small></span>
                            <?php if (!$arqueoPendiente): ?>
                                <button class="btn btn-chico" id="abrirSalida" type="button">+ Movimiento</button>
                                <button class="btn btn-peligro btn-chico" id="abrirArqueo" type="button">Realizar arqueo</button>
                            <?php else: ?>
                                <span class="corte-estado">Arqueo declarado</span>
                            <?php endif; ?>
                        </div>
                    </section>

                    <p class="corte-dialog-nota" role="note">Aviso: los pagos de Ventas/Pagos no se vinculan a este corte mientras sus controladores permanezcan sin cambios. Los totales comerciales solo incluirán pagos que ya tengan asociado este corte.</p>

                    <section class="tarjeta corte-movimientos" aria-labelledby="tituloMovimientos">
                        <div class="corte-seccion-titulo corte-movimientos-titulo">
                            <div><h2 id="tituloMovimientos">Movimientos de efectivo</h2><p>El saldo esperado no se muestra antes de enviar el arqueo.</p></div>
                        </div>
                        <div class="corte-tabla-envoltura">
                            <table class="corte-tabla">
                                <thead><tr><th>Tipo</th><th>Concepto</th><th>Fecha y hora</th><th>Responsable</th><th class="corte-monto">Importe</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td><span class="corte-tipo-movimiento entrada">Entrada</span></td>
                                        <td>Fondo inicial</td>
                                        <td><?= htmlspecialchars(date('H:i', strtotime($corteAbierto['apertura']))) ?></td>
                                        <td><?= htmlspecialchars($corteAbierto['cajero_apertura']) ?></td>
                                        <td class="corte-monto"><?= $moneda($corteAbierto['fondo_inicial']) ?></td>
                                    </tr>
                                    <?php foreach ($movimientosCorte as $movimiento): ?>
                                        <?php $esIngreso = $movimiento['tipo'] === 'ingreso manual'; ?>
                                        <tr>
                                            <td><span class="corte-tipo-movimiento <?= $esIngreso ? 'entrada' : 'salida' ?>"><?= $esIngreso ? 'Entrada' : 'Salida' ?></span></td>
                                            <td>
                                                <strong><?= htmlspecialchars($movimientosTipos[$movimiento['tipo']] ?? $movimiento['tipo']) ?></strong>
                                                <?= htmlspecialchars((string)$movimiento['concepto']) ?>
                                                <?php if ($movimiento['beneficiario']): ?><small> · Beneficiario: <?= htmlspecialchars($movimiento['beneficiario']) ?></small><?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($movimiento['fecha']))) ?></td>
                                            <td><?= htmlspecialchars($movimiento['responsable']) ?></td>
                                            <td class="corte-monto"><?= $esIngreso ? '' : '−' ?><?= $moneda($movimiento['importe']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (!$movimientosCorte): ?>
                                        <tr><td colspan="5">Todavía no hay movimientos manuales en este corte.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if ($corteAbierto && $arqueoPendiente): ?>
                    <section class="tarjeta corte-auditoria" aria-labelledby="tituloArqueoDeclarado">
                        <div><h2 id="tituloArqueoDeclarado">Declaración de efectivo recibida</h2><p>Confirma el resultado calculado en el servidor para cerrar el corte.</p></div>
                        <div class="corte-confirmacion-resumen">
                            <div><span>Efectivo esperado</span><strong><?= $moneda($arqueoPendiente['datos']['efectivo_esperado']) ?></strong></div>
                            <div><span>Efectivo contado</span><strong><?= $moneda($arqueoPendiente['datos']['efectivo_contado']) ?></strong></div>
                            <div><span>Diferencia</span><strong><?= $moneda(abs($arqueoPendiente['datos']['diferencia'])) ?></strong></div>
                            <div><span>Resultado</span><strong><?= $arqueoPendiente['datos']['diferencia'] > 0 ? 'Sobrante' : ($arqueoPendiente['datos']['diferencia'] < 0 ? 'Faltante' : 'Caja cuadrada') ?></strong></div>
                        </div>
                        <form method="post" action="../controllers/corte_caja.php" class="corte-acciones">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                            <input type="hidden" name="periodo" value="<?= htmlspecialchars($periodo) ?>">
                            <input type="hidden" name="accion" value="confirmar_cierre">
                            <input type="hidden" name="corte" value="<?= (int)$corteAbierto['id'] ?>">
                            <button class="btn btn-peligro" type="submit">Confirmar cierre</button>
                        </form>
                        <form method="post" action="../controllers/corte_caja.php" class="corte-acciones">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                            <input type="hidden" name="periodo" value="<?= htmlspecialchars($periodo) ?>">
                            <input type="hidden" name="accion" value="cancelar_arqueo">
                            <button class="btn-secundario" type="submit">Cancelar confirmación</button>
                        </form>
                    </section>
                <?php endif; ?>
            </section>

            <section class="corte-panel" id="panel-historial" role="tabpanel" aria-labelledby="tab-historial" data-panel="historial" <?= $activePanel === 'historial' ? '' : 'hidden' ?>>
                <div class="corte-panel-encabezado">
                    <div><h2>Historial de Cortes Z</h2><p>Consulta los cortes diarios del periodo seleccionado.</p></div>
                    <button class="btn-secundario btn-chico" type="button" data-imprimir>Imprimir</button>
                </div>
                <form method="get" action="corte_caja.php" class="tarjeta corte-filtros">
                    <input type="hidden" name="panel" value="historial">
                    <div class="corte-filtro">
                        <label class="label-campo" for="filtroPeriodo">Mes / año</label>
                        <input class="campo" id="filtroPeriodo" name="periodo" type="month" value="<?= htmlspecialchars($periodo) ?>">
                    </div>
                    <div class="corte-filtro corte-filtro--buscar">
                        <label class="label-campo" for="buscarHistorial">Folio o cajero</label>
                        <input class="campo" id="buscarHistorial" type="search" placeholder="Buscar por folio o nombre">
                    </div>
                    <button class="btn btn-chico" type="submit">Consultar</button>
                </form>

                <section class="tarjeta corte-historial" aria-labelledby="tituloHistorial">
                    <div class="corte-seccion-titulo">
                        <div><h2 id="tituloHistorial">Cortes registrados · <?= htmlspecialchars($periodoTexto) ?></h2><p id="resultadoHistorial"><?= count($historialCortes) ?> corte(s) consultados.</p></div>
                    </div>
                    <?php $tablaHistorialId = 'tablaHistorial'; include __DIR__ . '/cortes_z_tabla.php'; ?>
                </section>
            </section>

            <?php if ($esAdministrador && $resumenMensual): ?>
                <section class="corte-panel" id="panel-estadisticas" role="tabpanel" aria-labelledby="tab-estadisticas" data-panel="estadisticas" <?= $activePanel === 'estadisticas' ? '' : 'hidden' ?>>
                    <div class="corte-panel-encabezado">
                        <div><h2>Resumen mensual · <?= htmlspecialchars($periodoTexto) ?></h2><p>Se consolida únicamente a partir de cortes diarios cerrados; los cortes mensuales no se vuelven a sumar.</p></div>
                        <button class="btn-secundario btn-chico" type="button" data-imprimir>Imprimir</button>
                    </div>
                    <form method="get" action="corte_caja.php" class="tarjeta corte-estadisticas-filtro">
                        <input type="hidden" name="panel" value="estadisticas">
                        <label for="periodoEstadisticas"><strong>Periodo de análisis</strong></label>
                        <input class="campo" id="periodoEstadisticas" name="periodo" type="month" value="<?= htmlspecialchars($periodo) ?>">
                        <button class="btn btn-chico" type="submit">Consultar</button>
                    </form>

                    <div class="corte-kpis corte-kpis--periodo">
                        <article class="corte-kpi"><span class="corte-kpi-label">Ingresos en efectivo</span><strong><?= $moneda($resumenMensual['efectivo']) ?></strong></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Ingresos con tarjeta</span><strong><?= $moneda($resumenMensual['tarjeta']) ?></strong></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Ingresos por transferencia</span><strong><?= $moneda($resumenMensual['transferencia']) ?></strong></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Gastos</span><strong><?= $moneda($resumenMensual['movimientos']['gasto']) ?></strong></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Ingresos considerados</span><strong><?= $moneda($resumenMensual['ingresos_considerados']) ?></strong><small>Incluye ingresos manuales: <?= $moneda($resumenMensual['movimientos']['ingreso manual']) ?></small></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Egresos considerados</span><strong><?= $moneda($resumenMensual['egresos_considerados']) ?></strong><small>Nómina, retiros y gastos</small></article>
                        <article class="corte-kpi corte-kpi--total"><span class="corte-kpi-label">Flujo neto</span><strong><?= $moneda($resumenMensual['flujo_neto']) ?></strong></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Fondo inicial (separado)</span><strong><?= $moneda($resumenMensual['fondos_iniciales']) ?></strong><small><?= (int)$resumenMensual['cortes'] ?> corte(s) diario(s) cerrado(s)</small></article>
                    </div>

                    <section class="tarjeta corte-historial" aria-labelledby="tituloCortesZMensuales">
                        <div class="corte-seccion-titulo"><div><h2 id="tituloCortesZMensuales">Cortes Z del periodo</h2><p><?= count($historialCortes) ?> corte(s) diario(s).</p></div></div>
                        <?php $tablaHistorialId = 'tablaCortesZResumen'; include __DIR__ . '/cortes_z_tabla.php'; ?>
                    </section>

                    <div class="corte-graficas">
                        <section class="tarjeta corte-grafica" aria-labelledby="tituloMasVendidos">
                            <div class="corte-grafica-encabezado"><div><h2 id="tituloMasVendidos">Productos más vendidos</h2><p>Por cantidad durante el periodo</p></div></div>
                            <div class="corte-tabla-envoltura"><table class="corte-tabla">
                                <thead><tr><th>Producto</th><th>Cantidad</th><th>Importe generado</th></tr></thead>
                                <tbody><?php foreach ($resumenMensual['mas_vendidos'] as $producto): ?>
                                    <tr><td><?= htmlspecialchars($producto['nombre']) ?></td><td><?= (int)$producto['cantidad'] ?></td><td><?= $moneda($producto['importe']) ?></td></tr>
                                <?php endforeach; ?><?php if (!$resumenMensual['mas_vendidos']): ?><tr><td colspan="3">Sin ventas de productos en el periodo.</td></tr><?php endif; ?></tbody>
                            </table></div>
                        </section>
                        <section class="tarjeta corte-grafica" aria-labelledby="tituloMenosVendidos">
                            <div class="corte-grafica-encabezado"><div><h2 id="tituloMenosVendidos">Productos menos vendidos</h2><p>Incluye productos sin ventas</p></div></div>
                            <div class="corte-tabla-envoltura"><table class="corte-tabla">
                                <thead><tr><th>Producto</th><th>Cantidad</th><th>Importe generado</th></tr></thead>
                                <tbody><?php foreach ($resumenMensual['menos_vendidos'] as $producto): ?>
                                    <tr><td><?= htmlspecialchars($producto['nombre']) ?></td><td><?= (int)$producto['cantidad'] ?></td><td><?= $moneda($producto['importe']) ?></td></tr>
                                <?php endforeach; ?><?php if (!$resumenMensual['menos_vendidos']): ?><tr><td colspan="3">No hay productos registrados.</td></tr><?php endif; ?></tbody>
                            </table></div>
                        </section>
                    </div>
                </section>
            <?php endif; ?>

            <p class="corte-aviso" id="avisoCorte" role="status" aria-live="polite" hidden></p>
        </main>
    </div>

    <?php if ($corteAbierto): ?>
        <?php include __DIR__ . '/modal_arqueo_efectivo.php'; ?>
        <?php if (!$arqueoPendiente): ?>
            <?php include __DIR__ . '/modal_registrar_salida.php'; ?>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($detalleCorte): ?>
        <?php include __DIR__ . '/modal_detalle_corte.php'; ?>
    <?php endif; ?>
    <script src="js/corte_caja.js"></script>
</body>
</html>
