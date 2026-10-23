<?php
require_once __DIR__ . '/../models/usuarios_db.php';
$listaUsuarios = UsuariosDB::obtenerTodosUsuarios(ConexionDB::obtenerConexion());
// Mostrar nombre del usuario
if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}
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
          <div class="tabla-envoltura">
            <table border="1" style="width: 100%; border-collapse: collapse; text-align: left; background: #fff;">
              <thead>
                <tr style="background: #f5f5f5;">
                  <th style="padding: 10px;">ID</th>
                  <th style="padding: 10px;">Usuario</th>
                  <th style="padding: 10px;">Empleado Asociado</th>
                  <th style="padding: 10px;">Rol</th>
                  <th style="padding: 10px;">Permisos</th>
                  <th style="padding: 10px;">Fecha de creación</th>
                  <th style="padding: 10px;">Último acceso</th>
                  <th style="padding: 10px;">Estado</th>
                  <th style="padding: 10px; text-align: center;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($listaUsuarios as $u): ?>
                  <tr>
                    <td style="padding: 10px;"><?php echo htmlspecialchars((string)$u['id_usuario']); ?></td>
                    <td style="padding: 10px;"><?php echo htmlspecialchars($u['usuario']); ?></td>
                    <td style="padding: 10px;"><?php echo htmlspecialchars($u['empleado_asociado']); ?></td>
                    <td style="padding: 10px;"><?php echo htmlspecialchars($u['rol']); ?></td>
                    <td style="padding: 10px;"><?php echo htmlspecialchars($u['permisos']); ?></td>
                    <td style="padding: 10px;"><?php echo htmlspecialchars($u['fecha_creacion']); ?></td>
                    <td style="padding: 10px;"><?php echo htmlspecialchars($u['ultimo_acceso']); ?></td>
                    <td style="padding: 10px;"><?php echo htmlspecialchars($u['estado']); ?></td>

                    <!-- Celda de acciones con white-space: nowrap; -->
                    <td style="padding: 10px; text-align: center; white-space: nowrap;">
                      <a class="btn" href="editar_usuarios.php?id_usuario=<?= $u['id_usuario'] ?>">Editar</a>
                      <a class="btn" href="../controllers/eliminar_usuario.php?id=<?= $u['id_usuario'] ?>" style="margin-left: 6px;" onclick="return confirm('¿Seguro que quieres eliminar este usuario?');">Eliminar</a>
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