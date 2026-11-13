<?php
require_once __DIR__ . '/../models/Movimientos.php';

$movimientos = Movimientos::obtenerTodos();
$errorMovimientos = $movimientos === null;
$movimientos = $movimientos ?? [];
$totalMovimientos = count($movimientos);
$escaparMovimiento = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Movimientos de inventario</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>
  <div class="layout">
    <?php $paginaActiva = 'inventario'; include __DIR__ . '/layout/sidebar.php'; ?>

    <main>
      <div class="migaja"><a class="link-tenue" href="inventario.php">Gestión de inventario</a> / Movimientos</div>
      <div class="encabezado-pagina">
        <h1>Movimientos de inventario</h1>
      </div>

      <nav class="inventario-pestanas" aria-label="Secciones de inventario">
        <a href="inventario.php">1. Catálogo de productos</a>
        <a href="lotes.php">2. Gestión de lotes</a>
        <a href="pedidos.php#pedidos">3. Pedidos pendientes</a>
        <a href="#movimientos" class="activa" aria-current="page">4. Movimientos</a>
      </nav>

      <section id="movimientos" aria-labelledby="titulo-movimientos">
        <div class="movimientos-encabezado">
          <div>
            <h2 class="subtitulo" id="titulo-movimientos">Movimientos registrados</h2>
            <p class="movimientos-descripcion">Revisa las entradas y salidas registradas en el inventario.</p>
          </div>
          <a class="btn btn-secundario btn-chico" href="inventario.php">Volver al inventario</a>
        </div>

        <?php if ($errorMovimientos): ?>
          <div class="msg-error" role="alert">No se pudieron cargar los movimientos de inventario.</div>
        <?php endif; ?>

        <form class="movimientos-toolbar" aria-label="Filtros de movimientos" onsubmit="return false">
          <label class="visualmente-oculto" for="filtro-buscar-movimiento">Buscar movimiento</label>
          <input class="campo movimientos-buscador" id="filtro-buscar-movimiento" type="search" placeholder="Buscar por producto, empleado o motivo...">
          <label class="visualmente-oculto" for="filtro-tipo-movimiento">Filtrar por tipo de movimiento</label>
          <select class="campo movimientos-filtro-tipo" id="filtro-tipo-movimiento">
            <option value="">Todos los movimientos</option>
            <option value="entrada">Entradas</option>
            <option value="salida">Salidas</option>
            <option value="ajuste">Ajustes</option>
          </select>
          <label class="visualmente-oculto" for="filtro-fecha-desde">Fecha desde</label>
          <input class="campo movimientos-filtro-fecha" id="filtro-fecha-desde" type="date" aria-label="Fecha desde">
          <label class="visualmente-oculto" for="filtro-fecha-hasta">Fecha hasta</label>
          <input class="campo movimientos-filtro-fecha" id="filtro-fecha-hasta" type="date" aria-label="Fecha hasta">
        </form>

        <div class="movimientos-lista-encabezado">
          <div>
            <h3>Historial de movimientos</h3>
            <p>Registros guardados en el inventario.</p>
          </div>
          <span id="cantidad-movimientos"><?= $totalMovimientos ?> movimientos</span>
        </div>

        <div class="movimientos-lista" id="lista-movimientos">
          <?php foreach ($movimientos as $movimiento): ?>
            <?php
              $tipo = strtolower((string) $movimiento['tipo_movimiento']);
              $tipoClase = in_array($tipo, ['entrada', 'salida', 'ajuste'], true) ? $tipo : 'ajuste';
              $tipoTexto = ucfirst($tipo);
              $empleado = trim((string) ($movimiento['nombre_empleado'] ?? '') . ' ' . (string) ($movimiento['apellido_empleado'] ?? ''));
              $fecha = strtotime((string) $movimiento['fecha']);
              $fechaTexto = $fecha === false ? (string) $movimiento['fecha'] : date('d/m/Y, H:i', $fecha);
              $fechaFiltro = $fecha === false ? '' : date('Y-m-d', $fecha);
              $producto = $movimiento['nombre_producto'] ?? null;
            ?>
            <article
              class="movimiento-tarjeta"
              data-movimiento
              data-tipo="<?= $escaparMovimiento($tipo) ?>"
              data-fecha="<?= $escaparMovimiento($fechaFiltro) ?>"
            >
              <header class="movimiento-encabezado">
                <span class="movimiento-icono movimiento-icono-<?= $escaparMovimiento($tipoClase) ?>" aria-hidden="true"><?= $escaparMovimiento(strtoupper(substr($tipoClase, 0, 2))) ?></span>
                <div class="movimiento-identidad">
                  <span class="movimiento-etiqueta">ID de producto</span>
                  <h3>#<?= (int) $movimiento['id_producto'] ?></h3>
                </div>
                <span class="movimiento-tipo movimiento-tipo-<?= $escaparMovimiento($tipoClase) ?>"><?= $escaparMovimiento($tipoTexto) ?></span>
              </header>
              <div class="movimiento-producto">
                <span>Producto</span>
                <strong>
                  <?php if ($producto !== null): ?>
                    <?= $escaparMovimiento($producto) ?>
                    <?php if (!empty($movimiento['codigo_producto'])): ?>
                      (<?= $escaparMovimiento($movimiento['codigo_producto']) ?>)
                    <?php endif; ?>
                  <?php else: ?>
                    Producto ID <?= (int) $movimiento['id_producto'] ?>
                  <?php endif; ?>
                </strong>
              </div>
              <div class="movimiento-datos">
                <div><span>Fecha</span><strong><?= $escaparMovimiento($fechaTexto) ?></strong></div>
                <div><span>Motivo</span><strong><?= $escaparMovimiento($movimiento['motivo'] ?: 'Sin motivo registrado') ?></strong></div>
                <div><span>Empleado responsable</span><strong><?= $escaparMovimiento($empleado !== '' ? $empleado : 'No disponible') ?></strong></div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>

        <p class="movimientos-sin-resultados" id="sin-movimientos" <?= $totalMovimientos === 0 ? '' : 'hidden' ?>>
          <?= $errorMovimientos ? 'No hay movimientos disponibles.' : 'No hay movimientos que coincidan con los filtros.' ?>
        </p>
        <div class="movimiento-paginacion" aria-live="polite">
          <span id="resumen-movimientos">Mostrando <?= $totalMovimientos ?> de <?= $totalMovimientos ?> movimientos</span>
        </div>
      </section>
    </main>
  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

  <script>
    const tarjetasMovimiento = [...document.querySelectorAll('[data-movimiento]')];
    const filtroTexto = document.getElementById('filtro-buscar-movimiento');
    const filtroTipo = document.getElementById('filtro-tipo-movimiento');
    const filtroDesde = document.getElementById('filtro-fecha-desde');
    const filtroHasta = document.getElementById('filtro-fecha-hasta');
    const sinMovimientos = document.getElementById('sin-movimientos');

    const filtrarMovimientos = () => {
      const texto = filtroTexto.value.trim().toLocaleLowerCase('es');
      let visibles = 0;

      tarjetasMovimiento.forEach((tarjeta) => {
        const coincideTexto = tarjeta.textContent.toLocaleLowerCase('es').includes(texto);
        const coincideTipo = !filtroTipo.value || tarjeta.dataset.tipo === filtroTipo.value;
        const fecha = tarjeta.dataset.fecha;
        const coincideDesde = !filtroDesde.value || (fecha && fecha >= filtroDesde.value);
        const coincideHasta = !filtroHasta.value || (fecha && fecha <= filtroHasta.value);
        const mostrar = coincideTexto && coincideTipo && coincideDesde && coincideHasta;
        tarjeta.hidden = !mostrar;
        if (mostrar) visibles += 1;
      });

      sinMovimientos.hidden = visibles > 0;
      document.getElementById('cantidad-movimientos').textContent = `${visibles} movimientos`;
      document.getElementById('resumen-movimientos').textContent =
        `Mostrando ${visibles} de ${tarjetasMovimiento.length} movimientos`;
    };

    [filtroTexto, filtroTipo, filtroDesde, filtroHasta].forEach((filtro) => {
      filtro.addEventListener('input', filtrarMovimientos);
      filtro.addEventListener('change', filtrarMovimientos);
    });
  </script>
</body>
</html>
