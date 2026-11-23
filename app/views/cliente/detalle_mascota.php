<?php
session_start();


if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
$paginaActiva = 'mascotas';

require_once __DIR__ . '/../../models/Mascota.php';

$idMascota = (int) ($_GET['id_mascota'] ?? 0);
$mascota = Mascota::obtenerPorId($idMascota);

if (!$mascota || (int) $mascota['esta_activo'] !== 1 || (int) $mascota['id_cliente'] !== (int) $_SESSION['id_cliente']) {
    $_SESSION['errores'] = ['No se encontró la mascota.'];
    header('Location: mis_mascotas.php');
    exit;
}

function datoMascota(array $mascota, string $campo, string $siVacio = 'Sin registrar'): string {
    $valor = trim((string) ($mascota[$campo] ?? ''));
    return htmlspecialchars($valor === '' ? $siVacio : $valor);
}

$id     = (int) $mascota['id_mascota'];
$nombre = htmlspecialchars($mascota['nombre']);
$foto   = rutaFotoMascota($mascota['fotografia']);


$vacunas = trim($mascota['vacunas'] ?? '');
$tieneVacunas = $vacunas !== '' && strtolower($vacunas) !== 'ninguna';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $nombre ?> · Mis mascotas</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor">
        <p class="migas-de-pan"><a href="mis_mascotas.php">Mis mascotas</a> › <?= $nombre ?></p>

        <div class="encabezado-pagina">
            <div>
                <h1 class="titulo-pagina"><?= $nombre ?></h1>
                <p class="subtitulo-pagina">
                    <?= datoMascota($mascota, 'especie') ?> · <?= datoMascota($mascota, 'raza') ?> · <?= datoMascota($mascota, 'sexo') ?>
                </p>
            </div>

            <div class="grupo-botones">
                <a class="boton" href="agendar_cita.php?id_mascota=<?= $id ?>">Agendar cita</a>
                <a class="boton boton--contorno" href="editar_mascota.php?id_mascota=<?= $id ?>">Editar</a>
                <a class="boton boton--contorno" href="eliminar_mascota.php?id_mascota=<?= $id ?>">Eliminar</a>
            </div>
        </div>

        <div class="bloque-informacion">
            <section class="tarjeta">
                <?php if ($foto): ?>
                    <img class="foto-marcador foto-marcador--ficha" src="../../../<?= htmlspecialchars($foto) ?>" alt="Foto de <?= $nombre ?>" style="object-fit: cover; padding: 0;">
                <?php else: ?>
                    <div class="foto-marcador foto-marcador--ficha">Foto de la mascota</div>
                <?php endif; ?>

                <h2 class="bloque-informacion__titulo">Datos generales</h2>
                <dl class="bloque-informacion__lista">
                    <dt class="bloque-informacion__dato">Edad</dt>
                    <dd class="bloque-informacion__valor"><?= htmlspecialchars(textoEdadMascota($mascota['edad'])) ?></dd>

                    <dt class="bloque-informacion__dato">Peso</dt>
                    <dd class="bloque-informacion__valor"><?= $mascota['peso'] !== null ? htmlspecialchars($mascota['peso']) . ' kg' : 'Sin registrar' ?></dd>

                    <dt class="bloque-informacion__dato">Tamaño</dt>
                    <dd class="bloque-informacion__valor"><?= datoMascota($mascota, 'tamanio') ?></dd>

                    <dt class="bloque-informacion__dato">Color</dt>
                    <dd class="bloque-informacion__valor"><?= datoMascota($mascota, 'color') ?></dd>

                    <dt class="bloque-informacion__dato">Temperamento</dt>
                    <dd class="bloque-informacion__valor"><?= datoMascota($mascota, 'temperamento') ?></dd>
                </dl>
            </section>

            <section class="tarjeta">
                <h2 class="bloque-informacion__titulo">Salud</h2>
                <dl class="bloque-informacion__lista">
                    <dt class="bloque-informacion__dato">Alergias</dt>
                    <dd class="bloque-informacion__valor"><?= datoMascota($mascota, 'alergias', 'Ninguna') ?></dd>

                    <dt class="bloque-informacion__dato">Condiciones especiales</dt>
                    <dd class="bloque-informacion__valor"><?= datoMascota($mascota, 'condiciones_especiales', 'Ninguna') ?></dd>

                    <dt class="bloque-informacion__dato">Vacunas</dt>
                    <dd class="bloque-informacion__valor">
                        <?php if ($tieneVacunas): ?>
                            <span class="etiqueta etiqueta--exito">Registradas</span>
                            <?= htmlspecialchars($vacunas) ?>
                        <?php else: ?>
                            <span class="etiqueta">Sin vacunas registradas</span>
                        <?php endif; ?>
                    </dd>

                    <dt class="bloque-informacion__dato">Última desparasitación</dt>
                    <dd class="bloque-informacion__valor"><?= datoMascota($mascota, 'ultima_desparasitacion') ?></dd>
                </dl>
            </section>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
