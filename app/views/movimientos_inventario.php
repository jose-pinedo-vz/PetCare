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
            <p class="movimientos-descripcion">Revisa entradas, salidas y ajustes realizados en el inventario.</p>
          </div>
          <a class="btn btn-secundario btn-chico" href="inventario.php">Volver al inventario</a>
        </div>

        <form class="movimientos-toolbar" aria-label="Filtros de movimientos">
          <label class="visualmente-oculto" for="filtro-buscar-movimiento">Buscar movimiento</label>
          <input class="campo movimientos-buscador" id="filtro-buscar-movimiento" type="search" placeholder="Buscar por producto, ID o empleado...">
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
          <button class="btn btn-chico" type="button" disabled>Filtrar</button>
        </form>

        <div class="movimientos-lista-encabezado">
          <div>
            <h3>Historial de movimientos</h3>
            <p>Ejemplos de presentación; no son registros reales.</p>
          </div>
          <span>3 movimientos</span>
        </div>

        <div class="movimientos-lista">
          <article class="movimiento-tarjeta">
            <header class="movimiento-encabezado">
              <span class="movimiento-icono movimiento-icono-entrada" aria-hidden="true">EN</span>
              <div class="movimiento-identidad">
                <span class="movimiento-etiqueta">Movimiento de entrada</span>
                <h3>MOV-0001</h3>
              </div>
              <span class="movimiento-tipo movimiento-tipo-entrada">Entrada</span>
            </header>
            <div class="movimiento-producto">
              <span>Producto</span>
              <strong>Alimento para perro adulto 5 kg</strong>
            </div>
            <div class="movimiento-datos">
              <div><span>Fecha</span><strong>10/11/2026, 08:15</strong></div>
              <div><span>Cantidad</span><strong class="movimiento-cantidad-entrada">+12 unidades</strong></div>
              <div><span>Existencia anterior</span><strong>18</strong></div>
              <div><span>Existencia posterior</span><strong>30</strong></div>
              <div><span>Motivo</span><strong>Recepción de pedido</strong></div>
              <div><span>Empleado responsable</span><strong>María López</strong></div>
              <div><span>Número de factura</span><strong>FAC-10482</strong></div>
              <div class="movimiento-observaciones"><span>Observaciones</span><strong>Pedido recibido completo.</strong></div>
            </div>
          </article>

          <article class="movimiento-tarjeta">
            <header class="movimiento-encabezado">
              <span class="movimiento-icono movimiento-icono-salida" aria-hidden="true">SA</span>
              <div class="movimiento-identidad">
                <span class="movimiento-etiqueta">Movimiento de salida</span>
                <h3>MOV-0002</h3>
              </div>
              <span class="movimiento-tipo movimiento-tipo-salida">Salida</span>
            </header>
            <div class="movimiento-producto">
              <span>Producto</span>
              <strong>Shampoo dermatológico para mascotas</strong>
            </div>
            <div class="movimiento-datos">
              <div><span>Fecha</span><strong>09/11/2026, 16:42</strong></div>
              <div><span>Cantidad</span><strong class="movimiento-cantidad-salida">−3 unidades</strong></div>
              <div><span>Existencia anterior</span><strong>15</strong></div>
              <div><span>Existencia posterior</span><strong>12</strong></div>
              <div><span>Motivo</span><strong>Venta</strong></div>
              <div><span>Empleado responsable</span><strong>Carlos Ramírez</strong></div>
              <div><span>Número de factura</span><strong>FAC-10479</strong></div>
              <div class="movimiento-observaciones"><span>Observaciones</span><strong>Salida por venta de mostrador.</strong></div>
            </div>
          </article>

          <article class="movimiento-tarjeta">
            <header class="movimiento-encabezado">
              <span class="movimiento-icono movimiento-icono-ajuste" aria-hidden="true">AJ</span>
              <div class="movimiento-identidad">
                <span class="movimiento-etiqueta">Ajuste de inventario</span>
                <h3>MOV-0003</h3>
              </div>
              <span class="movimiento-tipo movimiento-tipo-ajuste">Ajuste</span>
            </header>
            <div class="movimiento-producto">
              <span>Producto</span>
              <strong>Juguete de cuerda para perro</strong>
            </div>
            <div class="movimiento-datos">
              <div><span>Fecha</span><strong>08/11/2026, 11:20</strong></div>
              <div><span>Cantidad</span><strong class="movimiento-cantidad-ajuste">−1 unidad</strong></div>
              <div><span>Existencia anterior</span><strong>9</strong></div>
              <div><span>Existencia posterior</span><strong>8</strong></div>
              <div><span>Motivo</span><strong>Corrección de inventario</strong></div>
              <div><span>Empleado responsable</span><strong>Andrea Torres</strong></div>
              <div><span>Número de factura</span><strong>—</strong></div>
              <div class="movimiento-observaciones"><span>Observaciones</span><strong>Una pieza dañada durante el conteo.</strong></div>
            </div>
          </article>
        </div>

        <div class="movimiento-paginacion" aria-label="Paginación del historial">
          <span>Mostrando 3 de 3 movimientos de ejemplo</span>
        </div>
      </section>
    </main>
  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>
</body>
</html>
