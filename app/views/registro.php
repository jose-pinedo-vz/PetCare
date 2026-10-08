<?php
require_once __DIR__ . '/../models/usuarios_db.php';
$listaUsuarios = UsuariosDB::obtenerTodosUsuarios(ConexionDB::obtenerConexion());
// Mostrar nombre del usuario
session_start();
if (!isset($_SESSION['usuario']))
{
  header("Location: ../views/login.php");
  exit();
}
$usuario=$_SESSION['usuario'];
// Mostrar nombre del usuario
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Usuarios</title>
  <link rel="stylesheet" href="css/estilos_base.css">
</head>
<body>

  <header>
    <div class="marca">
      <img src="img/logo3.svg" alt="Veterinaria PetCare">
    </div>

    <div class="sesion">
      <!-- Mostrar nombre del usuario -->
      <span>Empleado: <?php echo htmlspecialchars($usuario); ?></span>
      <a href="../controllers/Login.php?action=logout" class="btn-logout">Cerrar Sesión</a>
      <!-- Mostrar nombre del usuario -->
    </div>
  </header>

  <nav>
    <ul>
      <li><a href="mascotas.php">Mascotas</a></li>
      <li><a href="citas.php">Citas</a></li>
      <li><a href="clientes.php" class="activo">Clientes</a></li>
      <li><a href="empleados.php">Empleados</a></li>
      <li><a href="proveedores.php">Proveedores</a></li>

      
      <li><a href="#" class="deshabilitado">Inventario</a></li>
      <li><a href="#" class="deshabilitado">Ventas</a></li>
      <li><a href="#" class="deshabilitado">Servicios</a></li>
      <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
      <li><a href="#" class="deshabilitado">Veterinaria</a></li>
      <li><a href="#" class="deshabilitado">Pagos</a></li>
      <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
      <!-- Usuarios para administradores -->
      <?php if ($_SESSION['rol'] === 'admin'): ?>
        <li><a href="usuarios.php">Usuarios</a></li>
      <?php endif; ?>
      <!-- Usuarios para administradores -->

    </ul>
  </nav>
  <main>
    <h2>Registrar Nuevo Usuario</h2>
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
                <option value="admin">Administrador</option>
                <option value="veterinario">Veterinario</option>
                <option value="vendedor">Vendedor</option>
                <option value="cliente">Cliente</option>
                <option value="estilista">Estilista</option>
                <option value="recepcionista">Recepcionista</option>
                <option value="hotelero">Hotelero</option>
            </select>
        </div>

        <div style="margin-bottom: 12px;">
            <label class="label-campo">Permisos:</label>
            <select name="permisos" class="campo" multiple size="1">
                <option value="todos">Todo</option>
                <option value="lectura">Lectura</option>
                <option value="modificar">Modificcar</option>
                <option value="eliminar">Eliminar</option>
                <option value="insertar">Insertar</option>
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
        <a class="btn" href="usuarios.php" style="background: #757575; margin-left: 8px;">Cancelar</a>
    </form>
  </main> 
  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>