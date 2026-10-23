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
      <div class="migaja"><a href="inventario.php" class="link-tenue">Gestión de Inventario</a> / Editar</div>
      <div class="encabezado-pagina">
        <h1>Editar producto</h1>
      </div>

      <div class="tarjeta">
    <form action="actualizar_producto.php" method="POST" enctype="multipart/form-data">
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
            required placeholder="Ej. Croquetas Adulto Raza Mediana 15kg">
          </div>

          <div>
            <label class="label-campo" for="id_categoria">Categoría (ID):</label>
            <input 
            type="number" 
            class="campo" 
            id="id_categoria" 
            name="id_categoria" 
            placeholder="ID Categoría" required>
          </div>

          <div>
            <label class="label-campo" for="id_proveedor">Proveedor (ID):</label>
            <input 
            type="number" 
            class="campo" 
            id="id_proveedor" 
            name="id_proveedor" required>
          </div>

          <div>
            <label class="label-campo" for="contenido">Contenido:</label>
            <input 
            type="number" 
            step="0.01" 
            class="campo" 
            id="contenido" 
            name="contenido" 
            required placeholder="Ej. 15">
          </div>

          <div>
            <label class="label-campo" for="unidad_medida">Unidad de medida:</label>
            <select 
            class="campo" 
            id="unidad_medida" 
            name="unidad_medida" 
            required>
              <option value="pza">Pieza (pza)</option>
              <option value="kg">Kilogramo (kg)</option>
            </select>
          </div>

          <div class="campo-completo">
            <label class="label-campo" for="descripcion">Descripción:</label>
            <textarea 
            class="campo" 
            id="descripcion" 
            name="descripcion" 
            rows="2" 
            placeholder="Descripción breve del producto..." required></textarea>
          </div>
        </div>

        <div class="campo-completo-sep">
          <label class="label-campo" for="imagen">Imagen del producto:</label>
          <input 
          type="file" class="campo" id="imagen" name="imagen" accept="image/*" required>
          <img id="preview-crop" alt="Vista previa recortada (cuadrado)">
        </div>

        <div id="modal-crop">
          <div class="caja">
            <h3>Recortar foto (cuadrado)</h3>
            <img id="imagen-a-recortar" alt="Imagen a recortar">
            <div class="crop-acciones">
              <button type="button" id="btn-cancelar-crop" class="btn-secundario">Cancelar</button>
              <button type="button" id="btn-recortar">Recortar y usar</button>
            </div>
          </div>
        </div>
      </fieldset>
      
      <fieldset>
        <legend>Precios e Inventario</legend>

        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="precio_compra">Precio de compra ($):</label>
            <input 
            type="number" 
            step="0.01" 
            class="campo" 
            id="precio_compra" 
            name="precio_compra" 
            required placeholder="0.00">
          </div>

          <div>
            <label class="label-campo" for="precio_venta">Precio de venta ($):</label>
            <input 
            type="number" 
            step="0.01" 
            class="campo" 
            id="precio_venta" 
            name="precio_venta" 
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
            required placeholder="0">
          </div>

          <div>
            <label class="label-campo" for="estado">Estado:</label>
            <select 
            class="campo" 
            id="estado" 
            name="estado" 
            required>
              <option value="activo" selected>Activo</option>
              <option value="inactivo">Inactivo</option>
            </select>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Logística y Almacén</legend>

          <div>
            <label class="label-campo" for="ubicacion">Ubicación / Estante:</label>
            <input 
             type="text" 
             class="campo" 
             id="ubicacion" 
             name="ubicacion" maxlength="20" >
          </div>

      </fieldset>

      <div class="acciones-formulario">
        <a class="btn-secundario" href="inventario.php">Cancelar</a>
        <button type="submit">Guardar</button>
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
