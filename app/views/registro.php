<?php 
$titulo = "Registrar Usuario";
include 'layout/encabezado.php'; 
?>

<main>
    <h2>Registrar Nuevo Usuario</h2>
    <a href="Bienvenido.php" style="color: var(--accent-teal); text-decoration: underline;">← Volver al inicio</a>
    <hr style="border: 0; border-top: 1px solid var(--border-light); margin: 20px 0;">

    <?php if (!empty($mensaje)): ?>
        <p class='msg-success'><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <p class='msg-error'><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="Registro.php" method="POST">
        <div style="margin-bottom: 12px;">
            <label class="label-campo">Usuario (Obligatorio):</label>
            <input type="text" name="nuevo_usuario" class="campo" required>
        </div>

        <div style="margin-bottom: 12px;">
            <label class="label-campo">Contraseña (Obligatorio):</label>
            <input type="password" name="nueva_password" class="campo" required>
        </div>

        <div style="margin-bottom: 12px;">
            <label class="label-campo">Empleado Asociado:</label>
            <input type="text" name="empleado_asociado" class="campo">
        </div>

        <div style="margin-bottom: 12px;">
            <label class="label-campo">Rol:</label>
            <select name="rol" class="campo">
                <option value="usuario">Usuario Estándar</option>
                <option value="admin">Administrador</option>
            </select>
        </div>

        <div style="margin-bottom: 12px;">
            <label class="label-campo">Permisos:</label>
            <select name="permisos" class="campo">
                <option value="todos">Todo</option>
                <option value="lectura">Lecturas</option>
                <option value="venta">Ventas</option>
                <option value="reporte">Reportes</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label class="label-campo">Estado:</label>
            <select name="estado" class="campo">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>

        <button type="submit">Guardar Usuario</button>
    </form>
</main>

<?php include 'layout/pie.php'; ?>