<?php
session_start();
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
require_once __DIR__ . '/../../models/CitasCliente.php';
$paginaActiva = 'citas';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: agendar_cita.php');
    exit;
}
$idServicio = (int)($_POST['servicio'] ?? 0);
$idMascota = (int)($_POST['mascota'] ?? 0);
$fecha = (string)($_POST['fecha'] ?? '');
$hora = (string)($_POST['hora'] ?? '');
$res = CitasCliente::agendar((int)$_SESSION['id_cliente'], $idMascota, $idServicio, $fecha, $hora, (string)($_POST['motivo'] ?? ''));
if (!$res['ok']) {
    $_SESSION['error_cita'] = $res['error'];
    header('Location: agendar_cita.php?' . http_build_query(['servicio' => $idServicio, 'mascota' => $idMascota, 'fecha' => $fecha]));
    exit;
}
$cita = CitasCliente::obtener($res['id'], (int)$_SESSION['id_cliente']);
$total = (float)$cita['precio'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita agendada</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>


    <main class="contenedor contenedor--medio">
        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">¡Cita agendada!</h1>
            <p class="tarjeta__subtitulo">Te esperamos. Estos son los datos de tu cita.</p>

            <div class="tarjeta resumen-cita">
                <dl class="resumen-cita__lista">
                    <dt class="resumen-cita__dato">Servicio</dt>
                    <dd class="resumen-cita__valor"><?= htmlspecialchars($cita['servicio']) ?></dd>

                    <dt class="resumen-cita__dato">Mascota</dt>
                    <dd class="resumen-cita__valor"><?= htmlspecialchars($cita['mascota']) ?></dd>

                    <dt class="resumen-cita__dato">Fecha y hora</dt>
                    <dd class="resumen-cita__valor"><?= htmlspecialchars(date('d/m/Y · H:i', strtotime($cita['fecha']))) ?></dd>

                    <dt class="resumen-cita__dato">Estado</dt>
                    <dd class="resumen-cita__valor">Pendiente de anticipo</dd>
                </dl>
                <p class="resumen-cita__total"><span>Total</span> <span>$<?= number_format($total, 2) ?></span></p>
                <p class="tarjeta__subtitulo">Anticipo: $<?= number_format(CitasCliente::anticipo($total), 2) ?></p>
            </div>

            <div class="grupo-botones">
                <a class="boton" href="pagar_anticipo.php?id_cita=<?= (int)$cita['id_cita'] ?>">Pagar anticipo</a>
                <a class="boton boton--contorno" href="mis_citas.php">Ver mis citas</a>
                <a class="boton boton--contorno" href="inicio.php">Volver al inicio</a>
            </div>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>