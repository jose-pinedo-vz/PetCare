<?php
session_start();
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
require_once __DIR__ . '/../../models/CitasCliente.php';
require_once __DIR__ . '/../../models/PagosAdmin.php';
$dbCitas = ConexionDB::obtenerConexion();
$paginaActiva = 'citas';
$ver = ($_GET['ver'] ?? 'proximas') === 'anteriores' ? 'anteriores' : 'proximas';
$citas = CitasCliente::listar((int)$_SESSION['id_cliente'], $ver);
$msg = $_SESSION['exito_cita'] ?? '';
unset($_SESSION['exito_cita']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis citas</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor">
        <div class="encabezado-pagina">
            <div>
                <h1 class="titulo-pagina">Mis citas</h1>
                <p class="subtitulo-pagina">Consulta o cancela tus citas.</p>
            </div>
            <a class="boton" href="agendar_cita.php">+ Agendar cita</a>
        </div>

        <nav class="pestanas">
            <a class="pestanas__opcion<?= $ver === 'proximas' ? ' es-activo' : '' ?>" href="mis_citas.php?ver=proximas">Próximas</a>
            <a class="pestanas__opcion<?= $ver === 'anteriores' ? ' es-activo' : '' ?>" href="mis_citas.php?ver=anteriores">Anteriores</a>
        </nav>

        <?php if ($msg !== ''): ?>
            <div class="aviso aviso--exito"><p><?= htmlspecialchars($msg) ?></p></div>
        <?php endif; ?>
        <section>
            <?php if (empty($citas)): ?>
                <p class="aviso">No tienes citas en esta sección.</p>
            <?php endif; ?>
            <?php foreach ($citas as $ci):
                $ts = strtotime($ci['fecha']);
                $est = strtolower((string)$ci['estado_cita']);
                $mod = CitasCliente::modificable($ci);
                $meses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];
                $clase = $est === 'cancelada' ? 'cancelada' : ($est === 'atendida' ? 'atendida' : 'pendiente');
                $texto = $est === 'cancelada' ? 'Cancelada' : ($est === 'atendida' ? 'Atendida' : 'Pendiente');
                $total = (float)$ci['precio'];
                $pagadoCita = PagosAdmin::pagadoCita($dbCitas, (int)$ci['id_cita']);
                $anticipoOk = $pagadoCita >= CitasCliente::anticipo($total);
                if ($clase === 'pendiente' && $anticipoOk) { $clase = 'confirmada'; $texto = 'Confirmada'; }
                elseif ($clase === 'pendiente') { $texto = 'Falta anticipo'; }
            ?>
            <article class="tarjeta-cita<?= $ver === 'anteriores' ? ' tarjeta-cita--pasada' : '' ?>">
                <div class="tarjeta-cita__fecha">
                    <span class="tarjeta-cita__dia"><?= date('d', $ts) ?></span>
                    <span class="tarjeta-cita__mes"><?= $meses[(int)date('n', $ts) - 1] ?></span>
                </div>
                <div class="tarjeta-cita__informacion">
                    <h2 class="tarjeta-cita__titulo"><?= htmlspecialchars($ci['servicio'] . ' · ' . $ci['mascota']) ?></h2>
                    <p class="tarjeta-cita__detalle"><?= date('H:i', $ts) ?> · <?= (int)$ci['duracion_estimada'] ?> min · Atiende: <?= htmlspecialchars((string)$ci['empleado']) ?></p>
                    <p class="tarjeta-cita__detalle">Total: $<?= number_format($total, 2) ?> · Anticipo: $<?= number_format(CitasCliente::anticipo($total), 2) ?><?= $anticipoOk ? ' (pagado)' : '' ?></p>
                </div>
                <span class="estado-cita estado-cita--<?= $clase ?>"><?= $texto ?></span>
                <?php if ($mod): ?>
                <div class="tarjeta-cita__acciones">
                    <?php if (!$anticipoOk): ?><a class="boton boton--chico" href="pagar_anticipo.php?id_cita=<?= (int)$ci['id_cita'] ?>">Pagar anticipo</a><?php endif; ?>
                    <a class="boton boton--contorno boton--chico" href="reprogramar_cita.php?id_cita=<?= (int)$ci['id_cita'] ?>">Reprogramar</a>
                    <a class="boton boton--contorno boton--chico" href="cancelar_cita.php?id_cita=<?= (int)$ci['id_cita'] ?>">Cancelar</a>
                </div>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        </section>

    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
