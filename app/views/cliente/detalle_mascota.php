<?php
session_start();
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
$paginaActiva = 'mascotas';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luna · Mis mascotas</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>


    <main class="contenedor">
        <p class="migas-de-pan"><a href="mis_mascotas.php">Mis mascotas</a> › Luna</p> 

        <div class="encabezado-pagina">
            <div>
                <h1 class="titulo-pagina">Luna</h1> 
                <p class="subtitulo-pagina">Perro · Labrador · Hembra</p>
            </div>
            <div class="grupo-botones">
                <a class="boton" href="agendar_cita.php?id_mascota=1">Agendar cita</a>
                <a class="boton boton--contorno" href="editar_mascota.php?id_mascota=1">Editar</a>
                <a class="boton boton--contorno" href="eliminar_mascota.php?id_mascota=1">Eliminar</a>
            </div>
        </div>

        <div class="bloque-informacion">
            <section class="tarjeta">
                <div class="foto-marcador foto-marcador--ficha">Foto de la mascota</div> 

                <h2 class="bloque-informacion__titulo">Datos generales</h2>
                <dl class="bloque-informacion__lista">
                    <dt class="bloque-informacion__dato">Edad</dt>
                    <dd class="bloque-informacion__valor">3 años</dd>

                    <dt class="bloque-informacion__dato">Peso</dt>
                    <dd class="bloque-informacion__valor">24 kg</dd>

                    <dt class="bloque-informacion__dato">Color</dt>
                    <dd class="bloque-informacion__valor">Dorado</dd>

                    <dt class="bloque-informacion__dato">Temperamento</dt>
                    <dd class="bloque-informacion__valor">Juguetona</dd>
                </dl>
            </section>

            <section class="tarjeta">
                <h2 class="bloque-informacion__titulo">Salud</h2>
                <dl class="bloque-informacion__lista">
                    <dt class="bloque-informacion__dato">Alergias</dt>
                    <dd class="bloque-informacion__valor">Ninguna</dd>

                    <dt class="bloque-informacion__dato">Condiciones especiales</dt>
                    <dd class="bloque-informacion__valor">Ninguna</dd>

                    <dt class="bloque-informacion__dato">Vacunas</dt>
                    <dd class="bloque-informacion__valor"><span class="etiqueta etiqueta--exito">Al día</span></dd>

                    <dt class="bloque-informacion__dato">Última desparasitación</dt>
                    <dd class="bloque-informacion__valor">15 sep 2026</dd>
                </dl>
            </section>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>