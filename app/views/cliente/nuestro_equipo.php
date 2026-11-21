<?php
session_start();
$paginaActiva = 'equipo';

require_once __DIR__ . '/../../models/Empleados.php';
$todos = Empleados::obtenerTodos();

$tipo = $_GET['tipo'] ?? 'todos';


$filtros = [
    'veterinarios' => 'veterinari',
    'estilistas'   => 'estilista',
    'hotel'        => 'hotel',
];

if (!isset($filtros[$tipo])) {
    $tipo = 'todos';
}

$empleados = [];
foreach ($todos as $e) {
    if ($tipo === 'todos' || stripos($e['puesto'], $filtros[$tipo]) !== false) {
        $empleados[] = $e;
    }
}
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
            <a class="pestanas__opcion<?= $tipo === 'todos' ? ' es-activo' : '' ?>" href="nuestro_equipo.php">Todos</a>
            <a class="pestanas__opcion<?= $tipo === 'veterinarios' ? ' es-activo' : '' ?>" href="nuestro_equipo.php?tipo=veterinarios">Veterinarios</a>
            <a class="pestanas__opcion<?= $tipo === 'estilistas' ? ' es-activo' : '' ?>" href="nuestro_equipo.php?tipo=estilistas">Estilistas</a>
            <a class="pestanas__opcion<?= $tipo === 'hotel' ? ' es-activo' : '' ?>" href="nuestro_equipo.php?tipo=hotel">Hotel</a>
        </nav>

        <?php if (empty($empleados)): ?>
            <p class="subtitulo-pagina">Por ahora no hay personal para mostrar en esta sección.</p>
        <?php endif; ?>

        <section class="rejilla-tarjetas">

            <?php foreach ($empleados as $empleado): ?>
                <article class="tarjeta tarjeta-empleado">
   
                    <div class="foto-marcador foto-marcador--avatar" style="font-size: 3rem; font-weight: bold;">
                        <?= htmlspecialchars(mb_strtoupper(mb_substr($empleado['nombre'], 0, 1))) ?>
                    </div>

                    <h2 class="tarjeta-empleado__nombre">
                        <?= htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']) ?>
                    </h2>
                    <span class="tarjeta-empleado__puesto"><?= htmlspecialchars($empleado['puesto']) ?></span>

                    <?php if (!empty($empleado['especialidad'])): ?>
                        <p class="tarjeta-empleado__detalle">Especialidad: <?= htmlspecialchars($empleado['especialidad']) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($empleado['num_cedula_profesional'])): ?>
                        <p class="tarjeta-empleado__detalle">Cédula profesional: <?= htmlspecialchars($empleado['num_cedula_profesional']) ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>

        </section>

    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
