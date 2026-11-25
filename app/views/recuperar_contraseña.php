<?php
if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}
$titulo = "Recuperar Contraseña";
include 'layout/encabezado.php';
$error = $_SESSION['error'] ?? '';
$mensaje = $_SESSION['mensaje'] ?? '';
unset($_SESSION['error'], $_SESSION['mensaje']);
?>

<main style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: calc(100vh - 120px); padding: 20px;">
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="../views/img/logo3.svg" alt="Veterinaria PetCare" style="max-width: 150px; height: auto; display: block; margin: 0 auto 8px;">
    </div>
    <div class="tarjeta" style="width: 100%; max-width: 420px; background-color: var(--card); box-shadow: 0 8px 24px rgba(0,0,0,0.08);">
        <h2 style="margin-bottom: 8px; text-align: center; font-size: 20px;">Recuperar contraseña</h2>
        <p style="text-align: center; color: var(--texto-suave); font-size: 13px; margin-bottom: 20px;">Ingresa tu correo electrónico registrado</p>
        <?php if (!empty($mensaje)): ?>
            <p style="color: #2e7d32; background: #e8f5e9; border: 1px solid #c8e6c9; padding: 10px; border-radius: 6px; font-size: 13px; margin-bottom: 15px; text-align: center; font-weight: bold;"><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class='msg-error' style="margin-bottom: 15px;">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        <form action="../controllers/procesar_recuperacion.php" method="POST">
            <div style="margin-bottom: 20px;">
                <label for="correo" class="label-campo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" class="campo" required>
            </div>
            <button type="submit" style="width: 100%; margin-bottom: 15px;">Enviar enlace de recuperación</button>
            <div style="text-align: center; font-size: 13px;">
                <p style="margin: 0;">
                    <a href="login.php" class="link-tenue">Volver al inicio de sesión</a>
                </p>
            </div>
        </form>
    </div>
</main>
<?php include 'layout/pie.php'; ?>