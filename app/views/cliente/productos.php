<?php
session_start();

$paginaActiva = 'productos';

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

        <div class="rejilla-tarjetas">
            <div class="tarjeta tarjeta-mascota">
                <div class="foto-marcador foto-marcador--mascota">Foto del producto</div>
                <h3 class="tarjeta-mascota__nombre">Cama para perro mediana</h3>
                <p class="tarjeta-mascota__dato">Cama acolchada, lavable.</p>
                <p class="tarjeta-mascota__dato">$350.00</p>
                <div class="grupo-etiquetas">
                    <span class="etiqueta etiqueta--exito">Disponible</span>
                </div>
            </div>

            <div class="tarjeta tarjeta-mascota">
                <div class="foto-marcador foto-marcador--mascota">Foto del producto</div>
                <h3 class="tarjeta-mascota__nombre">Alimento para gato 1 kg</h3>
                <p class="tarjeta-mascota__dato">Croquetas para gato adulto.</p>
                <p class="tarjeta-mascota__dato">$120.00</p>
                <div class="grupo-etiquetas">
                    <span class="etiqueta etiqueta--exito">Disponible</span>
                </div>
            </div>

            <div class="tarjeta tarjeta-mascota">
                <div class="foto-marcador foto-marcador--mascota">Foto del producto</div>
                <h3 class="tarjeta-mascota__nombre">Collar ajustable</h3>
                <p class="tarjeta-mascota__dato">Collar de nylon, talla chica.</p>
                <p class="tarjeta-mascota__dato">$80.00</p>
                <div class="grupo-etiquetas">
                    <span class="etiqueta">Agotado</span>
                </div>
            </div>

        </div>
        <p class="aviso">Por ahora no hay productos para mostrar.</p>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>

</html>
