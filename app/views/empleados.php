<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Empleados</title>
  <link rel="stylesheet" href="css/estilos_base.css">
</head>
<body>

  <header>
    <div class="marca">
      <img src="img/logo.svg" alt="Veterinaria PetCare">
    </div>

    <div class="sesion">
      <span>Empleado: Nombre del Empleado</span>
      <a href="#">Cerrar sesión</a>
    </div>
  </header>

  <nav>
    <ul>
      <li><a href="mascotas.php">Mascotas</a></li>
      <li><a href="citas.php">citas</a></li>
      <li><a href="clientes.php">Clientes</a></li>
      <li><a href="empleados.php" class="activo">Empleados</a></li>

      <li><a href="#" class="deshabilitado">Proveedores</a></li>
      <li><a href="#" class="deshabilitado">Inventario</a></li>
      <li><a href="#" class="deshabilitado">Ventas</a></li>
      <li><a href="#" class="deshabilitado">Servicios</a></li>
      <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
      <li><a href="#" class="deshabilitado">Veterinaria</a></li>
      <li><a href="#" class="deshabilitado">Pagos</a></li>
      <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
    </ul>
  </nav>

  <main>
    <h1>Módulo de Empleados</h1>
    <div style="margin-bottom: 20px;">
      <a class="btn" href="agregar_empleado.php">+ Agregar empleado</a>
    </div>
    <div style="overflow-x: auto; border: 1px solid var(--border-light); border-radius: 6px;">
    <table border="1" style="width: 100%; min-width: 1400px; border-collapse: collapse; text-align: left;">
      <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Calle</th>
                <th>Número exterior</th>
                <th>Número interior</th>
                <th>Colonia</th>
                <th>Ciudad</th>
                <th>Estado</th>
                <th>Código postal</th>
                <th>Puesto</th>
                <th>Especialidad</th>
                <th>Cédula profesional</th>
                <th>Fecha de contratación</th>
                <th>Horario</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
                <tr>
                  <td>1</td>
                  <td>Ana</td>
                  <td>López García</td>
                  <td>4921234567</td>
                  <td>ana.lopez@gmail.com</td>
                  <td>Hidalgo</td>
                  <td>125</td>
                  <td>2</td>
                  <td>Centro</td>
                  <td>Zacatecas</td>
                  <td>Zacatecas</td>
                  <td>98000</td>
                  <td>Veterinaria</td>
                  <td>Medicina veterinaria</td>
                  <td>VET123456</td>
                  <td>2024-02-15</td>
                  <td>08:00 - 16:00</td>
                  <td style="padding: 15px; white-space: nowrap;">
                    <a class="btn" href="editar_empleado.php?id=1">Editar</a>
                    <a class="btn" href="eliminar_empleado.php?id=1" style="margin-left: 6px;">Eliminar</a>
                  </td>
                </tr>
              <tr>
                <td>2</td>
                <td>Carlos</td>
                <td>Martínez Pérez</td>
                <td>4927654321</td>
                <td>carlos.martinez@gmail.com</td>
                <td>Juárez</td>
                <td>230</td>
                <td>4</td>
                <td>La Loma</td>
                <td>Zacatecas</td>
                <td>Zacatecas</td>
                <td>98050</td>
                <td>Veterinario</td>
                <td>Cirugía veterinaria</td>
                <td>VET234567</td>
                <td>2023-08-10</td>
                <td>10:00 - 18:00</td>
                <td style="padding: 15px; white-space: nowrap;">
                  <a class="btn" href="editar_empleado.php?id=2">Editar</a>
                  <a class="btn" href="eliminar_empleado.php?id=2" style="margin-left: 6px;">Eliminar</a>
                </td>
              </tr>
              <tr>
                <td>3</td>
                <td>María</td>
                <td>Hernández Torres</td>
                <td>4929876543</td>
                <td>maria.hernandez@gmail.com</td>
                <td>Morelos</td>
                <td>87</td>
                <td>1</td>
                <td>Las Flores</td>
                <td>Guadalupe</td>
                <td>Zacatecas</td>
                <td>98600</td>
                <td>Recepcionista</td>
                <td>Atención al cliente</td>
                <td>N/A</td>
                <td>2025-01-20</td>
                <td>09:00 - 17:00</td>
                <td style="padding: 15px; white-space: nowrap;">
                  <a class="btn" href="editar_empleado.php?id=3">Editar</a>
                  <a class="btn" href="eliminar_empleado.php?id=3" style="margin-left: 6px;">Eliminar</a>
                </td>
              </tr>
            </tbody>
    </table>
    </div>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>