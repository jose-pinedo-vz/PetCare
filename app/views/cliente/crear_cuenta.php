<?php
session_start();
$paginaActiva = '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear mi cuenta</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor contenedor--medio">
        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Crear mi cuenta</h1>
            <p class="tarjeta__subtitulo">Los campos con <span class="campo__obligatorio">*</span> son obligatorios.</p>

            <form action="registrar_cuenta.php" method="post">

                <fieldset class="grupo-campos">
                    <legend class="tarjeta__seccion">Datos de acceso</legend>

                    <div class="formulario-rejilla">
                        <div class="campo">
                            <label class="campo__etiqueta" for="usuario">Usuario <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="usuario" name="usuario" placeholder="p. ej. maria.lopez" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="correo">Correo electrónico <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="email" id="correo" name="correo" placeholder="correo@ejemplo.com" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="contrasena">Contraseña <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="password" id="contrasena" name="contrasena" placeholder="••••••••" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="confirmar_contrasena">Confirmar contraseña <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="password" id="confirmar_contrasena" name="confirmar_contrasena" placeholder="••••••••" required>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="grupo-campos">
                    <legend class="tarjeta__seccion">Tus datos</legend>

                    <div class="formulario-rejilla">
                        <div class="campo">
                            <label class="campo__etiqueta" for="nombres">Nombre(s) <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="nombres" name="nombres" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="apellidos">Apellidos <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="apellidos" name="apellidos" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="telefono">Teléfono <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="tel" id="telefono" name="telefono" placeholder="10 dígitos" maxlength="10" pattern="[0-9]{10}" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="direccion">Dirección (opcional)</label>
                            <input class="campo__entrada" type="text" id="direccion" name="direccion">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="grupo-campos">
                    <legend class="tarjeta__seccion">Contacto de emergencia</legend>

                    <div class="formulario-rejilla">
                        <div class="campo">
                            <label class="campo__etiqueta" for="emergencia_nombre">Nombre <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="emergencia_nombre" name="emergencia_nombre" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="emergencia_telefono">Teléfono <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="tel" id="emergencia_telefono" name="emergencia_telefono" maxlength="10" pattern="[0-9]{10}" required>
                        </div>
                    </div>
                </fieldset>

                <div class="grupo-botones">
                    <button class="boton" type="submit">Crear cuenta</button>
                    <a class="boton boton--contorno" href="inicio.php">Cancelar</a>
                </div>
            </form>

            <p class="texto-enlace">¿Ya tienes cuenta? <a href="iniciar_sesion.php">Inicia sesión</a></p>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
