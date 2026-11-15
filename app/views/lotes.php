<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Lotes.php';
$productos = Lotes::obtenerProductosActivosConProveedor();
$lotes = Lotes::obtenerLotes();

$erroresLote = $_SESSION['errores'] ?? [];
$loteAnterior = $_SESSION['old'] ?? [];
$abrirFormularioLote = !empty($_SESSION['abrir_formulario_lote']);
unset(
    $_SESSION['errores'],
    $_SESSION['old'],
    $_SESSION['abrir_formulario_lote']
);

$escapar = static fn($valor) => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
$productoAnterior = null;
foreach ($productos as $producto) {
	if ((string) $producto['id_producto'] === (string) ($loteAnterior['id_producto'] ?? '')) {
		$productoAnterior = $producto;
		break;
	}
}

date_default_timezone_set('America/Mexico_City');
$hoy = new DateTimeImmutable('today');
$lotes_registrados = 0;
$por_caducar = 0;
$unidad_lotes = 0;
$lotes_vencidos = 0;
foreach ($lotes as $lote){
    $fechaCaducidad = new DateTimeImmutable($lote['fecha_caducidad']);
    $diasRestantes = (int) $hoy->diff($fechaCaducidad)->format('%r%a');
    
	if ($diasRestantes < 0) {
        $lotes_vencidos++;
    }else if ($diasRestantes <= 15) {
        $por_caducar++;
    }
	$lotes_registrados++;
	$unidad_lotes = $unidad_lotes + (int)$lote['cantidad'];
}
?>

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
						<strong id="total-lotes"><?=$lotes_registrados?></strong>
					</div>
					<div class="lote-resumen-tarjeta">
						<span>Por caducar (15 días)</span>
						<strong id="lotes-proximos"><?=$por_caducar?></strong>
					</div>
					<div class="lote-resumen-tarjeta">
						<span>Lotes vencidos</span>
						<strong id="lotes-vencidos"><?=$lotes_vencidos?></strong>
					</div>
					<div class="lote-resumen-tarjeta">
						<span>Unidades en lotes</span>
						<strong id="unidades-lotes"><?=$unidad_lotes?></strong>
					</div>
				</div>

				<div class="lotes-toolbar">
					<label class="visualmente-oculto" for="buscar-lote">Buscar lotes</label>
					<input id="buscar-lote" class="campo buscador-inteligente" type="search" placeholder="Buscar por lote, producto o ubicación...">
					<label class="visualmente-oculto" for="filtro-lote-estado">Filtrar lotes por estado</label>
					<select id="filtro-lote-estado" class="campo lotes-filtro-estado">
						<option value="">Todos los lotes</option>
						<option value="vigente">Vigentes</option>
						<option value="proximo">Por caducar</option>
						<option value="vencido">Vencidos</option>
					</select>
					<button class="btn" id="abrir-formulario-lote" type="button">+ Registrar lote</button>
				</div>

				<?php
					date_default_timezone_set('America/Mexico_City');
					$hoy = new DateTimeImmutable('today');
				?>

				<div class="lotes-grid" id="lista-lotes">

					<?php foreach ($lotes as $lote):
        				$fechaCaducidad = new DateTimeImmutable($lote['fecha_caducidad']);
        				$diasRestantes = (int) $hoy->diff($fechaCaducidad)->format('%r%a');

        				if ($diasRestantes < 0) {
        				    $estado = 'vencido';
        				    $textoEstado = 'Vencido';
        				} elseif ($diasRestantes <= 15) {
        				    $estado = 'proximo';
        				    $textoEstado = 'Por caducar';
        				} else {
        				    $estado = 'vigente';
        				    $textoEstado = 'Vigente';
        				}

        				$escapar = static fn($valor) => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    				?>

						<article class="lote-tarjeta" data-lote data-estado="<?= $estado ?>">
    			    	    <header class="lote-encabezado">
    			    	        <div class="lote-identidad">
    			    	            <span class="lote-etiqueta">Lote</span>
    			    	            <h2><?= $escapar($lote['lote']) ?></h2>
    			    	        </div>
    			    	        <span class="lote-estado <?= $estado ?>">
    			    	            <?= $textoEstado ?>
    			    	        </span>
    			    	    </header>

    			    	    <dl class="lote-detalles">
    			    	        <div>
    			    	            <dt>Producto</dt>
    			    	            <dd><?= $escapar($lote['id_producto']) ?> ·
    			    	                <?= $escapar($lote['nombre_producto']) ?></dd>
    			    	        </div>
    			    	        <div>
    			    	            <dt>Proveedor</dt>
    			    	            <dd><?= $escapar($lote['proveedor']) ?></dd>
    			    	        </div>
    			    	        <div>
    			    	            <dt>Fecha de caducidad</dt>
    			    	            <dd><?= $escapar($fechaCaducidad->format('Y-m-d')) ?></dd>
    			    	        </div>
    			    	        <div>
    			    	            <dt>Precio de compra</dt>
    			    	            <dd>$<?= number_format((float) $lote['precio_compra'], 2) ?></dd>
    			    	        </div>
    			    	        <div>
    			    	            <dt>Ubicación</dt>
    			    	            <dd><?= $escapar($lote['ubicacion']) ?></dd>
    			    	        </div>
    			    	    </dl>

    			    	    <div class="lote-cantidad">
    			    	        <span>Cantidad disponible</span>
    			    	        <strong><?= $escapar($lote['cantidad']) ?></strong>
    			    	    </div>
    			    	</article>
    				<?php endforeach; ?>
				</div>

				<p class="lotes-sin-resultados" id="sin-lotes" hidden>No hay lotes que coincidan con la búsqueda.</p>
			</section>
		</main>
	</div>

	<footer>
		<p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
	</footer>

	<dialog class="lote-dialog" id="dialogo-lote" aria-labelledby="titulo-dialogo-lote" data-abrir="<?= $abrirFormularioLote ? '1' : '0' ?>">
		<h2 id="titulo-dialogo-lote">Registrar lote</h2>
		<?php if (!empty($erroresLote)): ?>
			<div class="msg-error">
				<h4>Por favor corrige los siguientes errores:</h4>
				<ul>
					<?php foreach ($erroresLote as $error): ?>
						<li><?= $escapar($error) ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
		<form id="formulario-lote" action="../controllers/lotes.php" method="post" novalidate>
			<div class="lote-formulario">
				<div class="lote-campo">
					<label for="lote-id">Código de lote</label>
					<input class="campo" id="lote-id" name="lote" type="text" maxlength="20" value="<?= $escapar($loteAnterior['lote'] ?? '') ?>" required>
				</div>
				<div class="lote-campo contenedor-buscador">
					<label for="lote-producto">Producto</label>
					<input
						class="campo"
						id="lote-producto"
						type="text"
						autocomplete="off"
						placeholder="Seleccionar producto..."
						value="<?= $productoAnterior ? $escapar('Id: ' . $productoAnterior['id_producto'] . ' - ' . $productoAnterior['codigo'] . ' - ' . $productoAnterior['nombre']) : '' ?>"
						required
					>
					<input
						id="lote-producto-id"
						name="id_producto"
						type="hidden"
						value="<?= $escapar($loteAnterior['id_producto'] ?? '') ?>"
					>
					<ul id="lista_productos_lote" class="lista-resultados">
						<?php foreach ($productos as $producto): ?>
							<li
								data-id="<?= (int) $producto['id_producto'] ?>"
								data-proveedor="<?= $escapar($producto['proveedor']) ?>"
							>
								<?= $escapar('Id: ' . $producto['id_producto'] . ' - ' . $producto['codigo'] . ' - ' . $producto['nombre']) ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="lote-campo">
					<label for="lote-proveedor">Proveedor</label>
					<input class="campo" id="lote-proveedor" type="text" value="" readonly aria-describedby="ayuda-proveedor-lote">
					<small id="ayuda-proveedor-lote">Se obtiene del producto seleccionado.</small>
				</div>
				<div class="lote-campo">
					<label for="lote-caducidad">Fecha de caducidad</label>
					<input class="campo" id="lote-caducidad" name="fecha_caducidad" type="date" value="<?= $escapar($loteAnterior['fecha_caducidad'] ?? '') ?>" required>
				</div>
				<div class="lote-campo">
					<label for="lote-precio">Precio de compra</label>
					<input class="campo" id="lote-precio" name="precio_compra" type="number" min="0.01" step="0.01" value="<?= $escapar($loteAnterior['precio_compra'] ?? '') ?>" required>
				</div>
				<div class="lote-campo">
					<label for="lote-ubicacion">Ubicación</label>
					<input class="campo" id="lote-ubicacion" name="ubicacion" type="text" maxlength="20" value="<?= $escapar($loteAnterior['ubicacion'] ?? '') ?>" required>
				</div>
				<div class="lote-campo">
					<label for="lote-cantidad">Cantidad</label>
					<input class="campo" id="lote-cantidad" name="cantidad" type="number" min="0" step="1" value="<?= $escapar($loteAnterior['cantidad'] ?? '') ?>" required>
				</div>
			</div>
			<div class="lote-dialog-acciones">
				<button class="btn btn-secundario btn-chico" id="cerrar-dialogo-lote" type="button">Cancelar</button>
				<button class="btn btn-chico" type="submit">Registrar lote</button>
			</div>
		</form>
	</dialog>

	<script>
		const buscarLote = document.getElementById('buscar-lote');
		const filtroLote = document.getElementById('filtro-lote-estado');
		const listaLotes = document.getElementById('lista-lotes');
		const mensajeSinLotes = document.getElementById('sin-lotes');

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

		buscarLote.addEventListener('input', filtrarLotes);
		filtroLote.addEventListener('change', filtrarLotes);
		filtrarLotes();
	</script>
	<script src="js/lotes.js" defer></script>
</body>

</html>