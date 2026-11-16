<?php
session_start();
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['exito']);

$paginaActiva = 'reservaciones';

require_once __DIR__ . '/../../models/hotel.php';
$idCliente = (int) $_SESSION['id_cliente'];
// Primero recalcula Vencida / Abandonada y luego lista
Hotel::actualizarVencidas($idCliente);
$reservaciones = Hotel::reservacionesDelCliente($idCliente);

$mesesCortos = ['', 'ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
// Estado -> clase de color que ya existe en el CSS
$clasesEstado = [
    'Pendiente de pago' => 'pendiente',
    'Pendiente cartilla' => 'pendiente',
    'Confirmada' => 'confirmada',
    'Vencida' => 'cancelada',
    'Abandonada' => 'cancelada',
    'Cancelada' => 'cancelada',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis reservaciones</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>


    <main class="contenedor">
        <div class="encabezado-pagina">
            <div>
                <h1 class="titulo-pagina">Mis reservaciones</h1>
                <p class="subtitulo-pagina">Estancias de tus mascotas en el hotel.</p>
            </div>
            <a class="boton" href="solicitar_reservacion.php">+ Solicitar reservación</a>
        </div>

        <?php if ($exito !== ''): ?>
            <div class="aviso aviso--exito">
                <p><?= htmlspecialchars($exito) ?></p>
            </div>
        <?php endif; ?>

        <section>
            <?php if (!$reservaciones): ?>
                <p class="subtitulo-pagina">Aún no tienes reservaciones de hotel.</p>
            <?php endif; ?>

            <?php foreach ($reservaciones as $r): ?>
                <?php
                $entrada = new DateTime($r['fecha_entrada']);
                $salida = new DateTime($r['fecha_salida']);
                $mesEntrada = $mesesCortos[(int) $entrada->format('n')];
                $mesSalida = $mesesCortos[(int) $salida->format('n')];
                $noches = (int) $r['num_noches'];
                $claseEstado = $clasesEstado[$r['estado']] ?? 'pendiente';
                $esPasada = $salida < new DateTime() || in_array($r['estado'], ['Vencida', 'Abandonada', 'Cancelada'], true);
                ?>
                <article class="tarjeta-cita<?= $esPasada ? ' tarjeta-cita--pasada' : '' ?>">
                    <div class="tarjeta-cita__fecha">
                        <span class="tarjeta-cita__dia"><?= $entrada->format('d') ?></span>
                        <span class="tarjeta-cita__mes"><?= $mesEntrada ?></span>
                    </div>

                    <div class="tarjeta-cita__informacion">
                        <h2 class="tarjeta-cita__titulo">Hotel · <?= htmlspecialchars($r['mascota']) ?></h2>
                        <p class="tarjeta-cita__detalle"><?= $entrada->format('j') . ' ' . strtolower($mesEntrada) ?> a <?= $salida->format('j') . ' ' . strtolower($mesSalida) ?> · <?= $noches ?> <?= $noches === 1 ? 'noche' : 'noches' ?></p>
                        <p class="tarjeta-cita__detalle">Total: $<?= number_format((float) $r['anticipo'], 2) ?></p>
                    </div>

                    <span class="estado-cita estado-cita--<?= $claseEstado ?>"><?= htmlspecialchars($r['estado']) ?></span>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>