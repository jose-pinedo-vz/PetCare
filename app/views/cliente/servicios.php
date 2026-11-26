<?php
session_start();
require_once __DIR__ . '/../../models/Publico.php';
$paginaActiva = 'servicios';
$servicios = Publico::servicios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestros servicios</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor">
        <div>
            <h1 class="titulo-pagina">Nuestros servicios</h1>
            <p class="subtitulo-pagina">Elige el que necesita tu mascota y agenda tu cita. </p>
        </div>

        <section>

            <?php if (empty($servicios)): ?>
                <p class="aviso">Por ahora no hay servicios para mostrar.</p>
            <?php endif; ?>
            <?php foreach ($servicios as $srv): ?>
            <article class="tarjeta-servicio">
                <div class="foto-marcador tarjeta-servicio__foto"><?= htmlspecialchars($srv['nombre']) ?></div>

                <div class="tarjeta-servicio__descripcion">
                    <h2 class="tarjeta-servicio__titulo"><?= htmlspecialchars($srv['nombre']) ?></h2>
                    <p class="tarjeta-servicio__texto"><?= htmlspecialchars((string)($srv['descripcion'] ?? '')) ?></p>
                    <div class="grupo-etiquetas">
                        <span class="etiqueta"><?= (int)$srv['duracion'] ?> min</span>
                        <span class="etiqueta"><?= htmlspecialchars($srv['categoria']) ?></span>
                    </div>
                </div>

                <div class="tarjeta-servicio__precio">
                    <p class="tarjeta-servicio__monto">$<?= number_format((float)$srv['precio'], 2) ?></p>
                    <a class="boton" href="agendar_cita.php?servicio=<?= (int)$srv['id_servicio'] ?>">Agendar</a>
                </div>
            </article>
            <?php endforeach; ?>

            <article class="tarjeta-servicio tarjeta-servicio--destacada">
                <div class="foto-marcador foto-marcador--oscura tarjeta-servicio__foto">Foto: hotel</div>

                <div class="tarjeta-servicio__descripcion">
                    <h2 class="tarjeta-servicio__titulo">Hotel de mascotas</h2>
                    <p class="tarjeta-servicio__texto">Hospedaje cómodo y supervisado para perros, gatos y otros mamíferos pequeños.</p>
                    <ul class="tarjeta-servicio__requisitos">
                        <li>Esquema de vacunación completo (se revisa al llegar).</li>
                        <li>Baño y cortes se cobran aparte.</li>
                        <li>La reservación se hace en sucursal y la registra nuestro equipo.</li>
                    </ul>
                    <div class="grupo-etiquetas">
                        <span class="etiqueta etiqueta--sobre-oscuro">Desde $150 por noche (según tamaño)</span>
                    </div>
                </div>

                <div class="tarjeta-servicio__precio">
                    <!-- Las reservaciones de hotel se hacen en sucursal; un empleado las registra en su panel -->
                    <p class="tarjeta-servicio__texto">Reserva en sucursal: visítanos con tu mascota.</p>
                </div>
            </article>

        </section>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
