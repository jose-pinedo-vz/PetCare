<?php
session_start();
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
require_once __DIR__ . '/../../models/CitasCliente.php';
$paginaActiva = 'citas';
$idCliente = (int)$_SESSION['id_cliente'];
$idCita = (int)($_POST['id_cita'] ?? $_GET['id_cita'] ?? 0);
$cita = CitasCliente::obtener($idCita, $idCliente);
if (!$cita || !CitasCliente::modificable($cita)) {
    $_SESSION['exito_cita'] = 'Esa cita ya no se puede reprogramar.';
    header('Location: mis_citas.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = CitasCliente::reprogramar($idCita, $idCliente, (string)($_POST['fecha'] ?? ''), (string)($_POST['hora'] ?? ''));
    if ($error === '') {
        $_SESSION['exito_cita'] = 'Tu cita fue reprogramada.';
        header('Location: mis_citas.php');
        exit;
    }
}
$fecha = (string)($_GET['fecha'] ?? ($_POST['fecha'] ?? ''));
$ocupadas = $fecha !== '' ? CitasCliente::horasOcupadas((int)$cita['id_servicio'], $fecha, $idCita) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reprogramar cita</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>


    <main class="contenedor contenedor--medio">
        <p class="migas-de-pan"><a href="mis_citas.php">Mis citas</a> › Reprogramar cita</p>

        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Reprogramar cita</h1>
            <p class="tarjeta__subtitulo"><?= htmlspecialchars($cita['servicio'] . ' · ' . $cita['mascota']) ?>. Elige una nueva fecha y hora.</p>
            <?php if ($error !== ''): ?><div class="aviso aviso--error"><p><?= htmlspecialchars($error) ?></p></div><?php endif; ?> 

            <form id="formRe" action="reprogramar_cita.php" method="post">
                <input type="hidden" name="id_cita" value="<?= (int)$cita['id_cita'] ?>"> 

                <div class="formulario-rejilla">
                    <div class="campo">
                        <label class="campo__etiqueta" for="fecha">Nueva fecha <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="date" id="fecha" name="fecha" required min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($fecha) ?>" onchange="location.href='reprogramar_cita.php?id_cita=<?= (int)$cita['id_cita'] ?>&fecha='+this.value">
                    </div>
                </div>

                <p class="selector-horario__titulo">Nueva hora <span class="campo__obligatorio">*</span></p>
                <div class="selector-horario">
                    <?php foreach (CitasCliente::HORAS as $h):
                        $oc = in_array($h, $ocupadas, true) || ($fecha === date('Y-m-d') && $h <= date('H:i'));
                    ?>
                        <button class="selector-horario__hora<?= $oc ? ' es-ocupado' : '' ?>" type="button" data-hora="<?= $h ?>" <?= $oc ? 'disabled' : '' ?>><?= $h ?></button>
                    <?php endforeach; ?>
                </div>

                <input type="hidden" name="hora" id="hora" value="">

                <div class="grupo-botones">
                    <button class="boton" type="submit">Guardar cambios</button>
                    <a class="boton boton--contorno" href="mis_citas.php">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
    <script>
    document.querySelectorAll('.selector-horario__hora:not([disabled])').forEach(function (b) {
        b.addEventListener('click', function () {
            document.querySelectorAll('.selector-horario__hora').forEach(function (x) { x.classList.remove('es-seleccionado'); });
            b.classList.add('es-seleccionado');
            document.getElementById('hora').value = b.dataset.hora;
        });
    });
    document.getElementById('formRe').addEventListener('submit', function (e) {
        if (!document.getElementById('hora').value) { e.preventDefault(); alert('Elige una hora.'); }
    });
    </script>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>