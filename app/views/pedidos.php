<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Veterinaria PetCare - Pedidos pendientes</title>
	<link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>
	<div class="layout">
		<?php $paginaActiva = 'inventario'; include __DIR__ . '/layout/sidebar.php'; ?>

		<main>
			<div class="migaja">Gestión de inventario</div>
			<div class="encabezado-pagina">
				<h1>Control de inventario</h1>
			</div>

			<nav class="inventario-pestanas" aria-label="Secciones de inventario">
				<a href="inventario.php">1. Catálogo de productos</a>
				<a href="lotes.php#lotes">2. Gestión de lotes</a>
				<a href="#pedidos" class="activa" aria-current="page">3. Pedidos pendientes</a>
			</nav>

			<section id="pedidos" aria-label="Pedidos pendientes">
				<div class="inventario-pedidos-toolbar">
					<label class="visualmente-oculto" for="buscar-pedido">Buscar pedidos</label>
					<input id="buscar-pedido" class="campo" type="search" placeholder="Buscar por proveedor, ID pedido...">
					<button class="btn" type="button">+ Nuevo lote</button>
				</div>

				<div class="inventario-pedidos-grid" id="lista-pedidos">
					<article class="pedido-tarjeta" data-pedido>
						<header class="pedido-encabezado">
							<span class="pedido-icono" aria-hidden="true">OC</span>
							<div>
								<span class="pedido-etiqueta">Orden de compra</span>
								<h2>ORD-001</h2>
							</div>
							<span class="historial-estado pendiente">Pendiente</span>
						</header>
						<a class="pedido-proveedor" href="#pedido-001">
							<span>Proveedor</span>
							<strong>Distribuidores Caninos</strong>
							<span class="pedido-flecha" aria-hidden="true">›</span>
						</a>
						<div class="pedido-fechas">
							<div><span>Fecha de solicitud</span><time datetime="2026-10-15">2026-10-15</time></div>
							<div><span>Fecha esperada</span><time datetime="2026-10-20">2026-10-20</time></div>
						</div>
						<div class="pedido-total"><span>Total estimado</span><strong>$1,500.00</strong></div>
						<div class="pedido-acciones">
							<button class="btn pedido-registrar" type="button">Registrar pedido</button>
							<button class="btn-secundario" type="button">Ver detalles</button>
							<button class="btn btn-peligro pedido-eliminar" type="button" aria-label="Eliminar ORD-001">Eliminar</button>
						</div>
					</article>

					<article class="pedido-tarjeta" data-pedido>
						<header class="pedido-encabezado">
							<span class="pedido-icono" aria-hidden="true">OC</span>
							<div>
								<span class="pedido-etiqueta">Orden de compra</span>
								<h2>ORD-002</h2>
							</div>
							<span class="historial-estado pagada">Confirmado</span>
						</header>
						<a class="pedido-proveedor" href="#pedido-002">
							<span>Proveedor</span>
							<strong>Distribuidores Caninos</strong>
							<span class="pedido-flecha" aria-hidden="true">›</span>
						</a>
						<div class="pedido-fechas">
							<div><span>Fecha de solicitud</span><time datetime="2026-10-20">2026-10-20</time></div>
							<div><span>Fecha esperada</span><time datetime="2026-10-25">2026-10-25</time></div>
						</div>
						<div class="pedido-total"><span>Total estimado</span><strong>$1,500.00</strong></div>
						<div class="pedido-acciones">
							<button class="btn pedido-registrar" type="button">Registrar pedido</button>
							<button class="btn-secundario" type="button">Ver detalles</button>
							<button class="btn btn-peligro pedido-eliminar" type="button" aria-label="Eliminar ORD-002">Eliminar</button>
						</div>
					</article>

					<article class="pedido-tarjeta" data-pedido>
						<header class="pedido-encabezado">
							<span class="pedido-icono" aria-hidden="true">OC</span>
							<div>
								<span class="pedido-etiqueta">Orden de compra</span>
								<h2>ORD-003</h2>
							</div>
							<span class="historial-estado cancelada">Cancelado</span>
						</header>
						<a class="pedido-proveedor" href="#pedido-003">
							<span>Proveedor</span>
							<strong>Distribuidores Caninos</strong>
							<span class="pedido-flecha" aria-hidden="true">›</span>
						</a>
						<div class="pedido-fechas">
							<div><span>Fecha de solicitud</span><time datetime="2026-10-20">2026-10-20</time></div>
							<div><span>Fecha esperada</span><time datetime="2026-10-25">2026-10-25</time></div>
						</div>
						<div class="pedido-total"><span>Total estimado</span><strong>$1,500.00</strong></div>
						<div class="pedido-acciones">
							<button class="btn pedido-registrar" type="button">Registrar pedido</button>
							<button class="btn-secundario" type="button">Ver detalles</button>
							<button class="btn btn-peligro pedido-eliminar" type="button" aria-label="Eliminar ORD-003">Eliminar</button>
						</div>
					</article>
				</div>

				<p class="pedido-sin-resultados" id="sin-pedidos" hidden>No hay pedidos que coincidan con la búsqueda.</p>
				<div class="pedido-paginacion" aria-label="Paginación de pedidos">
					<span>1-3 de 8 pedidos</span>
					<button class="btn-secundario" type="button" aria-label="Página anterior" disabled>‹</button>
					<button class="btn-secundario" type="button" aria-label="Página siguiente">›</button>
				</div>
			</section>
		</main>
	</div>

	<footer>
		<p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
	</footer>

	<script>
		const buscadorPedidos = document.getElementById('buscar-pedido');
		const tarjetasPedidos = [...document.querySelectorAll('[data-pedido]')];
		const mensajeSinPedidos = document.getElementById('sin-pedidos');

		buscadorPedidos.addEventListener('input', () => {
			const consulta = buscadorPedidos.value.trim().toLocaleLowerCase('es');
			let pedidosVisibles = 0;

			tarjetasPedidos.forEach((tarjeta) => {
				const coincide = tarjeta.textContent.toLocaleLowerCase('es').includes(consulta);
				tarjeta.hidden = !coincide;
				if (coincide) pedidosVisibles += 1;
			});

			mensajeSinPedidos.hidden = pedidosVisibles > 0;
		});
	</script>
</body>

</html>
