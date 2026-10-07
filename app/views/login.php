<?php
$titulo = "Iniciar sesión";
include 'layout/encabezado.php';
?>

<main>
    <h2>Iniciar sesión</h2>

    <?php if (!empty($error)): ?>
        <p class='msg-error'><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form action="../app/controllers/Login.php" method="POST">
        <div style="margin-bottom: 15px;">
            <label for="usuario" class="label-campo">Usuario:</label>
            <input type="text" id="usuario" name="usuario" class="campo" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="password" class="label-campo">Contraseña:</label>
            <input type="password" id="password" name="password" class="campo" required>
        </div>

        <button type="submit">Ingresar</button>
    </form>
</main>

<?php include 'layout/pie.php'; ?>
