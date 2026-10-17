<?php
require_once __DIR__ . '/../models/Cliente.php';
$listaClientes = Cliente::obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Clientes</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'clientes'; include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja">Gestión de Clientes /</div>
      <div class="encabezado-pagina">
        <h1>Clientes</h1>
      </div>

      <div class="tarjeta">
    
    <div class="barra-acciones">
      <a class="btn" href="agregar_cliente.php">+ Agregar cliente</a>
    </div>
    
    <?php 
    $listaClientes = Cliente::obtenerTodos();
    if (empty($listaClientes)): ?>
      <div class="vacio">
        <p>No hay clientes registrados en la base de datos.</p>
        <a class="btn" href="agregar_cliente.php">+ Registrar el primer cliente</a>
      </div>
    <?php else: ?>
      
      <!-- INICIA CONTENEDOR RESPONSIVE -->
      <div class="tabla-envoltura">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Teléfono</th>
              <th>Correo</th>
              <th>Ciudad / Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($listaClientes as $c): ?>
              <tr>
                <td><strong>#<?php echo htmlspecialchars((string)$c['id_cliente']); ?></strong></td>
                <td><?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellido']); ?></td>
                <td><?php echo htmlspecialchars($c['telefono']); ?></td>
                <td><?php echo htmlspecialchars($c['correo']); ?></td>
                <td><?php echo htmlspecialchars($c['ciudad'] . ', ' . $c['estado']); ?></td>
                
                <!-- Celda de acciones con white-space: nowrap; -->
                <td class="acciones">
                  <a class="btn btn-chico" href="editar_cliente.php?id=<?php echo urlencode((string)$c['id_cliente']); ?>">Editar</a>
                  <a class="btn btn-chico btn-info" href="agregar_mascota.php?id_cliente=<?php echo urlencode((string)$c['id_cliente']); ?>">+ Mascota</a>
                  <a class="btn btn-chico btn-aviso" href="agendarCita.php?claveCliente=<?php echo urlencode((string)$c['id_cliente']); ?>">+ Cita</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div> 
      <!-- TERMINA CONTENEDOR RESPONSIVE -->

    <?php endif; ?>
  
      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>