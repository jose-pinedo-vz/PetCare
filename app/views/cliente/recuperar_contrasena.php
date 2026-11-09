<?php
session_start();
$paginaActiva = '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor contenedor--angosto">
        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Recuperar contraseña</h1>
            <p class="tarjeta__subtitulo">Escribe tu usuario y tu correo para continuar.</p>

            <form action="validar_recuperacion.php" method="post">
                <div class="formulario-rejilla">
                    <div class="campo campo--ancho-completo">
                        <label class="campo__etiqueta" for="usuario">Usuario</label>
                        <input class="campo__entrada" type="text" id="usuario" name="usuario" required>
                    </div>

                    <div class="campo campo--ancho-completo">
                        <label class="campo__etiqueta" for="correo">Correo electrónico</label>
                        <input class="campo__entrada" type="email" id="correo" name="correo" placeholder="correo@ejemplo.com" required>
                    </div>
                </div>

                <div class="grupo-botones">
                    <button class="boton" type="submit">Continuar</button>
                    <a class="boton boton--contorno" href="iniciar_sesion.php">Cancelar</a>
                </div>
            </form>

            <p class="texto-enlace">¿Recordaste tu contraseña? <a href="iniciar_sesion.php">Inicia sesión</a></p>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
