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
$user_edi=UsuariosDB::usuario_ID($idUsuario);
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
                    <input type="hidden" name="id" value="<?= htmlspecialchars($user_edi['id_usuario']) ?>">
                    <div style="margin-bottom: 12px;">
                        <label class="label-campo">Usuario (Obligatorio):</label>
                        <input type="text" name="nuevo_usuario" class="campo" value="<?= htmlspecialchars($user_edi['usuario']) ?>" required>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label class="label-campo">Contraseña (Obligatorio):</label>
                        <input type="password" name="nueva_password" class="campo" value="<?= htmlspecialchars($user_edi['contrasena']) ?>" required>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label class="label-campo">Empleado asociado:</label>
                        <select name="empleado_asociado" class="campo" required>
                            <option value="">Seleccione un empleado</option>
                            <?php if (!empty($empleados_disponibles)): ?>
                                <?php foreach ($empleados_disponibles as $emp): ?>
                                    <option value="<?= htmlspecialchars($emp['id_empleado']); ?>" <?= ($user_edi['empleado_asociado'] === $emp['nombre']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($emp['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No hay empleados disponibles</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label class="label-campo">Rol:</label>
                        <select name="rol" class="campo">
                            <option value="admin"           <?= ($user_edi['rol'] === 'admin') ? 'selected' : '' ?>>Administrador</option>
                            <option value="veterinario"     <?= ($user_edi['rol'] === 'veterinario') ? 'selected' : '' ?>>Veterinario</option>
                            <option value="vendedor"        <?= ($user_edi['rol'] === 'vendedor') ? 'selected' : '' ?>>Vendedor</option>
                            <option value="cliente"         <?= ($user_edi['rol'] === 'cliente') ? 'selected' : '' ?>>Cliente</option>
                            <option value="estilista"       <?= ($user_edi['rol'] === 'estilista') ? 'selected' : '' ?>>Estilista</option>
                            <option value="recepcionista"   <?= ($user_edi['rol'] === 'recepcionista') ? 'selected' : '' ?>>Recepcionista</option>
                            <option value="hotelero"        <?= ($user_edi['rol'] === 'hotelero') ? 'selected' : '' ?>>Hotelero</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label class="label-campo">Permisos:</label>
                        <select name="permisos" class="campo" multiple size="1">
                            <option value="todos"       <?= ($user_edi['permisos'] === 'todos') ? 'selected' : '' ?>>Todo</option>
                            <option value="lectura"     <?= ($user_edi['permisos'] === 'lectura') ? 'selected' : '' ?>>Lectura</option>
                            <option value="modificar"   <?= ($user_edi['permisos'] === 'modificar') ? 'selected' : '' ?>>Modificcar</option>
                            <option value="eliminar"    <?= ($user_edi['permisos'] === 'eliminar') ? 'selected' : '' ?>>Eliminar</option>
                            <option value="insertar"    <?= ($user_edi['permisos'] === 'insertar') ? 'selected' : '' ?>>Insertar</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label class="label-campo">Estado:</label>
                        <select name="estado" class="campo">
                            <option value="activo"      <?= ($user_edi['permisos'] === 'activo') ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo"    <?= ($user_edi['permisos'] === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
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
