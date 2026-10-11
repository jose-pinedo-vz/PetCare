<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Inicio</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>

  <div class="layout">

    <aside class="sidebar">
      <div class="marca">
        <div class="logo-circulo">
          <img src="img/logo3.svg" alt="Veterinaria PetCare">
        </div>
      </div>

      <nav>
        <ul>
          <li><a href="inicio.php" class="activo">Inicio</a></li>
          <li><a href="clientes.php">Clientes</a></li>
          <li><a href="mascotas.php">Mascotas</a></li>
          <li><a href="citas.php">Citas</a></li>
          <li><a href="empleados.php">Empleados</a></li>
          <li><a href="proveedores.php">Proveedores</a></li>
          <li><a href="inventario.php">Inventario</a></li>
          <li><a href="#" class="deshabilitado">Ventas</a></li>
          <li><a href="#" class="deshabilitado">Servicios</a></li>
          <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
          <li><a href="#" class="deshabilitado">Veterinaria</a></li>
          <li><a href="#" class="deshabilitado">Pagos</a></li>
          <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
          <li><a href="#" class="deshabilitado">Configuración</a></li>
        </ul>
      </nav>

      <div class="sesion">
        <span>Empleado: Nombre del Empleado</span>
        <a href="#">Cerrar sesión</a>
      </div>
    </aside>

    <main>
      <div class="migaja">Panel principal /</div>
      <div class="encabezado-pagina">
        <h1>Inicio</h1>
      </div>

      <div class="resumen">
        <div class="resumen-item">
          <span class="resumen-titulo">Citas de hoy</span>
          <span class="resumen-cifra">8</span>
        </div>
        <div class="resumen-item">
          <span class="resumen-titulo">Citas por cobrar</span>
          <span class="resumen-cifra">3</span>
        </div>
        <div class="resumen-item">
          <span class="resumen-titulo">Mascotas registradas</span>
          <span class="resumen-cifra">124</span>
        </div>
        <div class="resumen-item">
          <span class="resumen-titulo">Productos con stock bajo</span>
          <span class="resumen-cifra">5</span>
        </div>
      </div>

      <div class="panel-inicio">

        <div class="tarjeta">
          <h2>Citas de hoy</h2>
          <div class="tabla-envoltura">
            <table>
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Dueño</th>
                  <th>Motivo</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
              <tr>
                <td>2026-10-11 09:00</td>
                <td>Nombre del dueño</td>
                <td>Vacuna</td>
                <td><span class="estado-chip estado-espera">En espera</span></td>
              </tr>
              <tr>
                <td>2026-10-11 10:30</td>
                <td>Nombre del dueño</td>
                <td>Revisión general</td>
                <td><span class="estado-chip estado-atendida">Atendida</span></td>
              </tr>
              <tr>
                <td>2026-10-11 12:00</td>
                <td>Nombre del dueño</td>
                <td>Desparasitación</td>
                <td><span class="estado-chip estado-espera">En espera</span></td>
              </tr>
              <tr>
                <td>2026-10-11 13:30</td>
                <td>Nombre del dueño</td>
                <td>Control de peso</td>
                <td><span class="estado-chip estado-atendida">Atendida</span></td>
              </tr>
              </tbody>
            </table>
          </div>
          <a class="enlace-pie" href="citas.php">Ver todas las citas</a>
        </div>

        <div class="columna-lateral">

          <div class="tarjeta">
            <h2>Accesos rápidos</h2>
            <div class="lista-accesos">
              <a class="btn" href="agendarCita.php">+ Agendar cita</a>
              <a class="btn" href="agregar_mascota.php">+ Agregar mascota</a>
              <a class="btn" href="agregar_cliente.php">+ Agregar cliente</a>
              <a class="btn-secundario" href="inventario.php">Ver inventario</a>
            </div>
          </div>

          <div class="tarjeta">
            <h2>Alertas</h2>
            <div class="alerta-item">5 productos por debajo del stock mínimo</div>
            <div class="alerta-item">3 citas atendidas pendientes de pago</div>
          </div>

        </div>

      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>

</html>
