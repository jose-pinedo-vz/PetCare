<?php
session_start();
require_once __DIR__ . '/../../models/Publico.php';
$paginaActiva = 'equipo';
$tipo = $_GET['tipo'] ?? '';
if (!in_array($tipo, ['veterinarios', 'estilistas', 'hotel'], true)) {
    $tipo = '';
}
$equipo = Publico::equipo($tipo);
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
            <a class="pestanas__opcion<?= $tipo === '' ? ' es-activo' : '' ?>" href="nuestro_equipo.php">Todos</a>
            <a class="pestanas__opcion<?= $tipo === 'veterinarios' ? ' es-activo' : '' ?>" href="nuestro_equipo.php?tipo=veterinarios">Veterinarios</a>
            <a class="pestanas__opcion<?= $tipo === 'estilistas' ? ' es-activo' : '' ?>" href="nuestro_equipo.php?tipo=estilistas">Estilistas</a>
            <a class="pestanas__opcion<?= $tipo === 'hotel' ? ' es-activo' : '' ?>" href="nuestro_equipo.php?tipo=hotel">Hotel</a>
        </nav>

        <section class="rejilla-tarjetas">
            <?php foreach ($equipo as $em): ?>
            <article class="tarjeta tarjeta-empleado">
                <div class="foto-marcador foto-marcador--avatar"><?= htmlspecialchars(mb_substr($em['nombre'], 0, 1)) ?></div>
                <h2 class="tarjeta-empleado__nombre"><?= htmlspecialchars($em['nombre'] . ' ' . $em['apellido']) ?></h2>
                <span class="tarjeta-empleado__puesto"><?= htmlspecialchars($em['puesto']) ?></span>
                <?php if (!empty($em['especialidad'])): ?>
                    <p class="tarjeta-empleado__detalle">Especialidad: <?= htmlspecialchars($em['especialidad']) ?></p>
                <?php endif; ?>
                <?php if (!empty($em['num_cedula_profesional'])): ?>
                    <p class="tarjeta-empleado__detalle">Cédula profesional: <?= (int)$em['num_cedula_profesional'] ?></p>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        </section>
        <?php if (empty($equipo)): ?>
            <p class="aviso">Por ahora no hay personal para mostrar.</p>
        <?php endif; ?>

    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
