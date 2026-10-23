<?php
// Mostrar nombre del usuario
if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}
require_once __DIR__ . '/../models/usuarios_db.php';
$empleados_disponibles=UsuariosDB::empleados_disponibles();
$mensaje=$_SESSION['mensaje']??'';
$error=$_SESSION['error']??'';
unset($_SESSION['mensaje'],$_SESSION['error']);
$idUsuario = filter_input(INPUT_GET, 'id_usuario', FILTER_VALIDATE_INT);
if ($idUsuario === false || $idUsuario === null)
{
    header('Location: usuarios.php');
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
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>

    <div class="layout">
        <?php $paginaActiva = 'usuarios'; include __DIR__ . '/layout/sidebar.php'; ?>
        <main>
            <div class="migaja"><a href="usuarios.php" class="link-tenue">Gestión de usuarios</a> / Agregar usuarios</div>
            <div class="encabezado-pagina">
            <h1>Editar usuarios</h1>
            </div>

            <div class="tarjeta">
                <?php if (!empty($mensaje)): ?>
                    <p class='msg-success'><?= htmlspecialchars($mensaje) ?></p>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <p class='msg-error'><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <form action="../controllers/editar_usuario.php" method="POST">
                    <input type="hidden" name="id" value="<?= $idUsuario ?>">
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
                        <select name="empleado_asociado" class="campo" required>
                            <option value="">Seleccione un empleado</option>
                            <?php if (!empty($empleados_disponibles)): ?>
                                <?php foreach ($empleados_disponibles as $emp): ?>
                                    <option value="<?= htmlspecialchars($emp['id_empleado']); ?>">
                                        <?= htmlspecialchars($emp['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No hay empleados disponibles sin usuario</option>
                            <?php endif; ?>
                        </select>
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

                    <button type="submit">Editar Usuario</button>
                    <a class="btn" href="../views/usuarios.php" style="background: #757575; margin-left: 8px;">Cancelar</a>
                </form>
            </div>
        </main>
    </div>
  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>
