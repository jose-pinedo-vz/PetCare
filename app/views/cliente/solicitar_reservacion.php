<?php
session_start();
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
$paginaActiva = 'servicios';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar reservación de hotel</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>


    <main class="contenedor contenedor--medio">
        <p class="migas-de-pan"><a href="servicios.php">Servicios</a> › Hotel para mascotas</p>

        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Solicitar reservación de hotel</h1>
            <p class="tarjeta__subtitulo">Los campos con <span class="campo__obligatorio">*</span> son obligatorios.</p>

            <form action="solicitar_reservacion.php" method="post">
                <div class="formulario-rejilla">
                    <div class="campo campo--ancho-completo">
                        <label class="campo__etiqueta" for="id_mascota">Mascota <span class="campo__obligatorio">*</span></label>
                        <select class="campo__entrada" id="id_mascota" name="id_mascota" required>
                            <option value="">Elige una mascota</option>
         
                            <option value="1">Luna</option>
                            <option value="2">Michi</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label class="campo__etiqueta" for="fecha_entrada">Fecha de entrada <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="date" id="fecha_entrada" name="fecha_entrada" required>
                    </div>

                    <div class="campo">
                        <label class="campo__etiqueta" for="fecha_salida">Fecha de salida <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="date" id="fecha_salida" name="fecha_salida" required>
                    </div>

                    <div class="campo campo--ancho-completo">
                        <label class="campo__etiqueta" for="servicios_adicionales">Servicios adicionales</label>
                        <textarea class="campo__entrada" id="servicios_adicionales" name="servicios_adicionales" rows="3" placeholder="p. ej. baño antes de la salida"></textarea>
                    </div>
                </div>

                <p class="aviso">Tu solicitud será revisada. El precio y el anticipo se confirman después.</p>

                <div class="grupo-botones">
                    <button class="boton" type="submit">Enviar solicitud</button>
                    <a class="boton boton--contorno" href="servicios.php">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>