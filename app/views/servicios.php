<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once __DIR__ . '/../models/Servicios.php';
$servicios = Servicios::obtenerTodos();
$historial = Servicios::obtenerHistorial();
// Filtros del historial (buscar, fecha y estado)
$fBuscar = trim((string)($_GET['buscar'] ?? ''));
$fFecha = (string)($_GET['fecha'] ?? '');
$fEstado = strtolower(trim((string)($_GET['estado'] ?? '')));
$historial = array_values(array_filter($historial, function ($h) use ($fBuscar, $fFecha, $fEstado) {
    if ($fBuscar !== '' && stripos($h['cliente'] . ' ' . $h['mascota'] . ' ' . $h['servicio'], $fBuscar) === false) {
        return false;
    }
    if ($fFecha !== '' && $h['fecha'] !== $fFecha) {
        return false;
    }
    if ($fEstado !== '' && strtolower((string)$h['estado']) !== $fEstado) {
        return false;
    }
    return true;
}));
// Lee y limpia el error de sesion
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
$rolSesion = strtolower(trim((string)($_SESSION['rol'] ?? '')));
$esAdmin = in_array($rolSesion, ['admin', 'gerente'], true);
?>
<!DOCTYPE html>
<html lang="es">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Servicios</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
 
<body>
    <div class="layout">
        <?php $paginaActiva = 'servicios';
        include __DIR__ . '/layout/sidebar.php'; ?>
 
        <main>
            <div class="migaja">Gestión de servicios /</div>
            <div class="encabezado-pagina">
                <h1>Servicios</h1>
            </div>
 
            <div class="tarjeta">
                 <?php if ($error !== ''): ?>
                    <div class="msg-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <h2>Servicios disponibles</h2>
                <div class="inventario-resumen">
 
                   <?php foreach ($servicios as $s): ?>
    <?php $activo = ((int)$s['estado'] === 1); ?>
    <div class="inventario-tarjeta<?= $activo ? '' : ' servicio-inactivo' ?>">
        <span class="servicio-estado <?= $activo ? 'activo' : 'inactivo' ?>"><?= $activo ? 'Activo' : 'Inactivo' ?></span>
        <h3>
            <?php if ($activo): ?>
                <a class="servicio-link" href="agendarCita.php?id_servicio=<?= (int)$s['id_servicio'] ?>"><?= htmlspecialchars($s['nombre']) ?></a>
            <?php else: ?>
                <?= htmlspecialchars($s['nombre']) ?>
            <?php endif; ?>
        </h3>
        <p><?= htmlspecialchars($s['categoria']) ?> · <?= (int)$s['duracion'] ?> min</p>
        <p class="servicio-precio">$<?= number_format((float)$s['precio'], 2) ?></p>
        <?php if ($esAdmin): ?>
        <div class="servicio-acciones">
            <a class="btn btn-chico" href="editar_servicio.php?id_servicio=<?= (int)$s['id_servicio'] ?>">Editar</a>
            <form action="../controllers/guardar_servicio.php" method="POST">
                <input type="hidden" name="id_servicio" value="<?= (int)$s['id_servicio'] ?>">
                <input type="hidden" name="accion" value="<?= $activo ? 'desactivar' : 'activar' ?>">
                <button type="submit" class="btn btn-chico <?= $activo ? 'btn-peligro' : 'btn-activar' ?>"><?= $activo ? 'Desactivar' : 'Activar' ?></button>
            </form>
        </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
 
                    <?php if ($esAdmin): ?>
                    <div class="inventario-tarjeta servicio-nuevo">
                        <h3>Nuevo servicio</h3>
                        <p>Agrega otro servicio al catálogo</p>
                        <a class="btn inventario-btn-contactar btn-chico" href="agregar_servicio.php">+ Agregar servicio</a>
                    </div>
                    <?php endif; ?>
                </div>
 
                <p class="servicio-nota">Los servicios no se borran; se desactivan para conservar el historial. Solo el administrador puede editarlos.</p>
 
                <div style="margin-top: 24px;">
                    <h2>Historial de servicios</h2>
 
                    <form class="inventario-pedidos-toolbar" action="servicios.php" method="GET">
                        <input type="text" class="campo" name="buscar" value="<?= htmlspecialchars($fBuscar) ?>" placeholder="Buscar por cliente, mascota o servicio" aria-label="Buscar por cliente, mascota o servicio">
                        <input type="date" class="campo pedidos-filtro-estado" name="fecha" value="<?= htmlspecialchars($fFecha) ?>" aria-label="Fecha" onchange="this.form.submit()">
                        <select class="campo pedidos-filtro-estado" name="estado" aria-label="Estado" onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            <?php foreach (['pendiente' => 'Pendiente', 'atendida' => 'Atendida', 'cancelada' => 'Cancelada'] as $v => $t): ?>
                                <option value="<?= $v ?>" <?= $fEstado === $v ? 'selected' : '' ?>><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-chico" type="submit">Filtrar</button>
                        <?php if ($fBuscar !== '' || $fFecha !== '' || $fEstado !== ''): ?>
                            <a class="btn btn-secundario btn-chico" href="servicios.php">Limpiar</a>
                        <?php endif; ?>
                    </form>
 
                    <div class="tabla-envoltura">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Cliente</th>
                                    <th>Mascota</th>
                                    <th>Servicio</th>
                                    <th>Estilista</th>
                                    <th>Duración</th>
                                    <th>Estado</th>
                                    <th>Observación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($historial)): ?>
                                    <tr><td colspan="10"><?= ($fBuscar !== '' || $fFecha !== '' || $fEstado !== '') ? 'Ninguna cita coincide con el filtro.' : 'Sin citas registradas.' ?></td></tr>
                                <?php endif; ?>
                                <?php foreach ($historial as $h):
                                    $est = strtolower((string)$h['estado']);
                                    $clase = (strpos($est, 'cancel') !== false || strpos($est, 'no asist') !== false) ? 'cancelada'
                                        : ((strpos($est, 'sin atender') === false && (strpos($est, 'atendida') !== false || strpos($est, 'complet') !== false)) ? 'pagada' : 'pendiente');
                                ?>
                                <tr>
                                    <td><?php echo (int)$h['id_cita']; ?></td>
                                    <td><?php echo htmlspecialchars($h['fecha']); ?></td>
                                    <td><?php echo htmlspecialchars(substr((string)$h['hora'], 0, 5)); ?></td>
                                    <td><?php echo htmlspecialchars($h['cliente']); ?></td>
                                    <td><?php echo htmlspecialchars($h['mascota']); ?></td>
                                    <td><?php echo htmlspecialchars($h['servicio']); ?></td>
                                    <td><?php echo htmlspecialchars($h['empleado']); ?></td>
                                    <td><?php echo (int)$h['duracion_estimada']; ?> min</td>
                                    <td><span class="historial-estado <?php echo $clase; ?>"><?php echo htmlspecialchars(ucfirst((string)$h['estado'])); ?></span></td>
                                    <td><?php echo htmlspecialchars(($h['observacion'] ?? '') !== '' ? $h['observacion'] : '—'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
 
            </div>
        </main>
    </div>
 
    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
 
</html>