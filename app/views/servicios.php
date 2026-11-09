<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Servicios.php';
$servicios = Servicios::obtenerTodos();
// Lee y limpia el error de sesion
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
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
        <div class="servicio-acciones">
            <a class="btn btn-chico" href="editar_servicio.php?id_servicio=<?= (int)$s['id_servicio'] ?>">Editar</a>
            <form action="../controllers/guardar_servicio.php" method="POST">
                <input type="hidden" name="id_servicio" value="<?= (int)$s['id_servicio'] ?>">
                <input type="hidden" name="accion" value="<?= $activo ? 'desactivar' : 'activar' ?>">
                <button type="submit" class="btn btn-chico <?= $activo ? 'btn-peligro' : 'btn-activar' ?>"><?= $activo ? 'Desactivar' : 'Activar' ?></button>
            </form>
        </div>
    </div>
<?php endforeach; ?>
 
                    <div class="inventario-tarjeta servicio-nuevo">
                        <h3>Nuevo servicio</h3>
                        <p>Agrega otro servicio al catálogo</p>
                        <a class="btn inventario-btn-contactar btn-chico" href="agregar_servicio.php">+ Agregar servicio</a>
                    </div>
                </div>
 
                <p class="servicio-nota">Los servicios no se borran; se desactivan para conservar el historial. Solo el administrador puede editarlos.</p>
 
                <div style="margin-top: 24px;">
                    <h2>Historial de servicios</h2>
 
                    <form class="inventario-pedidos-toolbar" action="servicios.php" method="GET">
                        <input type="text" class="campo" name="buscar" placeholder="Buscar por cliente o mascota" aria-label="Buscar por cliente o mascota">
                        <input type="date" class="campo pedidos-filtro-estado" name="fecha" aria-label="Fecha">
                        <select class="campo pedidos-filtro-estado" name="estado" aria-label="Estado">
                            <option value="">Todos los estados</option>
                            <option value="Programada">Programada</option>
                            <option value="Confirmada">Confirmada</option>
                            <option value="Completada">Completada</option>
                            <option value="Cancelada">Cancelada</option>
                            <option value="No asistió">No asistió</option>
                        </select>
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
                                <tr>
                                    <td>1</td>
                                    <td>2026-10-27</td>
                                    <td>10:00</td>
                                    <td>María López</td>
                                    <td>Luna</td>
                                    <td>Baño</td>
                                    <td>Ana Torres</td>
                                    <td>60 min</td>
                                    <td><span class="historial-estado pendiente">Programada</span></td>
                                    <td>Piel sensible</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>2026-10-27</td>
                                    <td>12:30</td>
                                    <td>Carlos Ramírez</td>
                                    <td>Michi</td>
                                    <td>Corte de uñas</td>
                                    <td>Ana Torres</td>
                                    <td>20 min</td>
                                    <td><span class="historial-estado pendiente">Confirmada</span></td>
                                    <td>—</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>2026-10-24</td>
                                    <td>09:00</td>
                                    <td>Sofía Herrera</td>
                                    <td>Rocky</td>
                                    <td>Corte de pelo</td>
                                    <td>Luis Medina</td>
                                    <td>90 min</td>
                                    <td><span class="historial-estado pagada">Completada</span></td>
                                    <td>Corte tipo cachorro</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>2026-10-23</td>
                                    <td>16:00</td>
                                    <td>Jorge Díaz</td>
                                    <td>Thor</td>
                                    <td>Desparasitación</td>
                                    <td>Dra. Pérez</td>
                                    <td>30 min</td>
                                    <td><span class="historial-estado cancelada">Cancelada</span></td>
                                    <td>Canceló el cliente</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>2026-10-22</td>
                                    <td>11:00</td>
                                    <td>Elena Ruiz</td>
                                    <td>Nala</td>
                                    <td>Baño</td>
                                    <td>Luis Medina</td>
                                    <td>60 min</td>
                                    <td><span class="historial-estado cancelada">No asistió</span></td>
                                    <td>—</td>
                                </tr>
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