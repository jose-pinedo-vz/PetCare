<?php
function mostrarEncabezado( string $titulo)
{
    echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>$titulo</title></head><body>";
}

function mostrarPie()
{
    echo "</body></html>";
}

function vistaLogin(string $error="")
{
    mostrarEncabezado("Iniciar Sesión");
    ?>
    <h2>Iniciar Sesión</h2>

    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>    

    <form action="Login.php?action=login" method="POST">
        <input type="hidden" name="action" value="login">
        <label>Usuario:</label><br>
        <input type="text" name="usuario" required><br><br>
        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>
        <button type="submit">Ingresar</button>
    </form>

    <?php
    mostrarPie();
}

function vistaBienvenido(string $usuario, string $rol)
{
    mostrarEncabezado("Bienvenido");
    ?>

    <h1>¡Bienvenido, <?php echo htmlspecialchars($usuario); ?>!</h1>
    <hr>
    <p>Rol: <strong><?php echo htmlspecialchars($rol); ?></strong></p>
    <hr>
    <?php if($rol==='admin'): ?>

        <div style="background-color: #f0f0f0; padding: 15px; margin: 15px 0; border-radius: 5px;">
            <h3>Opciones de Administrador</h3>
            <a href="Login.php?action=registro">► Registrar nuevo Usuario</a><br>
            <a href="Login.php?action=modificar">► Modificar Cliente</a>
        </div>
    
    <?php elseif($rol==='usuario'): ?>

        <div style="background-color: #f0f0f0; padding: 15px; margin: 15px 0; border-radius: 5px;">
            <h3>Opciones de Usuario</h3>
            <a>► Nada ☻</a>
        </div>

    <?php endif; ?>

    <hr>
    <a href="Login.php?action=logout">Cerrar Sesión</a>
    <?php
    mostrarPie();
}

function vistaRegistro(string $mensaje="", string $error="")
{
    mostrarEncabezado("Registrar Usuario");
    ?>

    <h2>Registrar Nuevo Usuario</h2>
    <a href="Login.php?action=bienvenido">← Volver al inicio</a>
    <hr>

    <?php

        if ($mensaje) echo "<p style='color:green;'>$mensaje</p>"; 
        if ($error) echo "<p style='color:red;'>$error</p>"; 
    
    ?>

    <form action="Login.php?action=registro" method="POST">
        <input type="hidden" name="action" value="registro">

        <label>Usuario (Obligatorio):</label><br>
        <input type="text" name="nuevo_usuario" required><br><br>

        <label>Contraseña (Obligatorio):</label><br>
        <input type="password" name="nueva_password" required><br><br>

        <label>Empleado Asociado:</label><br>
        <input type="text" name="empleado_asociado"><br><br>

        <label>Rol:</label><br>
        <select name="rol">
            <option value="usuario">Usuario Estándar</option>
            <option value="admin">Administrador</option>
        </select><br><br>

        <label>Permisos:</label><br>
        <select name="permisos">
            <option value="todos">Todo</option>
            <option value="lectura">Lecturas</option>
            <option value="venta">Ventas</option>
            <option value="reporte">Reportes</option>
        </select><br><br>

        <label>Estado:</label><br>
        <select name="estado">
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
        </select><br><br>

        <button type="submit">Guardar Usuario</button>
    </form>

    <?php

    mostrarPie();
}

function vistaModificar(string $mensaje="", string $error="")
{
    mostrarEncabezado("Modificar Cliente");
    ?>

    <h2>Modificar Cliente</h2>
    <a href="Login.php?action=bienvenido">← Volver al inicio</a>
    <hr>

    <?php

        if ($mensaje) echo "<p style='color:green;'>$mensaje</p>"; 
        if ($error) echo "<p style='color:red;'>$error</p>"; 
    
    ?>

    <form action="Login.php?action=modificar" method="POST">
        <input type="hidden" name="action" value="modificar">

        <label>Nombre del Cliente:</label><br>
        <input type="text" name="nombre_cliente" required><br><br>
        
    </form>

    <?php

    mostrarPie();
}

?>