<?php
if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}
require_once __DIR__ . '/../models/usuarios_db.php';

$token = $_GET['token'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
$valido = false;

if (!empty($token))
{
    $usuario = UsuariosDB::buscarToken($token);
    if ($usuario)
    {
        $valido = true;
    }
    else
    {
        $error = "El enlace es inválido o ha expirado.";
    }
}
else
{
    $error = "No se proporcionó ningún token.";
}

$titulo = "Restablecer Contraseña";
include 'layout/encabezado.php';
?>

<main style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: calc(100vh - 120px); padding: 20px;">
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="../views/img/logo3.svg" alt="Veterinaria PetCare" style="max-width: 150px; height: auto; display: block; margin: 0 auto 8px;">
        <strong style="font-size: 22px; color: var(--primary-dark);">PetCare</strong>
    </div>
    <div class="tarjeta" style="width: 100%; max-width: 420px; background-color: var(--card); box-shadow: 0 8px 24px rgba(0,0,0,0.08);">
        <h2 style="margin-bottom: 20px; text-align: center; font-size: 20px;">Restablecer Contraseña</h2>
        <?php if (!empty($error)): ?>
            <div class='msg-error' style="margin-bottom: 15px;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($valido): ?>
            <form action="../controllers/actualizar_contraseña.php" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($usuario['tipo'], ENT_QUOTES, 'UTF-8'); ?>">
                <div style="margin-bottom: 15px;">
                    <label for="nombre_usuario" class="label-campo">Usuario:</label>
                    <input id="nombre_usuario" name="nombre_usuario" class="campo" required>
                </div>
                <div style="margin-bottom: 20px;">
                    <label for="nuevo_password" class="label-campo">Nueva contraseña:</label>
                    <input type="password" id="nuevo_password" name="nuevo_password" class="campo" required>
                </div>
                <button type="submit" style="width: 100%;">Actualizar contraseña</button>
            </form>
        <?php endif; ?>
    </div>
</main>
<?php include 'layout/pie.php'; ?>