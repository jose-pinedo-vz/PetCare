<?php
session_start();
require_once __DIR__ . '/../../models/Publico.php';
$serviciosInicio = array_slice(Publico::servicios(), 0, 4);
$equipoInicio = array_slice(Publico::equipo(), 0, 3);
$paginaActiva = 'inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetCare | Inicio</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor contenedor--sin-relleno-vertical">

        <section class="portada">
            <div class="portada__texto">
                <span class="etiqueta etiqueta--destacada">Veterinaria · Estética · Hotel de mascotas</span>
                <h1 class="portada__titulo">Cuidamos a tu mascota como parte de la familia</h1>
                <p class="portada__descripcion">Agenda su cita en línea, consulta nuestros servicios y conoce al equipo que la va a atender.</p>
                <div class="portada__botones">
                    <a class="boton" href="agendar_cita.php">Agendar una cita</a>
                    <a class="boton boton--contorno" href="servicios.php">Ver servicios</a>
                </div>
            </div>

            <div class="foto-marcador foto-marcador--hero">
                <p>Foto del negocio o de una mascota</p>
            </div>
        </section>

        <section class="seccion-inicio">
            <h2 class="seccion-inicio__titulo">¿Qué necesitas hoy?</h2>
            <p class="seccion-inicio__subtitulo">Entra a tu cuenta para ver tus mascotas y tus citas.</p>

            <div class="rejilla-tarjetas">
                <a class="tarjeta tarjeta-acceso" href="agendar_cita.php">
                    <span class="tarjeta-acceso__icono" aria-hidden="true"></span>
                    <h3 class="tarjeta-acceso__titulo">Agendar una cita</h3>
                    <p class="tarjeta-acceso__texto">Elige el servicio, el día y el horario.</p>
                </a>

                <a class="tarjeta tarjeta-acceso" href="registrar_mascota.php">
                    <span class="tarjeta-acceso__icono" aria-hidden="true"></span>
                    <h3 class="tarjeta-acceso__titulo">Registrar mi mascota</h3>
                    <p class="tarjeta-acceso__texto">Guarda sus datos básicos para agilizar tu visita.</p>
                </a>

                <a class="tarjeta tarjeta-acceso" href="servicios.php">
                    <span class="tarjeta-acceso__icono" aria-hidden="true"></span>
                    <h3 class="tarjeta-acceso__titulo">Ver servicios</h3>
                    <p class="tarjeta-acceso__texto">Conoce duración y precio de cada uno.</p>
                </a>

                <a class="tarjeta tarjeta-acceso" href="#equipo">
                    <span class="tarjeta-acceso__icono" aria-hidden="true"></span>
                    <h3 class="tarjeta-acceso__titulo">Conocer al equipo</h3>
                    <p class="tarjeta-acceso__texto">Veterinarios y estilistas que cuidan a tu mascota.</p>
                </a>
            </div>
        </section>

        <section class="seccion-inicio" id="servicios">
            <h2 class="seccion-inicio__titulo">Nuestros servicios</h2>
            <p class="seccion-inicio__subtitulo">Precios y duración actualizados.</p>

            <div class="rejilla-tarjetas">
                <?php foreach ($serviciosInicio as $srv): ?>
                <article class="tarjeta tarjeta-servicio-mini">
                    <div class="foto-marcador foto-marcador--servicio"><?= htmlspecialchars($srv['nombre']) ?></div>
                    <h3 class="tarjeta-servicio-mini__titulo"><?= htmlspecialchars($srv['nombre']) ?></h3>
                    <p class="tarjeta-servicio-mini__texto"><?= htmlspecialchars((string)($srv['descripcion'] ?? '')) ?></p>
                    <p class="tarjeta-servicio-mini__precio"><?= (int)$srv['duracion'] ?> min · $<?= number_format((float)$srv['precio'], 2) ?></p>
                    <a class="boton boton--chico" href="agendar_cita.php?servicio=<?= (int)$srv['id_servicio'] ?>">Agendar</a>
                </article>
                <?php endforeach; ?>

                <article class="tarjeta tarjeta--oscura tarjeta-servicio-mini">
                    <h3 class="tarjeta-servicio-mini__titulo">Hotel de mascotas</h3>
                    <p class="tarjeta-servicio-mini__texto">Hospedaje para perros, gatos y otros mamíferos pequeños. Pedimos el esquema de vacunación completo. El baño y los cortes se cobran aparte.</p>
                    <p class="tarjeta-servicio-mini__precio">Consulta disponibilidad con nosotros</p>
                    <a class="boton boton--secundario boton--chico" href="solicitar_reservacion.php">Reservar hotel</a>
                </article>
            </div>
        </section>

        <!-- Equipo -->
        <section class="seccion-inicio" id="equipo">
            <h2 class="seccion-inicio__titulo">Nuestro equipo</h2>
            <p class="seccion-inicio__subtitulo">Profesionales que se encargan de tu mascota.</p>

            <div class="rejilla-tarjetas">
                <?php foreach ($equipoInicio as $em): ?>
                <article class="tarjeta tarjeta-persona-mini">
                    <div class="foto-marcador foto-marcador--avatar"><?= htmlspecialchars(mb_substr($em['nombre'], 0, 1)) ?></div>
                    <h3><?= htmlspecialchars($em['nombre'] . ' ' . $em['apellido']) ?></h3>
                    <p><?= htmlspecialchars($em['puesto']) ?><?= !empty($em['especialidad']) ? ' · ' . htmlspecialchars($em['especialidad']) : '' ?></p>
                </article>
                <?php endforeach; ?>
            </div>

            <div class="grupo-botones">
                <a class="boton boton--contorno" href="nuestro_equipo.php">Ver a todo el equipo</a>
            </div>
        </section>

        <section class="seccion-inicio" id="conocenos">
            <h2 class="seccion-inicio__titulo">Conócenos</h2>

            <div class="bloque-informacion">
                <article class="tarjeta">
                    <h3 class="bloque-informacion__titulo">Quiénes somos</h3>

                    <h4 class="bloque-informacion__dato">Misión</h4>
                    <p class="bloque-informacion__valor">[Texto de la misión del negocio]</p>

                    <h4 class="bloque-informacion__dato">Visión</h4>
                    <p class="bloque-informacion__valor">[Texto de la visión del negocio]</p>
                </article>

                <article class="tarjeta">
                    <h3 class="bloque-informacion__titulo">Contacto</h3>

                    <h4 class="bloque-informacion__dato">Teléfono</h4>
                    <p class="bloque-informacion__valor">[Teléfono]</p>

                    <h4 class="bloque-informacion__dato">WhatsApp</h4>
                    <p class="bloque-informacion__valor">[WhatsApp]</p>

                    <h4 class="bloque-informacion__dato">Dirección</h4>
                    <p class="bloque-informacion__valor">[Dirección]</p>

                    <h4 class="bloque-informacion__dato">Horario</h4>
                    <p class="bloque-informacion__valor">9:00 a 19:00</p>
                </article>
            </div>
        </section>

    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
