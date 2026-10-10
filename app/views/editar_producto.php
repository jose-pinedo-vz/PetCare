<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Agregar producto</title>
  <link rel="stylesheet" href="css/estilos_base.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
  <style>
    #preview-crop { max-width: 200px; border-radius: 8px; display: none; margin-top: 8px; }
    #modal-crop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.7); z-index: 99; align-items: center; justify-content: center; }
    #modal-crop .caja { background: #fff; padding: 15px; border-radius: 8px; max-width: 500px; width: 90%; }
    #imagen-a-recortar { max-width: 100%; max-height: 60vh; display: block; }
  </style>
</head>

<body>
  <header>
    <div class="marca">
      <img src="img/logo3.svg" alt="Veterinaria PetCare">
    </div>

    <div class="sesion">
      <span>Empleado: Nombre del empleado</span>
      <a href="#">Cerrar sesión</a>
    </div>
  </header>

    <nav>
    <ul>
      <li><a href="mascotas.php">Mascotas</a></li>
      <li><a href="citas.php">Citas</a></li>
      <li><a href="clientes.php">Clientes</a></li>
      <li><a href="empleados.php">Empleados</a></li>
      <li><a href="proveedores.php">Proveedores</a></li>
      <li><a href="productos.php" class="activo">Inventario</a></li>

      <li><a href="#" class="deshabilitado">Ventas</a></li>
      <li><a href="#" class="deshabilitado">Servicios</a></li>
      <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
      <li><a href="#" class="deshabilitado">Veterinaria</a></li>
      <li><a href="#" class="deshabilitado">Pagos</a></li>
      <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
    </ul>
  </nav>

  <main>
    <h1>Editar producto</h1>

    <form action="actualizar_producto.php" method="POST" enctype="multipart/form-data">
       <fieldset>
        <legend style="font-weight: bold; color: #333; padding: 0 5px;">Datos básicos</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
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


          <div style="grid-column: 1 / -1;">
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

          <div style="grid-column: 1 / -1;">
            <label class="label-campo" for="descripcion">Descripción:</label>
            <textarea 
            class="campo" 
            id="descripcion" 
            name="descripcion" 
            rows="2" 
            placeholder="Descripción breve del producto..." required></textarea>
          </div>
        </div>

        <div style="margin-top: 12px;">
          <label class="label-campo" for="imagen">Imagen del producto:</label>
          <input 
          type="file" class="campo" id="imagen" name="imagen" accept="image/*" required>
          <img id="preview-crop" alt="Vista previa recortada (cuadrado)">
        </div>

        <div id="modal-crop">
          <div class="caja">
            <h3 style="margin-top:0;">Recortar foto (cuadrado)</h3>
            <img id="imagen-a-recortar" alt="Imagen a recortar">
            <div style="margin-top:10px; text-align:right; display:flex; gap:8px; justify-content:flex-end;">
              <button type="button" id="btn-cancelar-crop">Cancelar</button>
              <button type="button" id="btn-recortar">Recortar y usar</button>
            </div>
          </div>
        </div>
      </fieldset>
      
      <fieldset>
        <legend style="font-weight: bold; color: #333; padding: 0 5px;">Precios e Inventario</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
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
        <legend style="font-weight: bold; color: #333; padding: 0 5px;">Logística y Almacén</legend>

          <div>
            <label class="label-campo" for="ubicacion">Ubicación / Estante:</label>
            <input 
             type="text" 
             class="campo" 
             id="ubicacion" 
             name="ubicacion" maxlength="20" >
          </div>

      </fieldset>

      <button type="submit">Guardar</button>
      <a class="btn" href="inventario.php">Cancelar</a>
    </form>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
  <script src="js/crop-producto.js"></script>

</body>
</html>
      

