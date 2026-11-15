<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>

    <main class="contenedor contenedor--medio">
        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Mi perfil</h1>
            <p class="tarjeta__subtitulo">Actualiza tus datos de contacto. Los campos con <span class="campo__obligatorio">*</span> son obligatorios.</p>

          
            <form action="mi_perfil.php" method="post">
                <div class="formulario-rejilla">
                    <div class="campo">
                        <label class="campo__etiqueta" for="nombres">Nombres <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="text" id="nombres" name="nombres" value="María" required>
                    </div>

                    <div class="campo">
                        <label class="campo__etiqueta" for="apellidos">Apellidos <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="text" id="apellidos" name="apellidos" value="Gómez" required>
                    </div>

                    <div class="campo">
                        <label class="campo__etiqueta" for="telefono">Teléfono <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="tel" id="telefono" name="telefono" value="4921234567" required>
                    </div>

                    <div class="campo">
                        <label class="campo__etiqueta" for="correo">Correo <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="email" id="correo" name="correo" value="maria@correo.com" required>
                    </div>

                    <div class="campo campo--ancho-completo">
                        <label class="campo__etiqueta" for="direccion">Dirección <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="text" id="direccion" name="direccion" value="Calle Juárez 123, Centro" required>
                    </div>

                    <div class="campo">
                        <label class="campo__etiqueta" for="emergencia_nombre">Contacto de emergencia <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="text" id="emergencia_nombre" name="emergencia_nombre" value="Luis Gómez" required>
                    </div>

                    <div class="campo">
                        <label class="campo__etiqueta" for="emergencia_telefono">Teléfono de emergencia <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="tel" id="emergencia_telefono" name="emergencia_telefono" value="4927654321" required>
                    </div>
                </div>

                <div class="grupo-botones">
                    <button class="boton" type="submit">Guardar cambios</button>
                    <a class="boton boton--contorno" href="inicio.php">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>