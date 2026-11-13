<?php
session_start();
$paginaActiva = 'mascotas';

require_once __DIR__ . '/../../models/Mascota.php';

// TEMPORAL: mientras no exista el login, simulamos que entró el cliente 1.
// BORRAR esta linea cuando el login guarde el id_cliente real en la sesion.
$_SESSION['id_cliente'] = $_SESSION['id_cliente'] ?? 1;

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
                    <h3 class="tarjeta-mascota__nombre"><?= htmlspecialchars($mascota['nombre']) ?></h3>
                    <p class="tarjeta-mascota__dato"><?= htmlspecialchars($mascota['especie']) ?> · <?= htmlspecialchars($mascota['raza']) ?> · <?= htmlspecialchars($mascota['sexo']) ?></p>
                    <p class="tarjeta-mascota__dato"><?= textoEdadMascota($mascota['edad']) ?> · <?= $mascota['peso'] ?> kg</p>
                    <div class="grupo-etiquetas">
                        <?php $vacunas = trim(strtolower($mascota['vacunas'] ?? '')); ?>
                        <?php if ($vacunas === '' || $vacunas === 'ninguna'): ?>
                            <span class="etiqueta">Sin vacunas registradas</span>
                        <?php else: ?>
                            <span class="etiqueta etiqueta--exito">Vacunas registradas</span>
                        <?php endif; ?>
                    </div>
                    <div class="grupo-botones grupo-botones--compacto">
                        <a class="boton boton--chico" href="agendarCita.php?id_mascota=<?= $mascota['id_mascota'] ?>">Agendar cita</a>
                        <a class="boton boton--contorno boton--chico" href="editar_mascota.php?id_mascota=<?= $mascota['id_mascota'] ?>">Editar</a>
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