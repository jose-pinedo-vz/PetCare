<?php
session_start();
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
$paginaActiva = 'citas';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita agendada</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>


    <main class="contenedor contenedor--medio">
        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">¡Cita agendada!</h1>
            <p class="tarjeta__subtitulo">Te esperamos. Estos son los datos de tu cita.</p>

            <div class="tarjeta resumen-cita">
                <dl class="resumen-cita__lista">
                    <dt class="resumen-cita__dato">Servicio</dt>
                    <dd class="resumen-cita__valor">Baño</dd> 

                    <dt class="resumen-cita__dato">Mascota</dt>
                    <dd class="resumen-cita__valor">Luna</dd> 

                    <dt class="resumen-cita__dato">Fecha y hora</dt>
                    <dd class="resumen-cita__valor">12 nov · 10:00</dd> 

                    <dt class="resumen-cita__dato">Estado</dt>
                    <dd class="resumen-cita__valor">Pendiente de pago</dd> 
                </dl>
                <p class="resumen-cita__total"><span>Total</span> <span>$150.00</span></p> 
            </div>

            <div class="grupo-botones">
                <a class="boton" href="mis_citas.php">Ver mis citas</a>
                <a class="boton boton--contorno" href="inicio.php">Volver al inicio</a>
            </div>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>