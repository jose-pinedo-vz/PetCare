<?php
$titulo = "Iniciar sesión";
include 'layout/encabezado.php';
?>

<main style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: calc(100vh - 120px); padding: 20px; position: relative;">
    
    <div style="text-align: center; margin-bottom: 25px; opacity: 0.85;">
        <img src="../views/img/logo3.svg" alt="Veterinaria PetCare" style="max-width: 180px; height: auto; display: block; margin: 0 auto 10px;">
        <strong style="font-size: 24px; color: var(--primary-dark);">PetCare</strong>
    </div>

    <div class="tarjeta" style="width: 100%; max-width: 420px; background-color: var(--card); box-shadow: 0 8px 24px rgba(0,0,0,0.08);">
        <h2 style="margin-bottom: 20px; text-align: center; font-size: 20px;">Iniciar sesión</h2>

        <?php if (!empty($error)): ?>
            <p class='msg-error'><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form action="../controllers/Login.php" method="POST">
            <div style="margin-bottom: 15px;">
                <label for="usuario" class="label-campo">Usuario:</label>
                <input type="text" id="usuario" name="usuario" class="campo" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="password" class="label-campo">Contraseña:</label>
                <input type="password" id="password" name="password" class="campo" required>
            </div>

            <button type="submit" style="width: 100%; margin-bottom: 15px;">Ingresar</button>
            
            <div style="text-align: center; font-size: 13px;">
                <p style="margin: 5px 0;">
                    ¿No tienes una cuenta? <a href="registro_cliente.php" class="link-tenue">Regístrate aquí</a>
                </p>
                <p style="margin: 5px 0;">
                    <a href="../views/recuperar_contraseña.php" class="link-tenue">¿Olvidaste tu contraseña?</a>
                </p>
            </div>
        </form>
    </div>
</main>

<?php include 'layout/pie.php'; ?>
<!--  
ALTER TABLE clientes ADD COLUMN token_recuperacion VARCHAR(255) DEFAULT NULL;
ALTER TABLE clientes ADD COLUMN token_expiracion DATETIME DEFAULT NULL;
ALTER TABLE empleados ADD COLUMN token_recuperacion VARCHAR(255) DEFAULT NULL;
ALTER TABLE empleados ADD COLUMN token_expiracion DATETIME DEFAULT NULL;
-->
