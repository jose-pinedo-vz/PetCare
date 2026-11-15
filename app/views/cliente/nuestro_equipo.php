<?php
session_start();
$paginaActiva = 'equipo';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestro equipo</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor">
        <div>
            <h1 class="titulo-pagina">Nuestro equipo</h1>
            <p class="subtitulo-pagina">Las personas que cuidan a tu mascota en PetCare.</p>
        </div>

        <nav class="pestanas">
            <a class="pestanas__opcion es-activo" href="nuestro_equipo.php">Todos</a>
            <a class="pestanas__opcion" href="nuestro_equipo.php?tipo=veterinarios">Veterinarios</a>
            <a class="pestanas__opcion" href="nuestro_equipo.php?tipo=estilistas">Estilistas</a>
            <a class="pestanas__opcion" href="nuestro_equipo.php?tipo=hotel">Hotel</a>
        </nav>

        <section class="rejilla-tarjetas">

            <article class="tarjeta tarjeta-empleado">
                <div class="foto-marcador foto-marcador--avatar">Foto</div>
                <h2 class="tarjeta-empleado__nombre">Dra. Pérez</h2>
                <span class="tarjeta-empleado__puesto">Veterinaria</span>
                <p class="tarjeta-empleado__detalle">[Especialidad]</p>
                <p class="tarjeta-empleado__detalle">[Certificaciones]</p>
            </article>

            <article class="tarjeta tarjeta-empleado">
                <div class="foto-marcador foto-marcador--avatar">Foto</div>
                <h2 class="tarjeta-empleado__nombre">Dr. Ramírez</h2>
                <span class="tarjeta-empleado__puesto">Veterinario</span>
                <p class="tarjeta-empleado__detalle">[Especialidad]</p>
                <p class="tarjeta-empleado__detalle">[Certificaciones]</p>
            </article>

            <article class="tarjeta tarjeta-empleado">
                <div class="foto-marcador foto-marcador--avatar">Foto</div>
                <h2 class="tarjeta-empleado__nombre">Ana Torres</h2>
                <span class="tarjeta-empleado__puesto">Estilista</span>
                <p class="tarjeta-empleado__detalle">[Años de experiencia]</p>
                <p class="tarjeta-empleado__detalle">[Certificaciones]</p>
            </article>

            <article class="tarjeta tarjeta-empleado">
                <div class="foto-marcador foto-marcador--avatar">Foto</div>
                <h2 class="tarjeta-empleado__nombre">Luis Medina</h2>
                <span class="tarjeta-empleado__puesto">Estilista</span>
                <p class="tarjeta-empleado__detalle">[Años de experiencia]</p>
                <p class="tarjeta-empleado__detalle">[Certificaciones]</p>
            </article>

            <article class="tarjeta tarjeta-empleado">
                <div class="foto-marcador foto-marcador--avatar">Foto</div>
                <h2 class="tarjeta-empleado__nombre">[Nombre]</h2>
                <span class="tarjeta-empleado__puesto">Encargado del hotel</span>
                <p class="tarjeta-empleado__detalle">[Descripción breve]</p>
            </article>

        </section>

    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
