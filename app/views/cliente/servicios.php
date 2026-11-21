<?php
session_start();
$paginaActiva = 'servicios';

require_once __DIR__ . '/../../models/Servicios.php';
$todos = Servicios::obtenerTodos();

$servicios = [];
foreach ($todos as $s) {
    if ((int) $s['estado'] === 1) {
        $servicios[] = $s;
    }
}
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
            <p class="subtitulo-pagina">Elige el que necesita tu mascota y agenda tu cita.</p>
        </div>

        <section>

            <?php if (empty($servicios)): ?>
                <p class="subtitulo-pagina">Por ahora no hay servicios para mostrar.</p>
            <?php endif; ?>

            <?php foreach ($servicios as $servicio): ?>
                <article class="tarjeta-servicio">
                    <div class="foto-marcador tarjeta-servicio__foto"><?= htmlspecialchars($servicio['nombre']) ?></div>

                    <div class="tarjeta-servicio__descripcion">
                        <h2 class="tarjeta-servicio__titulo"><?= htmlspecialchars($servicio['nombre']) ?></h2>
                        <p class="tarjeta-servicio__texto"><?= htmlspecialchars($servicio['descripcion'] ?? '') ?></p>
                        <div class="grupo-etiquetas">
                            <span class="etiqueta"><?= (int) $servicio['duracion'] ?> min</span>
                            <span class="etiqueta"><?= htmlspecialchars($servicio['categoria']) ?></span>
                        </div>
                    </div>

                    <div class="tarjeta-servicio__precio">
                        <p class="tarjeta-servicio__monto">$<?= number_format((float) $servicio['precio'], 2) ?></p>
                        <a class="boton" href="agendar_cita.php?id_servicio=<?= (int) $servicio['id_servicio'] ?>">Agendar</a>
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
                        <li>La reservación la confirma nuestro equipo.</li>
                    </ul>
                    <div class="grupo-etiquetas">
                        <span class="etiqueta etiqueta--sobre-oscuro">Precio por noche (por definir)</span>
                    </div>
                </div>

                <div class="tarjeta-servicio__precio">
                    <a class="boton boton--secundario" href="solicitar_reservacion.php">Solicitar reservación</a>
                </div>
            </article>

        </section>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
