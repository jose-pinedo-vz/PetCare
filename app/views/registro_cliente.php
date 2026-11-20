<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$mensaje = $_SESSION['mensaje'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['error']);

$titulo = "Registro de cuenta";
include 'layout/encabezado.php';
?>

<main style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: calc(100vh - 120px); padding: 20px; position: relative;">

    <div style="text-align: center; margin-bottom: 25px; opacity: 0.85;">
        <img src="../views/img/logo3.svg" alt="Veterinaria PetCare" style="max-width: 180px; height: auto; display: block; margin: 0 auto 10px;">
    </div>

    <div class="tarjeta" style="width: 100%; max-width: 420px; background-color: var(--card); box-shadow: 0 8px 24px rgba(0,0,0,0.08);">
        <h2 style="margin-bottom: 20px; text-align: center; font-size: 20px;">Crear una nueva cuenta</h2>

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

            <div style="margin-bottom: 20px;">
                <label for="confirmar_password" class="label-campo">Confirmar Contraseña:</label>
                <input type="password" id="confirmar_password" name="confirmar_password" class="campo" required>
            </div>

            <button type="submit" style="width: 100%; margin-bottom: 15px;">Registrarse</button>

            <div style="text-align: center; font-size: 13px;">
                <p style="margin: 5px 0;">
                    ¿Ya tienes una cuenta? <a href="login.php" class="link-tenue">Inicia sesión aquí</a>
                </p>
            </div>
        </form>
    </div>
</main>

<?php include 'layout/pie.php'; ?>