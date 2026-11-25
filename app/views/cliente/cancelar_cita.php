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
    $_SESSION['exito_cita'] = 'Esa cita ya no se puede cancelar.';
    header('Location: mis_citas.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CitasCliente::cancelar($idCita, $idCliente);
    $_SESSION['exito_cita'] = 'Tu cita fue cancelada.';
    try {
        $dev = PagosAdmin::devolver(ConexionDB::obtenerConexion(), 'cita', $idCita, null);
        if (!empty($dev['ok']) && $dev['devuelto'] > 0) {
            $_SESSION['exito_cita'] .= ' Te devolvimos $' . number_format($dev['devuelto'], 2) . ' a tu tarjeta.';
        }
        if (!empty($dev['ok']) && $dev['pendiente'] > 0) {
            $_SESSION['exito_cita'] .= ' Pasa a sucursal por la devolución de $' . number_format($dev['pendiente'], 2) . '.';
        }
        if (empty($dev['ok'])) {
            $_SESSION['exito_cita'] .= ' La devolución quedó pendiente; el personal la procesará.';
        }
    } catch (Throwable $e) {
        error_log('Devolucion al cancelar: ' . $e->getMessage());
    }
    header('Location: mis_citas.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelar cita</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>


    <main class="contenedor contenedor--angosto">
        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Cancelar cita</h1>
            <p class="tarjeta__subtitulo">¿Seguro que quieres cancelar esta cita?</p>

            <div class="tarjeta resumen-cita">
                <dl class="resumen-cita__lista">
                    <dt class="resumen-cita__dato">Servicio</dt>
                    <dd class="resumen-cita__valor"><?= htmlspecialchars($cita['servicio'] . ' · ' . $cita['mascota']) ?></dd> 

                    <dt class="resumen-cita__dato">Fecha y hora</dt>
                    <dd class="resumen-cita__valor"><?= htmlspecialchars(date('d/m/Y · H:i', strtotime($cita['fecha']))) ?></dd> 
                </dl>
            </div>

            <form action="cancelar_cita.php" method="post">
                <input type="hidden" name="id_cita" value="<?= (int)$cita['id_cita'] ?>"> 
                <div class="grupo-botones">
                    <button class="boton" type="submit">Sí, cancelar cita</button>
                    <a class="boton boton--contorno" href="mis_citas.php">No, volver</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>