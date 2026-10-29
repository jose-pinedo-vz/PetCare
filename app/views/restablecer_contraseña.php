<?php
require_once __DIR__ . '/../models/usuarios_db.php';

$token = $_GET['token'] ?? '';
$error = '';
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

<main>
    <h2>Restablecer Contraseña</h2>

    <?php if (!empty($error)): ?>
        <p class='msg-error'><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <?php if ($valido): ?>
        <form action="../controllers/actualizar_contraseña.php" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($usuario['tipo'], ENT_QUOTES, 'UTF-8'); ?>">
            
            <div style="margin-bottom: 15px;">
                <label for="nombre_usuario" class="label-campo">Usuario:</label>
                <input id="nombre_usuario" name="nombre_usuario" class="campo" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="nuevo_password" class="label-campo">Nueva contraseña:</label>
                <input type="password" id="nuevo_password" name="nuevo_password" class="campo" required>
            </div>

            <button type="submit">Actualizar contraseña</button>
        </form>
    <?php endif; ?>
</main>

<?php include 'layout/pie.php'; ?>