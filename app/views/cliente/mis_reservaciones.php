<?php
session_start();
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
$paginaActiva = 'reservaciones';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis reservaciones</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>


    <main class="contenedor">
        <div class="encabezado-pagina">
            <div>
                <h1 class="titulo-pagina">Mis reservaciones</h1>
                <p class="subtitulo-pagina">Estancias de tus mascotas en el hotel.</p>
            </div>
            <a class="boton" href="solicitar_reservacion.php">+ Solicitar reservación</a>
        </div>

        <section>
       
            <article class="tarjeta-cita">
                <div class="tarjeta-cita__fecha">
                    <span class="tarjeta-cita__dia">22</span>
                    <span class="tarjeta-cita__mes">NOV</span>
                </div>

                <div class="tarjeta-cita__informacion">
                    <h2 class="tarjeta-cita__titulo">Hotel · Luna</h2>
                    <p class="tarjeta-cita__detalle">22 nov a 25 nov · 3 noches</p>
                </div>

                <span class="estado-cita estado-cita--pendiente">Por confirmar</span>
            </article>

            <article class="tarjeta-cita tarjeta-cita--pasada">
                <div class="tarjeta-cita__fecha">
                    <span class="tarjeta-cita__dia">03</span>
                    <span class="tarjeta-cita__mes">OCT</span>
                </div>

                <div class="tarjeta-cita__informacion">
                    <h2 class="tarjeta-cita__titulo">Hotel · Michi</h2>
                    <p class="tarjeta-cita__detalle">3 oct a 5 oct · 2 noches</p>
                </div>

                <span class="estado-cita estado-cita--atendida">Finalizada</span>
            </article>
        </section>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>