<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Prueba de menú responsive - PetCare</title>
	<link rel="stylesheet" href="css/estilos_base_v2.css">
	<style>
		.preview-layout {
			--preview-sidebar-width: 230px;
			min-height: 100vh;
		}

		.preview-sidebar {
			position: fixed;
			inset: 0 auto 0 0;
			z-index: 20;
			display: flex;
			width: var(--preview-sidebar-width);
			flex-direction: column;
			overflow-y: auto;
			background: var(--primary-dark);
			color: var(--sidebar-light);
		}

		.preview-brand {
			display: flex;
			justify-content: center;
			padding: 24px 16px;
			border-bottom: 1px solid rgba(255, 255, 255, 0.12);
		}

		.preview-logo {
			display: grid;
			width: 112px;
			height: 112px;
			place-items: center;
			border-radius: 50%;
			background: var(--white);
		}

		.preview-logo img {
			display: block;
			width: 78px;
			height: auto;
		}

		.preview-nav {
			padding: 12px 0;
		}

		.preview-nav ul {
			display: flex;
			flex-direction: column;
			margin: 0;
			padding: 0;
			list-style: none;
		}

		.preview-nav a {
			display: block;
			margin: 2px 12px;
			padding: 12px 16px;
			border-radius: 8px;
			color: var(--sidebar-light);
			font-size: 14px;
			font-weight: bold;
			text-decoration: none;
		}

		.preview-nav a:hover,
		.preview-nav a:focus-visible {
			background: rgba(255, 255, 255, 0.08);
		}

		.preview-nav a[aria-current="page"] {
			background: var(--accent-teal);
			color: var(--white);
		}

		.preview-nav a[aria-disabled="true"] {
			color: rgba(245, 239, 230, 0.42);
			cursor: not-allowed;
		}

		.preview-session {
			margin-top: auto;
			padding: 16px;
			border-top: 1px solid rgba(255, 255, 255, 0.12);
			font-size: 13px;
		}

		.preview-session span {
			display: block;
			margin-bottom: 7px;
		}

		.preview-session a {
			color: var(--white);
			text-decoration: underline;
		}

		.preview-mobile-bar {
			display: none;
		}

		.preview-main {
			min-height: 100vh;
			margin-left: var(--preview-sidebar-width);
			padding: 36px clamp(20px, 5vw, 64px);
		}

		.preview-main h1 {
			margin: 0 0 8px;
			color: var(--primary-dark);
		}

		.preview-intro {
			max-width: 720px;
			margin: 0 0 28px;
			color: var(--texto-suave);
			line-height: 1.6;
		}

		.preview-section {
			max-width: 920px;
			margin-bottom: 22px;
			padding: 22px;
			border: 1px solid var(--border-light);
			border-radius: 10px;
			background: var(--card);
			box-shadow: 0 2px 6px rgba(73, 49, 23, 0.08);
		}

		.preview-section h2 {
			margin: 0 0 10px;
			color: var(--primary-dark);
			font-size: 18px;
		}

		.preview-section p {
			margin: 0;
			line-height: 1.6;
		}

		.preview-badge {
			display: inline-block;
			margin-bottom: 12px;
			padding: 5px 9px;
			border-radius: 20px;
			background: var(--white);
			color: var(--texto-suave);
			font-size: 12px;
			font-weight: bold;
		}

		@media (max-width: 760px) {
			.preview-sidebar {
				display: none;
			}

			.preview-mobile-bar {
				position: fixed;
				inset: 0 0 auto;
				z-index: 30;
				display: flex;
				height: 64px;
				align-items: center;
				justify-content: space-between;
				padding: 0 16px;
				background: var(--primary-dark);
				box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
			}

			.preview-mobile-brand {
				display: flex;
				align-items: center;
				gap: 10px;
				color: var(--white);
				font-weight: bold;
			}

			.preview-mobile-brand img {
				display: block;
				width: 40px;
				height: 40px;
				padding: 3px;
				border-radius: 50%;
				background: var(--white);
			}

			.preview-menu-toggle {
				display: inline-flex;
				min-width: 44px;
				min-height: 44px;
				align-items: center;
				justify-content: center;
				border: 1px solid rgba(255, 255, 255, 0.35);
				border-radius: 8px;
				background: transparent;
				color: var(--white);
				font: inherit;
				font-size: 14px;
				font-weight: bold;
				cursor: pointer;
			}

			.preview-menu-toggle:hover,
			.preview-menu-toggle:focus-visible {
				background: rgba(255, 255, 255, 0.1);
			}

			.preview-mobile-menu {
				position: fixed;
				inset: 64px 0 auto;
				z-index: 25;
				max-height: calc(100dvh - 64px);
				overflow-y: auto;
				background: var(--primary-dark);
				box-shadow: 0 8px 14px rgba(0, 0, 0, 0.22);
			}

			.preview-mobile-menu[hidden] {
				display: none;
			}

			.preview-mobile-menu .preview-nav {
				padding: 8px 0;
			}

			.preview-mobile-menu .preview-nav a {
				padding: 13px 20px;
			}

			.preview-mobile-session {
				padding: 14px 20px 18px;
				border-top: 1px solid rgba(255, 255, 255, 0.12);
				color: var(--sidebar-light);
				font-size: 13px;
			}

			.preview-mobile-session span {
				display: block;
				margin-bottom: 6px;
			}

			.preview-mobile-session a {
				color: var(--white);
				text-decoration: underline;
			}

			.preview-main {
				min-height: 100vh;
				margin-left: 0;
				padding: 88px 16px 32px;
			}

			.preview-section {
				padding: 18px;
			}
		}

		@media (prefers-reduced-motion: no-preference) {
			.preview-mobile-menu {
				animation: preview-menu-appear 150ms ease-out;
			}

			@keyframes preview-menu-appear {
				from {
					opacity: 0;
					transform: translateY(-6px);
				}

				to {
					opacity: 1;
					transform: translateY(0);
				}
			}
		}
	</style>
</head>

<body>
	<div class="preview-layout">
		<aside class="preview-sidebar" aria-label="Menú principal">
			<div class="preview-brand">
				<div class="preview-logo">
					<img src="img/logo3.svg" alt="PetCare">
				</div>
			</div>
			<nav class="preview-nav" aria-label="Módulos">
				<ul>
					<li><a href="#contenido">Inicio</a></li>
					<li><a href="#contenido">Clientes</a></li>
					<li><a href="#contenido">Mascotas</a></li>
					<li><a href="#contenido">Citas</a></li>
					<li><a href="#contenido">Empleados</a></li>
					<li><a href="#contenido">Proveedores</a></li>
					<li><a href="#contenido" aria-current="page">Inventario</a></li>
					<li><a href="#contenido">Ventas</a></li>
					<li><a href="#contenido" aria-disabled="true" tabindex="-1">Servicios</a></li>
					<li><a href="#contenido">Adopción y venta</a></li>
					<li><a href="#contenido" aria-disabled="true" tabindex="-1">Veterinaria</a></li>
					<li><a href="#contenido" aria-disabled="true" tabindex="-1">Pagos</a></li>
					<li><a href="#contenido" aria-disabled="true" tabindex="-1">Pagos con tarjeta</a></li>
					<li><a href="#contenido" aria-disabled="true" tabindex="-1">Configuración</a></li>
				</ul>
			</nav>
			<div class="preview-session">
				<span>Empleado: Nombre del Empleado</span>
				<a href="#contenido">Cerrar sesión</a>
			</div>
		</aside>

		<header class="preview-mobile-bar">
			<div class="preview-mobile-brand">
				<img src="img/logo3.svg" alt="">
				<span>PetCare</span>
			</div>
			<button class="preview-menu-toggle" id="preview-menu-toggle" type="button" aria-expanded="false" aria-controls="preview-mobile-menu">
				<span aria-hidden="true">☰</span>
				<span class="visualmente-oculto">Abrir menú principal</span>
			</button>
		</header>

		<div class="preview-mobile-menu" id="preview-mobile-menu" hidden>
			<nav class="preview-nav" aria-label="Módulos">
				<ul>
					<li><a href="#contenido">Inicio</a></li>
					<li><a href="#contenido">Clientes</a></li>
					<li><a href="#contenido">Mascotas</a></li>
					<li><a href="#contenido">Citas</a></li>
					<li><a href="#contenido">Empleados</a></li>
					<li><a href="#contenido">Proveedores</a></li>
					<li><a href="#contenido" aria-current="page">Inventario</a></li>
					<li><a href="#contenido">Ventas</a></li>
					<li><a href="#contenido" aria-disabled="true" tabindex="-1">Servicios</a></li>
					<li><a href="#contenido">Adopción y venta</a></li>
					<li><a href="#contenido" aria-disabled="true" tabindex="-1">Veterinaria</a></li>
					<li><a href="#contenido" aria-disabled="true" tabindex="-1">Pagos</a></li>
					<li><a href="#contenido" aria-disabled="true" tabindex="-1">Pagos con tarjeta</a></li>
					<li><a href="#contenido" aria-disabled="true" tabindex="-1">Configuración</a></li>
				</ul>
			</nav>
			<div class="preview-mobile-session">
				<span>Empleado: Nombre del Empleado</span>
				<a href="#contenido">Cerrar sesión</a>
			</div>
		</div>

		<main class="preview-main" id="contenido">
			<span class="preview-badge">Vista previa responsive</span>
			<h1>Prueba del menú principal</h1>
			<p class="preview-intro">En una pantalla amplia, el menú permanece fijo a la izquierda. En una pantalla pequeña, usa el botón ☰ para abrir un menú desplegable fijo; si la lista es más alta que la pantalla, puedes desplazarte dentro del menú.</p>

			<section class="preview-section">
				<h2>Pantalla amplia</h2>
				<p>El menú lateral queda fijo mientras te desplazas por esta página. Reduce el ancho de la ventana para probar la versión móvil.</p>
			</section>

			<section class="preview-section">
				<h2>Pantalla pequeña</h2>
				<p>En móvil, la barra superior se mantiene visible. El menú se despliega debajo de ella y tiene su propio desplazamiento para evitar empalmes con el contenido.</p>
			</section>

			<section class="preview-section">
				<h2>Comportamiento</h2>
				<p>El botón informa si el menú está abierto, se puede usar con teclado y al elegir un enlace el desplegable se cierra. Esta página es una prueba aislada; no cambia el menú real de los demás módulos.</p>
			</section>

			<section class="preview-section">
				<h2>Desplazamiento de prueba</h2>
				<p>Este bloque existe para alargar la página y comprobar que el menú permanece fijo mientras recorres el contenido.</p>
			</section>

			<section class="preview-section">
				<h2>Más contenido de prueba</h2>
				<p>Continúa desplazándote. En móvil, abre el menú y prueba que puedas recorrer la lista sin que cubra permanentemente toda la página.</p>
			</section>
		</main>
	</div>

	<script>
		const botonMenu = document.getElementById('preview-menu-toggle');
		const menuMovil = document.getElementById('preview-mobile-menu');

		function cerrarMenu() {
			menuMovil.hidden = true;
			botonMenu.setAttribute('aria-expanded', 'false');
		}

		botonMenu.addEventListener('click', () => {
			const abierto = botonMenu.getAttribute('aria-expanded') === 'true';
			menuMovil.hidden = abierto;
			botonMenu.setAttribute('aria-expanded', String(!abierto));
		});

		menuMovil.querySelectorAll('a:not([aria-disabled="true"])').forEach((enlace) => {
			enlace.addEventListener('click', cerrarMenu);
		});

		document.addEventListener('keydown', (evento) => {
			if (evento.key === 'Escape') cerrarMenu();
		});

		window.matchMedia('(min-width: 761px)').addEventListener('change', cerrarMenu);
	</script>
</body>

</html>