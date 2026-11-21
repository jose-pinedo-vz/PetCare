<?php
session_start();

$paginaActiva = 'productos';

require_once __DIR__ . '/../../models/Productos.php';
$productos = Productos::obtenerCatalogoCliente();
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

        <?php if (empty($productos)): ?>
            <p class="aviso">Por ahora no hay productos para mostrar.</p>
        <?php endif; ?>

        <div class="rejilla-tarjetas">

            <?php foreach ($productos as $producto): ?>
                <?php
                $ruta = trim($producto['imagen'] ?? '');
                $hayImagen = $ruta !== '' && is_file(__DIR__ . '/../../../' . ltrim($ruta, '/'));
                ?>
                <div class="tarjeta tarjeta-mascota">
                    <?php if ($hayImagen): ?>
                        <img class="foto-marcador foto-marcador--mascota"
                             src="../../../<?= htmlspecialchars(ltrim($ruta, '/')) ?>"
                             alt="<?= htmlspecialchars($producto['nombre']) ?>"
                             style="object-fit: cover; padding: 0;">
                    <?php else: ?>
                        <div class="foto-marcador foto-marcador--mascota">Foto del producto</div>
                    <?php endif; ?>

                    <h3 class="tarjeta-mascota__nombre"><?= htmlspecialchars($producto['nombre']) ?></h3>
                    <p class="tarjeta-mascota__dato"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></p>
                    <p class="tarjeta-mascota__dato">$<?= number_format((float) $producto['precio_venta'], 2) ?></p>

                    <div class="grupo-etiquetas">
                        <?php if ((int) $producto['existencia'] > 0): ?>
                            <span class="etiqueta etiqueta--exito">Disponible</span>
                        <?php else: ?>
                            <span class="etiqueta">Agotado</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>

</html>
