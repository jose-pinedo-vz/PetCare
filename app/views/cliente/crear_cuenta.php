<?php
session_start();

$errores = $_SESSION['errores'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errores'], $_SESSION['old'], $_SESSION['nombre']);

// Valor anterior del formulario, ya escapado (las contraseñas nunca se guardan)
$v = function (string $clave) use ($old): string {
    return htmlspecialchars(is_string($old[$clave] ?? null) ? $old[$clave] : '');
};

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
            <?php if ($errores): ?>
                <div class="aviso aviso--error">
                    <?php foreach ($errores as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="../../controllers/cliente_registrar_cuenta.php" method="post">

                <fieldset class="grupo-campos">
                    <legend class="tarjeta__seccion">Datos de acceso</legend>

                    <div class="formulario-rejilla">
                        <div class="campo">
                            <label class="campo__etiqueta" for="usuario">Usuario <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="usuario" name="usuario" placeholder="Ej. maria.lopez" value="<?= $v('usuario') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="correo">Correo electrónico <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="email" id="correo" name="correo" placeholder="correo@ejemplo.com" value="<?= $v('correo') ?>" required>
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
                            <input class="campo__entrada" type="text" id="nombres" name="nombres" value="<?= $v('nombres') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="apellidos">Apellidos <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="apellidos" name="apellidos" value="<?= $v('apellidos') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="telefono">Teléfono <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="tel" id="telefono" name="telefono" placeholder="10 dígitos" maxlength="10" pattern="[0-9]{10}" value="<?= $v('telefono') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="direccion">Dirección (opcional)</label>
                            <input class="campo__entrada" type="text" id="direccion" name="direccion" value="<?= $v('direccion') ?>">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="grupo-campos">
                    <legend class="tarjeta__seccion">Contacto de emergencia</legend>

                    <div class="formulario-rejilla">
                        <div class="campo">
                            <label class="campo__etiqueta" for="emergencia_nombre">Nombre <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="emergencia_nombre" name="emergencia_nombre" value="<?= $v('emergencia_nombre') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="emergencia_telefono">Teléfono <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="tel" id="emergencia_telefono" name="emergencia_telefono" maxlength="10" pattern="[0-9]{10}" value="<?= $v('emergencia_telefono') ?>" required>
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
