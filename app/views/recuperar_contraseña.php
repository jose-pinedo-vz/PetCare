<?php
$titulo = "Recuperar Contraseña";
include 'layout/encabezado.php';
?>

<main>
    <h2>Recuperar contraseña</h2>
    <p>Ingresa tu correo electrónico registrado</p>

    <?php if (!empty($mensaje)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class='msg-error'><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form action="../controllers/procesar_recuperacion.php" method="POST">
        <div style="margin-bottom: 15px;">
            <label for="correo" class="label-campo">Correo electrónico:</label>
            <input type="email" id="correo" name="correo" class="campo" required>
        </div>

        <button type="submit">Enviar enlace de recuperación</button>
        <p style="margin-top: 15px;">
            <a href="login.php">Volver al inicio de sesión</a>
        </p>
    </form>
</main>

<?php include 'layout/pie.php'; ?>