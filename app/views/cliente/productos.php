<?php
session_start();
require_once __DIR__ . '/../../models/Publico.php';

$paginaActiva = 'productos';
$buscar = trim((string)($_GET['q'] ?? ''));
$productos = Publico::productos($buscar);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>

<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor">

        <div>
            <h1 class="titulo-pagina">Nuestros productos</h1>
            <p class="subtitulo-pagina">Consulta lo que tenemos y su precio. La compra se hace en la tienda.</p>
        </div>

        <form method="get" class="grupo-botones">
            <input class="campo__entrada" type="search" name="q" maxlength="50" placeholder="Buscar producto" value="<?= htmlspecialchars($buscar) ?>">
            <button class="boton boton--chico" type="submit">Buscar</button>
        </form>

        <div class="rejilla-tarjetas">
            <?php foreach ($productos as $pr):
                $img = trim((string)($pr['imagen'] ?? ''));
                $hay = (float)$pr['disponible'] > 0;
            ?>
            <div class="tarjeta tarjeta-mascota">
                <?php if ($img !== ''): ?>
                    <img class="foto-marcador foto-marcador--mascota" src="../../../<?= htmlspecialchars(ltrim($img, '/')) ?>" alt="<?= htmlspecialchars($pr['nombre']) ?>">
                <?php else: ?>
                    <div class="foto-marcador foto-marcador--mascota">Foto del producto</div>
                <?php endif; ?>
                <h3 class="tarjeta-mascota__nombre"><?= htmlspecialchars($pr['nombre']) ?></h3>
                <p class="tarjeta-mascota__dato"><?= htmlspecialchars((string)$pr['descripcion']) ?></p>
                <p class="tarjeta-mascota__dato">$<?= number_format((float)$pr['precio_venta'], 2) ?></p>
                <div class="grupo-etiquetas">
                    <span class="etiqueta<?= $hay ? ' etiqueta--exito' : '' ?>"><?= $hay ? 'Disponible' : 'Agotado' ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (empty($productos)): ?>
            <p class="aviso">Por ahora no hay productos para mostrar.</p>
        <?php endif; ?>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>

</html>
