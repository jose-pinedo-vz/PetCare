<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Veterinaria PetCare - Gestión de lotes</title>
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
				<a href="#lotes" class="activa" aria-current="page">2. Gestión de lotes</a>
				<a href="pedidos.php">3. Pedidos pendientes</a>
			</nav>

			<section id="lotes" aria-label="Gestión de lotes">
				<div class="lotes-resumen" aria-label="Resumen de lotes">
					<div class="lote-resumen-tarjeta">
						<span>Lotes registrados</span>
						<strong id="total-lotes">3</strong>
					</div>
					<div class="lote-resumen-tarjeta">
						<span>Por caducar (60 días)</span>
						<strong id="lotes-proximos">1</strong>
					</div>
					<div class="lote-resumen-tarjeta">
						<span>Lotes vencidos</span>
						<strong id="lotes-vencidos">1</strong>
					</div>
					<div class="lote-resumen-tarjeta">
						<span>Unidades en lotes</span>
						<strong id="unidades-lotes">42</strong>
					</div>
				</div>

				<div class="lotes-toolbar">
					<label class="visualmente-oculto" for="buscar-lote">Buscar lotes</label>
					<input id="buscar-lote" class="campo" type="search" placeholder="Buscar por lote, producto o ubicación...">
					<label class="visualmente-oculto" for="filtro-lote-estado">Filtrar lotes por estado</label>
					<select id="filtro-lote-estado" class="campo lotes-filtro-estado">
						<option value="">Todos los lotes</option>
						<option value="vigente">Vigentes</option>
						<option value="proximo">Por caducar</option>
						<option value="vencido">Vencidos</option>
					</select>
					<button class="btn" id="abrir-formulario-lote" type="button">+ Registrar lote</button>
				</div>

				<div class="lotes-grid" id="lista-lotes">
					<article class="lote-tarjeta" data-lote data-estado="proximo">
						<header class="lote-encabezado">
							<div class="lote-identidad">
								<span class="lote-etiqueta">Lote</span>
								<h2 data-lote-id>LOT-2026-014</h2>
							</div>
							<span class="lote-estado proximo" data-lote-estado>Por caducar</span>
						</header>
						<dl class="lote-detalles">
							<div><dt>ID producto</dt><dd data-lote-producto>1 · Pelota</dd></div>
							<div><dt>Fecha de caducidad</dt><dd data-lote-fecha>2026-11-20</dd></div>
							<div><dt>Precio de compra</dt><dd data-lote-precio>$18.50</dd></div>
							<div><dt>Ubicación</dt><dd data-lote-ubicacion>A-01</dd></div>
						</dl>
						<div class="lote-cantidad">
							<span>Cantidad disponible</span>
							<strong data-lote-cantidad>24</strong>
						</div>
					</article>

					<article class="lote-tarjeta" data-lote data-estado="vigente">
						<header class="lote-encabezado">
							<div class="lote-identidad">
								<span class="lote-etiqueta">Lote</span>
								<h2 data-lote-id>LOT-2026-018</h2>
							</div>
							<span class="lote-estado vigente" data-lote-estado>Vigente</span>
						</header>
						<dl class="lote-detalles">
							<div><dt>ID producto</dt><dd data-lote-producto>2 · Collar</dd></div>
							<div><dt>Fecha de caducidad</dt><dd data-lote-fecha>2027-06-15</dd></div>
							<div><dt>Precio de compra</dt><dd data-lote-precio>$72.00</dd></div>
							<div><dt>Ubicación</dt><dd data-lote-ubicacion>B-03</dd></div>
						</dl>
						<div class="lote-cantidad">
							<span>Cantidad disponible</span>
							<strong data-lote-cantidad>18</strong>
						</div>
					</article>

					<article class="lote-tarjeta" data-lote data-estado="vencido">
						<header class="lote-encabezado">
							<div class="lote-identidad">
								<span class="lote-etiqueta">Lote</span>
								<h2 data-lote-id>LOT-2025-009</h2>
							</div>
							<span class="lote-estado vencido" data-lote-estado>Vencido</span>
						</header>
						<dl class="lote-detalles">
							<div><dt>ID producto</dt><dd data-lote-producto>1 · Pelota</dd></div>
							<div><dt>Fecha de caducidad</dt><dd data-lote-fecha>2026-10-10</dd></div>
							<div><dt>Precio de compra</dt><dd data-lote-precio>$15.00</dd></div>
							<div><dt>Ubicación</dt><dd data-lote-ubicacion>A-02</dd></div>
						</dl>
						<div class="lote-cantidad">
							<span>Cantidad disponible</span>
							<strong data-lote-cantidad>0</strong>
						</div>
					</article>
				</div>

				<p class="lotes-sin-resultados" id="sin-lotes" hidden>No hay lotes que coincidan con la búsqueda.</p>
				<p class="nota-campo">Los registros de esta vista son de demostración; los cambios no se guardan en la base de datos.</p>
			</section>
		</main>
	</div>

	<footer>
		<p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
	</footer>

	<dialog class="lote-dialog" id="dialogo-lote" aria-labelledby="titulo-dialogo-lote">
		<h2 id="titulo-dialogo-lote">Registrar lote</h2>
		<form id="formulario-lote">
			<div class="lote-formulario">
				<div class="lote-campo">
					<label for="lote-id">Código de lote</label>
					<input class="campo" id="lote-id" name="lote" type="text" maxlength="20" required>
				</div>
				<div class="lote-campo">
					<label for="lote-producto">Producto</label>
					<select class="campo" id="lote-producto" name="producto" required>
						<option value="">Selecciona un producto</option>
						<option value="1">1 · Pelota</option>
						<option value="2">2 · Collar</option>
					</select>
				</div>
				<div class="lote-campo">
					<label for="lote-caducidad">Fecha de caducidad</label>
					<input class="campo" id="lote-caducidad" name="fecha_caducidad" type="date" required>
				</div>
				<div class="lote-campo">
					<label for="lote-precio">Precio de compra</label>
					<input class="campo" id="lote-precio" name="precio_compra" type="number" min="0" step="0.01" required>
				</div>
				<div class="lote-campo">
					<label for="lote-ubicacion">Ubicación</label>
					<input class="campo" id="lote-ubicacion" name="ubicacion" type="text" maxlength="20" required>
				</div>
				<div class="lote-campo">
					<label for="lote-cantidad">Cantidad</label>
					<input class="campo" id="lote-cantidad" name="cantidad" type="number" min="0" step="1" required>
				</div>
			</div>
			<div class="lote-dialog-acciones">
				<button class="btn-secundario" id="cerrar-dialogo-lote" type="button">Cancelar</button>
				<button class="btn" type="submit">Agregar a la vista</button>
			</div>
		</form>
	</dialog>

	<template id="plantilla-lote">
		<article class="lote-tarjeta" data-lote>
			<header class="lote-encabezado">
				<div class="lote-identidad">
					<span class="lote-etiqueta">Lote</span>
					<h2 data-lote-id></h2>
				</div>
				<span class="lote-estado" data-lote-estado></span>
			</header>
			<dl class="lote-detalles">
				<div><dt>ID producto</dt><dd data-lote-producto></dd></div>
				<div><dt>Fecha de caducidad</dt><dd data-lote-fecha></dd></div>
				<div><dt>Precio de compra</dt><dd data-lote-precio></dd></div>
				<div><dt>Ubicación</dt><dd data-lote-ubicacion></dd></div>
			</dl>
			<div class="lote-cantidad">
				<span>Cantidad disponible</span>
				<strong data-lote-cantidad></strong>
			</div>
		</article>
	</template>

	<script>
		const buscarLote = document.getElementById('buscar-lote');
		const filtroLote = document.getElementById('filtro-lote-estado');
		const listaLotes = document.getElementById('lista-lotes');
		const mensajeSinLotes = document.getElementById('sin-lotes');
		const dialogoLote = document.getElementById('dialogo-lote');
		const formularioLote = document.getElementById('formulario-lote');
		const plantillaLote = document.getElementById('plantilla-lote');
		const formatoPrecio = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });

		function actualizarResumen() {
			const lotes = [...listaLotes.querySelectorAll('[data-lote]')];
			document.getElementById('total-lotes').textContent = lotes.length;
			document.getElementById('lotes-proximos').textContent = lotes.filter((lote) => lote.dataset.estado === 'proximo').length;
			document.getElementById('lotes-vencidos').textContent = lotes.filter((lote) => lote.dataset.estado === 'vencido').length;
			document.getElementById('unidades-lotes').textContent = lotes.reduce((total, lote) => {
				return total + Number(lote.querySelector('[data-lote-cantidad]').textContent);
			}, 0);
		}

		function filtrarLotes() {
			const consulta = buscarLote.value.trim().toLocaleLowerCase('es');
			const estadoSeleccionado = filtroLote.value;
			let visibles = 0;

			listaLotes.querySelectorAll('[data-lote]').forEach((lote) => {
				const coincideTexto = lote.textContent.toLocaleLowerCase('es').includes(consulta);
				const coincideEstado = !estadoSeleccionado || lote.dataset.estado === estadoSeleccionado;
				lote.hidden = !coincideTexto || !coincideEstado;
				if (!lote.hidden) visibles += 1;
			});

			mensajeSinLotes.hidden = visibles > 0;
		}

		function obtenerEstadoLote(fechaCaducidad) {
			const hoy = new Date();
			hoy.setHours(0, 0, 0, 0);
			const fecha = new Date(`${fechaCaducidad}T00:00:00`);
			const diasRestantes = Math.ceil((fecha - hoy) / 86400000);

			if (diasRestantes < 0) return { clase: 'vencido', texto: 'Vencido' };
			if (diasRestantes <= 60) return { clase: 'proximo', texto: 'Por caducar' };
			return { clase: 'vigente', texto: 'Vigente' };
		}

		buscarLote.addEventListener('input', filtrarLotes);
		filtroLote.addEventListener('change', filtrarLotes);

		document.getElementById('abrir-formulario-lote').addEventListener('click', () => dialogoLote.showModal());
		document.getElementById('cerrar-dialogo-lote').addEventListener('click', () => dialogoLote.close());

		formularioLote.addEventListener('submit', (event) => {
			event.preventDefault();
			const datos = new FormData(formularioLote);
			const fechaCaducidad = datos.get('fecha_caducidad');
			const estado = obtenerEstadoLote(fechaCaducidad);
			const producto = document.getElementById('lote-producto').selectedOptions[0].textContent;
			const tarjeta = plantillaLote.content.firstElementChild.cloneNode(true);

			tarjeta.dataset.estado = estado.clase;
			tarjeta.querySelector('[data-lote-id]').textContent = datos.get('lote');
			tarjeta.querySelector('[data-lote-producto]').textContent = producto;
			tarjeta.querySelector('[data-lote-fecha]').textContent = fechaCaducidad;
			tarjeta.querySelector('[data-lote-precio]').textContent = formatoPrecio.format(Number(datos.get('precio_compra')));
			tarjeta.querySelector('[data-lote-ubicacion]').textContent = datos.get('ubicacion');
			tarjeta.querySelector('[data-lote-cantidad]').textContent = datos.get('cantidad');
			tarjeta.querySelector('[data-lote-estado]').classList.add(estado.clase);
			tarjeta.querySelector('[data-lote-estado]').textContent = estado.texto;

			listaLotes.prepend(tarjeta);
			formularioLote.reset();
			dialogoLote.close();
			actualizarResumen();
			filtrarLotes();
		});
	</script>
</body>

</html>
