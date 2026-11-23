<?php
session_start();
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);

$paginaActiva = 'mascotas';

require_once __DIR__ . '/../../models/Mascota.php';

$mascotas = Mascota::obtenerPorCliente((int) $_SESSION['id_cliente']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis mascotas</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>

<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor">

        <div>
            <h1 class="titulo-pagina">Mis mascotas</h1>
            <p class="subtitulo-pagina">Aquí están las mascotas que has registrado.</p>
            <?php if ($errores): ?>
                <div class="aviso aviso--error">
                    <?php foreach ($errores as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="rejilla-tarjetas">

            <?php foreach ($mascotas as $mascota): ?>
                <div class="tarjeta tarjeta-mascota">
                    <?php $foto = rutaFotoMascota($mascota['fotografia']); ?>
                    <?php if ($foto): ?>
                        <img class="foto-marcador foto-marcador--mascota" src="../../../<?= htmlspecialchars($foto) ?>" alt="Foto de <?= htmlspecialchars($mascota['nombre']) ?>">
                    <?php else: ?>
                        <div class="foto-marcador foto-marcador--mascota">Foto de la mascota</div>
                    <?php endif; ?>
                    <h3 class="tarjeta-mascota__nombre"><a href="detalle_mascota.php?id_mascota=<?= (int) $mascota['id_mascota'] ?>"><?= htmlspecialchars($mascota['nombre']) ?></a></h3>
                    <p class="tarjeta-mascota__dato"><?= htmlspecialchars($mascota['especie']) ?> · <?= htmlspecialchars($mascota['raza']) ?> · <?= htmlspecialchars($mascota['sexo']) ?></p>
                    <p class="tarjeta-mascota__dato"><?= textoEdadMascota($mascota['edad']) ?> · <?= $mascota['peso'] ?> kg</p>
                    <div class="grupo-botones grupo-botones--compacto">
                        <a class="boton boton--chico" href="agendar_cita.php?id_mascota=<?= $mascota['id_mascota'] ?>">Agendar cita</a>
                        <a class="boton boton--contorno boton--chico" href="editar_mascota.php?id_mascota=<?= $mascota['id_mascota'] ?>">Editar</a>
                        <a class="boton boton--contorno boton--chico" href="eliminar_mascota.php?id_mascota=<?= $mascota['id_mascota'] ?>">Eliminar</a>
                    </div>
                </div>
            <?php endforeach; ?>

            <a class="tarjeta-agregar" href="registrar_mascota.php">
                <span class="tarjeta-agregar__icono">+</span>
                <h3 class="tarjeta-agregar__titulo">Registrar otra mascota</h3>
                <p class="tarjeta-agregar__texto">Solo necesitamos sus datos básicos.</p>
            </a>

        </div>

        <p class="aviso">El historial médico y las vacunas los registra nuestro veterinario en la clínica.</p>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
