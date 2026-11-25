<?php
if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}
if (!isset($_SESSION['usuario']))
{
  header("Location: ../views/login.php");
  exit();
}
if (strtolower((string)($_SESSION['rol'] ?? '')) !== 'admin')
{
  header("Location: inicio.php");
  exit();
}
require_once __DIR__ . '/../models/usuarios_db.php';
$usuario=$_SESSION['usuario'];

$filtroTipo     = $_GET['tipo'] ?? 'todos';
$filtroRol      = $_GET['rol'] ?? 'todos';
$filtroEstado   = $_GET['estado'] ?? 'todos';
$filtroBusqueda = trim((string)($_GET['q'] ?? ''));
$listaUsuarios = UsuariosDB::obtenerTodosUsuarios(ConexionDB::obtenerConexion(),$filtroTipo,$filtroRol,$filtroEstado,$filtroBusqueda);
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
      <div class="migaja">Gestión de usuarios /</div>
      <div class="encabezado-pagina">
          <h1>Modulo de usuarios</h1>
      </div>
      <?php
      if (empty($listaUsuarios)): ?>
        <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center;">
          <p style="font-size: 16px; color: #666; margin-bottom: 15px;">No hay usuarios registrados en la base de datos.</p>
          <a class="btn" href="registro_usuario.php">+ Registrar el primer usuario</a>
        </div>
      <?php else: ?>
        
        <div class="tarjeta">
          <div class="barra-acciones">
              <a class="btn" href="registro_usuario.php">+ Agregar usuario</a>
          </div>
          <form method="GET" action="usuarios.php" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: end; margin-bottom: 16px;">
            <div>
              <label for="tipo" style="display: block; font-size: 13px;">Origen</label>
              <select name="tipo" id="tipo" style="padding: 6px 10px;">
                <option value="todos" <?= $filtroTipo === 'todos' ? 'selected' : '' ?>>Todos</option>
                <option value="sistema" <?= $filtroTipo === 'sistema' ? 'selected' : '' ?>>Sistema (Personal)</option>
                <option value="cliente" <?= $filtroTipo === 'cliente' ? 'selected' : '' ?>>Clientes</option>
              </select>
            </div>

            <div>
              <label for="rol" style="display: block; font-size: 13px;">Rol</label>
              <select name="rol" id="rol" style="padding: 6px 10px;">
                <option value="todos" <?= $filtroRol === 'todos' ? 'selected' : '' ?>>Todos los roles</option>
                <option value="cliente" <?= $filtroRol === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                <option value="admin" <?= $filtroRol === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="veterinario" <?= $filtroRol === 'veterinario' ? 'selected' : '' ?>>Veterinario</option>
                <option value="vendedor" <?= $filtroRol === 'vendedor' ? 'selected' : '' ?>>Vendedor</option>
                <option value="estilista" <?= $filtroRol === 'estilista' ? 'selected' : '' ?>>Estilista</option>
                <option value="recepcionista" <?= $filtroRol === 'recepcionista' ? 'selected' : '' ?>>Recepcionista</option>
                <option value="hotelero" <?= $filtroRol === 'hotelero' ? 'selected' : '' ?>>Hotelero</option>
                <option value="cajero" <?= $filtroRol === 'cajero' ? 'selected' : '' ?>>Cajero</option>
              </select>
            </div>

            <div>
              <label for="estado" style="display: block; font-size: 13px;">Estado</label>
              <select name="estado" id="estado" style="padding: 6px 10px;">
                <option value="todos" <?= $filtroEstado === 'todos' ? 'selected' : '' ?>>Todos</option>
                <option value="activo" <?= $filtroEstado === 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= $filtroEstado === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
              </select>
            </div>

            <div>
              <label for="q" style="display: block; font-size: 13px;">Buscar</label>
              <input type="text" name="q" id="q" value="<?= htmlspecialchars($filtroBusqueda) ?>" placeholder="Usuario o nombre..." style="padding: 6px 10px;">
            </div>

            <div style="display: flex; gap: 8px;">
              <button type="submit" class="btn btn-chico">Filtrar</button>
              <a href="usuarios.php" class="btn btn-chico">Limpiar</a>
            </div>
          </form>
          <div class="tabla-envoltura">
            <table border="1" style="width: 100%; border-collapse: collapse; text-align: left; background: #fff;">
              <thead>
                <tr style="background: #f5f5f5;">
                  <th style="padding: 10px;">ID</th>
                  <th style="padding: 10px;">Usuario</th>
                  <th style="padding: 10px;">Empleado Asociado</th>
                  <th style="padding: 10px;">Rol</th>
                  <th style="padding: 10px;">Fecha de creación</th>
                  <th style="padding: 10px;">Último acceso</th>
                  <th style="padding: 10px;">Estado</th>
                  <th style="padding: 10px; text-align: center;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($listaUsuarios as $u): ?>
                  <?php $esCliente = (($u['origen'] ?? 'sistema') === 'cliente'); ?>
                  <tr>
                    <td style="padding: 10px;"><?= htmlspecialchars((string)($u['id_registro'] ?? '')) ?></td>
                    <td style="padding: 10px;"><?= htmlspecialchars((string)($u['usuario'] ?? '')) ?></td>
                    <td style="padding: 10px;"><?= htmlspecialchars((string)($u['nombre_asociado'] ?? '')) ?></td>
                    <td style="padding: 10px;"><?= htmlspecialchars((string)($u['rol'] ?? '')) ?></td>
                    <td style="padding: 10px;"><?= htmlspecialchars((string)($u['fecha_creacion'] ?? '')) ?></td>
                    <td style="padding: 10px;"><?= htmlspecialchars((string)($u['ultimo_acceso'] ?? '—')) ?></td>
                    <td style="padding: 10px;"><?= htmlspecialchars((string)($u['estado'] ?? '')) ?></td>
                    <td style="padding: 10px; text-align: center; white-space: nowrap;">
                      <?php if (!$esCliente): ?>
                        <a class="btn btn-chico" href="editar_usuarios.php?id_usuario=<?= urlencode((string)$u['id_registro']) ?>">Editar</a>
                        <a class="btn btn-chico btn-peligro" href="../controllers/eliminar_usuario.php?id=<?= urlencode((string)$u['id_registro']) ?>" style="margin-left: 6px;" onclick="return confirm('¿Seguro que quieres eliminar este usuario?');">Eliminar</a>
                      <?php else: ?>
                        <span style="font-size: 12px; opacity: 0.75;">Cuenta de cliente</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div> 
        </div>  

      <?php endif; ?>
    </main>
  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>