<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/estilos_base.css">
    <link rel="stylesheet" href="css/estilos_ficha.css">
</head>

<body>
  <!-- Todo esto es nomas el menu -->
    <div class="layout">
    <aside class="sidebar">
      <div class="marca">
        <div class="logo-circulo">
          <img src="img/logo.svg" alt="Veterinaria PetCare">
        </div>
      </div>

      <nav>
        <ul>
          <li><a href="#" class="deshabilitado">Inicio</a></li>
          <li><a href="#" class="deshabilitado">Clientes</a></li>
          <li><a href="#" class="deshabilitado">Mascotas</a></li>
          <li><a href="#" class="deshabilitado">Empleados</a></li>
          <li><a href="#" class="deshabilitado">Proveedores</a></li>
          <li><a href="#" class="deshabilitado">Inventario</a></li>
          <li><a href="#" class="deshabilitado">Ventas</a></li>
          <li><a href="#" class="deshabilitado">Servicios</a></li>
          <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
          <li><a href="FichaMedica.php" class="habilitado">Veterinaria</a></li>
          <li><a href="#" class="deshabilitado">Pagos</a></li>
          <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
          <li><a href="#" class="deshabilitado">Configuración</a></li>
        </ul>
      </nav>

      <div class="sesion">
        <span>Empleado: Nombre del Empleado</span>
        <a href="#">Cerrar sesión</a>
      </div>
    </aside>

    <main>
      <!-- Para decir de donde era o nose, angel no me explico #AyudaAngel --->
      <div class="migaja">Mascotas/Consulta veterinaria</div>
      <div class="encabezado-pagina">
        <h1>Ficha Veterinaria</h1>
      </div>
      <div class="migaja">Bienvenido "Inserte veterinario"</div>

      <!--La tarjetita de la interfaz real del modulo -->
      <div class="tarjeta">
        <form action="#" method="post">
          <fieldset>
            <legend>Datos de la consulta</legend>
              <div class="grid-formulario">

                <label>Mascota</label>
                <select name="mascota" id="mascota"> 
                    <option value="">Selecciona una mascota</option>
                    <?php foreach ($mascotas as $m): ?>
                        <option value="<?= (int)$m['id'] ?>">
                            <?= htmlspecialchars($m['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

              <!-- Ventana de datos -->
              <dialog id="datosModal" class="modal">
                <h2>Datos del paciente</h2>
                <div class="grid-formulario">
                  <label>Sintomas</label>
                  <textarea name="sintomas" placeholder="Sintomas presentados"></textarea>
                  <label>Temperatura</label>
                  <input type="number" id="temperatura" name="temperatura" min="10" max="60" step="0.1" placeholder="c*">
                  <label>Peso</label>
                  <input type="number" id="peso" name="peso" min="0.5" max="100" step="0.1" placeholder="kg">
                  <label>Frecuencia Cardiaca</label>
                  <input type="number" id="frecuenciaC" name="frecuenciaC" placeholder="Frecuencia Cardiaca">
                  <label>Frecuencia Respiratoria</label>
                  <input type="number" id="frecuenciaR" name="frecuenciaR" placeholder="Frecuencia Respiratoria">
                </div>
                <button type="button" class="cerrar BtnDerecha">Cerrar</button>
              </dialog>

              <!--Precio-->
                <div class="grid-formulario">
                  <label class="label-campo">Costo</label> <!--Decimal(10,2)-->
                  <input type="number" name="precio" min="0" placeholder="Ingresa el precio">
                </div>

                
<!--           VENTANA DE TRATAMIENTOS,MEDICAMENTOS Y DETALLES -->
              <dialog id="tratamientoModal" class="modal">
                <h2>Detalles del tratamiento del paciente</h2>
                <div class="grid-formulario">
                  <label>Tratamiento</label> 
                  <textarea name="tratamiento" placeholder="tratamiento para el paciente"></textarea>
                  <label>Medicamentos</label> 
                  <textarea name="Medicamento" placeholder="medicamentos recetados"></textarea>
                  <label>Dosis</label> 
                  <textarea name="Dosis" placeholder="dosis"></textarea>
                  <label>Indicaciones</label> 
                  <textarea name="indicaciones" placeholder="Indicaciones para papa y mama"></textarea>
                  <label>Estudios Asociados</label> 
                  <textarea name="estudiosA" placeholder="Estudios asociados"></textarea>
                </div>
                <button type="button" class="cerrar BtnDerecha">Cerrar</button>
              </dialog>

              <!--Datos finales de la cita-->
                <div class="grid-formulario">
                  <label class="label-campo">Proxima Cita</label> <!--Date time-->
                  <input type="date" id="fecha" name="fecha" min="<?= date('Y-m-d') ?>">
                </div>
                <div class="campo">
                  <label class="label-campo">Observaciones</label> 
                  <textarea class="campo" name="observaciones" placeholder="Escriba observaciones" rows=15></textarea>
                </div>
                <div class="campo">
                  <label class="label-campo">Diagnostico</label> 
                  <textarea class="campo" name="diagnostico" placeholder="Diagnostico del paciente" rows=15></textarea>
                </div>
              </div>
          </fieldset>
          
          <!-- BOtonsitos finales-->
          <div class="grid-formulario">
            <div>
              <button type="button" id="abrirDatos">Datos del paciente</button>
              <button type="button" id="abrirTratamiento">Detalles tratamiento</button>
            </div>

            <div>
              <button type="submit" class="BtnDerecha">Terminar consulta</button>
            </div>         
          </div>
        </form>
      </div>
    </main>

  <!--Este script me permite abrir los modales, ventanitas emergentes dentro de la misma pagina (Es con js, no me regañen porfas) -->
  <script>
    const datos=document.getElementById('datosModal');
    const tratamiento=document.getElementById('tratamientoModal');
    document.getElementById('abrirDatos').onclick=()=>datos.showModal();
    document.getElementById('abrirTratamiento').onclick=()=>tratamiento.showModal();
    document.querySelectorAll('.cerrar').forEach(boton=>{boton.onclick=()=>boton.closest('dialog').close()});
  </script>

</body>
</html>