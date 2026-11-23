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
    'ingreso manual' => 'Otro ingreso de caja',
    'nomina' => 'Nómina',
    'retiro' => 'Retiro / resguardo',
    'gasto' => 'Gasto',
];
$anchoGrafica = static function ($valor, $maximo): float {
    if ((float)$maximo <= 0) {
        return 0.0;
    }

    return max(0.0, min(100.0, ((float)$valor / (float)$maximo) * 100));
};
$bienvenidasCaja = [
    'admin' => [
        'rol' => 'Administrador',
        'descripcion' => 'Puedes operar tu corte diario y consultar los cortes de todos los empleados, además del corte mensual y las estadísticas.',
        'accesos' => ['Corte diario propio', 'Historial general', 'Corte mensual', 'Estadísticas con filtros por empleado'],
    ],
    'gerente' => [
        'rol' => 'Gerente',
        'descripcion' => 'Puedes abrir y operar tu corte diario y revisar el historial de tus propios cortes.',
        'accesos' => ['Corte diario propio', 'Historial de tus cortes'],
    ],
    'vendedor' => [
        'rol' => 'Vendedor',
        'descripcion' => 'Puedes abrir y operar tu corte diario y revisar el historial de tus propios cortes.',
        'accesos' => ['Corte diario propio', 'Historial de tus cortes'],
    ],
    'recepcionista' => [
        'rol' => 'Recepcionista',
        'descripcion' => 'Puedes abrir y operar tu corte diario y revisar el historial de tus propios cortes.',
        'accesos' => ['Corte diario propio', 'Historial de tus cortes'],
    ],
];
$bienvenidaCaja = $bienvenidasCaja[$rolSesion] ?? null;
$motivosAccesoCaja = [
    'sin_empleado' => 'Tu cuenta no está asociada a un empleado. Pide a un administrador que vincule tu usuario con un empleado activo.',
    'empleado_no_encontrado' => 'No se encontró el empleado asociado a tu cuenta. Pide a un administrador que revise la asociación.',
    'empleado_inactivo' => 'El empleado asociado a tu cuenta está inactivo. Contacta a un administrador para revisar tu acceso.',
    'puesto_no_autorizado' => 'El puesto de tu empleado no está habilitado para operar caja.',
    'rol_puesto_no_coincide' => 'El rol de tu usuario no coincide con el puesto de tu empleado para operar caja.',
];
$mensajeMotivoCaja = $motivosAccesoCaja[$motivoAccesoCaja] ?? 'Tu cuenta no tiene acceso para operar caja. Contacta a un administrador.';
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

            <?php if ($puedeOperarCaja && $bienvenidaCaja): ?>
                <section class="corte-acceso corte-acceso--bienvenida" aria-labelledby="tituloBienvenidaCaja" role="status">
                    <div class="corte-acceso-contenido">
                        <span class="corte-kicker">Bienvenido, <?= htmlspecialchars((string)$nombreEmpleado) ?></span>
                        <h2 id="tituloBienvenidaCaja">Acceso de <?= htmlspecialchars($bienvenidaCaja['rol']) ?></h2>
                        <p><?= htmlspecialchars($bienvenidaCaja['descripcion']) ?></p>
                    </div>
                    <div class="corte-acceso-secciones">
                        <strong>Secciones disponibles</strong>
                        <ul>
                            <?php foreach ($bienvenidaCaja['accesos'] as $acceso): ?>
                                <li><?= htmlspecialchars($acceso) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </section>
            <?php elseif (!$puedeOperarCaja): ?>
                <section class="corte-acceso corte-acceso--denegado" aria-labelledby="tituloAccesoCaja" role="alert">
                    <div class="corte-acceso-contenido">
                        <span class="corte-kicker">Acceso no disponible</span>
                        <h2 id="tituloAccesoCaja">No puedes operar caja todavía</h2>
                        <p><?= htmlspecialchars($mensajeMotivoCaja) ?></p>
                        <p class="corte-acceso-reglas">Acceso permitido: rol <strong>admin</strong> o <strong>gerente</strong> con puesto <strong>Gerente</strong>; rol <strong>vendedor</strong> con puesto <strong>Vendedor</strong>; o rol <strong>recepcionista</strong> con puesto <strong>Recepcionista</strong>. El empleado debe estar activo.</p>
                    </div>
                    <a class="btn btn-secundario btn-chico" href="inicio.php">Volver al inicio</a>
                </section>
            <?php endif; ?>

            <?php if ($puedeOperarCaja): ?>
            <nav class="corte-tabs" role="tablist" aria-label="Secciones de corte de caja">
                <button class="corte-tab <?= $activePanel === 'diario' ? 'activa' : '' ?>" id="tab-diario" type="button" role="tab" aria-selected="<?= $activePanel === 'diario' ? 'true' : 'false' ?>" aria-controls="panel-diario" tabindex="<?= $activePanel === 'diario' ? '0' : '-1' ?>" data-tab="diario">1. Corte diario</button>
                <button class="corte-tab <?= $activePanel === 'historial' ? 'activa' : '' ?>" id="tab-historial" type="button" role="tab" aria-selected="<?= $activePanel === 'historial' ? 'true' : 'false' ?>" aria-controls="panel-historial" tabindex="<?= $activePanel === 'historial' ? '0' : '-1' ?>" data-tab="historial">2. Historial de Cortes Z</button>
                <?php if ($esAdministrador): ?>
                    <button class="corte-tab <?= $activePanel === 'mensual' ? 'activa' : '' ?>" id="tab-mensual" type="button" role="tab" aria-selected="<?= $activePanel === 'mensual' ? 'true' : 'false' ?>" aria-controls="panel-mensual" tabindex="<?= $activePanel === 'mensual' ? '0' : '-1' ?>" data-tab="mensual">3. Corte mensual</button>
                    <button class="corte-tab <?= $activePanel === 'estadisticas' ? 'activa' : '' ?>" id="tab-estadisticas" type="button" role="tab" aria-selected="<?= $activePanel === 'estadisticas' ? 'true' : 'false' ?>" aria-controls="panel-estadisticas" tabindex="<?= $activePanel === 'estadisticas' ? '0' : '-1' ?>" data-tab="estadisticas">4. Estadísticas</button>
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

                <?php if (!$hayCorteAbierto && $idEmpleado && $puedeOperarCaja): ?>
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
                                    <label class="label-campo" for="fondoInicial">Fondo inicial</label>
                                    <div class="corte-input-monto">
                                        <span aria-hidden="true">$</span>
                                        <input class="campo" id="fondoInicial" name="fondo_inicial" type="number" min="0" step="0.01" value="<?= number_format(CorteCaja::FONDO_INICIAL, 2, '.', '') ?>" required>
                                    </div>
                                </div>
                                <button class="btn btn-chico" type="submit">Abrir caja</button>
                            </form>
                        </div>
                        <div class="corte-apertura-estado">
                            <span class="corte-estado cerrado">Cerrado</span>
                            <span class="corte-apertura-detalle"><strong>Sin corte abierto</strong><small>El corte quedará asociado a tu usuario.</small></span>
                        </div>
                    </section>
                <?php elseif ($hayCorteAbierto && $puedeOperarCaja): ?>
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

                    <p class="corte-dialog-nota" role="note">Este resumen cuenta las ventas cobradas durante este corte. Los cobros en efectivo de citas y hotel entran a la caja como movimientos de efectivo.</p>

                    <section class="corte-kpis corte-kpis--periodo" aria-label="Pagos asociados a este corte">
                        <article class="corte-kpi"><span class="corte-kpi-label">Efectivo cobrado</span><strong><?= $moneda($resumenPagosCorte['efectivo']) ?></strong></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Tarjeta</span><strong><?= $moneda($resumenPagosCorte['tarjeta']) ?></strong></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Transferencia</span><strong><?= $moneda($resumenPagosCorte['transferencia']) ?></strong></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Ventas relacionadas</span><strong><?= (int)$resumenPagosCorte['ventas'] ?></strong></article>
                        <article class="corte-kpi corte-kpi--total"><span class="corte-kpi-label">Total cobrado</span><strong><?= $moneda($resumenPagosCorte['general']) ?></strong><small><?= (int)$resumenPagosCorte['pagos'] ?> pago(s) registrado(s) en este corte</small></article>
                    </section>

                    <section class="tarjeta corte-movimientos" aria-labelledby="tituloPagosCorte">
                        <div class="corte-seccion-titulo"><div><h2 id="tituloPagosCorte">Pagos registrados en la caja</h2><p>Solo se muestran los pagos registrados durante este corte.</p></div></div>
                        <div class="corte-tabla-envoltura">
                            <table class="corte-tabla">
                                <thead><tr><th>Fecha</th><th>Venta</th><th>Estado de venta</th><th>Cliente</th><th>Forma de pago</th><th>Referencia</th><th class="corte-monto">Importe del pago</th><th class="corte-monto">Total de la venta</th></tr></thead>
                                <tbody>
                                    <?php foreach ($pagosCorte as $pago): ?>
                                        <tr>
                                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($pago['fecha']))) ?></td>
                                            <td><?= htmlspecialchars($pago['folio_venta']) ?></td>
                                            <td><?= htmlspecialchars($pago['estado_venta']) ?></td>
                                            <td><?= htmlspecialchars($pago['cliente'] ?? 'Público general') ?></td>
                                            <td><?= htmlspecialchars(ucfirst($pago['forma_pago'])) ?></td>
                                            <td><?= htmlspecialchars($pago['referencia_pago'] ?? '—') ?></td>
                                            <td class="corte-monto"><?= $moneda($pago['importe']) ?></td>
                                            <td class="corte-monto"><?= $moneda($pago['total_venta']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (!$pagosCorte): ?><tr><td colspan="8">Todavía no hay pagos registrados en este corte.</td></tr><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>

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

                <?php if ($corteAbierto && $arqueoPendiente && $puedeOperarCaja): ?>
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
                        <label class="label-campo" for="estadoHistorial">Estado del corte</label>
                        <select class="campo" id="estadoHistorial" name="estado">
                            <option value="todos" <?= $estadoHistorial === 'todos' ? 'selected' : '' ?>>Todos</option>
                            <option value="abierto" <?= $estadoHistorial === 'abierto' ? 'selected' : '' ?>>Abiertos</option>
                            <option value="cerrado" <?= $estadoHistorial === 'cerrado' ? 'selected' : '' ?>>Cerrados</option>
                        </select>
                    </div>
                    <?php if ($esAdministrador): ?>
                        <div class="corte-filtro">
                            <label class="label-campo" for="filtroEmpleado">Cajero</label>
                            <select class="campo" id="filtroEmpleado" name="empleado">
                                <option value="">Todos los cajeros</option>
                                <?php foreach ($empleados as $empleado): ?>
                                    <option value="<?= (int)$empleado['id_empleado'] ?>" <?= $empleadoHistorial === (int)$empleado['id_empleado'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <button class="btn btn-chico" type="submit">Consultar</button>
                </form>

                <section class="tarjeta corte-historial" aria-labelledby="tituloHistorial">
                    <div class="corte-seccion-titulo">
                        <div><h2 id="tituloHistorial">Cortes registrados · <?= htmlspecialchars($periodoTexto) ?></h2><p id="resultadoHistorial"><?= count($historialCortes) ?> corte(s) consultados.</p></div>
                    </div>
                    <?php $tablaHistorialId = 'tablaHistorial'; include __DIR__ . '/cortes_z_tabla.php'; ?>
                </section>
            </section>

            <?php if ($esAdministrador): ?>
                <section class="corte-panel" id="panel-mensual" role="tabpanel" aria-labelledby="tab-mensual" data-panel="mensual" <?= $activePanel === 'mensual' ? '' : 'hidden' ?>>
                    <div class="corte-panel-encabezado">
                        <div><h2>Corte mensual de caja</h2><p>Selecciona un mes para consultar e imprimir resultados de sus cortes diarios cerrados. No se guarda otro registro de caja.</p></div>
                        <button class="btn-secundario btn-chico" type="button" data-imprimir>Imprimir</button>
                    </div>
                    <form method="get" action="corte_caja.php" class="tarjeta corte-estadisticas-filtro">
                        <input type="hidden" name="panel" value="mensual">
                        <div class="corte-filtro">
                            <label class="label-campo" for="periodoCorteMensual">Mes</label>
                            <input class="campo" id="periodoCorteMensual" name="periodo" type="month" value="<?= htmlspecialchars($periodo) ?>" required>
                        </div>
                        <div class="corte-filtro">
                            <label class="label-campo" for="empleadoMensual">Cajero</label>
                            <select class="campo" id="empleadoMensual" name="empleado_mensual">
                                <option value="">Todos los cajeros</option>
                                <?php foreach ($empleados as $empleado): ?>
                                    <option value="<?= (int)$empleado['id_empleado'] ?>" <?= $filtroEmpleadoMensual === (int)$empleado['id_empleado'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button class="btn btn-chico" type="submit">Consultar resultados</button>
                    </form>

                    <?php
                    $maxPagoMensual = max(
                        1,
                        $resumenMensualPeriodo['efectivo'],
                        $resumenMensualPeriodo['tarjeta'],
                        $resumenMensualPeriodo['transferencia']
                    );
                    $maxComparacionMensual = max(1, $resumenMensualPeriodo['ventas_importe'], $resumenMensualPeriodo['comercial']);
                    $maxMovimientosMensual = max(
                        1,
                        $resumenMensualPeriodo['comercial'],
                        $resumenMensualPeriodo['movimientos']['ingreso manual'],
                        $resumenMensualPeriodo['gastos_operativos'],
                        $resumenMensualPeriodo['retiros_resguardo']
                    );
                    ?>
                    <div class="corte-kpis corte-kpis--periodo">
                        <article class="corte-kpi"><span class="corte-kpi-label">Cortes diarios cerrados</span><strong><?= (int)$resumenMensualPeriodo['cortes'] ?></strong></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Ventas registradas</span><strong><?= $moneda($resumenMensualPeriodo['ventas_importe']) ?></strong><small><?= (int)$resumenMensualPeriodo['ventas_cantidad'] ?> venta(s)</small></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Pagos cobrados</span><strong><?= $moneda($resumenMensualPeriodo['comercial']) ?></strong><small>Vinculados a los cortes diarios</small></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Efectivo</span><strong><?= $moneda($resumenMensualPeriodo['efectivo']) ?></strong></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Tarjeta</span><strong><?= $moneda($resumenMensualPeriodo['tarjeta']) ?></strong></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Transferencia</span><strong><?= $moneda($resumenMensualPeriodo['transferencia']) ?></strong></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Otros ingresos de caja</span><strong><?= $moneda($resumenMensualPeriodo['movimientos']['ingreso manual']) ?></strong><small>No son pagos de ventas</small></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Gastos operativos</span><strong><?= $moneda($resumenMensualPeriodo['gastos_operativos']) ?></strong><small>Nómina + gastos</small></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Efectivo a resguardo</span><strong><?= $moneda($resumenMensualPeriodo['retiros_resguardo']) ?></strong><small>Traspaso; no es gasto operativo</small></article>
                    </div>

                    <p class="corte-dialog-nota">Los importes solo incluyen cortes diarios cerrados. El fondo inicial se conserva aparte; los pagos de ventas, otros ingresos de caja y gastos operativos son conceptos distintos. El efectivo a resguardo es un traspaso y no se cuenta como gasto. Las ventas se agrupan por fecha de venta; los pagos se toman de cortes del mes y solo incluyen los vinculados a una caja.</p>

                    <div class="corte-graficas">
                        <section class="tarjeta corte-grafica" aria-labelledby="tituloFormasPagoMensual">
                            <div class="corte-grafica-encabezado"><div><h2 id="tituloFormasPagoMensual">Pagos por forma de cobro</h2><p>Importes acumulados de los cortes diarios</p></div></div>
                            <div class="corte-grafica-barras" role="group" aria-label="Pagos por efectivo, tarjeta y transferencia">
                                <?php foreach ([
                                    ['Efectivo', $resumenMensualPeriodo['efectivo'], 'efectivo'],
                                    ['Tarjeta', $resumenMensualPeriodo['tarjeta'], 'tarjeta'],
                                    ['Transferencia', $resumenMensualPeriodo['transferencia'], 'transferencia'],
                                ] as [$etiquetaPago, $importePago, $clasePago]): ?>
                                    <div class="corte-grafica-fila">
                                        <span><?= htmlspecialchars($etiquetaPago) ?></span>
                                        <div class="corte-grafica-pista"><i class="<?= htmlspecialchars($clasePago) ?>" style="width: <?= $anchoGrafica($importePago, $maxPagoMensual) ?>%"></i></div>
                                        <strong><?= $moneda($importePago) ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                        <section class="tarjeta corte-grafica" aria-labelledby="tituloVentasPagosMensual">
                            <div class="corte-grafica-encabezado"><div><h2 id="tituloVentasPagosMensual">Ventas vs. pagos cobrados</h2><p>Importes por separado; pueden corresponder a fechas distintas</p></div></div>
                            <div class="corte-grafica-barras" role="group" aria-label="Importe de ventas y pagos cobrados">
                                <div class="corte-grafica-fila">
                                    <span>Ventas</span>
                                    <div class="corte-grafica-pista"><i class="ventas" style="width: <?= $anchoGrafica($resumenMensualPeriodo['ventas_importe'], $maxComparacionMensual) ?>%"></i></div>
                                    <strong><?= $moneda($resumenMensualPeriodo['ventas_importe']) ?></strong>
                                </div>
                                <div class="corte-grafica-fila">
                                    <span>Pagos</span>
                                    <div class="corte-grafica-pista"><i class="pagos" style="width: <?= $anchoGrafica($resumenMensualPeriodo['comercial'], $maxComparacionMensual) ?>%"></i></div>
                                    <strong><?= $moneda($resumenMensualPeriodo['comercial']) ?></strong>
                                </div>
                            </div>
                        </section>
                        <section class="tarjeta corte-grafica" aria-labelledby="tituloFlujoMensual">
                            <div class="corte-grafica-encabezado"><div><h2 id="tituloFlujoMensual">Movimientos de caja</h2><p>Pagos, otros ingresos, gastos operativos y efectivo a resguardo</p></div></div>
                            <div class="corte-grafica-barras" role="group" aria-label="Movimientos de caja del periodo">
                                <div class="corte-grafica-fila">
                                    <span>Pagos cobrados</span>
                                    <div class="corte-grafica-pista"><i class="pagos" style="width: <?= $anchoGrafica($resumenMensualPeriodo['comercial'], $maxMovimientosMensual) ?>%"></i></div>
                                    <strong><?= $moneda($resumenMensualPeriodo['comercial']) ?></strong>
                                </div>
                                <div class="corte-grafica-fila">
                                    <span>Otros ingresos</span>
                                    <div class="corte-grafica-pista"><i class="ingresos" style="width: <?= $anchoGrafica($resumenMensualPeriodo['movimientos']['ingreso manual'], $maxMovimientosMensual) ?>%"></i></div>
                                    <strong><?= $moneda($resumenMensualPeriodo['movimientos']['ingreso manual']) ?></strong>
                                </div>
                                <div class="corte-grafica-fila">
                                    <span>Gastos operativos</span>
                                    <div class="corte-grafica-pista"><i class="egresos" style="width: <?= $anchoGrafica($resumenMensualPeriodo['gastos_operativos'], $maxMovimientosMensual) ?>%"></i></div>
                                    <strong><?= $moneda($resumenMensualPeriodo['gastos_operativos']) ?></strong>
                                </div>
                                <div class="corte-grafica-fila">
                                    <span>A resguardo</span>
                                    <div class="corte-grafica-pista"><i class="egresos" style="width: <?= $anchoGrafica($resumenMensualPeriodo['retiros_resguardo'], $maxMovimientosMensual) ?>%"></i></div>
                                    <strong><?= $moneda($resumenMensualPeriodo['retiros_resguardo']) ?></strong>
                                </div>
                            </div>
                        </section>
                        <section class="tarjeta corte-grafica corte-grafica-origen" aria-labelledby="tituloProductosIngresosMensual">
                            <div class="corte-grafica-encabezado"><div><h2 id="tituloProductosIngresosMensual">Productos que más ingresos generan</h2><p>Ordenados por importe vendido en el periodo</p></div></div>
                            <div class="corte-tabla-envoltura"><table class="corte-tabla">
                                <thead><tr><th>Producto</th><th>Unidades</th><th>Importe generado</th></tr></thead>
                                <tbody>
                                    <?php foreach ($resumenMensualPeriodo['mas_ingresos'] as $producto): ?>
                                        <tr><td><?= htmlspecialchars($producto['nombre']) ?></td><td><?= (int)$producto['cantidad'] ?></td><td><?= $moneda($producto['importe']) ?></td></tr>
                                    <?php endforeach; ?>
                                    <?php if (!$resumenMensualPeriodo['mas_ingresos']): ?><tr><td colspan="3">No hay ventas de productos en este periodo.</td></tr><?php endif; ?>
                                </tbody>
                            </table></div>
                        </section>
                    </div>

                    <section class="tarjeta corte-historial" aria-labelledby="tituloMovimientosMensuales">
                        <div class="corte-seccion-titulo"><div><h2 id="tituloMovimientosMensuales">Detalle de ingresos y egresos</h2><p><?= count($movimientosMensuales) ?> movimiento(s) de los cortes diarios cerrados.</p></div></div>
                        <?php $movimientosReporte = $movimientosMensuales; include __DIR__ . '/corte_movimientos_reporte.php'; ?>
                    </section>

                    <section class="tarjeta corte-historial" aria-labelledby="tituloPagosMensuales">
                        <div class="corte-seccion-titulo"><div><h2 id="tituloPagosMensuales">Detalle de pagos vinculados</h2><p><?= count($pagosMensuales) ?> pago(s) asociado(s) a cortes del periodo.</p></div></div>
                        <?php $pagosReporte = $pagosMensuales; include __DIR__ . '/corte_pagos_reporte.php'; ?>
                    </section>

                    <section class="tarjeta corte-historial" aria-labelledby="tituloResumenMensual">
                        <div class="corte-seccion-titulo">
                            <div><h2 id="tituloResumenMensual">Cortes diarios del periodo · <?= htmlspecialchars($periodoTexto) ?></h2><p><?= count($cortesResumenMensual) ?> corte(s) que coinciden con los filtros.</p></div>
                        </div>
                        <?php $historialCortes = $cortesResumenMensual; $tablaHistorialId = 'tablaCortesMensuales'; include __DIR__ . '/cortes_z_tabla.php'; ?>
                    </section>
                </section>
            <?php endif; ?>

            <?php if ($esAdministrador && $resumenMensual): ?>
                <section class="corte-panel" id="panel-estadisticas" role="tabpanel" aria-labelledby="tab-estadisticas" data-panel="estadisticas" <?= $activePanel === 'estadisticas' ? '' : 'hidden' ?>>
                    <div class="corte-panel-encabezado">
                        <div><h2>Estadísticas de caja</h2><p><?= htmlspecialchars($fechaInicioEstadisticas->format('d/m/Y')) ?> – <?= htmlspecialchars($fechaFinEstadisticas->format('d/m/Y')) ?>. Solo considera cortes diarios cerrados; no suma cierres mensuales.</p></div>
                        <button class="btn-secundario btn-chico" type="button" data-imprimir>Imprimir</button>
                    </div>
                    <form method="get" action="corte_caja.php" class="tarjeta corte-estadisticas-filtro">
                        <input type="hidden" name="panel" value="estadisticas">
                        <div class="corte-filtro">
                            <label class="label-campo" for="fechaInicioEstadisticas">Desde</label>
                            <input class="campo" id="fechaInicioEstadisticas" name="fecha_inicio" type="date" value="<?= htmlspecialchars($fechaInicioEstadisticas->format('Y-m-d')) ?>" required>
                        </div>
                        <div class="corte-filtro">
                            <label class="label-campo" for="fechaFinEstadisticas">Hasta</label>
                            <input class="campo" id="fechaFinEstadisticas" name="fecha_fin" type="date" value="<?= htmlspecialchars($fechaFinEstadisticas->format('Y-m-d')) ?>" required>
                        </div>
                        <div class="corte-filtro">
                            <label class="label-campo" for="empleadoEstadisticas">Cajero</label>
                            <select class="campo" id="empleadoEstadisticas" name="empleado_estadisticas">
                                <option value="">Todos los cajeros</option>
                                <?php foreach ($empleados as $empleado): ?>
                                    <option value="<?= (int)$empleado['id_empleado'] ?>" <?= $filtroEmpleadoEstadisticas === (int)$empleado['id_empleado'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button class="btn btn-chico" type="submit">Consultar</button>
                    </form>

                    <?php
                    $maxPagoEstadisticas = max(
                        1,
                        $resumenMensual['efectivo'],
                        $resumenMensual['tarjeta'],
                        $resumenMensual['transferencia']
                    );
                    $maxGeneralEstadisticas = max(
                        1,
                        $resumenMensual['ventas_importe'],
                        $resumenMensual['comercial'],
                        $resumenMensual['movimientos']['ingreso manual'],
                        $resumenMensual['gastos_operativos'],
                        $resumenMensual['retiros_resguardo']
                    );
                    ?>
                    <div class="corte-kpis corte-kpis--periodo">
                        <article class="corte-kpi"><span class="corte-kpi-label">Ventas registradas</span><strong><?= $moneda($resumenMensual['ventas_importe']) ?></strong><small><?= (int)$resumenMensual['ventas_cantidad'] ?> venta(s)</small></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Pagos cobrados</span><strong><?= $moneda($resumenMensual['comercial']) ?></strong><small>Según cortes diarios cerrados</small></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Ingresos en efectivo</span><strong><?= $moneda($resumenMensual['efectivo']) ?></strong></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Ingresos con tarjeta</span><strong><?= $moneda($resumenMensual['tarjeta']) ?></strong></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Ingresos por transferencia</span><strong><?= $moneda($resumenMensual['transferencia']) ?></strong></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Otros ingresos de caja</span><strong><?= $moneda($resumenMensual['movimientos']['ingreso manual']) ?></strong><small>No son pagos de ventas</small></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Gastos operativos</span><strong><?= $moneda($resumenMensual['gastos_operativos']) ?></strong><small>Nómina: <?= $moneda($resumenMensual['movimientos']['nomina']) ?> · Gastos: <?= $moneda($resumenMensual['movimientos']['gasto']) ?></small></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Efectivo a resguardo</span><strong><?= $moneda($resumenMensual['retiros_resguardo']) ?></strong><small>Traspaso; no es gasto operativo</small></article>
                        <article class="corte-kpi"><span class="corte-kpi-label">Fondos iniciales acumulados</span><strong><?= $moneda($resumenMensual['fondos_iniciales']) ?></strong><small>Suma por corte; no son ingresos</small></article>
                    </div>

                    <p class="corte-dialog-nota">Ventas y pagos cobrados son métricas distintas y no se suman entre sí. Otros ingresos de caja se muestran aparte de los pagos por ventas. Nómina y gastos son gastos operativos; el efectivo a resguardo es un traspaso, no un gasto. El fondo inicial se conserva separado.</p>

                    <div class="corte-graficas">
                        <section class="tarjeta corte-grafica" aria-labelledby="tituloFormasPagoEstadisticas">
                            <div class="corte-grafica-encabezado"><div><h2 id="tituloFormasPagoEstadisticas">¿Qué forma de pago genera más?</h2><p>Importes cobrados en cortes diarios</p></div></div>
                            <div class="corte-grafica-barras" role="group" aria-label="Pagos por efectivo, tarjeta y transferencia">
                                <?php foreach ([
                                    ['Efectivo', $resumenMensual['efectivo'], 'efectivo'],
                                    ['Tarjeta', $resumenMensual['tarjeta'], 'tarjeta'],
                                    ['Transferencia', $resumenMensual['transferencia'], 'transferencia'],
                                ] as [$etiquetaPago, $importePago, $clasePago]): ?>
                                    <div class="corte-grafica-fila">
                                        <span><?= htmlspecialchars($etiquetaPago) ?></span>
                                        <div class="corte-grafica-pista"><i class="<?= htmlspecialchars($clasePago) ?>" style="width: <?= $anchoGrafica($importePago, $maxPagoEstadisticas) ?>%"></i></div>
                                        <strong><?= $moneda($importePago) ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                        <section class="tarjeta corte-grafica" aria-labelledby="tituloVentasVsPagos">
                            <div class="corte-grafica-encabezado"><div><h2 id="tituloVentasVsPagos">Ventas y movimientos de caja</h2><p>Importes por separado; los traspasos no son gastos</p></div></div>
                            <div class="corte-grafica-barras" role="group" aria-label="Ventas y movimientos de caja">
                                <?php foreach ([
                                    ['Ventas', $resumenMensual['ventas_importe'], 'ventas', $maxGeneralEstadisticas],
                                    ['Pagos', $resumenMensual['comercial'], 'pagos', $maxGeneralEstadisticas],
                                    ['Otros ingresos', $resumenMensual['movimientos']['ingreso manual'], 'ingresos', $maxGeneralEstadisticas],
                                    ['Gastos operativos', $resumenMensual['gastos_operativos'], 'egresos', $maxGeneralEstadisticas],
                                    ['A resguardo', $resumenMensual['retiros_resguardo'], 'egresos', $maxGeneralEstadisticas],
                                ] as [$etiqueta, $importe, $clase, $maximo]): ?>
                                    <div class="corte-grafica-fila">
                                        <span><?= htmlspecialchars($etiqueta) ?></span>
                                        <div class="corte-grafica-pista"><i class="<?= htmlspecialchars($clase) ?>" style="width: <?= $anchoGrafica($importe, $maximo) ?>%"></i></div>
                                        <strong><?= $moneda($importe) ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    </div>

                    <section class="tarjeta corte-historial" aria-labelledby="tituloCortesZMensuales">
                        <div class="corte-seccion-titulo"><div><h2 id="tituloCortesZMensuales">Cortes Z del periodo</h2><p><?= count($cortesEstadisticas) ?> corte(s) diario(s) cerrado(s).</p></div></div>
                        <?php $historialCortes = $cortesEstadisticas; $tablaHistorialId = 'tablaCortesZResumen'; include __DIR__ . '/cortes_z_tabla.php'; ?>
                    </section>

                    <section class="tarjeta corte-historial" aria-labelledby="tituloMovimientosEstadisticas">
                        <div class="corte-seccion-titulo"><div><h2 id="tituloMovimientosEstadisticas">Detalle de ingresos y egresos</h2><p><?= count($movimientosEstadisticas) ?> movimiento(s) de los cortes diarios cerrados.</p></div></div>
                        <?php $movimientosReporte = $movimientosEstadisticas; include __DIR__ . '/corte_movimientos_reporte.php'; ?>
                    </section>

                    <section class="tarjeta corte-historial" aria-labelledby="tituloPagosEstadisticas">
                        <div class="corte-seccion-titulo"><div><h2 id="tituloPagosEstadisticas">Detalle de pagos por venta</h2><p><?= count($pagosEstadisticas) ?> pago(s) aplicado(s), incluyendo devoluciones.</p></div></div>
                        <?php $pagosReporte = $pagosEstadisticas; include __DIR__ . '/corte_pagos_reporte.php'; ?>
                    </section>

                    <div class="corte-graficas">
                        <section class="tarjeta corte-grafica" aria-labelledby="tituloProductosIngresosEstadisticas">
                            <div class="corte-grafica-encabezado"><div><h2 id="tituloProductosIngresosEstadisticas">Productos que más ingresos generan</h2><p>Ordenados por importe vendido</p></div></div>
                            <div class="corte-tabla-envoltura"><table class="corte-tabla">
                                <thead><tr><th>Producto</th><th>Unidades</th><th>Importe generado</th></tr></thead>
                                <tbody><?php foreach ($resumenMensual['mas_ingresos'] as $producto): ?>
                                    <tr><td><?= htmlspecialchars($producto['nombre']) ?></td><td><?= (int)$producto['cantidad'] ?></td><td><?= $moneda($producto['importe']) ?></td></tr>
                                <?php endforeach; ?><?php if (!$resumenMensual['mas_ingresos']): ?><tr><td colspan="3">Sin ventas de productos en el periodo.</td></tr><?php endif; ?></tbody>
                            </table></div>
                        </section>
                        <section class="tarjeta corte-grafica" aria-labelledby="tituloMasVendidos">
                            <div class="corte-grafica-encabezado"><div><h2 id="tituloMasVendidos">Productos más vendidos</h2><p>Ordenados por cantidad</p></div></div>
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
