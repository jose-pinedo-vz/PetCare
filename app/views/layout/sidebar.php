<?php
$sbActiva = $paginaActiva ?? '';

$sbModulos = [
    'inicio'        => ['Inicio',            null],
    'clientes'      => ['Clientes',          'clientes.php'],
    'mascotas'      => ['Mascotas',          'mascotas.php'],
    'citas'         => ['Citas',             'citas.php'],
    'empleados'     => ['Empleados',         'empleados.php'],
    'proveedores'   => ['Proveedores',       'proveedores.php'],
    'inventario'    => ['Inventario',        'inventario.php'],
    'ventas'        => ['Ventas',            null],
    'servicios'     => ['Servicios',         null],
    'adopcion'      => ['Adopción y venta',  null],
    'veterinaria'   => ['Veterinaria',       null],
    'pagos'         => ['Pagos',             null],
    'pagos_tarjeta' => ['Pagos con tarjeta', null],
    'configuracion' => ['Configuración',     null],
];
?>
    <aside class="sidebar">
      <div class="marca">
        <div class="logo-circulo">
          <img src="img/logo3.svg" alt="Veterinaria PetCare">
        </div>
      </div>

      <nav>
        <ul>
<?php foreach ($sbModulos as $sbClave => [$sbTexto, $sbEnlace]): ?>
<?php if ($sbEnlace === null): ?>
          <li><a href="#" class="deshabilitado"><?= $sbTexto ?></a></li>
<?php else: ?>
          <li><a href="<?= $sbEnlace ?>"<?= $sbClave === $sbActiva ? ' class="activo"' : '' ?>><?= $sbTexto ?></a></li>
<?php endif; ?>
<?php endforeach; ?>
        </ul>
      </nav>

      <div class="sesion">
        <span>Empleado: Nombre del Empleado</span>
        <a href="#">Cerrar sesión</a>
      </div>
    </aside>
