<?php
$titulo = "Bienvenido";
include 'layout/encabezado.php';
?>

<main>
    <h1>¡Bienvenido, <?= htmlspecialchars($usuario); ?>!</h1>
    <hr style="border: 0; border-top: 1px solid var(--border-light); margin: 20px 0;">
    <p style="text-align: center;">Rol actual: <strong><?= htmlspecialchars($rol); ?></strong></p>
    <hr style="border: 0; border-top: 1px solid var(--border-light); margin: 20px 0;">

    <?php if ($rol === 'admin'): ?>
        <div class="panel-opciones">
            <h3>Opciones de Administrador</h3>
            <a href="Registro.php" class="btn-link">► Registrar nuevo Usuario</a>
            <a href="Modificar.php" class="btn-link">► Modificar Cliente</a>
        </div>
    <?php elseif ($rol === 'usuario'): ?>
        <div class="panel-opciones">
            <h3>Opciones de Usuario</h3>
            <p>No hay acciones disponibles por el momento.</p>
        </div>
    <?php endif; ?>

    <hr style="border: 0; border-top: 1px solid var(--border-light); margin: 20px 0;">
    <div style="text-align: center;">
        <a href="Login.php?action=logout" class="btn-logout">Cerrar Sesión</a>
    </div>
</main>

<?php include 'layout/pie.php'; ?>
