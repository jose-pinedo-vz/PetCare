<?php
session_start();
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
$paginaActiva = 'mascotas';

require_once __DIR__ . '/../../models/Mascota.php';

// la mascota viene en la URL: eliminar_mascota.php?id_mascota=3
$idMascota = (int) ($_GET['id_mascota'] ?? 0);
$mascota = Mascota::obtenerPorId($idMascota);

// si no existe, ya esta borrada o es de otro cliente, se regresa a la lista
if (!$mascota || !$mascota['esta_activo'] || (int) $mascota['id_cliente'] !== (int) $_SESSION['id_cliente']) {
    $_SESSION['errores'] = ['No se encontró la mascota.'];
    header('Location: mis_mascotas.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar mascota</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>


    <main class="contenedor contenedor--angosto">
        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Eliminar mascota</h1>
            <p class="tarjeta__subtitulo">¿Seguro que quieres eliminar a <strong><?= htmlspecialchars($mascota['nombre']) ?></strong>?</p>

            <p class="aviso">Dejará de aparecer en tu cuenta. Su historial médico se conserva en la veterinaria.</p>

            <form action="../../controllers/cliente_eliminar_mascota.php" method="post">
                <input type="hidden" name="id_mascota" value="<?= (int) $mascota['id_mascota'] ?>">
                <div class="grupo-botones">
                    <button class="boton" type="submit">Sí, eliminar</button>
                    <a class="boton boton--contorno" href="mis_mascotas.php">No, volver</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>