<?php
session_start();
$paginaActiva = 'servicios';
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
            <p class="subtitulo-pagina">Elige el que necesita tu mascota y agenda tu cita. Los precios son de ejemplo.</p>
        </div>

        <section>

            <article class="tarjeta-servicio">
                <div class="foto-marcador tarjeta-servicio__foto">Foto: baño</div>

                <div class="tarjeta-servicio__descripcion">
                    <h2 class="tarjeta-servicio__titulo">Baño</h2>
                    <p class="tarjeta-servicio__texto">Baño y secado para tu mascota. Puede incluir tratamientos especiales si lo necesita.</p>
                    <div class="grupo-etiquetas">
                        <span class="etiqueta">60 min</span>
                        <span class="etiqueta">Estética</span>
                    </div>
                </div>

                <div class="tarjeta-servicio__precio">
                    <p class="tarjeta-servicio__monto">$150</p>
                    <a class="boton" href="agendarCita.php?servicio=bano">Agendar</a>
                </div>
            </article>

            <article class="tarjeta-servicio">
                <div class="foto-marcador tarjeta-servicio__foto">Foto: corte de pelo</div>

                <div class="tarjeta-servicio__descripcion">
                    <h2 class="tarjeta-servicio__titulo">Corte de pelo</h2>
                    <p class="tarjeta-servicio__texto">Corte según el tipo de pelaje y el estilo que prefieras.</p>
                    <div class="grupo-etiquetas">
                        <span class="etiqueta">90 min</span>
                        <span class="etiqueta">Estética</span>
                    </div>
                </div>

                <div class="tarjeta-servicio__precio">
                    <p class="tarjeta-servicio__monto">$250</p>
                    <a class="boton" href="agendarCita.php?servicio=corte_pelo">Agendar</a>
                </div>
            </article>

            <article class="tarjeta-servicio">
                <div class="foto-marcador tarjeta-servicio__foto">Foto: corte de uñas</div>

                <div class="tarjeta-servicio__descripcion">
                    <h2 class="tarjeta-servicio__titulo">Corte de uñas</h2>
                    <p class="tarjeta-servicio__texto">Corte y limado seguro de uñas.</p>
                    <div class="grupo-etiquetas">
                        <span class="etiqueta">20 min</span>
                        <span class="etiqueta">Estética</span>
                    </div>
                </div>

                <div class="tarjeta-servicio__precio">
                    <p class="tarjeta-servicio__monto">$60</p>
                    <a class="boton" href="agendarCita.php?servicio=corte_unas">Agendar</a>
                </div>
            </article>

            <article class="tarjeta-servicio">
                <div class="foto-marcador tarjeta-servicio__foto">Foto: desparasitación</div>

                <div class="tarjeta-servicio__descripcion">
                    <h2 class="tarjeta-servicio__titulo">Desparasitación</h2>
                    <p class="tarjeta-servicio__texto">Aplicación del desparasitante adecuado a su peso y especie, a cargo de nuestro veterinario.</p>
                    <div class="grupo-etiquetas">
                        <span class="etiqueta">30 min</span>
                        <span class="etiqueta">Veterinaria</span>
                    </div>
                </div>

                <div class="tarjeta-servicio__precio">
                    <p class="tarjeta-servicio__monto">$120</p>
                    <a class="boton" href="agendarCita.php?servicio=desparasitacion">Agendar</a>
                </div>
            </article>

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
