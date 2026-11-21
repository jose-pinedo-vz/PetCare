<?php
session_start();
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
require_once __DIR__ . '/../../models/CitasCliente.php';
require_once __DIR__ . '/../../models/PagosAdmin.php';
$paginaActiva = 'citas';
$idCliente = (int)$_SESSION['id_cliente'];
$idCita = (int)($_POST['id_cita'] ?? $_GET['id_cita'] ?? 0);
$cita = CitasCliente::obtener($idCita, $idCliente);
if (!$cita || !CitasCliente::modificable($cita)) {
    $_SESSION['exito_cita'] = 'Esa cita ya no admite pago de anticipo.';
    header('Location: mis_citas.php');
    exit;
}
$db = ConexionDB::obtenerConexion();
$total = (float)$cita['precio'];
$anticipo = CitasCliente::anticipo($total);
$pagado = PagosAdmin::pagadoCita($db, $idCita);
if ($pagado >= $anticipo) {
    $_SESSION['exito_cita'] = 'El anticipo de esa cita ya está pagado.';
    header('Location: mis_citas.php');
    exit;
}
$aPagar = round($anticipo - $pagado, 2);
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metodo = (string)($_POST['metodo_pago'] ?? '');
    if ($metodo === 'Efectivo') {
        $_SESSION['exito_cita'] = 'Paga tu anticipo de $' . number_format($aPagar, 2) . ' en sucursal; el personal lo registrará.';
        header('Location: mis_citas.php');
        exit;
    }
    $titular = trim((string)($_POST['titular'] ?? ''));
    $venc = (string)($_POST['vencimiento'] ?? '');
    $cvv = (string)($_POST['cvv'] ?? '');
    $num = preg_replace('/\D/', '', (string)($_POST['numero'] ?? ''));
    if (!preg_match('/^\p{L}[\p{L} .\'\-]{2,59}$/u', $titular)) {
        $error = 'Escribe el nombre del titular como aparece en la tarjeta.';
    } elseif (strlen($num) < 13 || strlen($num) > 19) {
        $error = 'El número de tarjeta debe tener entre 13 y 19 dígitos.';
    } elseif (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $venc) || $venc < date('Y-m')) {
        $error = 'La tarjeta está vencida o la fecha no es válida.';
    } elseif (!preg_match('/^\d{3,4}$/', $cvv)) {
        $error = 'El CVV debe tener 3 o 4 dígitos.';
    } else {
        $r = PagosAdmin::pagarAnticipoCliente($db, $idCita, $aPagar, ['numero' => $num, 'vencimiento' => $venc, 'cvv' => $cvv]);
        if ($r['ok']) {
            $_SESSION['exito_cita'] = 'Anticipo pagado. Autorización del banco: ' . $r['folio'] . '.';
            header('Location: mis_citas.php');
            exit;
        }
        $error = $r['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagar anticipo</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>


    <main class="contenedor contenedor--medio">
        <p class="migas-de-pan"><a href="mis_citas.php">Mis citas</a> › Pagar anticipo</p>

        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Pagar anticipo</h1>
            <p class="tarjeta__subtitulo"><?= htmlspecialchars($cita['servicio'] . ' · ' . $cita['mascota'] . ' · ' . date('d/m/Y H:i', strtotime($cita['fecha']))) ?></p>
            <?php if ($error !== ''): ?>
                <div class="aviso aviso--error"><p><?= htmlspecialchars($error) ?></p></div>
            <?php endif; ?>

            <div class="tarjeta resumen-cita">
                <dl class="resumen-cita__lista">
                    <dt class="resumen-cita__dato">Total del servicio</dt>
                    <dd class="resumen-cita__valor">$<?= number_format($total, 2) ?></dd>
                </dl>
                <p class="resumen-cita__total"><span>Anticipo a pagar</span> <span>$<?= number_format($aPagar, 2) ?></span></p>
            </div>

            <form action="pagar_anticipo.php" method="post" id="formAnticipo">
                <input type="hidden" name="id_cita" value="<?= (int)$cita['id_cita'] ?>">
                <div class="formulario-rejilla">
                    <div class="campo campo--ancho-completo">
                        <label class="campo__etiqueta" for="metodo_pago">Método de pago <span class="campo__obligatorio">*</span></label>
                        <select class="campo__entrada" id="metodo_pago" name="metodo_pago" required>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Efectivo">Efectivo en sucursal</option>
                        </select>
                    </div>
                    <div class="campo campo--ancho-completo datos-tarjeta">
                        <label class="campo__etiqueta" for="titular">Titular</label>
                        <input class="campo__entrada" type="text" id="titular" name="titular" maxlength="60" autocomplete="cc-name" value="<?= htmlspecialchars((string)($_POST['titular'] ?? '')) ?>">
                    </div>
                    <div class="campo campo--ancho-completo datos-tarjeta">
                        <label class="campo__etiqueta" for="numero">Número de tarjeta</label>
                        <input class="campo__entrada" type="text" id="numero" name="numero" inputmode="numeric" maxlength="23" autocomplete="cc-number">
                    </div>
                    <div class="campo datos-tarjeta">
                        <label class="campo__etiqueta" for="vencimiento">Vencimiento</label>
                        <input class="campo__entrada" type="month" id="vencimiento" name="vencimiento" min="<?= date('Y-m') ?>" max="<?= (date('Y') + 15) ?>-12">
                    </div>
                    <div class="campo datos-tarjeta">
                        <label class="campo__etiqueta" for="cvv">CVV</label>
                        <input class="campo__entrada" type="password" id="cvv" name="cvv" inputmode="numeric" maxlength="4" autocomplete="cc-csc">
                    </div>
                </div>
                <div class="grupo-botones">
                    <button class="boton" type="submit">Pagar anticipo</button>
                    <a class="boton boton--contorno" href="mis_citas.php">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
    <script>
    (function () {
        var m = document.getElementById('metodo_pago');
        function ver() {
            var t = m.value === 'Tarjeta';
            document.querySelectorAll('.datos-tarjeta').forEach(function (e) { e.hidden = !t; });
            ['titular', 'numero', 'vencimiento', 'cvv'].forEach(function (id) { document.getElementById(id).required = t; });
        }
        m.addEventListener('change', ver);
        ['numero', 'cvv'].forEach(function (id) {
            document.getElementById(id).addEventListener('input', function (e) { e.target.value = e.target.value.replace(/[^\d ]/g, ''); });
        });
        ver();
    })();
    </script>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>