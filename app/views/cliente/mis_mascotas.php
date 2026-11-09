<?php
session_start();
$paginaActiva = 'mascotas';
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

            <div class="tarjeta tarjeta-mascota">
                <div class="foto-marcador foto-marcador--mascota">Foto de la mascota</div>
                <h3 class="tarjeta-mascota__nombre">Luna</h3>
                <p class="tarjeta-mascota__dato">Perro · Labrador · Hembra</p>
                <p class="tarjeta-mascota__dato">3 años · 24 kg</p>
                <div class="grupo-etiquetas">
                    <span class="etiqueta etiqueta--exito">Vacunas al día</span>
                </div>
                <div class="grupo-botones grupo-botones--compacto">
                    <a class="boton boton--chico" href="agendarCita.php?id_mascota=1">Agendar cita</a>
                    <a class="boton boton--contorno boton--chico" href="editar_mascota.php?id_mascota=1">Editar</a>
                </div>
            </div>

            <div class="tarjeta tarjeta-mascota">
                <div class="foto-marcador foto-marcador--mascota">Foto de la mascota</div>
                <h3 class="tarjeta-mascota__nombre">Michi</h3>
                <p class="tarjeta-mascota__dato">Gato · Siamés · Macho</p>
                <p class="tarjeta-mascota__dato">1 año · 4 kg</p>
                <div class="grupo-etiquetas">
                    <span class="etiqueta">Sin vacunas registradas</span>
                </div>
                <div class="grupo-botones grupo-botones--compacto">
                    <a class="boton boton--chico" href="agendarCita.php?id_mascota=2">Agendar cita</a>
                    <a class="boton boton--contorno boton--chico" href="editar_mascota.php?id_mascota=2">Editar</a>
                </div>
            </div>

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
