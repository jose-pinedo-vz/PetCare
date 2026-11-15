<?php
session_start();
$paginaActiva = '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor contenedor--angosto">
        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Iniciar sesión</h1>
            <p class="tarjeta__subtitulo">Entra para ver tus mascotas y tus citas.</p>

            <form action="validar_login.php" method="post">
                <div class="formulario-rejilla">
                    <div class="campo campo--ancho-completo">
                        <label class="campo__etiqueta" for="usuario">Usuario</label>
                        <input class="campo__entrada" type="text" id="usuario" name="usuario" required>
                    </div>

                    <div class="campo campo--ancho-completo">
                        <label class="campo__etiqueta" for="contrasena">Contraseña</label>
                        <input class="campo__entrada" type="password" id="contrasena" name="contrasena" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="grupo-botones">
                    <button class="boton" type="submit">Entrar</button>
                </div>
            </form>

            <p class="texto-enlace">¿Olvidaste tu contraseña? <a href="recuperar_contrasena.php">Recupérala</a></p>
            <p class="texto-enlace">¿Aún no tienes cuenta? <a href="crear_cuenta.php">Crea tu cuenta</a></p>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
