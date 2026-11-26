<?php
session_start();
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
require_once __DIR__ . '/../../models/CitasCliente.php';
require_once __DIR__ . '/../../models/Publico.php';
require_once __DIR__ . '/../../models/Mascota.php';
$idCliente = (int)$_SESSION['id_cliente'];
$paginaActiva = 'citas';
$servicios = Publico::servicios();
$mascotas = obtenerMascotasPorCliente($idCliente);
$idServicio = (int)($_GET['servicio'] ?? 0);
$idMascota = (int)($_GET['mascota'] ?? $_GET['id_mascota'] ?? 0);
$fecha = (string)($_GET['fecha'] ?? '');
$hora = (string)($_GET['hora'] ?? '');
$error = $_SESSION['error_cita'] ?? '';
$old = $_SESSION['old'] ?? [];
unset($_SESSION['error_cita'], $_SESSION['old']);

// Valor anterior del formulario, ya escapado
$v = function (string $clave) use ($old): string {
    return htmlspecialchars(is_string($old[$clave] ?? null) ? $old[$clave] : '');
};
$srv = $idServicio ? CitasCliente::servicio($idServicio) : null;
$ocupadas = ($srv && $fecha !== '') ? CitasCliente::horasOcupadas($idServicio, $fecha) : [];
$minFecha = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar cita</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>


    <main class="contenedor">
        <p class="migas-de-pan"><a href="mis_citas.php">Mis citas</a> › Agendar cita</p>

        <div class="columnas">
            <div class="columnas__principal tarjeta tarjeta--formulario">
                <h1 class="tarjeta__titulo">Agendar cita</h1>
                <p class="tarjeta__subtitulo">Los campos con <span class="campo__obligatorio">*</span> son obligatorios.</p>
                <?php if ($error !== ''): ?>
                    <div class="aviso aviso--error"><p><?= htmlspecialchars($error) ?></p></div>
                <?php endif; ?>
                <?php if (empty($mascotas)): ?>
                    <div class="aviso"><p>Primero registra una mascota en <a href="registrar_mascota.php">Mis mascotas</a>.</p></div>
                <?php endif; ?>

                <form id="formCita" action="guardar_cita.php" method="post">
                    <div class="formulario-rejilla">
                        <div class="campo">
                            <label class="campo__etiqueta" for="id_servicio">Servicio <span class="campo__obligatorio">*</span></label>
                            <select class="campo__entrada" id="id_servicio" name="servicio" required onchange="recargar()">
                                <option value="">Elige un servicio</option>
                                <?php foreach ($servicios as $sv): ?>
                                    <option value="<?= (int)$sv['id_servicio'] ?>" <?= $idServicio === (int)$sv['id_servicio'] ? 'selected' : '' ?>><?= htmlspecialchars($sv['nombre']) ?> ($<?= number_format((float)$sv['precio'], 2) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="id_mascota">Mascota <span class="campo__obligatorio">*</span></label>
                            <select class="campo__entrada" id="id_mascota" name="mascota" required onchange="recargar()">
                                <option value="">Elige una mascota</option>
                                <?php foreach ($mascotas as $ma): ?>
                                    <option value="<?= (int)$ma['id_mascota'] ?>" <?= $idMascota === (int)$ma['id_mascota'] ? 'selected' : '' ?>><?= htmlspecialchars($ma['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="campo campo--ancho-completo">
                            <label class="campo__etiqueta" for="fecha">Fecha <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="date" id="fecha" name="fecha" required min="<?= $minFecha ?>" max="<?= date('Y-m-d', strtotime('+6 months')) ?>" value="<?= htmlspecialchars($fecha) ?>" onchange="recargar()">
                        </div>
                    </div>

                    <p class="selector-horario__titulo">Hora <span class="campo__obligatorio">*</span></p>
                    <?php if (!$srv || $fecha === ''): ?>
                        <p class="aviso">Elige servicio y fecha para ver los horarios.</p>
                    <?php else: ?>
                    <div class="selector-horario">
                        <?php foreach (CitasCliente::HORAS as $h):
                            $oc = in_array($h, $ocupadas, true) || ($fecha === $minFecha && $h <= date('H:i'));
                        ?>
                            <button class="selector-horario__hora<?= $oc ? ' es-ocupado' : '' ?><?= $hora === $h ? ' es-seleccionado' : '' ?>" type="button" data-hora="<?= $h ?>" <?= $oc ? 'disabled' : '' ?>><?= $h ?></button>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <input type="hidden" name="hora" id="hora" value="<?= htmlspecialchars($hora) ?>">

                    <div class="formulario-rejilla">
                        <div class="campo campo--ancho-completo">
                            <label class="campo__etiqueta" for="motivo">Motivo o comentarios</label>
                            <textarea class="campo__entrada" id="motivo" name="motivo" rows="3" maxlength="250"><?= $v('motivo') ?></textarea>
                        </div>
                    </div>

                    <div class="grupo-botones">
                        <button class="boton" type="submit">Confirmar cita</button>
                        <a class="boton boton--contorno" href="mis_citas.php">Cancelar</a>
                    </div>
                </form>
            </div>

            <aside class="columnas__lateral tarjeta resumen-cita">
                <h2 class="resumen-cita__titulo">Resumen</h2>
                <dl class="resumen-cita__lista">
                    <dt class="resumen-cita__dato">Servicio</dt>
                    <dd class="resumen-cita__valor"><?= $srv ? htmlspecialchars($srv['nombre']) : '—' ?></dd>
                    <dt class="resumen-cita__dato">Fecha y hora</dt>
                    <dd class="resumen-cita__valor"><?= $fecha !== '' ? htmlspecialchars($fecha . ($hora !== '' ? ' · ' . $hora : '')) : '—' ?></dd>
                    <dt class="resumen-cita__dato">Duración</dt>
                    <dd class="resumen-cita__valor"><?= $srv ? (int)$srv['duracion'] . ' min' : '—' ?></dd>
                </dl>
                <p class="resumen-cita__total"><span>Total</span> <span>$<?= $srv ? number_format((float)$srv['precio'], 2) : '0.00' ?></span></p>
                <?php if ($srv): ?>
                    <p class="tarjeta__subtitulo">Anticipo: $<?= number_format(CitasCliente::anticipo((float)$srv['precio']), 2) ?></p>
                <?php endif; ?>
            </aside>
        </div>
    </main>
    <script>
    // Al cambiar servicio, mascota o fecha la pagina se recarga para mostrar los horarios;
    // el motivo se guarda un momento en el navegador para no perderlo en la recarga.
    var motivo = document.getElementById('motivo');
    try {
        var guardado = sessionStorage.getItem('cita_motivo');
        if (guardado !== null && motivo.value === '') motivo.value = guardado;
        sessionStorage.removeItem('cita_motivo');
    } catch (e) {}

    function recargar() {
        var f = document.getElementById('formCita');
        try { sessionStorage.setItem('cita_motivo', motivo.value); } catch (e) {}
        var q = new URLSearchParams({
            servicio: f.servicio.value, mascota: f.mascota.value, fecha: f.fecha.value, hora: ''
        });
        location.href = 'agendar_cita.php?' + q.toString();
    }
    document.querySelectorAll('.selector-horario__hora:not([disabled])').forEach(function (b) {
        b.addEventListener('click', function () {
            document.querySelectorAll('.selector-horario__hora').forEach(function (x) { x.classList.remove('es-seleccionado'); });
            b.classList.add('es-seleccionado');
            document.getElementById('hora').value = b.dataset.hora;
        });
    });
    document.getElementById('formCita').addEventListener('submit', function (e) {
        if (!document.getElementById('hora').value) { e.preventDefault(); alert('Elige una hora.'); }
    });
    </script>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>