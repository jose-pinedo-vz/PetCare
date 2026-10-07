<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/estilos_base.css">
    <!-- <link rel="stylesheet" href="css/estilos_ficha.css"> -->
</head>

<body>
  <!-- Todo esto es el menu -->
    <div class="layout">
    <aside class="sidebar">
      <div class="marca">
        <div class="logo-circulo">
          <img src="img/logo.svg" alt="Veterinaria PetCare">
        </div>
      </div>

      <nav>
        <ul>
          <li><a href="mascotas.php">Mascotas</a></li>
          <li><a href="citas.php" class="activo">Citas</a></li>
          <li><a href="clientes.php">Clientes</a></li>
          <li><a href="empleados.php">Empleados</a></li>

          <li><a href="#" class="deshabilitado">Proveedores</a></li>
          <li><a href="#" class="deshabilitado">Inventario</a></li>
          <li><a href="#" class="deshabilitado">Ventas</a></li>
          <li><a href="#" class="deshabilitado">Servicios</a></li>
          <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
          <li><a href="#" class="deshabilitado">Veterinaria</a></li>
          <li><a href="#" class="deshabilitado">Pagos</a></li>
          <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
        </ul>
      </nav>

      <div class="sesion">
        <span>Empleado: Nombre del Empleado</span>
        <a href="#">Cerrar sesión</a>
      </div>
    </aside>

    <main>
      <!-- Para decir de donde era --->
      <div class="migaja">Mascotas/Consulta veterinaria</div>
      <div class="encabezado-pagina">
        <h1>Ficha Veterinaria</h1>
      </div>
      <div class="migaja">Bienvenido "Inserte veterinario"</div>

      <!--La tarjetita de la interfaz real del modulo -->
      <div class="tarjeta">
        <form action="../controllers/FichaMedicaController.php" method="POST">

        <!-- Para mostrar errores-->
        <?php if (isset($_SESSION['error'])): ?>
          <div style="background: #ffe6e6; color: #d32f2f; padding: 12px 15px; border-radius: 6px; border: 1px solid #ffcdd2; margin-bottom: 20px; font-weight: bold;">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
          </div>
        <?php endif; ?>

        <!-- -->
          
          <fieldset>
            <legend>Datos de la consulta</legend>
              <div class="grid-formulario">
                <div>
                <label>Mascota</label>
                <select name="mascota" id="mascota" required> 
                    <option value="">Selecciona una mascota</option>
                    <?php foreach ($mascotas as $m): ?>
                        <option value="<?= (int)$m['id'] ?>">
                            <?= htmlspecialchars($m['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

              <!--Precio--> 
                <div style=" display:flex; justify-content:flex-end;">
                  <label class="label-campo">Costo</label> <!--Decimal(10,2)-->
                  <input type="number" name="precio" min="1" placeholder="Ingresa el precio" required>
                </div>

                <!-- Ventana de datos -->
              
                <h2>Datos del paciente</h2>
                  <label>Temperatura</label>
                  <input type="number" id="temperatura" name="temperatura" min="30" max="45" step="0.1" placeholder="c*" required>
                  <label>Peso</label>
                  <input type="number" id="peso" name="peso" min="0.5" max="100" step="0.1" placeholder="kg" required>
                  <label>Frecuencia Cardiaca</label>
                  <input type="number" id="frecuenciaC" name="frecuenciaC" placeholder="Frecuencia Cardiaca" required>
                  <label>Frecuencia Respiratoria</label>
                  <input type="number" id="frecuenciaR" name="frecuenciaR" placeholder="Frecuencia Respiratoria" required>
                  <div class="grid-formulario">
                    <label class="label-campo">Sintomas</label>
                    <textarea class="campo" name="sintomas" placeholder="Sintomas presentados" rows=15 required></textarea> 
                  <div>

   
              <!--           VENTANA DE TRATAMIENTOS,MEDICAMENTOS Y DETALLES -->
              
                <h2>Detalles del tratamiento del paciente</h2>
                <div class="grid-formulario">
                  <label classs="label-campo">Tratamiento</label> 
                  <textarea class="campo" rows=8 name="tratamiento" placeholder="tratamiento para el paciente" required></textarea>
                </div>
                <div class="grid-formulario">
                  <label classs="label-campo">Medicamentos</label> 
                  <textarea class="campo" rows=8 name="Medicamento" placeholder="medicamentos recetados" required></textarea>
                </div>
                <div class="grid-formulario">
                  <label classs="label-campo">Dosis</label> 
                  <textarea class="campo" rows=8 name="Dosis" placeholder="dosis"></textarea>
                </div>
                <div class="grid-formulario">
                  <label classs="label-campo">Indicaciones</label> 
                  <textarea class="campo" rows=8 name="indicaciones" placeholder="Indicaciones para papa y mama" required></textarea>
                </div>
                <div class="grid-formulario">
                  <label classs="label-campo">Estudios Asociados</label> 
                  <textarea class="campo" rows=8 name="estudiosA" placeholder="Estudios asociados"></textarea>
                </div>

              <!--Datos finales de la cita-->
                <div class="grid-formulario">
                  <label class="label-campo">Observaciones</label> 
                  <textarea class="campo" name="observaciones" placeholder="Escriba observaciones" rows=15></textarea>
                </div>
                <div class="grid-formulario">
                  <label class="label-campo">Diagnostico</label> 
                  <textarea class="campo" name="diagnostico" placeholder="Diagnostico del paciente" rows=15 required></textarea>
                </div>
                <div class="grid-formulario">
                  <label class="label-campo">Proxima Cita</label> <!--Date time-->
                  <input type="date" id="fecha" name="fecha" min="<?= date('Y-m-d') ?>">
                </div>
              </div>
          </fieldset>
          
          <!-- BOtonsitos finales-->
          <div class="grid-formulario">
            <div>
                <button type="submit" class="BtnDerecha">Terminar consulta</button> 
            </div>         
          </div>
        </form>
      </div>
    </main>

  <!--Este script me permite abrir los modales, ventanitas emergentes dentro de la misma pagina -->
  <script>
    const datos=document.getElementById('datosModal');
    const tratamiento=document.getElementById('tratamientoModal');
    document.getElementById('abrirDatos').onclick=()=>datos.showModal();
    document.getElementById('abrirTratamiento').onclick=()=>tratamiento.showModal();
    document.getElementById('cerrarDatos').onclick = () => datos.close();
    document.getElementById('cerrarTratamiento').onclick = () => tratamiento.close();
  </script>
  
</body>
</html>