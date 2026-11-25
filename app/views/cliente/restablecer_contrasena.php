<?php
session_start();

require_once __DIR__ . '/../../models/Cliente.php';

$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);

$token = is_string($_GET['token'] ?? null) ? trim($_GET['token']) : '';
$tokenValido = ($token !== '') && Cliente::obtenerPorTokenRecuperacion($token) !== null;

$paginaActiva = '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor contenedor--angosto">
        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Restablecer contraseña</h1>

            <?php if (!$tokenValido): ?>
                <div class="aviso aviso--error">
                    <p>El enlace no es válido o ya expiró</p>
                </div>

                <p class="texto-enlace"><a href="recuperar_contrasena.php">Solicitar un nuevo enlace</a></p>
            <?php else: ?>
                <p class="tarjeta__subtitulo">Escribe tu nueva contraseña (mínimo 8 caracteres).</p>

                <?php if ($errores): ?>
                    <div class="aviso aviso--error">
                        <?php foreach ($errores as $error): ?>
                            <p><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="../../controllers/cliente_restablecer.php" method="post">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div class="formulario-rejilla">
                        <div class="campo campo--ancho-completo">
                            <label class="campo__etiqueta" for="contrasena">Nueva contraseña</label>
                            <input class="campo__entrada" type="password" id="contrasena" name="contrasena" minlength="8" autocomplete="new-password" required>
                        </div>

                        <div class="campo campo--ancho-completo">
                            <label class="campo__etiqueta" for="confirmar_contrasena">Confirmar contraseña</label>
                            <input class="campo__entrada" type="password" id="confirmar_contrasena" name="confirmar_contrasena" minlength="8" autocomplete="new-password" required>
                        </div>
                    </div>

                    <div class="grupo-botones">
                        <button class="boton" type="submit">Guardar contraseña</button>
                        <a class="boton boton--contorno" href="iniciar_sesion.php">Cancelar</a>
                    </div>
                </form>
            <?php endif; ?>

            <p class="texto-enlace">¿Recordaste tu contraseña? <a href="iniciar_sesion.php">Inicia sesión</a></p>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
