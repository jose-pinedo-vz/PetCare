<?php

$mcActiva   = $paginaActiva ?? '';
$mcCliente  = $_SESSION['cliente_nombre'] ?? null;
$mcConSesion = $mcCliente !== null;

$mcModulos = [
    'inicio'    => ['Inicio',         'inicio.php',       false],
    'mascotas'  => ['Mis mascotas',   'mis_mascotas.php', true],
    'citas'     => ['Mis citas',      'mis_citas.php',    true],
    'servicios' => ['Servicios',      'servicios.php',    false],
    'equipo'    => ['Nuestro equipo', 'nuestro_equipo.php',       false],
];

function mcEsc(string $texto): string {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}
?>
    <header class="barra-navegacion" id="menu-cliente">
      <a class="barra-navegacion__marca" href="inicio.php">
        <span class="barra-navegacion__logo">
          <img src="../img/logo3.svg" alt="Veterinaria PetCare">
        </span>
        PetCare
      </a>

      <button class="barra-navegacion__boton-menu" id="menu-cliente-toggle" type="button" aria-expanded="false" aria-controls="menu-cliente-contenido">
        <span class="barra-navegacion__boton-icono" aria-hidden="true">☰</span>
        <span class="visualmente-oculto" id="menu-cliente-etiqueta">Abrir menú principal</span>
      </button>

      <div class="barra-navegacion__desplegable" id="menu-cliente-contenido">
        <nav class="barra-navegacion__menu" aria-label="Menú principal">
<?php foreach ($mcModulos as $mcClave => [$mcTexto, $mcEnlace, $mcRequiereSesion]): ?>
<?php   if ($mcRequiereSesion && !$mcConSesion) continue; ?>
<?php   if ($mcEnlace === null): ?>
          <a href="#" class="barra-navegacion__enlace es-deshabilitado" aria-disabled="true"><?= mcEsc($mcTexto) ?></a>
<?php   else: ?>
          <a href="<?= mcEsc($mcEnlace) ?>" class="barra-navegacion__enlace<?= $mcClave === $mcActiva ? ' es-activo' : '' ?>"<?= $mcClave === $mcActiva ? ' aria-current="page"' : '' ?>><?= mcEsc($mcTexto) ?></a>
<?php   endif; ?>
<?php endforeach; ?>
        </nav>

        <div class="barra-navegacion__cuenta">
<?php if ($mcConSesion): ?>
          <span class="barra-navegacion__saludo">Hola, <?= mcEsc($mcCliente) ?></span>
          <a class="boton boton--contorno-claro boton--chico" href="cerrar_sesion.php">Salir</a>
<?php else: ?>
          <a class="boton boton--contorno-claro boton--chico" href="acceso.php">Iniciar sesión</a>
          <a class="boton boton--secundario boton--chico" href="acceso.php#crear-cuenta">Crear cuenta</a>
<?php endif; ?>
        </div>
      </div>
    </header>

    <script>
      (() => {
        const menu = document.getElementById('menu-cliente');
        const boton = document.getElementById('menu-cliente-toggle');
        const etiquetaBoton = document.getElementById('menu-cliente-etiqueta');
        const iconoBoton = boton.querySelector('.barra-navegacion__boton-icono');
        const enlaces = menu.querySelectorAll('.barra-navegacion__desplegable a:not(.es-deshabilitado)');
        const vistaMovil = window.matchMedia('(max-width: 768px)');

        function cerrarMenu() {
          menu.classList.remove('es-abierto');
          boton.setAttribute('aria-expanded', 'false');
          etiquetaBoton.textContent = 'Abrir menú principal';
          iconoBoton.textContent = '☰';
        }

        boton.addEventListener('click', () => {
          const abierto = menu.classList.toggle('es-abierto');
          boton.setAttribute('aria-expanded', String(abierto));
          etiquetaBoton.textContent = abierto ? 'Cerrar menú principal' : 'Abrir menú principal';
          iconoBoton.textContent = abierto ? '×' : '☰';
        });

        enlaces.forEach((enlace) => enlace.addEventListener('click', cerrarMenu));

        menu.querySelectorAll('.es-deshabilitado').forEach((enlace) =>
          enlace.addEventListener('click', (evento) => evento.preventDefault()));

        document.addEventListener('click', (evento) => {
          if (!menu.contains(evento.target)) cerrarMenu();
        });

        document.addEventListener('keydown', (evento) => {
          if (evento.key === 'Escape') cerrarMenu();
        });

        vistaMovil.addEventListener('change', cerrarMenu);
      })();
    </script>
