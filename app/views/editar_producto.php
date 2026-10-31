<?php
session_start();

    // Obtener errores y datos enviados previamente (si existen)
    $errores = $_SESSION['errores'] ?? [];
    $old     = $_SESSION['old'] ?? [];

    // Limpiar la sesión para que las alertas no reaparezcan al recargar
    unset($_SESSION['errores']);
    unset($_SESSION['old']);

if (!isset($_GET['id']) && !$old['id']) {
    // Si alguien entra por la fuerza sin ID, lo mandamos de regreso a la tabla
    header("Location: inventario.php");
    exit();
}
require_once __DIR__ . '/../models/proveedores.php';
$proveedor = obtenerProveedoresActivos();

require_once __DIR__ . '/../models/categ_prod.php';
$categorias = Categoria::obtenerCategoriasActivas();

require_once __DIR__ . '/../models/Productos.php';
$id_producto = (int)($_GET['id'] ?? $old['id'] ?? 0);
$producto = Productos::obtenerProductoPorId((int)($id_producto));


?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Editar producto</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
</head>

<body>

  <div class="layout">

  <?php $paginaActiva = 'inventario'; include __DIR__ . '/layout/sidebar.php'; ?>

    <main>
      <div class="migaja"><a href="inventario.php" class="link-tenue">Gestión de inventario</a> / Editar</div>
      <div class="encabezado-pagina">
        <h1>Editar producto</h1>
      </div>

      <div class="tarjeta">

      <?php if (!empty($errores)): ?>
            <div class="msg-error">
              <h4>Por favor corrige los siguientes errores:</h4>
              <ul>
                <?php foreach ($errores as $error): ?>
                  <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
        <?php endif; ?>

    <form action="../controllers/actualizar_producto.php" method="POST" enctype="multipart/form-data">

      <input type="hidden" id="id_producto" name="id_producto" value="<?= (int)$id_producto?>">
       <fieldset>
        <legend>Datos básicos</legend>

        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="codigo">Código:</label>
            <input
            type="text"
            class="campo"
            id="codigo"
            name="codigo"
            maxlength="20"
            value="<?= htmlspecialchars($old['codigo'] ?? $producto['codigo'] ?? '') ?>"
            required placeholder="Ej. PROD-001">
          </div>


          <div class="campo-completo">
            <label class="label-campo" for="nombre">Nombre:</label>
            <input
            type="text"
            class="campo"
            id="nombre"
            name="nombre"
            maxlength="50"
            value="<?= htmlspecialchars($old['nombre'] ?? $producto['nombre'] ?? '') ?>"
            required placeholder="Ej. Croquetas Adulto Raza Mediana 15kg">
          </div>

          <div class="contenedor-buscador">
                <label class="label-campo" for="id_categoria">Categoría (ID):</label>
                <input
                    type="text"
                    class="campo"
                    id="id_categoria"
                    placeholder="Seleccionar categoría..."
                    autocomplete="off"
                    value="<?= htmlspecialchars($old['id_categoria'] ?? $producto['id_categoria'] ?? '') ?>"
                    required>

                <ul id="lista_categorias" class="lista-resultados">
                    <?php foreach ($categorias as $cat): ?>
                      <li data-id="<?php echo $cat['id_categoria']; ?>" data-nombre="<?php echo htmlspecialchars($cat['nombre']); ?>"><?php echo htmlspecialchars("Id: " . $cat['id_categoria'] . " - " . $cat['nombre']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <input class="label-campo" type="hidden" id="categoria_id_oculto" name="id_categoria" value="<?= htmlspecialchars($old['id_categoria'] ?? $producto['id_categoria'] ?? '') ?>">

            <script src="js/busc_cat.js"></script>

          <div class="contenedor-buscador">
            <label class="label-campo" for="id_proveedor">Proveedor (ID):</label>
            <input
            type="text"
            class="campo"
            id="id_proveedor"
            placeholder="Seleccionar..."
            autocomplete="off"
            value="<?= htmlspecialchars($old['id_proveedor'] ?? $producto['id_proveedor'] ?? '') ?>"
            required>

            <ul id="lista_proveedores" class="lista-resultados">
              <?php foreach ($proveedor as $prov): ?>
                <li data-id="<?php echo $prov['id_proveedor']; ?>" data-razon="<?php echo htmlspecialchars($prov['razon_social']); ?>"><?php echo htmlspecialchars("Id: " . $prov['id_proveedor'] . " - " . $prov['razon_social']); ?>
                </li>
              <?php endforeach; ?>
          </div>
          <input class="label-campo" type="hidden" id="proveedor_id_oculto" name="id_proveedor" value="<?= htmlspecialchars($old['id_proveedor'] ?? $producto['id_proveedor'] ?? '') ?>">
          <script src="js/busc_proveed.js"></script>

          <div>
            <label class="label-campo" for="contenido">Contenido:</label>
            <input
            type="number"
            step="0.01"
            class="campo"
            id="contenido"
            name="contenido"
            value="<?= htmlspecialchars($old['contenido'] ?? $producto['contenido'] ?? '') ?>"
            required placeholder="Ej. 15">
          </div>

          <?php
            $unidadActual = $old['unidad_medida'] ?? $producto['unidad_medida'] ?? '';
          ?>
          <div>
            <label class="label-campo" for="unidad_medida">Unidad de medida:</label>
            <select
            class="campo"
            id="unidad_medida"
            name="unidad_medida"
            required>
              <option value="pza" <?= ($unidadActual === 'pza') ? 'selected' : '' ?>    >Pieza (pza)</option>
              <option value="kg" <?= ($unidadActual === 'kg') ? 'selected' : '' ?>    >Kilogramo (kg)</option>
              <option value="g" <?= ($unidadActual === 'g') ? 'selected' : '' ?>    >Gramos (g)</option>
              <option value="l" <?= ($unidadActual === 'l') ? 'selected' : '' ?>    >Litros (l)</option>
              <option value="ml" <?= ($unidadActual === 'ml') ? 'selected' : '' ?>    >Mililitros (ml)</option>
              <option value="caja" <?= ($unidadActual === 'caja') ? 'selected' : '' ?>    >Caja (caja)</option>
              <option value="paq" <?= ($unidadActual === 'paq') ? 'selected' : '' ?>        >Paquete (paq)</option>
            </select>
          </div>

          <div class="campo-completo">
            <label class="label-campo" for="descripcion">Descripción:</label>
            <textarea
            class="campo"
            id="descripcion"
            name="descripcion"
            rows="2"
            placeholder="Descripción breve del producto..." required><?= htmlspecialchars($old['nombre'] ?? $producto['nombre'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="campo-completo-sep">
          <label class="label-campo" for="imagen">Imagen del producto:</label>
          <input
          type="file" class="campo" id="imagen" name="imagen" accept="image/*">
          <img id="preview-crop" alt="Vista previa recortada (cuadrado)">
        </div>

        <div id="modal-crop">
          <div class="caja">
            <h3>Recortar foto (cuadrado)</h3>
            <img id="imagen-a-recortar" alt="Imagen a recortar">
            <div class="crop-acciones">
              <button type="button" id="btn-cancelar-crop" class="btn btn-secundario btn-chico">Cancelar</button>
              <button type="button" id="btn-recortar" class="btn btn-chico">Recortar y usar</button>
            </div>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Precios e Inventario</legend>

        <div class="grid-formulario">


          <div>
            <label class="label-campo" for="precio_venta">Precio de venta ($):</label>
            <input
            type="number"
            step="0.01"
            class="campo"
            id="precio_venta"
            name="precio_venta"
            value="<?= htmlspecialchars($old['precio_venta'] ?? $producto['precio_venta'] ?? '') ?>"
            required placeholder="0.00">
          </div>

          <div>
            <label class="label-campo" for="existencia">Existencia (Stock):</label>
            <input
            type="number"
            step="0.01"
            class="campo"
            id="existencia"
            name="existencia"
            value="<?= htmlspecialchars($old['existencia'] ?? $producto['existencia'] ?? '') ?>"
            required placeholder="0">
          </div>

          <div>
            <label class="label-campo" for="existencia_minima">Existencia mínima:</label>
            <input
            type="number"
            step="0.01"
            class="campo"
            id="existencia_minima"
            name="existencia_minima"
            value="<?= htmlspecialchars($old['existencia_minima'] ?? $producto['existencia_minima'] ?? '') ?>"
            required placeholder="0">
          </div>

          <div>
            <label class="label-campo" for="existencia_maxima">Existencia máxima:</label>
            <input
            type="number"
            step="0.01"
            class="campo"
            id="existencia_maxima"
            name="existencia_maxima"
            value="<?= htmlspecialchars($old['existencia_maxima'] ?? $producto['existencia_maxima'] ?? '') ?>"
            required placeholder="0">
          </div>

          <div>
            <label class="label-campo" for="fecha_ingreso">Fecha de ingreso:</label>
            <input
             type="date"
             class="campo"
             id="fecha_ingreso"
             name="fecha_ingreso"
              value="<?= htmlspecialchars($old['fecha_ingreso'] ?? $producto['fecha_ingreso'] ?? '') ?>"
             required>
          </div>

          <?php
            $estado = $old['estado'] ?? $producto['estado'] ?? '';
          ?>
          <div>
            <label class="label-campo" for="estado">Estado:</label>
            <select
            class="campo"
            id="estado"
            name="estado"
            required>
              <option value="1" <?= ((int)$estado === 1) ? 'selected' : '' ?>>Activo</option>
              <option value="0" <?= ((int)$estado === 0) ? 'selected' : '' ?>>Inactivo</option>
            </select>
          </div>
        </div>
      </fieldset>



      <div class="acciones-formulario">
        <a class="btn btn-secundario btn-chico" href="inventario.php">Cancelar</a>
        <button type="submit" class="btn btn-chico">Guardar</button>
      </div>
    </form>

      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
  <script src="js/crop-producto.js"></script>
</body>

</html>
