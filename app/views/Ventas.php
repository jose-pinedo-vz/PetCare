<?php
// Mostrar nombre del usuario
session_start();
if (!isset($_SESSION['usuario']))
{
  header("Location: ../views/login.php");
  exit();
}
$rolSesion = strtolower(trim((string)($_SESSION['rol'] ?? '')));
if (!in_array($rolSesion, ['admin', 'gerente', 'vendedor', 'recepcionista'], true)) {
  header("Location: inicio.php");
  exit();
}
$usuario=$_SESSION['usuario'];
$empleado_asociado = $_SESSION['empleado_asociado'] ?? null;
$horario_empleado = $_SESSION['horario_empleado'] ?? null;
$id_empleado = $_SESSION['id_empleado'] ?? null;

require_once __DIR__ . '/../models/conexion_db.php';
require_once __DIR__ . '/../models/CorteCaja.php';

$tieneCajaAbierta = false;
$puedeOperarCaja = false;
if (!empty($id_empleado)) {
    try {
        $dbCorte = ConexionDB::obtenerConexion();
        $puedeOperarCaja = CorteCaja::puedeOperarCaja(
            $dbCorte,
            (int)$id_empleado,
            strtolower(trim((string)($_SESSION['rol'] ?? '')))
        );
        $tieneCajaAbierta = CorteCaja::obtenerCorteAbierto($dbCorte, (int)$id_empleado) !== null;
    } catch (Exception $e) {
        $tieneCajaAbierta = false;
    }
}
require_once __DIR__ . '/../models/PagosAdmin.php';
$periodoNomina = date('Y-m');
$mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
$etiquetaPeriodo = ucfirst($mesesEs[(int)date('n') - 1]) . ' ' . date('Y');
$pagosServicios = [];
$nominaFilas = [];
$cajaPagos = ['abierta' => false, 'efectivo' => 0.0, 'movimientos' => []];
$puedeAdminPagos = in_array($rolSesion, ['admin', 'gerente'], true);
try {
    $dbPagos = ConexionDB::obtenerConexion();
    $pagosServicios = PagosAdmin::servicios($dbPagos);
    $nominaFilas = PagosAdmin::nomina($dbPagos, $periodoNomina);
    if (!empty($id_empleado)) {
        $cajaPagos = PagosAdmin::resumenCaja($dbPagos, (int)$id_empleado);
    }
} catch (Throwable $e) {
    error_log('Ventas pagos: ' . $e->getMessage());
}
$serviciosCitas = array_values(array_filter($pagosServicios, fn($f) => $f['tipo'] === 'cita'));
$serviciosHotel = array_values(array_filter($pagosServicios, fn($f) => $f['tipo'] === 'hotel'));
$pendientesServ = array_filter($serviciosCitas, fn($f) => $f['saldo'] > 0 && $f['estado'] !== 'devolver');
$porDevolver = array_filter($serviciosCitas, fn($f) => $f['estado'] === 'devolver');
$pendientesHotel = array_filter($serviciosHotel, fn($f) => $f['saldo'] > 0 && $f['estado'] !== 'devolver');
$porDevolverHotel = array_filter($serviciosHotel, fn($f) => $f['estado'] === 'devolver');
$saldoHotel = array_sum(array_column($pendientesHotel, 'saldo'));
$conAbonoHotel = count(array_filter($pendientesHotel, fn($f) => $f['abonado'] > 0));
$saldoServ = array_sum(array_column($pendientesServ, 'saldo'));
$conAbonoServ = count(array_filter($pendientesServ, fn($f) => $f['abonado'] > 0));
$categoriasServ = array_values(array_unique(array_filter(array_column($serviciosCitas, 'categoria'))));
$nominaPendiente = array_sum(array_map(fn($f) => $f['pagado'] ? 0 : $f['sueldo'], $nominaFilas));
$nominaPagados = count(array_filter($nominaFilas, fn($f) => $f['pagado']));
// Mostrar nombre del usuario
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Ventas</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>
    <div class="layout">
        <?php $paginaActiva = 'ventas'; include __DIR__ . '/layout/sidebar.php'; ?>

        <main>
            <div class="migaja">Ventas y pagos /</div>
            <header class="encabezado-pagina">
                <h1>Ventas y pagos</h1>
                <div class="venta-cajero">Cajero: <strong><?php echo htmlspecialchars((string)$empleado_asociado); ?></strong> | Turno: <strong><?php echo htmlspecialchars((string)$horario_empleado); ?></strong> | Caja: <strong style="color: <?php echo $tieneCajaAbierta ? '#166534' : '#991b1b'; ?>;"><?php echo $tieneCajaAbierta ? 'Abierta' : 'Sin abrir'; ?></strong></div>
            </header>

            <nav class="inventario-pestanas" aria-label="Secciones de ventas y pagos">
                <a href="#ventas" class="activa" id="pestanaVentas" aria-controls="panel-ventas" aria-current="page">1. Punto de venta</a>
                <a href="#pagos" id="pestanaPagos" aria-controls="panel-pagos">2. Pagos de servicios</a>
                <a href="#pagos-empleados" id="pestanaPagosEmpleados" aria-controls="panel-pagos-empleados">3. Pagos a empleados</a>
                <a href="#pagos-hotel" id="pestanaPagosHotel" aria-controls="panel-pagos-hotel">4. Pagos de hotel</a>
            </nav>

            <section id="panel-ventas" class="ventas-panel" aria-labelledby="pestanaVentas">
            <div class="venta-contenedor">
                <section class="tarjeta venta-carrito" aria-labelledby="titulo-carrito">
                    <h2 id="titulo-carrito">Productos de la venta</h2>
                    <div class="venta-captura">
                        <div class="input-group venta-codigo">
                            <label for="barcodeInput">Código de barras</label>
                            <div class="venta-busqueda">
                                <input class="campo" type="text" id="barcodeInput" placeholder="Escanea o escribe un código" autocomplete="off">
                                <button class="btn btn-info btn-chico" id="btnBuscarProducto" type="button">Buscar producto</button>
                            </div>
                            <span class="nota-campo">El producto se agregará a la lista de venta.</span>
                        </div>
                        
                        <div class="input-group venta-cliente">
                            <label for="clienteInput">Cliente</label>
                            <input class="campo" type="tel" id="clienteInput" placeholder="Teléfono (opcional)" inputmode="numeric" autocomplete="off">
                        </div>
                    </div>

                    <div class="tabla-envoltura venta-tabla-envoltura">
                        <table class="venta-tabla">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Código</th>
                                    <th>Precio U.</th>
                                    <th>Cant.</th><!--este campo se puede hacer de ingremento(+,-) si asi se quiere -->
                                    <th>Descuento</th><!--este campo se puede hacer de entrada si se desea agregar algun descuento, o igual de incremento (+,-) -->
                                    <th>Subtotal</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody id="cartTableBody">
                                <tr>
                                    <td>Pelota</td>
                                    <td>12345</td>
                                    <td>$10.00</td>
                                    <td>2</td>
                                    <td>$0.00</td>
                                    <td>$20.00</td>
                                    <td class="acciones">
                                        <a class="btn btn-peligro btn-chico" href="enespera.php?id=1">Eliminar</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="venta-carrito-acciones">
                        <button class="btn btn-info btn-chico" id="btnAgregarServicio" type="button">Agregar servicio</button>
                        <button class="btn btn-chico" id="btnNuevaVenta" type="button">Nueva venta</button> <!-- este boton deberia limpiar la venta actual y reiniciar todo, tambien se deberia preguntar si se desea cancelar la venta actual, o eso interprete -->
                    </div>
                    <p class="venta-mensaje" id="ventaMensaje" role="status" aria-live="polite"></p>
                </section>

                <div class="venta-lateral">
                    <aside class="tarjeta venta-resumen" aria-label="Resumen de venta">
                        <h2>Resumen</h2>
                        <div class="venta-totales">
                            <div><span>Artículos</span><strong id="lblItems">2</strong></div>
                            <div><span>Subtotal</span><strong id="lblSubtotal">$20.00</strong></div>
                            <div><span>IVA (16%)</span><strong id="lblIva">$3.20</strong></div>
                            <div class="venta-total"><span>Total</span><strong id="lblTotal">$23.20 MXN</strong></div>
                        </div>
                        <div class="venta-acciones">
                            <button class="btn btn-info btn-chico" id="btnCobrar" type="button" data-caja-abierta="<?php echo $tieneCajaAbierta ? '1' : '0'; ?>">Cobrar</button>
                            <button class="btn btn-peligro btn-chico" id="btnCancelarVenta" type="button">Cancelar venta</button> <!-- este boton deberia cancelar la venta actual, y se deberia preguntar si se desea cancelar la venta actual y limpiar la ventana actual, o eso entnedi -->
                        </div>
                    </aside>
                    <section class="tarjeta venta-herramientas" aria-labelledby="tituloHerramientasVenta">
                        <h2 id="tituloHerramientasVenta">Accesos</h2>
                        <div class="venta-reportes">
                            <?php if ($puedeOperarCaja): ?>
                                <a class="btn btn-secundario btn-chico" href="corte_caja.php">Corte de caja</a>
                            <?php endif; ?>
                            <a class="btn btn-secundario btn-chico" href="historial_ventas.php">Historial de ventas</a>
                        </div>
                    </section>
                </div>
            </div>
            </section>

            <section id="panel-pagos" class="ventas-panel" aria-labelledby="pestanaPagos" hidden>
                <div class="inventario-resumen pagos-resumen" aria-label="Resumen de pagos pendientes">
                    <article class="inventario-tarjeta">
                        <h3>Servicios por cobrar</h3>
                        <p id="pagosPendientesConteo"><?= count($pendientesServ) ?></p>
                    </article>
                    <article class="inventario-tarjeta">
                        <h3>Saldo pendiente</h3>
                        <p id="pagosPendientesTotal">$<?= number_format($saldoServ, 2) ?></p>
                    </article>
                    <article class="inventario-tarjeta">
                        <h3>Con abonos</h3>
                        <p id="pagosConAbonoConteo"><?= $conAbonoServ ?></p>
                    </article>
                    <article class="inventario-tarjeta">
                        <h3>Por devolver</h3>
                        <p id="pagosPorDevolver">$<?= number_format(array_sum(array_column($porDevolver, 'abonado')), 2) ?></p>
                    </article>
                </div>

                <section class="tarjeta" aria-labelledby="tituloPagosPendientes">
                    <div class="pagos-encabezado">
                        <div>
                            <h2 id="tituloPagosPendientes">Pagos de servicios</h2>
                            <p>Citas de consulta y estética por pagar y pagadas. Al liquidarlas pasan a Pagado.</p>
                        </div>
                    </div>
                    <div class="pagos-filtros">
                        <div>
                            <label class="label-campo" for="buscarPagoServicio">Buscar</label>
                            <input class="campo" id="buscarPagoServicio" type="search" placeholder="Cliente, mascota o servicio">
                        </div>
                        <div>
                            <label class="label-campo" for="filtroTipoServicio">Tipo de servicio</label>
                            <select class="campo" id="filtroTipoServicio">
                                <option value="">Todos los servicios</option>
                                <?php foreach ($categoriasServ as $cat): ?>
                                    <option value="<?= htmlspecialchars(strtolower($cat)) ?>"><?= htmlspecialchars($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="label-campo" for="filtroEstadoPago">Estado del pago</label>
                            <select class="campo" id="filtroEstadoPago">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Por pagar</option>
                                <option value="parcial">Abono parcial</option>
                                <option value="pagado">Pagado</option>
                                <option value="devolver">Por devolver</option>
                            </select>
                        </div>
                    </div>
                    <div class="tabla-envoltura pagos-tabla-envoltura">
                        <table class="pagos-tabla">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente / mascota</th>
                                    <th>Servicio</th>
                                    <th>Total</th>
                                    <th>Abonado</th>
                                    <th>Saldo</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tablaPagosPendientes">
                                <?php if (empty($serviciosCitas)): ?>
                                    <tr><td colspan="8">No hay citas por cobrar.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($serviciosCitas as $ps):
                                    $clsEstado = $ps['estado'] === 'pagado' ? 'pagada' : ($ps['estado'] === 'parcial' ? 'parcial' : ($ps['estado'] === 'devolver' ? 'cancelada' : 'pendiente'));
                                    $txtEstado = ['pagado' => 'Pagado', 'parcial' => 'Abono parcial', 'devolver' => 'Por devolver', 'pendiente' => 'Por pagar'][$ps['estado']];
                                ?>
                                <tr data-kind="<?= $ps['tipo'] ?>" data-id="<?= (int)$ps['id'] ?>" data-tipo="<?= htmlspecialchars(strtolower((string)$ps['categoria'])) ?>" data-estado="<?= $ps['estado'] ?>" data-fecha="<?= htmlspecialchars(substr((string)$ps['fecha'], 0, 10)) ?>" data-total="<?= $ps['total'] ?>" data-pagado="<?= $ps['abonado'] ?>" data-saldo="<?= $ps['saldo'] ?>">
                                    <td><?= $ps['fecha'] ? date('d/m/Y', strtotime($ps['fecha'])) : '' ?></td>
                                    <td><?= htmlspecialchars((string)$ps['cliente']) ?><small><?= htmlspecialchars((string)$ps['mascota'] . ' · ' . (string)$ps['especie']) ?></small></td>
                                    <td><?= htmlspecialchars((string)$ps['servicio']) ?></td>
                                    <td>$<?= number_format($ps['total'], 2) ?></td>
                                    <td class="pago-abonado">$<?= number_format($ps['abonado'], 2) ?></td>
                                    <td class="pago-saldo"><strong>$<?= number_format($ps['saldo'], 2) ?></strong></td>
                                    <td class="pago-estado"><span class="historial-estado <?= $clsEstado ?>"><?= $txtEstado ?></span></td>
                                    <td>
                                        <?php if ($ps['estado'] === 'devolver'): ?>
                                            <button class="btn btn-secundario btn-chico" type="button" data-devolver <?= $puedeAdminPagos ? '' : 'disabled' ?>>Devolver</button>
                                        <?php else: ?>
                                            <button class="btn btn-info btn-chico" type="button" data-registrar-pago <?= $ps['saldo'] <= 0 ? 'disabled' : '' ?>>Registrar pago</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p id="mensajePagoServicio" class="venta-mensaje" role="status" aria-live="polite"></p>
                </section>
            </section>

            <section id="panel-pagos-hotel" class="ventas-panel" aria-labelledby="pestanaPagosHotel" hidden>
                <div class="inventario-resumen pagos-resumen" aria-label="Resumen de pagos de hotel">
                    <article class="inventario-tarjeta">
                        <h3>Reservaciones por pagar</h3>
                        <p><?= count($pendientesHotel) ?></p>
                    </article>
                    <article class="inventario-tarjeta">
                        <h3>Saldo pendiente</h3>
                        <p>$<?= number_format($saldoHotel, 2) ?></p>
                    </article>
                    <article class="inventario-tarjeta">
                        <h3>Con abonos</h3>
                        <p><?= $conAbonoHotel ?></p>
                    </article>
                    <article class="inventario-tarjeta">
                        <h3>Por devolver</h3>
                        <p>$<?= number_format(array_sum(array_column($porDevolverHotel, 'abonado')), 2) ?></p>
                    </article>
                </div>

                <section class="tarjeta" aria-labelledby="tituloPagosHotel">
                    <div class="pagos-encabezado">
                        <div>
                            <h2 id="tituloPagosHotel">Pagos de hotel</h2>
                            <p>Reservaciones de hotel por pagar. Al liquidarlas pasan a Pagado.</p>
                        </div>
                    </div>
                    <div class="pagos-filtros">
                        <div>
                            <label class="label-campo" for="buscarPagoHotel">Buscar</label>
                            <input class="campo" id="buscarPagoHotel" type="search" placeholder="Cliente o mascota">
                        </div>
                        <div>
                            <label class="label-campo" for="filtroEstadoHotel">Estado del pago</label>
                            <select class="campo" id="filtroEstadoHotel">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Por pagar</option>
                                <option value="parcial">Abono parcial</option>
                                <option value="pagado">Pagado</option>
                                <option value="devolver">Por devolver</option>
                            </select>
                        </div>
                    </div>
                    <div class="tabla-envoltura pagos-tabla-envoltura">
                        <table class="pagos-tabla">
                            <thead>
                                <tr>
                                    <th>Entrada</th>
                                    <th>Cliente / mascota</th>
                                    <th>Estancia</th>
                                    <th>Total</th>
                                    <th>Abonado</th>
                                    <th>Saldo</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tablaPagosHotel">
                                <?php if (empty($serviciosHotel)): ?>
                                    <tr><td colspan="8">No hay reservaciones de hotel.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($serviciosHotel as $ph):
                                    $clsEstadoH = $ph['estado'] === 'pagado' ? 'pagada' : ($ph['estado'] === 'parcial' ? 'parcial' : ($ph['estado'] === 'devolver' ? 'cancelada' : 'pendiente'));
                                    $txtEstadoH = ['pagado' => 'Pagado', 'parcial' => 'Abono parcial', 'devolver' => 'Por devolver', 'pendiente' => 'Por pagar'][$ph['estado']];
                                ?>
                                <tr data-kind="hotel" data-id="<?= (int)$ph['id'] ?>" data-estado="<?= $ph['estado'] ?>" data-total="<?= $ph['total'] ?>" data-pagado="<?= $ph['abonado'] ?>" data-saldo="<?= $ph['saldo'] ?>">
                                    <td><?= $ph['fecha'] ? date('d/m/Y', strtotime($ph['fecha'])) : '' ?></td>
                                    <td><?= htmlspecialchars((string)$ph['cliente']) ?><small><?= htmlspecialchars((string)$ph['mascota'] . ' · ' . (string)$ph['especie']) ?></small></td>
                                    <td><?= htmlspecialchars((string)$ph['servicio']) ?></td>
                                    <td>$<?= number_format($ph['total'], 2) ?></td>
                                    <td class="pago-abonado">$<?= number_format($ph['abonado'], 2) ?></td>
                                    <td class="pago-saldo"><strong>$<?= number_format($ph['saldo'], 2) ?></strong></td>
                                    <td class="pago-estado"><span class="historial-estado <?= $clsEstadoH ?>"><?= $txtEstadoH ?></span></td>
                                    <td>
                                        <?php if ($ph['estado'] === 'devolver'): ?>
                                            <button class="btn btn-secundario btn-chico" type="button" data-devolver <?= $puedeAdminPagos ? '' : 'disabled' ?>>Devolver</button>
                                        <?php else: ?>
                                            <button class="btn btn-info btn-chico" type="button" data-registrar-pago <?= $ph['saldo'] <= 0 ? 'disabled' : '' ?>>Registrar pago</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p id="mensajePagoHotel" class="venta-mensaje" role="status" aria-live="polite"></p>
                </section>
            </section>

            <section id="panel-pagos-empleados" class="ventas-panel" aria-labelledby="pestanaPagosEmpleados" hidden>
                <div class="inventario-resumen pagos-resumen nomina-resumen" aria-label="Resumen de pagos a empleados">
                    <article class="inventario-tarjeta">
                        <h3>Efectivo disponible en caja</h3>
                        <p id="saldoCajaEmpleados">$<?= number_format($cajaPagos['efectivo'], 2) ?></p>
                    </article>
                    <article class="inventario-tarjeta">
                        <h3>Nómina pendiente</h3>
                        <p id="nominaPendienteTotal">$<?= number_format($nominaPendiente, 2) ?></p>
                    </article>
                    <article class="inventario-tarjeta">
                        <h3>Empleados pagados</h3>
                        <p id="empleadosPagadosConteo"><?= $nominaPagados ?> de <?= count($nominaFilas) ?></p>
                    </article>
                </div>

                <section class="tarjeta" aria-labelledby="tituloPagosEmpleados">
                    <div class="pagos-encabezado nomina-encabezado">
                        <div>
                            <h2 id="tituloPagosEmpleados">Pagos a empleados</h2>
                            <p>Los pagos se cubren completos y exclusivamente en efectivo, descontando el importe de caja.</p>
                        </div>
                        <?php if ($puedeAdminPagos): ?><button class="btn btn-secundario btn-chico" id="btnAbonarCaja" type="button">Abonar a caja</button><?php endif; ?>
                    </div>
                    <p class="nomina-nota">Periodo: <?= htmlspecialchars($etiquetaPeriodo) ?>. <?= $cajaPagos['abierta'] ? 'Los pagos salen del efectivo de tu caja abierta.' : 'Abre caja en Corte de caja para poder pagar nómina.' ?><?= $puedeAdminPagos ? ' Captura el sueldo de cada empleado antes de pagar.' : ' Solo administrador o gerente pueden pagar nómina.' ?></p>
                    <div class="tabla-envoltura pagos-tabla-envoltura">
                        <table class="pagos-tabla nomina-tabla">
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>Puesto</th>
                                    <th>Periodo</th>
                                    <th>Pago pendiente</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tablaPagosEmpleados">
                                <?php foreach ($nominaFilas as $ne): ?>
                                <tr data-id="<?= (int)$ne['id_empleado'] ?>" data-empleado="<?= htmlspecialchars($ne['nombre']) ?>" data-pago="<?= $ne['sueldo'] ?>" data-estado="<?= $ne['pagado'] ? 'pagado' : 'pendiente' ?>">
                                    <td><strong><?= htmlspecialchars($ne['nombre']) ?></strong></td>
                                    <td><?= htmlspecialchars((string)$ne['puesto']) ?></td>
                                    <td><?= htmlspecialchars($etiquetaPeriodo) ?></td>
                                    <td>
                                        <?php if ($puedeAdminPagos && !$ne['pagado']): ?>
                                            <input class="campo" type="number" min="0" max="999999" step="0.01" value="<?= $ne['sueldo'] ?>" data-sueldo aria-label="Sueldo de <?= htmlspecialchars($ne['nombre']) ?>" style="max-width:120px">
                                        <?php else: ?>
                                            $<?= number_format($ne['sueldo'], 2) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="nomina-estado"><span class="historial-estado <?= $ne['pagado'] ? 'pagada' : 'pendiente' ?>"><?= $ne['pagado'] ? 'Pagado' : 'Pendiente' ?></span></td>
                                    <td><button class="btn btn-info btn-chico" type="button" data-pagar-empleado <?= ($ne['pagado'] || !$puedeAdminPagos) ? 'disabled' : '' ?>>Pagar en efectivo</button></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p id="mensajePagoEmpleado" class="venta-mensaje" role="status" aria-live="polite"></p>
                </section>

                <section class="tarjeta nomina-movimientos" aria-labelledby="tituloMovimientosCaja">
                    <div class="pagos-encabezado">
                        <div>
                            <h2 id="tituloMovimientosCaja">Movimientos de caja</h2>
                            <p>Abonos y pagos de nómina de tu corte abierto.</p>
                        </div>
                    </div>
                    <div class="tabla-envoltura">
                        <table class="pagos-tabla movimientos-caja-tabla">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Concepto</th>
                                    <th>Movimiento</th>
                                    <th>Importe</th>
                                </tr>
                            </thead>
                            <tbody id="tablaMovimientosCaja">
                                <?php if (empty($cajaPagos['movimientos'])): ?>
                                    <tr id="sinMovimientosCaja"><td colspan="4">Aún no hay movimientos en este corte.</td></tr>
                                <?php endif; ?>
                                <?php foreach (array_reverse($cajaPagos['movimientos']) as $mv): $ent = $mv['tipo'] === 'ingreso manual'; ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($mv['fecha'])) ?></td>
                                        <td><?= htmlspecialchars((string)$mv['concepto']) ?></td>
                                        <td><?= $ent ? 'Entrada' : 'Salida' ?></td>
                                        <td><?= $ent ? '+' : '-' ?>$<?= number_format((float)$mv['importe'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </section>
        </main>
    </div>
    <?php include __DIR__ . '/buscar_productos.php'; ?>
    <?php include __DIR__ . '/buscar_servicios.php'; ?>
    <?php include __DIR__ . '/pagar_vents.php'; ?>
    <!-- Backend: tras validar y registrar el pago, abrir este dialgo para que se elija imprimir ticket, generar factura o iniciar una nueva venta. -->
    <dialog class="inventario-dialog producto-dialog resultado-dialog" id="modalPagoConfirmado" aria-labelledby="tituloPagoConfirmado">
        <div class="producto-dialog-encabezado">
            <h1 id="tituloPagoConfirmado">Pago confirmado</h1>
            <form method="dialog">
                <button class="producto-dialog-cerrar" type="submit" aria-label="Cerrar">&times;</button>
            </form>
        </div>
        <div class="resultado-contenido">
            <p class="resultado-mensaje">Procesando el cobro...</p>
            <div class="venta-total">Total de la venta: <strong id="totalVentaDemostracion">$23.20 MXN</strong></div>
            <p class="resultado-factura" id="mensajeFactura" role="status" hidden></p>
            <div class="venta-carrito-acciones">
                <button class="btn btn-secundario btn-chico" id="btnImprimirTicket" type="button">Imprimir ticket</button>
                <button class="btn btn-chico" id="btnGenerarFactura" type="button">Generar factura</button>
                <button class="btn btn-secundario btn-chico" id="btnNuevaVentaConfirmada" type="button">Nueva venta</button>
            </div>
        </div>
    </dialog>
    <dialog class="inventario-dialog producto-dialog factura-dialog" id="modalDatosFactura" aria-labelledby="tituloDatosFactura">
        <div class="producto-dialog-encabezado">
            <h1 id="tituloDatosFactura">Datos para facturación</h1>
            <form method="dialog">
                <button class="producto-dialog-cerrar" type="submit" aria-label="Cerrar">&times;</button>
            </form>
        </div>
        <form class="factura-formulario" id="formDatosFactura">
            <label class="label-campo" for="facturaRfc">RFC</label>
            <input class="campo" id="facturaRfc" name="rfc" type="text" autocomplete="off" required>
            <label class="label-campo" for="facturaRazonSocial">Razón social</label>
            <input class="campo" id="facturaRazonSocial" name="razonSocial" type="text" autocomplete="organization" required>
            <label class="label-campo" for="facturaCodigoPostal">Código postal fiscal</label>
            <input class="campo" id="facturaCodigoPostal" name="codigoPostal" type="text" inputmode="numeric" autocomplete="postal-code" required>
            <label class="label-campo" for="facturaCorreo">Correo electrónico</label>
            <input class="campo" id="facturaCorreo" name="correo" type="email" autocomplete="email" required>
            <div class="pago-acciones">
                <button class="btn btn-secundario btn-chico" id="btnVolverPagoConfirmado" type="button">Cancelar</button>
                <button class="btn btn-chico" type="submit">Continuar</button>
            </div>
        </form>
    </dialog>
    <dialog class="inventario-dialog producto-dialog pago-empleado-dialog" id="modalConfirmarPagoEmpleado" aria-labelledby="tituloConfirmarPagoEmpleado">
        <div class="producto-dialog-encabezado pago-dialog-encabezado">
            <h1 id="tituloConfirmarPagoEmpleado">Confirmar pago a empleado</h1>
            <button class="producto-dialog-cerrar" id="cerrarConfirmarPagoEmpleado" type="button" aria-label="Cerrar">&times;</button>
        </div>
        <div class="pago-empleado-contenido">
            <p id="resumenPagoEmpleado"></p>
            <p class="pago-nota">Este pago se descontará del efectivo disponible en caja. No se aceptan otros métodos de pago.</p>
            <div class="pago-acciones">
                <button class="btn btn-secundario btn-chico" id="cancelarPagoEmpleado" type="button">Cancelar</button>
                <button class="btn btn-chico" id="confirmarPagoEmpleado" type="button">Confirmar pago en efectivo</button>
            </div>
        </div>
    </dialog>
    <dialog class="inventario-dialog producto-dialog pago-empleado-dialog" id="modalAbonoCaja" aria-labelledby="tituloAbonoCaja">
        <div class="producto-dialog-encabezado pago-dialog-encabezado">
            <h1 id="tituloAbonoCaja">Abonar efectivo a caja</h1>
            <button class="producto-dialog-cerrar" id="cerrarAbonoCaja" type="button" aria-label="Cerrar">&times;</button>
        </div>
        <form class="pago-servicio-formulario" id="formAbonoCaja">
            <p id="notaAbonoCaja">Registra un ingreso manual de efectivo a caja.</p>
            <label class="label-campo" for="montoAbonoCaja">Monto del abono</label>
            <input class="campo" id="montoAbonoCaja" name="monto" type="number" min="0.01" step="0.01" inputmode="decimal" required>
            <label class="label-campo" for="conceptoAbonoCaja">Concepto</label>
            <input class="campo" id="conceptoAbonoCaja" name="concepto" type="text" maxlength="100" value="Abono administrativo" required>
            <p class="pago-nota">Este ingreso se registra en tu caja abierta. En producción, solo el administrador debe tener este permiso.</p>
            <div class="pago-acciones">
                <button class="btn btn-secundario btn-chico" id="cancelarAbonoCaja" type="button">Cancelar</button>
                <button class="btn btn-chico" type="submit">Registrar abono en efectivo</button>
            </div>
        </form>
    </dialog>
    <script>
        const botonBuscarProducto = document.getElementById('btnBuscarProducto');
        const modalBuscarProducto = document.getElementById('modalBuscarProducto');
        const botonAgregarServicio = document.getElementById('btnAgregarServicio');
        const modalBuscarServicio = document.getElementById('modalBuscarServicio');
        const botonCobrar = document.getElementById('btnCobrar');
        const modalPagarVenta = document.getElementById('modalPagarVenta');
        const modalPagoConfirmado = document.getElementById('modalPagoConfirmado');
        const modalDatosFactura = document.getElementById('modalDatosFactura');
        const botonConfirmarPago = document.getElementById('btnConfirmarPago');
        const inputMontoRecibido = document.getElementById('montoRecibido');
        const inputMontoAbono = document.getElementById('montoAbonoServicio');
        const metodosPago = [...document.querySelectorAll('[data-metodo-pago]')];
        const detallesTarjeta = document.getElementById('detallesTarjeta');
        const detallesTransferencia = document.getElementById('detallesTransferencia');
        const filasResumenPago = document.getElementById('tablaResumenPago').querySelector('tbody');
        const resumenVentaOriginal = filasResumenPago.innerHTML;
        const numeroTarjeta = document.getElementById('numeroTarjeta');
        const nombreTarjeta = document.getElementById('nombreTarjeta');
        const vencimientoTarjeta = document.getElementById('vencimientoTarjeta');
        const cvvTarjeta = document.getElementById('cvvTarjeta');
        let metodoPagoActual = 'Efectivo';
        let contextoPago = { tipo: 'venta', total: 23.20, fila: null };

        async function llamarPagos(datos) {
            try {
                const r = await fetch('../controllers/pagos.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(datos)
                });
                return await r.json();
            } catch (e) {
                return { ok: false, error: 'No se pudo conectar con el servidor.' };
            }
        }

        function recargarCon(mensaje, hash) {
            try { sessionStorage.setItem('mensajePagos', JSON.stringify({ mensaje, hash })); } catch (e) {}
            window.location.hash = hash;
            window.location.reload();
        }

        const pestanasVentas = [
            { enlace: document.getElementById('pestanaVentas'), panel: document.getElementById('panel-ventas') },
            { enlace: document.getElementById('pestanaPagos'), panel: document.getElementById('panel-pagos') },
            { enlace: document.getElementById('pestanaPagosEmpleados'), panel: document.getElementById('panel-pagos-empleados') },
            { enlace: document.getElementById('pestanaPagosHotel'), panel: document.getElementById('panel-pagos-hotel') }
        ];

        function activarPestana() {
            const indiceActivo = window.location.hash === '#pagos-hotel'
                ? 3
                : window.location.hash === '#pagos-empleados'
                    ? 2
                    : window.location.hash === '#pagos'
                        ? 1
                        : 0;
            pestanasVentas.forEach(({ enlace, panel }, indice) => {
                const activa = indice === indiceActivo;
                enlace.classList.toggle('activa', activa);
                if (activa) enlace.setAttribute('aria-current', 'page');
                else enlace.removeAttribute('aria-current');
                panel.hidden = !activa;
            });
        }

        window.addEventListener('hashchange', activarPestana);
        activarPestana();

        botonBuscarProducto.addEventListener('click', () => {
            modalBuscarProducto.showModal();
        });

        botonAgregarServicio.addEventListener('click', () => {
            modalBuscarServicio.showModal();
        });

        function obtenerMontoVenta() {
            const texto = document.getElementById('lblTotal').textContent;
            return Number(texto.replace(/[^\d.-]/g, ''));
        }

        function establecerResumenPago({ tipo, total, fila }) {
            const esServicio = tipo === 'servicio';
            const etiquetaTotal = esServicio ? 'Saldo pendiente' : 'Total';
            document.getElementById('tituloPagarVenta').textContent = esServicio ? 'Pagar servicio' : 'Pagar venta';
            document.getElementById('etiquetaResumenPago').textContent = esServicio ? 'Resumen del servicio' : 'Resumen de la venta';
            document.getElementById('etiquetaTotalPago').textContent = etiquetaTotal;
            document.getElementById('totalPago').textContent = moneda.format(total);
            document.getElementById('montoTotalPago').innerHTML = `${moneda.format(total)} <small>MXN</small>`;
            document.getElementById('filaIvaPago').hidden = esServicio;
            document.getElementById('filaSubtotalPago').hidden = esServicio;
            document.getElementById('tablaResumenPago').hidden = esServicio;
            document.getElementById('campoAbonoServicio').hidden = !esServicio;
            document.getElementById('campoEfectivo').hidden = esServicio;
            document.getElementById('etiquetaMontoTotal').textContent = esServicio ? 'Saldo pendiente' : 'Total a pagar';
            document.getElementById('notaMetodoPago').textContent = esServicio
                ? 'Puedes abonar una parte o liquidar el saldo del servicio.'
                : 'El monto recibido debe cubrir el total de la venta.';
            document.getElementById('etiquetaCambio').hidden = esServicio;
            document.getElementById('cambioPago').hidden = esServicio;

            if (esServicio) {
                const nombreServicio = fila.cells[2].textContent.trim();
                const clienteMascota = fila.cells[1].textContent.trim();
                filasResumenPago.replaceChildren();
                const renglon = document.createElement('tr');
                const detalle = document.createElement('td');
                detalle.textContent = `${nombreServicio} · ${clienteMascota}`;
                const cantidad = document.createElement('td');
                cantidad.textContent = '1';
                const precio = document.createElement('td');
                precio.textContent = moneda.format(total);
                const subtotal = document.createElement('td');
                subtotal.textContent = moneda.format(total);
                renglon.append(detalle, cantidad, precio, subtotal);
                filasResumenPago.append(renglon);
                document.getElementById('saldoServicioPago').innerHTML = `${moneda.format(total)} <small>MXN</small>`;
                inputMontoAbono.max = total.toFixed(2);
                inputMontoAbono.value = total.toFixed(2);
                inputMontoAbono.required = true;
                botonConfirmarPago.textContent = 'Registrar abono';
            } else {
                filasResumenPago.innerHTML = resumenVentaOriginal;
                inputMontoRecibido.value = total.toFixed(2);
                document.getElementById('subtotalPago').textContent = document.getElementById('lblSubtotal').textContent;
                document.getElementById('ivaPago').textContent = document.getElementById('lblIva').textContent;
                inputMontoAbono.required = false;
                botonConfirmarPago.textContent = 'Confirmar pago';
            }

            contextoPago = { tipo, total, fila };
            actualizarCambio();
        }

        function actualizarCambio() {
            if (contextoPago.tipo !== 'venta' || metodoPagoActual !== 'Efectivo') {
                document.getElementById('cambioPago').innerHTML = '$0.00 <small>MXN</small>';
                return;
            }
            const recibido = Number(inputMontoRecibido.value);
            const cambio = Number.isFinite(recibido) ? Math.max(0, recibido - contextoPago.total) : 0;
            document.getElementById('cambioPago').innerHTML = `${moneda.format(cambio)} <small>MXN</small>`;
        }

        function seleccionarMetodoPago(metodo) {
            metodoPagoActual = metodo;
            metodosPago.forEach((boton) => {
                const seleccionado = boton.dataset.metodoPago === metodo;
                boton.classList.toggle('seleccionado', seleccionado);
                boton.setAttribute('aria-pressed', String(seleccionado));
            });

            const esEfectivo = metodo === 'Efectivo';
            const esTarjeta = metodo === 'Tarjeta';
            const esTransferencia = metodo === 'Transferencia';
            document.getElementById('campoEfectivo').hidden = contextoPago.tipo === 'servicio' || !esEfectivo;
            document.getElementById('campoAbonoServicio').hidden = contextoPago.tipo !== 'servicio';
            document.getElementById('etiquetaCambio').hidden = contextoPago.tipo === 'servicio' || !esEfectivo;
            document.getElementById('cambioPago').hidden = contextoPago.tipo === 'servicio' || !esEfectivo;
            detallesTarjeta.hidden = !esTarjeta;
            detallesTransferencia.hidden = !esTransferencia;
            [numeroTarjeta, nombreTarjeta, vencimientoTarjeta, cvvTarjeta].forEach((campo) => {
                campo.required = esTarjeta;
            });
            document.getElementById('notaMetodoPago').textContent = contextoPago.tipo === 'servicio'
                ? 'Puedes abonar una parte o liquidar el saldo del servicio.'
                : esEfectivo
                    ? 'El monto recibido debe cubrir el total de la venta.'
                    : esTarjeta
                        ? 'El cobro se autoriza con el banco: usa el CVV dinámico de la banca en línea (cambia cada 5 minutos).'
                        : 'La referencia de transferencia es opcional; la validación bancaria se conectará después.';
            actualizarCambio();
        }

        function abrirModalPagoVenta() {
            establecerResumenPago({ tipo: 'venta', total: obtenerMontoVenta(), fila: null });
            seleccionarMetodoPago('Efectivo');
            modalPagarVenta.showModal();
        }

        botonCobrar.addEventListener('click', abrirModalPagoVenta);
        inputMontoRecibido.addEventListener('input', actualizarCambio);
        metodosPago.forEach((boton) => {
            boton.addEventListener('click', () => seleccionarMetodoPago(boton.dataset.metodoPago));
        });

        [numeroTarjeta, cvvTarjeta].forEach((campo) => {
            campo.addEventListener('input', () => campo.value = campo.value.replace(/\D/g, ''));
        });

        numeroTarjeta.addEventListener('input', () => {
            const digitos = numeroTarjeta.value.replace(/\D/g, '').slice(0, 19);
            numeroTarjeta.value = digitos.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
            const cantidadDigitos = digitos.length;
            numeroTarjeta.setCustomValidity(cantidadDigitos >= 13 && cantidadDigitos <= 19 ? '' : 'Ingresa entre 13 y 19 dígitos.');
        });

        document.getElementById('vencimientoTarjeta').min = new Date().toISOString().slice(0, 7);
        document.getElementById('vencimientoTarjeta').max = (new Date().getFullYear() + 15) + '-12';

        function limpiarDatosTarjeta() {
            [numeroTarjeta, nombreTarjeta, vencimientoTarjeta, cvvTarjeta].forEach((campo) => {
                campo.value = '';
                campo.required = false;
                campo.setCustomValidity('');
            });
            document.getElementById('referenciaTransferencia').value = '';
            seleccionarMetodoPago('Efectivo');
        }

        document.getElementById('cerrarModalPago').addEventListener('click', () => modalPagarVenta.close());
        document.getElementById('cancelarPago').addEventListener('click', () => modalPagarVenta.close());
        modalPagarVenta.addEventListener('close', limpiarDatosTarjeta);

        botonConfirmarPago.addEventListener('click', () => {
            const esServicio = contextoPago.tipo === 'servicio';
            const importe = esServicio ? Number(inputMontoAbono.value) : contextoPago.total;

            if (!Number.isFinite(importe) || importe <= 0 ||
                (esServicio && importe > contextoPago.total) ||
                (!esServicio && metodoPagoActual === 'Efectivo' && Number(inputMontoRecibido.value) < contextoPago.total)) {
                (esServicio ? inputMontoAbono : inputMontoRecibido).setCustomValidity(
                    esServicio
                        ? 'El abono debe ser mayor a cero y no superar el saldo pendiente.'
                        : 'El monto recibido debe cubrir el total de la venta.'
                );
                (esServicio ? inputMontoAbono : inputMontoRecibido).reportValidity();
                (esServicio ? inputMontoAbono : inputMontoRecibido).setCustomValidity('');
                return;
            }

            if (metodoPagoActual === 'Tarjeta') {
                const tarjetaValida = [numeroTarjeta, nombreTarjeta, vencimientoTarjeta, cvvTarjeta]
                    .every((campo) => campo.reportValidity());
                if (!tarjetaValida) return;
            }
            if (metodoPagoActual === 'Transferencia' && !document.getElementById('referenciaTransferencia').reportValidity()) return;

            if (esServicio) {
                const fila = contextoPago.fila;
                botonConfirmarPago.disabled = true;
                llamarPagos({
                    accion: 'registrar_pago',
                    tipo: fila.dataset.kind,
                    id: Number(fila.dataset.id),
                    monto: importe,
                    metodo: metodoPagoActual,
                    referencia: document.getElementById('referenciaTransferencia').value,
                    tarjeta: {
                        numero: numeroTarjeta.value,
                        titular: nombreTarjeta.value,
                        vencimiento: vencimientoTarjeta.value,
                        cvv: cvvTarjeta.value
                    }
                }).then((r) => {
                    botonConfirmarPago.disabled = false;
                    const esHotel = fila.dataset.kind === 'hotel';
                    if (!r.ok) {
                        document.getElementById(esHotel ? 'mensajePagoHotel' : 'mensajePagoServicio').textContent = r.error || 'No se pudo registrar el pago.';
                        modalPagarVenta.close();
                        return;
                    }
                    modalPagarVenta.close();
                    recargarCon(`Abono de ${moneda.format(importe)} con ${metodoPagoActual} registrado` + (r.autorizacion ? ` (autorización ${r.autorizacion}).` : '.'), esHotel ? '#pagos-hotel' : '#pagos');
                });
                return;
            }

            document.getElementById('totalVentaDemostracion').textContent = moneda.format(contextoPago.total);
            modalPagarVenta.close();
            modalPagoConfirmado.showModal();
        });

        document.getElementById('btnGenerarFactura').addEventListener('click', () => {
            modalPagoConfirmado.close();
            modalDatosFactura.showModal();
        });

        document.getElementById('btnVolverPagoConfirmado').addEventListener('click', () => {
            modalDatosFactura.close();
            modalPagoConfirmado.showModal();
        });

        document.getElementById('formDatosFactura').addEventListener('submit', (evento) => {
            evento.preventDefault();
            modalDatosFactura.close();
            const mensajeFactura = document.getElementById('mensajeFactura');
            mensajeFactura.textContent = 'Datos fiscales capturados. La factura queda pendiente de emisión.';
            mensajeFactura.hidden = false;
            modalPagoConfirmado.showModal();
        });

        document.getElementById('btnNuevaVentaConfirmada').addEventListener('click', () => {
            window.location.reload();
        });

        const moneda = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
        const filasPagoServicio = [...document.querySelectorAll('#tablaPagosPendientes tr[data-id], #tablaPagosHotel tr[data-id]')];
        const filasSoloServicios = [...document.querySelectorAll('#tablaPagosPendientes tr[data-id]')];
        const filasSoloHotel = [...document.querySelectorAll('#tablaPagosHotel tr[data-id]')];

        function actualizarResumenPagos() {
            const pendientes = filasPagoServicio.filter((fila) => Number(fila.dataset.saldo) > 0);
            const conAbono = pendientes.filter((fila) => Number(fila.dataset.pagado) > 0);
            const saldoTotal = pendientes.reduce((total, fila) => total + Number(fila.dataset.saldo), 0);
            document.getElementById('pagosPendientesConteo').textContent = String(pendientes.length);
            document.getElementById('pagosPendientesTotal').textContent = moneda.format(saldoTotal);
            document.getElementById('pagosConAbonoConteo').textContent = String(conAbono.length);
        }

        filasPagoServicio.forEach((fila) => {
            const btnDev = fila.querySelector('[data-devolver]');
            if (btnDev) {
                btnDev.addEventListener('click', () => {
                    if (!confirm(`¿Devolver ${moneda.format(Number(fila.dataset.pagado))} de ${fila.cells[1].textContent.trim()}? Tarjeta: se reversa en el banco. Efectivo: sale de tu caja.`)) return;
                    btnDev.disabled = true;
                    llamarPagos({ accion: 'devolver', tipo: fila.dataset.kind, id: Number(fila.dataset.id) }).then((r) => {
                        if (!r.ok) {
                            btnDev.disabled = false;
                            document.getElementById(fila.dataset.kind === 'hotel' ? 'mensajePagoHotel' : 'mensajePagoServicio').textContent = r.error || 'No se pudo devolver.';
                            return;
                        }
                        recargarCon(`Devolución registrada: ${moneda.format(r.devuelto || 0)}.`, fila.dataset.kind === 'hotel' ? '#pagos-hotel' : '#pagos');
                    });
                });
                return;
            }
            fila.querySelector('[data-registrar-pago]').addEventListener('click', () => {
                const saldo = Number(fila.dataset.saldo);
                establecerResumenPago({ tipo: 'servicio', total: saldo, fila });
                seleccionarMetodoPago('Efectivo');
                modalPagarVenta.showModal();
            });
        });

        function filtrarPagosServicio() {
            const q = document.getElementById('buscarPagoServicio').value.trim().toLowerCase();
            const tipo = document.getElementById('filtroTipoServicio').value;
            const estado = document.getElementById('filtroEstadoPago').value;
            filasSoloServicios.forEach((fila) => {
                const coincide = (!q || fila.textContent.toLowerCase().includes(q)) &&
                    (!tipo || fila.dataset.tipo === tipo) && (!estado || fila.dataset.estado === estado);
                fila.hidden = !coincide;
            });
        }
        ['buscarPagoServicio', 'filtroTipoServicio', 'filtroEstadoPago'].forEach((id) => {
            document.getElementById(id).addEventListener('input', filtrarPagosServicio);
        });

        function filtrarPagosHotel() {
            const q = document.getElementById('buscarPagoHotel').value.trim().toLowerCase();
            const estado = document.getElementById('filtroEstadoHotel').value;
            filasSoloHotel.forEach((fila) => {
                fila.hidden = !((!q || fila.textContent.toLowerCase().includes(q)) && (!estado || fila.dataset.estado === estado));
            });
        }
        ['buscarPagoHotel', 'filtroEstadoHotel'].forEach((id) => {
            document.getElementById(id).addEventListener('input', filtrarPagosHotel);
        });

        try {
            const guardado = sessionStorage.getItem('mensajePagos');
            if (guardado) {
                sessionStorage.removeItem('mensajePagos');
                const { mensaje, hash } = JSON.parse(guardado);
                const destino = document.getElementById(hash === '#pagos-empleados' ? 'mensajePagoEmpleado' : (hash === '#pagos-hotel' ? 'mensajePagoHotel' : 'mensajePagoServicio'));
                if (destino) destino.textContent = mensaje;
            }
        } catch (e) {}

        const filasPagoEmpleado = [...document.querySelectorAll('#tablaPagosEmpleados tr[data-id]')];
        const modalConfirmarPagoEmpleado = document.getElementById('modalConfirmarPagoEmpleado');
        const modalAbonoCaja = document.getElementById('modalAbonoCaja');
        const inputAbonoCaja = document.getElementById('montoAbonoCaja');
        const monedaCaja = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
        let saldoCajaEmpleados = <?= json_encode((float)$cajaPagos['efectivo']) ?>;
        const periodoNomina = <?= json_encode($periodoNomina) ?>;
        let empleadoPagoSeleccionado = null;

        function obtenerPendienteNomina() {
            return filasPagoEmpleado
                .filter((fila) => fila.dataset.estado === 'pendiente')
                .reduce((total, fila) => total + Number(fila.dataset.pago), 0);
        }

        function actualizarResumenEmpleados() {
            const pagados = filasPagoEmpleado.filter((fila) => fila.dataset.estado === 'pagado').length;
            document.getElementById('saldoCajaEmpleados').textContent = monedaCaja.format(saldoCajaEmpleados);
            document.getElementById('nominaPendienteTotal').textContent = monedaCaja.format(obtenerPendienteNomina());
            document.getElementById('empleadosPagadosConteo').textContent = `${pagados} de ${filasPagoEmpleado.length}`;
        }

        function registrarMovimientoCaja(concepto, tipo, importe) {
            const tabla = document.getElementById('tablaMovimientosCaja');
            document.getElementById('sinMovimientosCaja')?.remove();

            const fila = document.createElement('tr');
            const fecha = document.createElement('td');
            fecha.textContent = new Intl.DateTimeFormat('es-MX', { dateStyle: 'short' }).format(new Date());
            const detalle = document.createElement('td');
            detalle.textContent = concepto;
            const movimiento = document.createElement('td');
            movimiento.textContent = tipo;
            const monto = document.createElement('td');
            monto.textContent = `${tipo === 'Salida' ? '-' : '+'}${monedaCaja.format(importe)}`;
            fila.append(fecha, detalle, movimiento, monto);
            tabla.prepend(fila);
        }

        function abrirConfirmacionPagoEmpleado(fila) {
            empleadoPagoSeleccionado = fila;
            const monto = Number(fila.dataset.pago);
            if (!(monto > 0)) {
                document.getElementById('mensajePagoEmpleado').textContent = 'Captura primero el sueldo del empleado.';
                return;
            }
            if (monto > saldoCajaEmpleados) {
                const faltante = monto - saldoCajaEmpleados;
                document.getElementById('mensajePagoEmpleado').textContent =
                    `No hay efectivo suficiente para pagar a ${fila.dataset.empleado}. Faltan ${monedaCaja.format(faltante)}; se requiere un abono a caja del administrador.`;
                inputAbonoCaja.value = faltante.toFixed(2);
                document.getElementById('notaAbonoCaja').textContent =
                    `El pago de ${fila.dataset.empleado} requiere ${monedaCaja.format(monto)}; hay ${monedaCaja.format(saldoCajaEmpleados)} en caja. Abona al menos ${monedaCaja.format(faltante)} para continuar.`;
                modalAbonoCaja.showModal();
                return;
            }

            document.getElementById('mensajePagoEmpleado').textContent = '';
            document.getElementById('resumenPagoEmpleado').textContent =
                `¿Confirmas el pago en efectivo de ${monedaCaja.format(monto)} a ${fila.dataset.empleado}?`;
            modalConfirmarPagoEmpleado.showModal();
        }

        filasPagoEmpleado.forEach((fila) => {
            fila.querySelector('[data-pagar-empleado]').addEventListener('click', () => abrirConfirmacionPagoEmpleado(fila));
        });

        document.getElementById('btnAbonarCaja')?.addEventListener('click', () => {
            empleadoPagoSeleccionado = null;
            inputAbonoCaja.value = '';
            document.getElementById('notaAbonoCaja').textContent = 'Registra un ingreso manual de efectivo a caja.';
            document.getElementById('formAbonoCaja').reset();
            modalAbonoCaja.showModal();
        });

        document.getElementById('formAbonoCaja').addEventListener('submit', (evento) => {
            evento.preventDefault();
            const importe = Number(inputAbonoCaja.value);
            if (!Number.isFinite(importe) || importe <= 0) {
                inputAbonoCaja.setCustomValidity('Ingresa un abono mayor que cero.');
                inputAbonoCaja.reportValidity();
                inputAbonoCaja.setCustomValidity('');
                return;
            }

            const concepto = document.getElementById('conceptoAbonoCaja').value.trim();
            if (!concepto) {
                document.getElementById('conceptoAbonoCaja').reportValidity();
                return;
            }

            llamarPagos({ accion: 'abonar_caja', monto: importe, concepto }).then((r) => {
                modalAbonoCaja.close();
                if (!r.ok) {
                    document.getElementById('mensajePagoEmpleado').textContent = r.error || 'No se pudo registrar el abono.';
                    return;
                }
                recargarCon(`Abono de ${monedaCaja.format(importe)} registrado en caja.`, '#pagos-empleados');
            });
        });

        document.getElementById('confirmarPagoEmpleado').addEventListener('click', () => {
            if (!empleadoPagoSeleccionado) return;
            const fila = empleadoPagoSeleccionado;
            const nombre = fila.dataset.empleado;
            const importe = Number(fila.dataset.pago);
            llamarPagos({ accion: 'pagar_empleado', id_empleado: Number(fila.dataset.id), periodo: periodoNomina }).then((r) => {
                modalConfirmarPagoEmpleado.close();
                if (!r.ok) {
                    document.getElementById('mensajePagoEmpleado').textContent = r.error || 'No se pudo registrar el pago.';
                    return;
                }
                recargarCon(`Pago en efectivo de ${monedaCaja.format(importe)} a ${nombre} registrado en caja.`, '#pagos-empleados');
            });
        });

        document.querySelectorAll('[data-sueldo]').forEach((campo) => {
            campo.addEventListener('change', () => {
                const fila = campo.closest('tr');
                const valor = Number(campo.value);
                if (!Number.isFinite(valor) || valor < 0) {
                    campo.setCustomValidity('Sueldo no válido.');
                    campo.reportValidity();
                    campo.setCustomValidity('');
                    return;
                }
                llamarPagos({ accion: 'guardar_sueldo', id_empleado: Number(fila.dataset.id), sueldo: valor }).then((r) => {
                    if (!r.ok) {
                        document.getElementById('mensajePagoEmpleado').textContent = r.error || 'No se pudo guardar el sueldo.';
                        return;
                    }
                    fila.dataset.pago = valor.toFixed(2);
                    actualizarResumenEmpleados();
                    document.getElementById('mensajePagoEmpleado').textContent = 'Sueldo guardado.';
                });
            });
        });

        document.getElementById('cerrarConfirmarPagoEmpleado').addEventListener('click', () => modalConfirmarPagoEmpleado.close());
        document.getElementById('cancelarPagoEmpleado').addEventListener('click', () => modalConfirmarPagoEmpleado.close());
        document.getElementById('cerrarAbonoCaja').addEventListener('click', () => modalAbonoCaja.close());
        document.getElementById('cancelarAbonoCaja').addEventListener('click', () => modalAbonoCaja.close());
    </script>
    <script src="js/ventas.js"></script>
</body>
</html>
