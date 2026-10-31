<?php
$sbActiva = $paginaActiva ?? '';

$sbModulos = [
    'inicio'        => ['Inicio',            'inicio.php'],
    'clientes'      => ['Clientes',          'clientes.php'],
    'mascotas'      => ['Mascotas',          'mascotas.php'],
    'citas'         => ['Citas',             'citas.php'],
    'empleados'     => ['Empleados',         'empleados.php'],
    'proveedores'   => ['Proveedores',       'proveedores.php'],
    'inventario'    => ['Inventario',        'inventario.php'],
    'ventas'        => ['Ventas',            'Venta.php'],
    'servicios'     => ['Servicios',         'servicios.php'],
    'adopcion'      => ['Adopción y venta',  'adopcion_venta.php'],
    'veterinaria'   => ['Veterinaria',       null],
    'pagos'         => ['Pagos',             null],
    'pagos_tarjeta' => ['Pagos con tarjeta', null],
    'usuarios'      => ['Usuarios',          'usuarios.php'],
    'configuracion' => ['Configuración',     null],
];
?>
    <aside class="sidebar" id="menu-principal">
      <div class="marca">
        <div class="logo-circulo">
          <img src="img/logo3.svg" alt="Veterinaria PetCare">
        </div>
        <button class="menu-toggle" id="menu-principal-toggle" type="button" aria-expanded="false" aria-controls="menu-principal-links">
          <span class="menu-toggle-icon" aria-hidden="true">☰</span>
          <span class="visualmente-oculto" id="menu-principal-etiqueta">Abrir menú principal</span>
        </button>
      </div>

      <nav id="menu-principal-links" aria-label="Módulos">
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

    <script>
      (() => {
        const menu = document.getElementById('menu-principal');
        const boton = document.getElementById('menu-principal-toggle');
        const etiquetaBoton = document.getElementById('menu-principal-etiqueta');
        const iconoBoton = boton.querySelector('.menu-toggle-icon');
        const enlaces = menu.querySelectorAll('nav a:not(.deshabilitado)');
        const vistaMovil = window.matchMedia('(max-width: 768px)');

        function cerrarMenu() {
          menu.classList.remove('menu-abierto');
          boton.setAttribute('aria-expanded', 'false');
          etiquetaBoton.textContent = 'Abrir menú principal';
          iconoBoton.textContent = '☰';
        }

        boton.addEventListener('click', () => {
          const abierto = menu.classList.toggle('menu-abierto');
          boton.setAttribute('aria-expanded', String(abierto));
          etiquetaBoton.textContent = abierto ? 'Cerrar menú principal' : 'Abrir menú principal';
          iconoBoton.textContent = abierto ? '×' : '☰';
        });

        enlaces.forEach((enlace) => enlace.addEventListener('click', cerrarMenu));

        document.addEventListener('click', (evento) => {
          if (!menu.contains(evento.target)) cerrarMenu();
        });

        document.addEventListener('keydown', (evento) => {
          if (evento.key === 'Escape') cerrarMenu();
        });

        vistaMovil.addEventListener('change', cerrarMenu);
      })();
    </script>
