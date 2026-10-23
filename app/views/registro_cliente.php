<?php
if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}
$mensaje = $_SESSION['mensaje'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['error']);

$titulo = "Registro de cuenta";
include 'layout/encabezado.php';
?>

<main>
    <h2>Crear una nueva cuenta</h2>

    <?php if (!empty($mensaje)): ?>
        <p class='msg-success'><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class='msg-error'><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form action="../controllers/registroCliente.php" method="POST">
        <div style="margin-bottom: 15px;">
            <label for="usuario" class="label-campo">Nombre de Usuario:</label>
            <input type="text" id="usuario" name="usuario" class="campo" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="password" class="label-campo">Contraseña:</label>
            <input type="password" id="password" name="password" class="campo" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="confirmar_password" class="label-campo">Confirmar Contraseña:</label>
            <input type="password" id="confirmar_password" name="confirmar_password" class="campo" required>
        </div>

        <button type="submit">Registrarse</button>
        <a class="btn" href="../views/login.php" style="background: #757575; margin-left: 8px; text-decoration: none; padding: 8px 15px; display: inline-block; color: #fff; border-radius: 4px;">Volver al login</a>
    </form>
</main>

<?php include 'layout/pie.php'; ?>