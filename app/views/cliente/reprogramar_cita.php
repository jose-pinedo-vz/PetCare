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
    <title>Reprogramar cita</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>


    <main class="contenedor contenedor--medio">
        <p class="migas-de-pan"><a href="mis_citas.php">Mis citas</a> › Reprogramar cita</p>

        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Reprogramar cita</h1>
            <p class="tarjeta__subtitulo">Baño · Luna. Elige una nueva fecha y hora.</p> 

            <form action="reprogramar_cita.php" method="post">
                <input type="hidden" name="id_cita" value="1"> 

                <div class="formulario-rejilla">
                    <div class="campo">
                        <label class="campo__etiqueta" for="fecha">Nueva fecha <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="date" id="fecha" name="fecha" required>
                    </div>
                </div>

                <p class="selector-horario__titulo">Nueva hora <span class="campo__obligatorio">*</span></p>
                <div class="selector-horario">

                    <button class="selector-horario__hora" type="button">09:00</button>
                    <button class="selector-horario__hora" type="button">10:00</button>
                    <button class="selector-horario__hora es-ocupado" type="button" disabled>11:00</button>
                    <button class="selector-horario__hora" type="button">12:00</button>
                    <button class="selector-horario__hora" type="button">15:00</button>
                </div>

                <input type="hidden" name="hora" value="">

                <div class="grupo-botones">
                    <button class="boton" type="submit">Guardar cambios</button>
                    <a class="boton boton--contorno" href="mis_citas.php">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>