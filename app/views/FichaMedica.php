<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Ficha Veterinaria</title>
    <link rel="stylesheet" href="../views/css/estilos_base.css">
</head>

<body>
  <header>
    <div class="marca">
      <img src="../views/img/logo3.svg" alt="Veterinaria PetCare">
    </div>

    <div class="sesion">
      <span>Empleado: Veterinario</span>
      <a href="/petcare/app/controllers/Login.php?action=logout">Cerrar sesión</a>
    </div>
  </header>

  <nav>
    <ul>
      <li><a href="../views/mascotas.php">Mascotas</a></li>
      <li><a href="../views/citas.php" class="activo">Citas</a></li>
      <li><a href="../views/clientes.php">Clientes</a></li>
      <li><a href="../views/empleados.php">Empleados</a></li>
      <li><a href="../views/proveedores.php">Proveedores</a></li>
      <li><a href="../views/inventario.php">Inventario</a></li>
      <li><a href="#" class="deshabilitado">Ventas</a></li>
      <li><a href="#" class="deshabilitado">Servicios</a></li>
      <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
      <li><a href="#" class="deshabilitado">Veterinaria</a></li>
      <li><a href="#" class="deshabilitado">Pagos</a></li>
      <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
    </ul>
  </nav>

  <main>
    <h1>Ficha Veterinaria</h1>
      
      <!-- Para mostrar errores-->
      <?php if (isset($_SESSION['error'])): ?>
        <div style="background: #ffe6e6; color: #d32f2f; padding: 12px 15px; border-radius: 6px; border: 1px solid #ffcdd2; margin-bottom: 20px; font-weight: bold;">
          <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>
      <!-- -->


      <form action="../controllers/FichaMedicaController.php" method="POST">
        <input type="hidden" name="id_consulta" value="<?= (int)($idConsulta ?? $_SESSION['oldid_consulta'] ?? 0) ?>">
        <input type="hidden" name="id_veterinario" value="<?= (int)($idVeterinario ?? $_SESSION['oldid_veterinario'] ?? 1) ?>">
        
        <fieldset>
          <legend>Datos de la consulta</legend>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
              <label class="label-campo" for="mascota">Mascota*:</label>
              <select class="campo" name="mascota" id="mascota" required> 
                  <option value="">Selecciona una mascota</option>
                  <?php foreach ($mascotas as $m): ?>
                      <option value="<?= (int)$m['id'] ?>">
                          <?= htmlspecialchars($m['nombre']) ?>
                      </option>
                  <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label class="label-campo" for="precio">Costo ($)*:</label>
              <input type="number" step="0.01" min="1" class="campo" id="precio" name="precio" placeholder="Ingresa el costo" required>
            </div>
          </div>
        </fieldset>

        <fieldset>
          <legend>Datos del paciente</legend>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
              <label class="label-campo" for="temperatura">Temperatura (°C)*:</label>
              <input type="number" class="campo" id="temperatura" name="temperatura" min="30" max="45" step="0.1" placeholder="Ej. 38.5" required>
            </div>

            <div>
              <label class="label-campo" for="peso">Peso (kg)*:</label>
              <input type="number" class="campo" id="peso" name="peso" min="0.5" max="100" step="0.1" placeholder="Ej. 10.5" required>
            </div>

            <div>
              <label class="label-campo" for="frecuenciaC">Frecuencia Cardíaca (lpm)*:</label>
              <input type="number" class="campo" id="frecuenciaC" name="frecuenciaC" placeholder="Ej. 90" required>
            </div>

            <div>
              <label class="label-campo" for="frecuenciaR">Frecuencia Respiratoria (rpm)*:</label>
              <input type="number" class="campo" id="frecuenciaR" name="frecuenciaR" placeholder="Ej. 24" required>
            </div>

            <div style="grid-column: 1 / -1;">
              <label class="label-campo" for="sintomas">Síntomas presentados*:</label>
              <textarea class="campo" id="sintomas" name="sintomas" placeholder="Describe los síntomas observados" rows="4" required></textarea> 
            </div>
          </div>
        </fieldset>

        <fieldset>
          <legend>Detalles del tratamiento y diagnóstico</legend>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div style="grid-column: 1 / -1;">
              <label class="label-campo" for="diagnostico">Diagnóstico clínico*:</label> 
              <textarea class="campo" id="diagnostico" name="diagnostico" placeholder="Diagnóstico del paciente" rows="3" required></textarea>
            </div>

            <div>
              <label class="label-campo" for="tratamiento">Tratamiento*:</label> 
              <textarea class="campo" id="tratamiento" name="tratamiento" rows="3" placeholder="Tratamiento prescrito" required></textarea>
            </div>

            <div>
              <label class="label-campo" for="Medicamento">Medicamentos*:</label> 
              <textarea class="campo" id="Medicamento" name="Medicamento" rows="3" placeholder="Medicamentos recetados" required></textarea>
            </div>

            <div>
              <label class="label-campo" for="Dosis">Dosis:</label> 
              <textarea class="campo" id="Dosis" name="Dosis" rows="3" placeholder="Ej. 1 tableta cada 12 horas"></textarea>
            </div>

            <div>
              <label class="label-campo" for="indicaciones">Indicaciones para el dueño*:</label> 
              <textarea class="campo" id="indicaciones" name="indicaciones" rows="3" placeholder="Cuidados y recomendaciones" required></textarea>
            </div>

            <div>
              <label class="label-campo" for="estudiosA">Estudios Asociados:</label> 
              <textarea class="campo" id="estudiosA" name="estudiosA" rows="3" placeholder="Rayos X, análisis de sangre, etc."></textarea>
            </div>

            <div>
              <label class="label-campo" for="fecha">Próxima Cita:</label>
              <input type="date" class="campo" id="fecha" name="fecha" min="<?= date('Y-m-d') ?>">
            </div>

            <div style="grid-column: 1 / -1;">
              <label class="label-campo" for="observaciones">Observaciones:</label> 
              <textarea class="campo" id="observaciones" name="observaciones" placeholder="Notas u observaciones adicionales" rows="3"></textarea>
            </div>
          </div>
        </fieldset>

        <div style="margin-top: 20px;">
          <button type="submit" class="btn">Terminar consulta</button> 
          <a class="btn" href="../views/citas.php" style="background: #757575; margin-left: 8px;">Cancelar</a>
        </div>
      </form>
    </main>

    <footer>
      <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>

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