<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    header("Location: ../views/login.php");
    exit();
}
$rolSesion = strtolower(trim((string)($_SESSION['rol'] ?? '')));
if (!in_array($rolSesion, ['admin', 'gerente', 'hotelero', 'recepcionista'], true)) {
    header("Location: inicio.php");
    exit();
}

require_once __DIR__ . '/../models/Hotel.php';

if (empty($_SESSION['csrf_hotel'])) {
    $_SESSION['csrf_hotel'] = bin2hex(random_bytes(32));
}
$jaulas = Hotel::obtenerJaulasParaListado();
$reservaciones = Hotel::obtenerReservacionesParaListado();
$errorJaulas = is_array($jaulas) && isset($jaulas['error']);
$errorReservaciones = is_array($reservaciones) && isset($reservaciones['error']);

if ($errorJaulas || !is_array($jaulas)) {
    $mensajeErrorJaulas = is_array($jaulas) && isset($jaulas['error']) ? $jaulas['error'] : 'No fue posible cargar la disponibilidad de jaulas.';
    $jaulas = [];
}
if ($errorReservaciones || !is_array($reservaciones)) {
    $mensajeErrorReservaciones = is_array($reservaciones) && isset($reservaciones['error']) ? $reservaciones['error'] : 'No fue posible cargar las reservaciones.';
    $reservaciones = [];
}

$escapar = static fn($valor) => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
$fechaCorta = static function ($fecha) use ($escapar) {
    $timestamp = $fecha ? strtotime((string) $fecha) : false;
    return $timestamp === false ? '—' : $escapar(date('Y-m-d', $timestamp));
};

$conteoJaulas = [
    'disponible' => 0,
    'ocupado' => 0,
    'mantenimiento' => 0,
];
foreach ($jaulas as &$jaula) {
    $estadoOriginal = strtolower(trim((string) $jaula['estado_actual']));
    if ($estadoOriginal === 'mantenimiento') {
        $jaula['estado_actual'] = 'mantenimiento';
    } elseif ($estadoOriginal === 'ocupado' || !empty($jaula['mascota_actual'])) {
        $jaula['estado_actual'] = 'ocupado';
    } elseif ($estadoOriginal === 'disponible') {
        $jaula['estado_actual'] = 'disponible';
    }

    if (isset($conteoJaulas[$jaula['estado_actual']])) {
        $conteoJaulas[$jaula['estado_actual']]++;
    }
}
unset($jaula);

$estadosReservacion = [
    'por pagar' => 'Por pagar',
    'pagado' => 'Pagado',
    'cancelado' => 'Cancelado',
    'no asistio' => 'No asistio',
    'finalizado' => 'Finalizado',
    //'cancelada' => 'Cancelada',
    //'pendiente cartilla' => 'Pendiente cartilla',
    //'vencida' => 'Vencida',
    //'abandonada' => 'Abandonada',
    //'activo' => 'Activo',
    //'inactivo' => 'Inactivo',
    //'pendiente de pago' => 'Pendiente de pago',
    //'confirmada' => 'Confirmada',
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Hotel de mascotas</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
    <style>
        .hotel-tabla-envoltura {
            border: 1px solid var(--border-light);
            border-radius: 10px;
            overflow-x: auto;
            overflow-y: hidden;
        }
    </style>
</head>

<body>
    <div class="layout">
        <?php $paginaActiva = 'hotel'; include __DIR__ . '/layout/sidebar.php'; ?>

        <main>
            <div class="migaja">Gestión de hotel /</div>
            <div class="encabezado-pagina">
                <h1>Hotel de mascotas</h1>
            </div>
            <div class="tarjeta">
                <div class="barra-acciones-busqueda">
                    <h2>Disponibilidad de jaulas</h2>
                    <div class="barra-acciones">
                        <a class="btn" href="reservacion_hotel.php">+ Nueva reservación</a>
                    </div>
                </div>
                <p id="mensaje-acciones-hotel" role="alert" hidden></p>

                <div class="inventario-resumen">
                    <div class="inventario-tarjeta">
                        <h3>Disponibles</h3>
                        <p><?= $conteoJaulas['disponible'] ?></p>
                    </div>
                    <div class="inventario-tarjeta">
                        <h3>Ocupadas</h3>
                        <p><?= $conteoJaulas['ocupado'] ?></p>
                    </div>
                    <div class="inventario-tarjeta">
                        <h3>En mantenimiento</h3>
                        <p><?= $conteoJaulas['mantenimiento'] ?></p>
                    </div>
                </div>

                <div class="inventario-tarjeta">
                    <h3>Total de jaulas</h3>
                    <p><?= count($jaulas) ?></p>
                </div>
                <br>
                <div class="barra-acciones">
                        <button class="btn" type="button" id="btn-abrir-modal-jaula">+ Agregar jaula</button>
                    </div>
                <?php if ($errorJaulas): ?>
                    <p class="msg-error" role="alert"><?= $escapar($mensajeErrorJaulas) ?></p>
                <?php elseif (!$jaulas): ?>
                    <p class="vacio">No hay jaulas registradas.</p>
                <?php else: ?>
                    <div class="hotel-tabla-envoltura">
                        <table>
                            <thead>
                                <tr>
                                    <th>Jaula</th>
                                    <th>Tamaño</th>
                                    <th>Estado</th>
                                    <th>Mascota actual</th>
                                    <th>Libre a partir de</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jaulas as $jaula):
                                    $estado = $jaula['estado_actual'];
                                    $textoEstado = [
                                        'disponible' => 'Disponible',
                                        'ocupado' => 'Reservada',
                                        'mantenimiento' => 'Mantenimiento',
                                    ][$estado] ?? ucfirst($estado);
                                    $claseEstado = $estado === 'disponible'
                                        ? 'pagada'
                                        : ($estado === 'mantenimiento' ? 'cancelada' : 'pendiente');
                                    $libreDesde = $estado === 'disponible'
                                        ? 'Ahora'
                                        : ($estado === 'ocupado'
                                            ? (!empty($jaula['libre_a_partir']) ? $fechaCorta($jaula['libre_a_partir']) : 'Sin fecha')
                                            : 'Sin fecha');
                                    if ($estado === 'ocupado' && !empty($jaula['libre_a_partir'])) {
                                        $fechaLibre = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string) $jaula['libre_a_partir'], 0, 10));
                                        if ($fechaLibre !== false) {
                                            $hoy = new DateTimeImmutable('today');
                                            $diasRestantes = (int) $hoy->diff($fechaLibre)->format('%r%a');
                                            if ($diasRestantes >= 0) {
                                                $libreDesde .= ' (' . $diasRestantes . ' ' . ($diasRestantes === 1 ? 'día' : 'días') . ')';
                                            }
                                        }
                                    }
                                ?>
                                    <tr>
                                        <td><?= $escapar('J-' . $jaula['id']) ?></td>
                                        <td><?= $escapar(ucfirst((string) $jaula['tamano'])) ?></td>
                                        <td><span class="historial-estado <?= $claseEstado ?>"><?= $escapar($textoEstado) ?></span></td>
                                        <td><?= $escapar($jaula['mascota_actual'] ?: '—') ?></td>
                                        <td><?= $libreDesde ?></td>
                                        <td class="acciones">
                                            <?php if ($estado === 'disponible'): ?>
                                                <button type="button" class="btn btn-chico" data-hotel-accion="jaula_mantenimiento" data-id="<?= (int) $jaula['id'] ?>">Enviar a mantenimiento</button>
                                                <button type="button" class="btn btn-eliminar btn-chico" data-hotel-accion="jaula_eliminar" data-id="<?= (int) $jaula['id'] ?>">Eliminar</button>
                                            <?php elseif ($estado === 'mantenimiento'): ?>
                                                <button type="button" class="btn btn-chico" data-hotel-accion="jaula_disponible" data-id="<?= (int) $jaula['id'] ?>">Marcar disponible</button>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <section style="margin-top: 24px;" aria-labelledby="titulo-reservaciones">
                    <h2 id="titulo-reservaciones">Reservaciones</h2>

                    <div class="inventario-pedidos-toolbar">
                        <label class="visualmente-oculto" for="buscar-reservacion">Buscar por cliente o mascota</label>
                        <input
                            id="buscar-reservacion"
                            class="campo buscador-inteligente"
                            type="search"
                            placeholder="Buscar por cliente o mascota..."
                        >
                        <label class="visualmente-oculto" for="filtro-fecha-reservacion">Filtrar por fecha de entrada</label>
                        <input
                            id="filtro-fecha-reservacion"
                            class="campo pedidos-filtro-estado"
                            type="date"
                            aria-label="Fecha de entrada"
                        >
                        <label class="visualmente-oculto" for="filtro-estado-reservacion">Filtrar por estado</label>
                        <select id="filtro-estado-reservacion" class="campo pedidos-filtro-estado">
                            <option value="">Todos los estados</option>
                            <?php foreach ($estadosReservacion as $valor => $etiqueta): ?>
                                <option value="<?= $escapar($valor) ?>"><?= $escapar($etiqueta) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($errorReservaciones): ?>
                        <p class="msg-error" role="alert"><?= $escapar($mensajeErrorReservaciones) ?></p>
                    <?php else: ?>
                        <div class="tabla-envoltura reservaciones-tabla">
                            <table id="tabla-reservaciones">
                                <thead>
                                    <tr>
                                        <th>Folio</th>
                                        <th>Cliente</th>
                                        <th>Mascota</th>
                                        <th>Jaula</th>
                                        <th>Entrada</th>
                                        <th>Salida</th>
                                        <th>Noches</th>
                                        <th>Saldo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservaciones as $reservacion):
                                        $estado = strtolower(trim((string) $reservacion['estado']));
                                        $textoEstado = $estadosReservacion[$estado] ?? ucfirst($estado);
                                        $claseEstado = $estado === 'pagado' || $estado === 'finalizado'
                                            ? 'pagada'
                                            : (in_array($estado, ['cancelado', 'cancelada', 'no asistio', 'inactivo', 'vencida', 'abandonada'], true)
                                                ? 'cancelada'
                                                : 'pendiente');
                                        $estadosAbiertos = ['por pagar', 'pagado', 'activo', 'pendiente de pago', 'pendiente cartilla', 'confirmada'];
                                        $fechaSalidaISO = substr((string) $reservacion['fecha_salida'], 0, 10);
                                        $puedeFinalizar = $estado === 'pagado'
                                            && $fechaSalidaISO !== ''
                                            && $fechaSalidaISO <= date('Y-m-d');
                                    ?>
                                        <tr
                                            data-reservacion
                                            data-estado="<?= $escapar($estado) ?>"
                                            data-fecha-entrada="<?= $fechaCorta($reservacion['fecha_entrada']) ?>"
                                        >
                                            <td><?= $escapar('HT-' . $reservacion['id_reservacion']) ?></td>
                                            <td><?= $escapar($reservacion['cliente']) ?></td>
                                            <td><?= $escapar($reservacion['mascota']) ?></td>
                                            <td>
                                                <?= $escapar('J-' . $reservacion['id_jaula']) ?>
                                                <?php if (!empty($reservacion['tamano_jaula'])): ?>
                                                    · <?= $escapar(ucfirst((string) $reservacion['tamano_jaula'])) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $fechaCorta($reservacion['fecha_entrada']) ?></td>
                                            <td><?= $fechaCorta($reservacion['fecha_salida']) ?></td>
                                            <td><?= $escapar($reservacion['num_noches'] ?? '—') ?></td>
                                            <td>$<?= number_format((float) $reservacion['saldo'], 2) ?></td>
                                            <td><span class="historial-estado <?= $claseEstado ?>"><?= $escapar($textoEstado) ?></span></td>
                                            <td class="acciones">
                                                <?php if (in_array($estado, $estadosAbiertos, true)): ?>
                                                    <button type="button" class="btn btn-secundario btn-chico" data-hotel-accion="reservacion_cancelar" data-id="<?= (int) $reservacion['id_reservacion'] ?>">Cancelar</button>
                                                    <button type="button" class="btn btn-secundario btn-chico" data-hotel-accion="reservacion_no_asistio" data-id="<?= (int) $reservacion['id_reservacion'] ?>">No asistió</button>
                                                    
                                                    <?php if ($puedeFinalizar): ?>
                                                        <button type="button" class="btn btn-chico" data-hotel-accion="reservacion_finalizar" data-id="<?= (int) $reservacion['id_reservacion'] ?>">Finalizar</button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    —
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <p id="sin-reservaciones" class="vacio" <?= $reservaciones ? 'hidden' : '' ?>>
                            No hay reservaciones que coincidan con los filtros.
                        </p>
                    <?php endif; ?>
                </section>
            </div>
        </main>
    </div>

    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>

    <?php include __DIR__ . '/modal_jaula.php'; ?>
    <script src="js/buscador_inteligente.js"></script>
    <script src="js/hotel_filtros.js"></script>
    <script src="js/hotel_acciones.js"></script>
</body>

</html>
