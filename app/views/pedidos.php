<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

require_once __DIR__ . '/../models/Pedidos.php';

if (empty($_SESSION['csrf_pedidos'])) {
	$_SESSION['csrf_pedidos'] = bin2hex(random_bytes(32));
}

$escaparPedido = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
$pedidos = [];
$errorPedidos = '';
try {
	$pedidos = Pedidos::obtenerPedidos();
	foreach ($pedidos as &$pedido) {
		$pedido['productos'] = Pedidos::obtenerDetalles((int) $pedido['clave_compra']);
	}
	unset($pedido);
} catch (Throwable $e) {
	error_log('Error al cargar pedidos: ' . $e->getMessage());
	$pedidos = [];
	$errorPedidos = 'No se pudieron cargar los pedidos. Verifica la conexión y las tablas de órdenes de compra.';
}
?>
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
			<div class="">
			<div class="migaja">Gestión de inventario</div>
			<div class="encabezado-pagina">
				<h1>Control de inventario</h1>
			</div>

			<nav class="inventario-pestanas" aria-label="Secciones de inventario">
				<a href="inventario.php">1. Catálogo de productos</a>
				<a href="lotes.php">2. Gestión de lotes</a>
				<a href="#pedidos" class="activa" aria-current="page">3. Pedidos pendientes</a>
			</nav>

			<section id="pedidos" aria-label="Pedidos pendientes">
				<?php if ($errorPedidos !== ''): ?>
					<p class="pedido-sin-resultados" role="alert"><?= $escaparPedido($errorPedidos) ?></p>
				<?php endif; ?>
				<div class="inventario-pedidos-toolbar">
					<label class="visualmente-oculto" for="buscar-pedido">Buscar pedidos</label>
					<input id="buscar-pedido" class="campo" type="search" placeholder="Buscar por proveedor, ID pedido...">
					<label class="visualmente-oculto" for="filtro-estado-pedido">Filtrar pedidos por estado</label>
					<select id="filtro-estado-pedido" class="campo pedidos-filtro-estado">
						<option value="">Todos los estados</option>
						<option value="confirmado">Entregado</option>
						<option value="pendiente">Pendiente</option>
						<option value="cancelado">Cancelado</option>
					</select>
				</div>

				<div class="inventario-pedidos-grid" id="lista-pedidos">
					<?php foreach ($pedidos as $pedido): ?>
						<?php
						$idPedido = (int) $pedido['clave_compra'];
						$estadoPedido = strtolower((string) $pedido['estado']);
						$esPendiente = $estadoPedido === 'pendiente';
						$claseEstado = $estadoPedido === 'entregado'
							? 'pagada'
							: ($estadoPedido === 'cancelado' ? 'cancelada' : 'pendiente');
						$textoEstado = $estadoPedido === 'entregado'
							? 'Entregado'
							: ($estadoPedido === 'cancelado' ? 'Cancelado' : 'Pendiente');
						?>
					<article class="pedido-tarjeta" data-pedido data-pedido-id="<?= $idPedido ?>" data-estado="<?= $escaparPedido($estadoPedido) ?>">
						<header class="pedido-encabezado">
							<span class="pedido-icono" aria-hidden="true">OC</span>
							<div>
								<span class="pedido-etiqueta">Orden de compra</span>
								<h2>ORD-<?= $escaparPedido($pedido['clave_compra']) ?></h2>
							</div>
							<span class="historial-estado <?= $claseEstado ?>"><?= $textoEstado ?></span>
						</header>
						<a class="pedido-proveedor" href="#pedido-<?= $idPedido ?>">
							<span>Proveedor</span>
							<strong><?= $escaparPedido($pedido['proveedor']) ?></strong>
							<span class="pedido-flecha" aria-hidden="true">›</span>
						</a>
						<div class="pedido-fechas">
							<div><span>Fecha de solicitud</span><time datetime="<?= $escaparPedido($pedido['fecha_solicitud']) ?>"><?= $escaparPedido($pedido['fecha_solicitud']) ?></time></div>
							<div><span>Fecha esperada</span><time datetime="<?= $escaparPedido($pedido['fecha_esperada'] ?? '') ?>"><?= $escaparPedido($pedido['fecha_esperada'] ?? 'Sin definir') ?></time></div>
						</div>
						<div class="pedido-total"><span>Total estimado</span><strong>$<?= number_format((float) $pedido['total_estimado'], 2, '.', ',') ?></strong></div>
						<div class="pedido-acciones">
							<?php if ($esPendiente): ?>
								<button class="btn pedido-registrar btn-chico" type="button" data-abrir-registro>Registrar pedido</button>
							<?php endif; ?>
							<button class="btn btn-secundario btn-chico" type="button" data-abrir-detalles>Ver detalles</button>
							<?php if ($esPendiente): ?>
								<button class="btn btn-peligro pedido-eliminar btn-chico" type="button" aria-label="Eliminar ORD-<?= $escaparPedido($pedido['clave_compra']) ?>">Eliminar</button>
							<?php endif; ?>
						</div>
						<template data-productos>
							<?php foreach ($pedido['productos'] as $productoPedido): ?>
								<?php
								$precioUnitario = (float) $productoPedido['precio_unitario'];
								$cantidadPedido = (int) $productoPedido['cantidad'];
								?>
								<tr><td><?= $escaparPedido($productoPedido['nombre']) ?></td><td><?= $cantidadPedido ?></td><td>$<?= number_format($precioUnitario, 2, '.', ',') ?></td><td>$<?= number_format($precioUnitario * $cantidadPedido, 2, '.', ',') ?></td></tr>
							<?php endforeach; ?>
						</template>
					</article>
					<?php endforeach; ?>
				</div>

				<p class="pedido-sin-resultados" id="sin-pedidos" <?= $pedidos === [] ? '' : 'hidden' ?>>No hay pedidos que coincidan con la búsqueda.</p>
				<div class="pedido-paginacion" aria-label="Paginación de pedidos">
					<span><?= count($pedidos) ?> pedidos</span>
				</div>
			</section>
			</div>
		</main>
	</div>

	<dialog class="lote-dialog" id="dialogo-registro-pedido" aria-labelledby="titulo-registro-pedido" data-csrf="<?= $escaparPedido($_SESSION['csrf_pedidos']) ?>">
		<h2 id="titulo-registro-pedido">Registrar pedido</h2>
		<form id="formulario-registro-pedido">
			<div class="lote-formulario">
				<div class="lote-campo">
					<label for="pedido-id">Código de lote</label>
					<input class="campo" id="pedido-id" name="lote" type="text" maxlength="20" pattern="(?=.*[a-zA-ZñÑ])(?=.*\d)[a-zA-Z0-9ñÑ_-]+" required>
				</div>
				<div class="lote-campo">
					<label for="pedido-producto">Producto</label>
					<select class="campo" id="pedido-producto" name="producto" required>
						<option value="">Selecciona un producto</option>
					</select>
				</div>
				<div class="lote-campo">
					<label for="pedido-proveedor">Proveedor</label>
					<select class="campo" id="pedido-proveedor" name="proveedor" required>
						<option value="">Selecciona un proveedor</option>
					</select>
				</div>
				<div class="lote-campo">
					<label for="pedido-caducidad">Fecha de caducidad</label>
					<input class="campo" id="pedido-caducidad" name="fecha_caducidad" type="date" required>
				</div>
				<div class="lote-campo">
					<label for="pedido-precio">Precio de compra</label>
					<input class="campo" id="pedido-precio" name="precio_compra" type="number" min="0.01" step="0.01" readonly required>
				</div>
				<div class="lote-campo">
					<label for="pedido-ubicacion">Ubicación</label>
					<input class="campo" id="pedido-ubicacion" name="ubicacion" type="text" maxlength="20" pattern="(?=.*[a-zA-ZñÑ])(?=.*\d)[a-zA-Z0-9ñÑ_-]+" required>
				</div>
				<div class="lote-campo">
					<label for="pedido-cantidad">Cantidad</label>
					<input class="campo" id="pedido-cantidad" name="cantidad" type="number" min="1" step="1" required>
				</div>
			</div>
			<div class="lote-dialog-acciones">
				<button class="btn btn-secundario btn-chico" id="cerrar-registro-pedido" type="button">Cancelar</button>
				<button class="btn btn-chico" type="button">Registrar pedido</button>
			</div>
		</form>
	</dialog>

	<dialog class="pedido-detalle-modal" id="modal-detalle-pedido" aria-labelledby="modal-pedido-folio">
		<div class="pedido-detalle-contenido">
			<header class="pedido-detalle-encabezado">
				<div>
					<span class="pedido-etiqueta">Detalle de orden de compra</span>
					<h2 id="modal-pedido-folio"></h2>
				</div>
				<button class="pedido-modal-cerrar" type="button" data-cerrar-modal aria-label="Cerrar detalles">&times;</button>
			</header>

			<div class="pedido-detalle-resumen">
				<div><span>Proveedor</span><strong id="modal-pedido-proveedor"></strong></div>
				<div><span>Fecha de solicitud</span><time id="modal-pedido-solicitud"></time></div>
				<div><span>Fecha esperada</span><time id="modal-pedido-esperada"></time></div>
				<div><span>Estado actual</span><span class="historial-estado" id="modal-pedido-estado"></span></div>
			</div>

			<section class="pedido-detalle-productos" aria-labelledby="modal-pedido-productos-titulo">
				<h3 id="modal-pedido-productos-titulo">Productos del pedido</h3>
				<div class="pedido-detalle-tabla-envoltura">
					<table>
						<thead>
							<tr><th>Producto</th><th>Cantidad</th><th>Precio unitario</th><th>Subtotal</th></tr>
						</thead>
						<tbody id="modal-pedido-productos"></tbody>
					</table>
				</div>
			</section>

			<div class="pedido-detalle-pie">
				<div class="pedido-detalle-observaciones" id="modal-pedido-observaciones" hidden>
					<strong>Observaciones</strong>
					<p></p>
				</div>
				<div class="pedido-detalle-total">
					<span>Total estimado</span>
					<strong id="modal-pedido-total"></strong>
				</div>
			</div>

			<footer class="pedido-detalle-acciones">
				<button class="btn btn-chico" type="button">Registrar entrega</button>
				<button class="btn btn-peligro btn-chico" type="button">Cancelar pedido</button>
				<button class="btn btn-secundario btn-chico" type="button">Imprimir / Descargar</button>
			</footer>
		</div>
	</dialog>

	<footer>
		<p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
	</footer>

	<script>
		const buscadorPedidos = document.getElementById('buscar-pedido');
		const tarjetasPedidos = [...document.querySelectorAll('[data-pedido]')];
		const mensajeSinPedidos = document.getElementById('sin-pedidos');
		const modalDetallePedido = document.getElementById('modal-detalle-pedido');
		const dialogoRegistroPedido = document.getElementById('dialogo-registro-pedido');

		document.querySelectorAll('[data-abrir-registro]').forEach((boton) => {
			boton.addEventListener('click', () => dialogoRegistroPedido.showModal());
		});

		document.getElementById('cerrar-registro-pedido').addEventListener('click', () => dialogoRegistroPedido.close());
		dialogoRegistroPedido.addEventListener('click', (evento) => {
			if (evento.target === dialogoRegistroPedido) dialogoRegistroPedido.close();
		});

		document.querySelectorAll('[data-abrir-detalles]').forEach((boton) => {
			boton.addEventListener('click', () => {
				const tarjeta = boton.closest('[data-pedido]');
				if (!tarjeta) return;

				const fechas = tarjeta.querySelectorAll('.pedido-fechas time');
				const estado = tarjeta.querySelector('.historial-estado');
				const observaciones = tarjeta.querySelector('[data-observaciones]');

				document.getElementById('modal-pedido-folio').textContent = tarjeta.querySelector('.pedido-encabezado h2').textContent;
				document.getElementById('modal-pedido-proveedor').textContent = tarjeta.querySelector('.pedido-proveedor strong').textContent;
				document.getElementById('modal-pedido-solicitud').textContent = fechas[0].textContent;
				document.getElementById('modal-pedido-solicitud').dateTime = fechas[0].dateTime;
				document.getElementById('modal-pedido-esperada').textContent = fechas[1].textContent;
				document.getElementById('modal-pedido-esperada').dateTime = fechas[1].dateTime;
				document.getElementById('modal-pedido-total').textContent = tarjeta.querySelector('.pedido-total strong').textContent;

				const estadoModal = document.getElementById('modal-pedido-estado');
				estadoModal.textContent = estado.textContent;
				estadoModal.className = estado.className;

				const filasProductos = tarjeta.querySelector('template[data-productos]');
				document.getElementById('modal-pedido-productos').replaceChildren(
					...(filasProductos ? [...filasProductos.content.querySelectorAll('tr')].map((fila) => fila.cloneNode(true)) : [])
				);

				const seccionObservaciones = document.getElementById('modal-pedido-observaciones');
				seccionObservaciones.hidden = !observaciones || !observaciones.textContent.trim();
				if (observaciones) seccionObservaciones.querySelector('p').textContent = observaciones.textContent.trim();

				modalDetallePedido.showModal();
			});
		});

		document.querySelector('[data-cerrar-modal]').addEventListener('click', () => modalDetallePedido.close());
		modalDetallePedido.addEventListener('click', (evento) => {
			if (evento.target === modalDetallePedido) modalDetallePedido.close();
		});

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
	<script src="js/pedidos.js?v=2"></script>
</body>

</html>
